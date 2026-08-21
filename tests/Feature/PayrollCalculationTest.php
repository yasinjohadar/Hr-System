<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\OvertimeRecord;
use App\Models\Payroll;
use App\Models\PayrollItem;
use App\Models\SalaryComponent;
use App\Models\TaxSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * اختبارات توصيفية (characterization tests) لمحرك حساب الرواتب.
 *
 * الغرض: تثبيت السلوك الحالي لـ PayrollController::calculate() كما هو — صحيحاً كان
 * أو خاطئاً — قبل استخراجه إلى PayrollCalculationService. أي تغيير في هذه القيم
 * بعد إعادة الهيكلة يعني أن الريفاكتور غيّر النتائج المالية.
 *
 * الاختبارات تمرّ عبر مسار HTTP لا عبر الدوال الخاصة، فتبقى صالحة بعد الاستخراج.
 *
 * ملاحظات على السلوك المُثبَّت هنا (موثّقة في SYSTEM_AUDIT_2026_07.md):
 *  - أيام العمل = كل الأيام ما عدا الجمعة والسبت (مكتوب صلباً، لا يقرأ العطل الرسمية).
 *  - خصم التأخير = (late_minutes / 15) × (salary × 0.01) — ثوابت مكتوبة صلباً
 *    ولا تُقرأ من AttendanceRule.
 *  - خصم الإجازة غير المدفوعة = (salary / 30) × الأيام.
 *  - مساهمات صاحب العمل = 12% و 2% من الإجمالي، مكتوبة صلباً لا من tax_settings.
 *  - لا يوجد round() على المكونات قبل الحفظ في decimal(12,2).
 */
class PayrollCalculationTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = $this->createPayrollAdmin();
    }

    /**
     * مستخدم بصلاحيات الرواتب الكاملة وبدور لا يخضع لتقييد نطاق القسم.
     */
    private function createPayrollAdmin(): User
    {
        foreach (['payroll-list', 'payroll-create', 'payroll-edit', 'payroll-show', 'payroll-delete'] as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }

        $role = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $role->givePermissionTo(Permission::whereIn('name', [
            'payroll-list', 'payroll-create', 'payroll-edit', 'payroll-show', 'payroll-delete',
        ])->get());

        $user = User::factory()->create(['is_active' => true]);
        $user->assignRole('admin');

        return $user;
    }

    /**
     * تشغيل الحساب على كشف راتب وإرجاعه محدَّثاً من قاعدة البيانات.
     */
    private function calculate(Payroll $payroll): Payroll
    {
        $this->actingAs($this->admin)
            ->post(route('admin.payrolls.calculate', $payroll->id))
            ->assertRedirect();

        return $payroll->fresh();
    }

    /**
     * فترة ثابتة لا تعتمد على تاريخ التشغيل: مارس 2026.
     * يبدأ يوم الأحد، وفيه 31 يوماً منها 8 أيام جمعة/سبت
     * (الجمعة: 6،13،20،27 — السبت: 7،14،21،28) ⇒ 23 يوم عمل.
     */
    private function marchPayroll(Employee $employee): Payroll
    {
        return Payroll::factory()
            ->for($employee)
            ->forPeriod('2026-03-01', '2026-03-31')
            ->create();
    }

    public function test_base_salary_is_copied_from_the_employee(): void
    {
        $employee = Employee::factory()->salary(6000)->create();

        $payroll = $this->calculate($this->marchPayroll($employee));

        $this->assertEquals(6000.00, (float) $payroll->base_salary);
        $this->assertSame('calculated', $payroll->status);
    }

    public function test_employee_without_salary_is_treated_as_zero(): void
    {
        $employee = Employee::factory()->withoutSalary()->create();

        $payroll = $this->calculate($this->marchPayroll($employee));

        $this->assertEquals(0.0, (float) $payroll->base_salary);
        $this->assertEquals(0.0, (float) $payroll->net_salary);
    }

    public function test_working_days_exclude_friday_and_saturday_only(): void
    {
        $employee = Employee::factory()->salary(6000)->create();

        $payroll = $this->calculate($this->marchPayroll($employee));

        // مارس 2026: نحسب المتوقع بنفس قاعدة الكود (استثناء الجمعة والسبت)
        $expected = 0;
        $cursor = \Carbon\Carbon::parse('2026-03-01');
        while ($cursor->lte(\Carbon\Carbon::parse('2026-03-31'))) {
            if (! in_array($cursor->dayOfWeek, [\Carbon\Carbon::FRIDAY, \Carbon\Carbon::SATURDAY], true)) {
                $expected++;
            }
            $cursor->addDay();
        }

        $this->assertSame($expected, (int) $payroll->working_days);
        // مارس 2026 يبدأ الأحد: 31 يوماً − (4 جمعة + 4 سبت) = 23
        // توثيق: العطل الرسمية (public_holidays) لا تُخصم من أيام العمل حالياً
        $this->assertSame(23, (int) $payroll->working_days);
    }

    public function test_present_and_absent_days_are_counted_from_attendance(): void
    {
        $employee = Employee::factory()->salary(6000)->create();

        Attendance::factory()->for($employee)->present()->onDate('2026-03-02')->create();
        Attendance::factory()->for($employee)->present()->onDate('2026-03-03')->create();
        Attendance::factory()->for($employee)->absent()->onDate('2026-03-04')->create();

        // خارج الفترة — يجب ألا يُحتسب
        Attendance::factory()->for($employee)->present()->onDate('2026-04-01')->create();

        $payroll = $this->calculate($this->marchPayroll($employee));

        $this->assertSame(2, (int) $payroll->present_days);
        $this->assertSame(1, (int) $payroll->absent_days);
    }

    public function test_late_deduction_uses_the_hardcoded_fifteen_minute_one_percent_rule(): void
    {
        $employee = Employee::factory()->salary(6000)->create();

        // 30 دقيقة تأخير: (30 / 15) × (6000 × 0.01) = 2 × 60 = 120
        Attendance::factory()->for($employee)->late(30)->onDate('2026-03-02')->create();

        $payroll = $this->calculate($this->marchPayroll($employee));

        $this->assertSame(1, (int) $payroll->late_days);
        $this->assertEquals(120.00, (float) $payroll->late_deduction);
    }

    public function test_late_minutes_below_fifteen_still_produce_a_fractional_deduction(): void
    {
        $employee = Employee::factory()->salary(6000)->create();

        // 5 دقائق: (5 / 15) × 60 = 20 — لا يوجد حد أدنى ولا تقريب لأقرب شريحة
        Attendance::factory()->for($employee)->late(5)->onDate('2026-03-02')->create();

        $payroll = $this->calculate($this->marchPayroll($employee));

        $this->assertEquals(20.00, (float) $payroll->late_deduction);
    }

    public function test_absent_day_alone_does_not_create_a_deduction(): void
    {
        $employee = Employee::factory()->salary(6000)->create();

        Attendance::factory()->for($employee)->absent()->onDate('2026-03-02')->create();

        $payroll = $this->calculate($this->marchPayroll($employee));

        // سلوك مُثبَّت: الغياب يُعَد ولا يُخصم — لا يوجد خصم غياب في المحرك
        $this->assertSame(1, (int) $payroll->absent_days);
        $this->assertEquals(0.0, (float) $payroll->late_deduction);
        $this->assertEquals(6000.00, (float) $payroll->gross_salary);
    }

    public function test_unpaid_leave_is_deducted_at_one_thirtieth_of_salary_per_day(): void
    {
        $employee = Employee::factory()->salary(6000)->create();
        $unpaid = LeaveType::factory()->unpaid()->create();

        LeaveRequest::factory()
            ->for($employee)
            ->for($unpaid, 'leaveType')
            ->approved()
            ->between('2026-03-10', '2026-03-12')
            ->create();

        $payroll = $this->calculate($this->marchPayroll($employee));

        // 3 أيام × (6000 / 30) = 600
        $this->assertSame(3, (int) $payroll->leave_days);
        $this->assertEquals(600.00, (float) $payroll->leave_deduction);
    }

    public function test_paid_leave_counts_days_without_deduction(): void
    {
        $employee = Employee::factory()->salary(6000)->create();
        $paid = LeaveType::factory()->paid()->create();

        LeaveRequest::factory()
            ->for($employee)
            ->for($paid, 'leaveType')
            ->approved()
            ->between('2026-03-10', '2026-03-12')
            ->create();

        $payroll = $this->calculate($this->marchPayroll($employee));

        $this->assertSame(3, (int) $payroll->leave_days);
        $this->assertEquals(0.0, (float) $payroll->leave_deduction);
    }

    public function test_pending_leave_is_ignored(): void
    {
        $employee = Employee::factory()->salary(6000)->create();
        $unpaid = LeaveType::factory()->unpaid()->create();

        LeaveRequest::factory()
            ->for($employee)
            ->for($unpaid, 'leaveType')
            ->between('2026-03-10', '2026-03-12')
            ->create(); // status = pending

        $payroll = $this->calculate($this->marchPayroll($employee));

        $this->assertSame(0, (int) $payroll->leave_days);
        $this->assertEquals(0.0, (float) $payroll->leave_deduction);
    }

    public function test_leave_overlapping_the_period_edge_is_clipped_to_the_period(): void
    {
        $employee = Employee::factory()->salary(6000)->create();
        $unpaid = LeaveType::factory()->unpaid()->create();

        // إجازة تبدأ قبل الفترة وتنتهي داخلها — يجب أن تُحتسب من 1 مارس فقط
        LeaveRequest::factory()
            ->for($employee)
            ->for($unpaid, 'leaveType')
            ->approved()
            ->between('2026-02-25', '2026-03-03')
            ->create();

        $payroll = $this->calculate($this->marchPayroll($employee));

        // 1، 2، 3 مارس = 3 أيام
        $this->assertSame(3, (int) $payroll->leave_days);
        $this->assertEquals(600.00, (float) $payroll->leave_deduction);
    }

    public function test_approved_overtime_is_added_and_linked_to_the_payroll(): void
    {
        $employee = Employee::factory()->salary(6000)->create();

        $record = OvertimeRecord::factory()
            ->for($employee)
            ->approved()
            ->hours(4, 300)
            ->onDate('2026-03-05')
            ->create();

        $payroll = $this->calculate($this->marchPayroll($employee));

        $this->assertEquals(4.0, (float) $payroll->overtime_hours);
        $this->assertEquals(300.00, (float) $payroll->overtime_amount);
        // المحرك يربط السجل بكشف الراتب لمنع احتسابه مرتين
        $this->assertSame($payroll->id, $record->fresh()->payroll_id);
    }

    public function test_pending_overtime_is_ignored(): void
    {
        $employee = Employee::factory()->salary(6000)->create();

        OvertimeRecord::factory()
            ->for($employee)
            ->pending()
            ->hours(4, 300)
            ->onDate('2026-03-05')
            ->create();

        $payroll = $this->calculate($this->marchPayroll($employee));

        $this->assertEquals(0.0, (float) $payroll->overtime_amount);
    }

    public function test_overtime_already_linked_to_another_payroll_is_not_double_counted(): void
    {
        $employee = Employee::factory()->salary(6000)->create();

        $otherPayroll = Payroll::factory()->for($employee)->forPeriod('2026-02-01', '2026-02-28')->create();

        OvertimeRecord::factory()
            ->for($employee)
            ->approved()
            ->hours(4, 300)
            ->onDate('2026-03-05')
            ->create(['payroll_id' => $otherPayroll->id]);

        $payroll = $this->calculate($this->marchPayroll($employee));

        $this->assertEquals(0.0, (float) $payroll->overtime_amount);
    }

    public function test_fixed_and_percentage_components_are_aggregated_by_type(): void
    {
        $employee = Employee::factory()->salary(6000)->create();

        SalaryComponent::factory()->allowance()->fixed(500)->create();
        SalaryComponent::factory()->allowance()->percentage(10)->create();   // 600
        SalaryComponent::factory()->deduction()->fixed(200)->create();
        SalaryComponent::factory()->bonus()->fixed(300)->create();

        $payroll = $this->calculate($this->marchPayroll($employee));

        $this->assertEquals(1100.00, (float) $payroll->total_allowances);
        $this->assertEquals(200.00, (float) $payroll->total_deductions);
        $this->assertEquals(300.00, (float) $payroll->bonuses);
    }

    public function test_components_create_payroll_items_and_recalculation_replaces_them(): void
    {
        $employee = Employee::factory()->salary(6000)->create();
        SalaryComponent::factory()->allowance()->fixed(500)->create();

        $payroll = $this->marchPayroll($employee);

        $this->calculate($payroll);
        $this->assertSame(1, PayrollItem::where('payroll_id', $payroll->id)->count());

        // إعادة الحساب يجب ألا تُضاعف البنود
        $this->calculate($payroll);
        $this->assertSame(1, PayrollItem::where('payroll_id', $payroll->id)->count());
    }

    public function test_zero_value_components_do_not_create_items(): void
    {
        $employee = Employee::factory()->salary(6000)->create();
        SalaryComponent::factory()->allowance()->fixed(0)->create();

        $payroll = $this->calculate($this->marchPayroll($employee));

        // سلوك مُثبَّت: الشرط `$amount > 0` يُسقط البنود الصفرية
        $this->assertSame(0, PayrollItem::where('payroll_id', $payroll->id)->count());
        $this->assertEquals(0.0, (float) $payroll->total_allowances);
    }

    public function test_attendance_based_component_multiplies_present_days(): void
    {
        $employee = Employee::factory()->salary(6000)->create();
        SalaryComponent::factory()->allowance()->attendanceBased(10)->create();

        Attendance::factory()->for($employee)->present()->onDate('2026-03-02')->create();
        Attendance::factory()->for($employee)->present()->onDate('2026-03-03')->create();

        $payroll = $this->calculate($this->marchPayroll($employee));

        $this->assertEquals(20.00, (float) $payroll->total_allowances);
    }

    public function test_income_tax_is_a_percentage_of_gross_salary(): void
    {
        $employee = Employee::factory()->salary(6000)->create();
        TaxSetting::factory()->incomeTax(10)->create();

        $payroll = $this->calculate($this->marchPayroll($employee));

        // الإجمالي = 6000، الضريبة = 10%
        $this->assertEquals(6000.00, (float) $payroll->gross_salary);
        $this->assertEquals(600.00, (float) $payroll->income_tax);
    }

    public function test_tax_exemption_reduces_the_taxable_amount(): void
    {
        $employee = Employee::factory()->salary(6000)->create();
        TaxSetting::factory()->incomeTax(10)->withExemption(1000)->create();

        $payroll = $this->calculate($this->marchPayroll($employee));

        // (6000 - 1000) × 10% = 500
        $this->assertEquals(500.00, (float) $payroll->income_tax);
    }

    public function test_inactive_tax_setting_is_ignored(): void
    {
        $employee = Employee::factory()->salary(6000)->create();
        TaxSetting::factory()->incomeTax(10)->inactive()->create();

        $payroll = $this->calculate($this->marchPayroll($employee));

        $this->assertEquals(0.0, (float) $payroll->income_tax);
    }

    public function test_employer_contributions_are_hardcoded_at_twelve_and_two_percent(): void
    {
        $employee = Employee::factory()->salary(6000)->create();

        $payroll = $this->calculate($this->marchPayroll($employee));

        // سلوك مُثبَّت: النسب مكتوبة صلباً في الكود ولا تُقرأ من tax_settings
        $this->assertEquals(720.00, (float) $payroll->social_insurance_employer);
        $this->assertEquals(120.00, (float) $payroll->health_insurance_employer);
        $this->assertEquals(6840.00, (float) $payroll->total_employer_cost);
    }

    public function test_net_salary_subtracts_deductions_leave_late_and_taxes(): void
    {
        $employee = Employee::factory()->salary(6000)->create();

        SalaryComponent::factory()->allowance()->fixed(1000)->create();
        SalaryComponent::factory()->deduction()->fixed(200)->create();
        TaxSetting::factory()->incomeTax(10)->create();

        Attendance::factory()->for($employee)->late(30)->onDate('2026-03-02')->create();

        $unpaid = LeaveType::factory()->unpaid()->create();
        LeaveRequest::factory()
            ->for($employee)
            ->for($unpaid, 'leaveType')
            ->approved()
            ->between('2026-03-10', '2026-03-10')
            ->create();

        $payroll = $this->calculate($this->marchPayroll($employee));

        // الإجمالي = 6000 + 1000 + 0 بونص + 0 أوفرتايم = 7000
        $this->assertEquals(7000.00, (float) $payroll->gross_salary);
        // الضريبة = 700، إجمالي الضرائب = 700
        $this->assertEquals(700.00, (float) $payroll->total_taxes);
        // خصم التأخير = 120، خصم الإجازة = 200، خصومات المكونات = 200
        $this->assertEquals(120.00, (float) $payroll->late_deduction);
        $this->assertEquals(200.00, (float) $payroll->leave_deduction);
        // الصافي = 7000 - 200 - 200 - 120 - 700 = 5780
        $this->assertEquals(5780.00, (float) $payroll->net_salary);
    }

    public function test_a_paid_payroll_cannot_be_recalculated(): void
    {
        $employee = Employee::factory()->salary(6000)->create();
        $payroll = Payroll::factory()->for($employee)->forPeriod('2026-03-01', '2026-03-31')->paid()->create();

        $this->actingAs($this->admin)
            ->post(route('admin.payrolls.calculate', $payroll->id))
            ->assertRedirect();

        $this->assertSame('paid', $payroll->fresh()->status);
        $this->assertEquals(0.0, (float) $payroll->fresh()->base_salary);
    }

    public function test_calculate_requires_the_payroll_create_permission(): void
    {
        $employee = Employee::factory()->salary(6000)->create();
        $payroll = $this->marchPayroll($employee);

        $stranger = User::factory()->create(['is_active' => true]);
        $stranger->assignRole(Role::firstOrCreate(['name' => 'viewer', 'guard_name' => 'web']));

        $this->actingAs($stranger)
            ->post(route('admin.payrolls.calculate', $payroll->id))
            ->assertForbidden();

        $this->assertSame('draft', $payroll->fresh()->status);
    }
}
