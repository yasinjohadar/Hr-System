<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\LeaveAccrualRule;
use App\Models\LeaveBalance;
use App\Models\LeaveType;
use App\Services\LeaveAccrualService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * اختبارات استحقاق الإجازات.
 *
 * تحذير: بعض الاختبارات هنا موسومة بـ markTestIncomplete وتصف عيوباً قائمة
 * موثّقة في SYSTEM_AUDIT_2026_07.md (القسم 4.2). هي مكتوبة بالسلوك الصحيح
 * المطلوب، لا بالسلوك الحالي، لتصبح معياراً عند إصلاح الخدمة:
 *
 *   1. لا يوجد مفتاح idempotency — تشغيل الاستحقاق مرتين لنفس الشهر يضيف الأيام مرتين.
 *   2. (int) round($days) يبتر الكسور — 1.75 يوم/شهر تصبح 2 → 24 يوماً سنوياً بدل 21.
 *
 * الاختبارات غير الموسومة تُثبّت السلوك الصحيح القائم فعلاً.
 */
class LeaveAccrualServiceTest extends TestCase
{
    use RefreshDatabase;

    private function service(): LeaveAccrualService
    {
        return app(LeaveAccrualService::class);
    }

    public function test_accrual_creates_a_balance_for_an_active_employee(): void
    {
        $employee = Employee::factory()->create();
        $type = LeaveType::factory()->create();
        LeaveAccrualRule::factory()->for($type, 'leaveType')->perMonth(2)->create();

        $processed = $this->service()->runMonthlyAccrual(2026, 3);

        $this->assertSame(1, $processed);

        $balance = LeaveBalance::where('employee_id', $employee->id)
            ->where('leave_type_id', $type->id)
            ->where('year', 2026)
            ->first();

        $this->assertNotNull($balance);
        $this->assertSame(2, (int) $balance->total_days);
        $this->assertSame(2, (int) $balance->remaining_days);
    }

    public function test_accrual_skips_inactive_employees(): void
    {
        Employee::factory()->inactive()->create();
        $type = LeaveType::factory()->create();
        LeaveAccrualRule::factory()->for($type, 'leaveType')->perMonth(2)->create();

        $processed = $this->service()->runMonthlyAccrual(2026, 3);

        $this->assertSame(0, $processed);
        $this->assertSame(0, LeaveBalance::count());
    }

    public function test_inactive_rules_are_ignored(): void
    {
        Employee::factory()->create();
        LeaveAccrualRule::factory()->inactive()->perMonth(2)->create();

        $this->assertSame(0, $this->service()->runMonthlyAccrual(2026, 3));
        $this->assertSame(0, LeaveBalance::count());
    }

    public function test_max_balance_caps_the_total(): void
    {
        $employee = Employee::factory()->create();
        $type = LeaveType::factory()->create();
        LeaveAccrualRule::factory()->for($type, 'leaveType')->perMonth(5)->maxBalance(3)->create();

        $this->service()->runMonthlyAccrual(2026, 3);

        $balance = LeaveBalance::where('employee_id', $employee->id)->first();
        $this->assertSame(3, (int) $balance->total_days);
    }

    public function test_remaining_days_accounts_for_used_and_carried_forward(): void
    {
        $employee = Employee::factory()->create();
        $type = LeaveType::factory()->create();

        $balance = LeaveBalance::factory()
            ->for($employee)
            ->for($type, 'leaveType')
            ->create([
                'year' => 2026,
                'total_days' => 10,
                'used_days' => 4,
                'carried_forward' => 2,
            ]);

        $balance->updateRemaining();

        // 10 + 2 - 4 = 8
        $this->assertSame(8, (int) $balance->fresh()->remaining_days);
    }

    /**
     * عيب قائم رقم 1: غياب idempotency.
     */
    public function test_running_accrual_twice_for_the_same_month_must_not_double_count(): void
    {
        $employee = Employee::factory()->create();
        $type = LeaveType::factory()->create();
        LeaveAccrualRule::factory()->for($type, 'leaveType')->perMonth(2)->create();

        $this->service()->runMonthlyAccrual(2026, 3);
        $this->service()->runMonthlyAccrual(2026, 3);

        $balance = LeaveBalance::where('employee_id', $employee->id)->first();

        $this->markTestIncomplete(
            'عيب قائم: لا يوجد مفتاح idempotency في LeaveAccrualService. '
            . 'الرصيد الآن ' . $balance->total_days . ' بدل 2. '
            . 'الإصلاح: عمود last_accrued_period أو جدول قيود استحقاق.'
        );

        $this->assertSame(2, (int) $balance->total_days);
    }

    /**
     * عيب قائم رقم 2: بتر الكسور العشرية.
     */
    public function test_fractional_accrual_days_must_not_be_rounded_to_integers(): void
    {
        $employee = Employee::factory()->create();
        $type = LeaveType::factory()->create();
        // 1.75 يوم/شهر = 21 يوماً سنوياً
        LeaveAccrualRule::factory()->for($type, 'leaveType')->perMonth(1.75)->create();

        for ($month = 1; $month <= 12; $month++) {
            $this->service()->runMonthlyAccrual(2026, $month);
        }

        $balance = LeaveBalance::where('employee_id', $employee->id)->first();

        $this->markTestIncomplete(
            'عيب قائم: (int) round() في LeaveAccrualService:51 يحوّل 1.75 إلى 2، '
            . 'والرصيد الآن ' . $balance->total_days . ' بدل 21. '
            . 'الإصلاح: تحويل أعمدة الأرصدة إلى decimal(6,2) وإزالة التقريب.'
        );

        $this->assertEqualsWithDelta(21.0, (float) $balance->total_days, 0.01);
    }
}
