<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Department;
use Illuminate\Http\Request;

class OrganizationChartController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:organization-chart-view')->only(['index', 'getData']);
    }

    /**
     * عرض الهيكل التنظيمي
     */
    public function index(Request $request)
    {
        $viewType = $request->get('view', 'department'); // department أو employee
        
        $departments = Department::where('is_active', true)
            ->with(['manager', 'parent', 'employees' => function($query) {
                $query->where('is_active', true);
            }])
            ->get();

        $employees = Employee::where('is_active', true)
            ->with(['department', 'position', 'manager'])
            ->get();

        return view('admin.pages.organization-chart.index', compact('departments', 'employees', 'viewType'));
    }

    /**
     * الحصول على بيانات الهيكل التنظيمي (JSON)
     */
    public function getData(Request $request)
    {
        $viewType = $request->get('view', 'department');
        $departmentId = $request->get('department_id');
        $branchId = $request->get('branch_id');

        if ($viewType == 'employee') {
            // الهيكل التنظيمي حسب الموظفين (Manager-Subordinate)
            $query = Employee::where('is_active', true)
                ->with(['department', 'position', 'manager', 'subordinates']);

            if ($departmentId) {
                $query->where('department_id', $departmentId);
            }

            if ($branchId) {
                $query->where('branch_id', $branchId);
            }

            $employees = $query->get();

            // بناء الشجرة
            $tree = $this->buildEmployeeTree($employees);

            return response()->json($tree);
        } else {
            // الهيكل التنظيمي حسب الأقسام
            $query = Department::where('is_active', true)
                ->with(['manager', 'parent', 'children', 'employees' => function($q) {
                    $q->where('is_active', true)->with('position');
                }]);

            if ($branchId) {
                // إذا كان هناك ربط بين الأقسام والفروع
                $query->whereHas('employees', function($q) use ($branchId) {
                    $q->where('branch_id', $branchId);
                });
            }

            $departments = $query->get();

            // بناء الشجرة
            $tree = $this->buildDepartmentTree($departments);

            return response()->json($tree);
        }
    }

    /**
     * بناء شجرة الموظفين
     */
    private function buildEmployeeTree($employees)
    {
        $tree = [];
        $employeeMap = [];

        // إنشاء خريطة للموظفين
        foreach ($employees as $employee) {
            $employeeMap[$employee->id] = [
                'id' => $employee->id,
                'name' => $employee->full_name,
                'title' => $employee->position->title ?? 'غير محدد',
                'department' => $employee->department->name ?? 'غير محدد',
                'photo' => $employee->photo ? asset('storage/' . $employee->photo) : null,
                'children' => []
            ];
        }

        // بناء الشجرة
        foreach ($employees as $employee) {
            if ($employee->manager_id && isset($employeeMap[$employee->manager_id])) {
                // الموظف لديه مدير
                $employeeMap[$employee->manager_id]['children'][] = &$employeeMap[$employee->id];
            } else {
                // الموظف في القمة (لا يوجد له مدير)
                $tree[] = &$employeeMap[$employee->id];
            }
        }

        return $tree;
    }

    /**
     * بناء شجرة الأقسام
     */
    private function buildDepartmentTree($departments)
    {
        $tree = [];
        $departmentMap = [];

        // إنشاء خريطة للأقسام
        foreach ($departments as $department) {
            $employees = $department->employees->map(function($emp) {
                return [
                    'id' => 'emp_' . $emp->id,
                    'name' => $emp->full_name,
                    'title' => $emp->position->title ?? 'غير محدد',
                    'photo' => $emp->photo ? asset('storage/' . $emp->photo) : null,
                ];
            })->toArray();

            // الحصول على مدير القسم
            $managerName = null;
            if ($department->manager) {
                if ($department->manager->employee) {
                    $managerName = $department->manager->employee->full_name;
                } else {
                    $managerName = $department->manager->name;
                }
            }
            
            $departmentMap[$department->id] = [
                'id' => 'dept_' . $department->id,
                'name' => $department->name,
                'manager' => $managerName,
                'employees' => $employees,
                'children' => []
            ];
        }

        // بناء الشجرة
        foreach ($departments as $department) {
            if ($department->parent_id && isset($departmentMap[$department->parent_id])) {
                // القسم لديه قسم أب
                $departmentMap[$department->parent_id]['children'][] = &$departmentMap[$department->id];
            } else {
                // القسم في القمة
                $tree[] = &$departmentMap[$department->id];
            }
        }

        return $tree;
    }
}
