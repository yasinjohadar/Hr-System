<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Employee;
use App\Services\DepartmentHeadRoleService;
use Illuminate\Database\Seeder;

/**
 * هيكل تنظيمي متعدّد المستويات لمعاينة صفحة admin/team/structure، وإصلاح
 * تعيين رؤساء الأقسام الناقص من DepartmentSeeder (انظر تعليقه).
 *
 * يُشغَّل بعد EmployeeSeeder عمداً — يحتاج موظفين حقيقيين موجودين لكي
 * يُعيد توزيعهم على الأقسام الفرعية الجديدة.
 */
class DepartmentStructureSeeder extends Seeder
{
    public function run(): void
    {
        $this->grantMissingDepartmentHeadRoles();
        $this->seedSubDepartments();
    }

    /**
     * HR وIT لهما manager_id صحيح (مفتاح مستخدم فعلي)، لكن السيدر القديم
     * كان يضبطه عبر update() مباشر متجاوزاً DepartmentHeadRoleService —
     * فلا يملك صاحب القسم دور department_head، ولا يقدر فعلياً على فتح
     * admin/team/* رغم أنه "مدير القسم" رسمياً في قاعدة البيانات.
     *
     * نمرّر manager_id الحالي كما هو (لا نغيّر من هو المدير) عبر الخدمة
     * الرسمية فقط لمنح الدور الناقص.
     */
    private function grantMissingDepartmentHeadRoles(): void
    {
        $roleService = app(DepartmentHeadRoleService::class);

        Department::whereNotNull('manager_id')->with('manager')->get()
            ->each(function (Department $department) use ($roleService) {
                if ($department->manager && ! $department->manager->hasRole('department_head') && ! $department->manager->hasRole('admin')) {
                    $roleService->assignDepartments($department->manager, [$department->id]);
                    $this->command->info("منح دور department_head لـ {$department->manager->name} (مدير {$department->name}).");
                }
            });
    }

    /**
     * أقسام فرعية تحت IT وFinance — القاعدة كانت مسطّحة بالكامل (كل الأقسام
     * الثمانية بلا parent_id)، فلا يمكن لصفحة الهيكل التنظيمي أن تُظهر أي
     * تعشيش فعلي. ننقل موظفاً أو موظفَين من كل قسم أب إلى فرعه الجديد
     * ليظهر التوزيع الهرمي فعلياً لا شكلاً فارغاً.
     */
    private function seedSubDepartments(): void
    {
        $itDept = Department::where('code', 'IT')->first();
        $finDept = Department::where('code', 'FIN')->first();

        if ($itDept) {
            $dev = Department::firstOrCreate(
                ['code' => 'IT-DEV'],
                ['name' => 'تطوير البرمجيات', 'description' => 'تطوير وصيانة أنظمة الشركة الداخلية', 'parent_id' => $itDept->id, 'is_active' => true]
            );
            $support = Department::firstOrCreate(
                ['code' => 'IT-SUP'],
                ['name' => 'الدعم الفني', 'description' => 'دعم المستخدمين وصيانة الأجهزة والشبكات', 'parent_id' => $itDept->id, 'is_active' => true]
            );

            $this->moveOneEmployee($itDept->id, $dev->id);
            $this->moveOneEmployee($itDept->id, $support->id);
        }

        if ($finDept) {
            $accounting = Department::firstOrCreate(
                ['code' => 'FIN-ACC'],
                ['name' => 'المحاسبة', 'description' => 'القيود المحاسبية والتسويات المالية', 'parent_id' => $finDept->id, 'is_active' => true]
            );

            $this->moveOneEmployee($finDept->id, $accounting->id);
        }
    }

    /**
     * ينقل أقدم موظف نشط لم يُنقَل بعد من $fromDepartmentId إلى
     * $toDepartmentId. idempotent: إن كان القسم الفرعي يملك موظفاً بالفعل
     * (تشغيل سابق للسيدر) لا يُنقَل أحد آخر.
     */
    private function moveOneEmployee(int $fromDepartmentId, int $toDepartmentId): void
    {
        if (Employee::where('department_id', $toDepartmentId)->exists()) {
            return;
        }

        $employee = Employee::where('department_id', $fromDepartmentId)
            ->where('is_active', true)
            ->orderBy('id')
            ->first();

        if (! $employee) {
            return;
        }

        $employee->update(['department_id' => $toDepartmentId]);
    }
}
