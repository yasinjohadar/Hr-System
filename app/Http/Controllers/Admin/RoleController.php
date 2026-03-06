<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;

class RoleController extends Controller
{

public function __construct()
{
    // يمكنه فقط رؤية قائمة الصلاحيات (index)
    $this->middleware(['permission:role-list'])->only('index');

    // يمكنه فقط إنشاء صلاحية جديدة (create + store)
    $this->middleware(['permission:role-create'])->only(['create', 'store']);

    // يمكنه فقط تعديل الصلاحية (edit + update)
    $this->middleware(['permission:role-edit'])->only(['edit', 'update']);

    // يمكنه فقط حذف الصلاحية (destroy)
    $this->middleware(['permission:role-delete'])->only('destroy');
}

    /**
     * تصنيفات الصلاحيات لعرضها مجمعة في الواجهة (مفتاح = عنوان التصنيف، قيمة = بادئات أسماء الصلاحيات).
     */
    protected function permissionCategoryPrefixes(): array
    {
        return [
            'المستخدمون والأدوار' => ['role-', 'user-'],
            'إدارة الموارد البشرية' => ['employee-', 'department-', 'position-', 'branch-', 'country-', 'currency-'],
            'الرواتب وكشوف الرواتب' => ['salary-', 'payroll-', 'salary-component-', 'tax-setting-', 'bank-account-', 'payroll-payment-', 'payroll-approval-'],
            'إدارة الإجازات' => ['leave-type-', 'leave-request-', 'leave-balance-'],
            'الحضور والانصراف' => ['attendance-', 'shift-', 'shift-assignment-', 'attendance-rule-', 'overtime-', 'attendance-location-', 'attendance-break-'],
            'التقييمات والتدريب' => ['performance-review-', 'training-', 'training-record-'],
            'التوظيف والعروض' => ['job-vacancy-', 'candidate-', 'job-application-', 'interview-', 'offer-letter-', 'requisition-'],
            'المزايا والتعويضات' => ['benefit-type-', 'employee-benefit-'],
            'التقارير' => ['report-', 'reports-'],
            'المصروفات' => ['expense-category-', 'expense-request-'],
            'المستندات والمهارات والشهادات والأهداف' => ['employee-document-', 'employee-skill-', 'employee-certificate-', 'employee-goal-'],
            'إنهاء الخدمة' => ['employee-exit-'],
            'الأصول' => ['asset-', 'asset-assignment-', 'asset-maintenance-'],
            'المخالفات والتأديبات' => ['violation-type-', 'disciplinary-action-', 'employee-violation-'],
            'المشاريع والمهام' => ['project-', 'task-'],
            'التذاكر والاجتماعات' => ['ticket-', 'meeting-'],
            'التقييم 360 والمكافآت' => ['feedback-request-', 'reward-type-', 'employee-reward-'],
            'الاستقبال والت onboarding' => ['onboarding-template-', 'onboarding-process-'],
            'سجلات التدقيق' => ['audit-log-'],
            'الاستبيانات والقوالب' => ['survey-', 'email-template-', 'document-template-'],
            'الإعدادات والإشعارات' => ['setting-', 'notification-', 'settings-manage'],
            'لوحة التحكم والهيكل والدليل' => ['dashboard-view', 'organization-chart-view', 'employee-directory-view', 'workflow-', 'succession-plan-', 'calendar-', 'export-data'],
        ];
    }

    /**
     * تجميع الصلاحيات حسب التصنيفات المعرّفة.
     *
     * @return array<string, \Illuminate\Support\Collection>
     */
    protected function groupPermissionsByCategory(): array
    {
        $all = Permission::orderBy('name')->get();
        $prefixes = $this->permissionCategoryPrefixes();
        $grouped = [];
        foreach ($prefixes as $categoryName => $prefixList) {
            $grouped[$categoryName] = collect();
        }
        $grouped['أخرى'] = collect();

        foreach ($all as $permission) {
            $placed = false;
            foreach ($prefixes as $categoryName => $prefixList) {
                foreach ($prefixList as $prefix) {
                    if (str_starts_with($permission->name, $prefix)) {
                        $grouped[$categoryName]->push($permission);
                        $placed = true;
                        break 2;
                    }
                }
            }
            if (!$placed) {
                $grouped['أخرى']->push($permission);
            }
        }

        return array_filter($grouped, fn ($c) => $c->isNotEmpty());
    }




    public function index()
        {
            $permissions = Permission::all();
            $roles = Role::all();
            return view("admin.pages.roles.index" , compact("roles" , "permissions"));
        }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $permissionsGrouped = $this->groupPermissionsByCategory();
        return view("admin.pages.roles.create", compact("permissionsGrouped"));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $role = Role::create([
            "name" => $request->name
        ]);

        $role->syncPermissions($request->permissions);

        return back()->with("success" , "تم اضافة الروول بنجاح");
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $role = Role::findOrFail($id);
        $permissionsGrouped = $this->groupPermissionsByCategory();
        return view("admin.pages.roles.edit", compact("role", "permissionsGrouped"));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $role = Role::findOrFail($request->id);

        $role->update([

            "name" => $request->name
            ]
        );
        $role->syncPermissions($request->permissions);
        return redirect()->route("roles.index")->with("success" , "تم تعديل الروول بنجاح");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request )
    {
        $role = Role::findOrFail($request->id);
        $role->delete();
        return redirect()->route("roles.index")->with("success" , "تم حذف الدور بنجاح");
    }
}
