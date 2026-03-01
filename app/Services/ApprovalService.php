<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\Department;
use App\Models\User;
use App\Models\WorkflowStep;
use Illuminate\Support\Facades\Log;

class ApprovalService
{
    /**
     * تحديد الموافق بناءً على نوع الموافق في WorkflowStep
     * 
     * @param WorkflowStep $step
     * @param Employee $employee الموظف صاحب الطلب
     * @param mixed $entity الكيان المطلوب الموافقة عليه (LeaveRequest, ExpenseRequest, etc.)
     * @return User|null
     */
    public function getApproverForStep(WorkflowStep $step, Employee $employee, $entity = null): ?User
    {
        return match($step->approver_type) {
            'user' => $this->getUserApprover($step),
            'role' => $this->getRoleApprover($step),
            'employee_manager' => $this->getEmployeeManager($employee),
            'department_manager' => $this->getDepartmentManager($employee),
            'custom' => $this->getCustomApprover($step, $employee, $entity),
            default => null,
        };
    }

    /**
     * الحصول على الموافق من نوع User
     */
    private function getUserApprover(WorkflowStep $step): ?User
    {
        return $step->approver_id ? User::find($step->approver_id) : null;
    }

    /**
     * الحصول على الموافق من نوع Role (أول مستخدم لهذا الدور)
     */
    private function getRoleApprover(WorkflowStep $step): ?User
    {
        if (!$step->role_id) {
            return null;
        }

        $role = \Spatie\Permission\Models\Role::find($step->role_id);
        if (!$role) {
            return null;
        }

        // الحصول على أول مستخدم نشط لهذا الدور
        return $role->users()->where('is_active', true)->first();
    }

    /**
     * الحصول على المدير المباشر للموظف
     */
    private function getEmployeeManager(Employee $employee): ?User
    {
        // إذا كان الموظف لديه manager_id
        if ($employee->manager_id) {
            $manager = Employee::find($employee->manager_id);
            if ($manager && $manager->user_id) {
                return User::find($manager->user_id);
            }
        }

        // إذا لم يكن لديه مدير مباشر، البحث عن مدير القسم
        return $this->getDepartmentManager($employee);
    }

    /**
     * الحصول على مدير القسم
     */
    private function getDepartmentManager(Employee $employee): ?User
    {
        if (!$employee->department_id) {
            return null;
        }

        $department = Department::find($employee->department_id);
        if (!$department || !$department->manager_id) {
            // إذا لم يكن للقسم مدير، البحث في القسم الأب
            if ($department && $department->parent_id) {
                $parentDepartment = Department::find($department->parent_id);
                if ($parentDepartment && $parentDepartment->manager_id) {
                    return User::find($parentDepartment->manager_id);
                }
            }
            return null;
        }

        return User::find($department->manager_id);
    }

    /**
     * الحصول على الموافق المخصص (يمكن تطويره لاحقاً)
     */
    private function getCustomApprover(WorkflowStep $step, Employee $employee, $entity = null): ?User
    {
        // يمكن إضافة منطق مخصص هنا بناءً على conditions
        if ($step->conditions && is_array($step->conditions)) {
            // مثال: إذا كان المبلغ أكبر من X، يحتاج موافقة المدير العام
            // يمكن تطوير هذا لاحقاً
        }

        return $step->approver_id ? User::find($step->approver_id) : null;
    }

    /**
     * الحصول على جميع الموافقين المطلوبين لطلب معين
     * 
     * @param string $workflowType نوع سير العمل (leave_request, expense_request, etc.)
     * @param Employee $employee الموظف صاحب الطلب
     * @return array [step_order => User]
     */
    public function getAllRequiredApprovers(string $workflowType, Employee $employee): array
    {
        $workflow = \App\Models\Workflow::where('type', $workflowType)
            ->where('is_active', true)
            ->first();

        if (!$workflow) {
            return [];
        }

        $approvers = [];
        $steps = $workflow->steps()->orderBy('step_order')->get();

        foreach ($steps as $step) {
            if ($step->is_required) {
                $approver = $this->getApproverForStep($step, $employee);
                if ($approver) {
                    $approvers[$step->step_order] = [
                        'user' => $approver,
                        'step' => $step,
                    ];
                }
            }
        }

        return $approvers;
    }

    /**
     * التحقق من أن المستخدم الحالي يمكنه الموافقة على طلب معين
     * 
     * @param User $user المستخدم الحالي
     * @param string $workflowType نوع سير العمل
     * @param Employee $employee الموظف صاحب الطلب
     * @param int $approvalLevel مستوى الموافقة المطلوب
     * @return bool
     */
    public function canUserApprove(User $user, string $workflowType, Employee $employee, int $approvalLevel = 1): bool
    {
        $workflow = \App\Models\Workflow::where('type', $workflowType)
            ->where('is_active', true)
            ->first();

        if (!$workflow) {
            return false;
        }

        $step = $workflow->steps()
            ->where('step_order', $approvalLevel)
            ->first();

        if (!$step) {
            return false;
        }

        $requiredApprover = $this->getApproverForStep($step, $employee);

        if (!$requiredApprover) {
            return false;
        }

        // التحقق من أن المستخدم هو الموافق المطلوب
        if ($requiredApprover->id === $user->id) {
            return true;
        }

        // التحقق من الصلاحيات الإضافية (مثل admin يمكنه الموافقة على كل شيء)
        if ($user->hasPermissionTo($workflowType . '-approve-all')) {
            return true;
        }

        return false;
    }

    /**
     * الحصول على الموافق التالي المطلوب
     * 
     * @param string $workflowType
     * @param Employee $employee
     * @param array $completedLevels المستويات المكتملة
     * @return array|null ['user' => User, 'step' => WorkflowStep, 'level' => int]
     */
    public function getNextApprover(string $workflowType, Employee $employee, array $completedLevels = []): ?array
    {
        $workflow = \App\Models\Workflow::where('type', $workflowType)
            ->where('is_active', true)
            ->first();

        if (!$workflow) {
            return null;
        }

        $steps = $workflow->steps()
            ->where('is_required', true)
            ->orderBy('step_order')
            ->get();

        foreach ($steps as $step) {
            // إذا كانت هذه الخطوة مكتملة، تخطيها
            if (in_array($step->step_order, $completedLevels)) {
                continue;
            }

            $approver = $this->getApproverForStep($step, $employee);
            if ($approver) {
                return [
                    'user' => $approver,
                    'step' => $step,
                    'level' => $step->step_order,
                ];
            }
        }

        return null;
    }

    /**
     * التحقق من اكتمال جميع الموافقات المطلوبة
     * 
     * @param string $workflowType
     * @param Employee $employee
     * @param array $completedLevels
     * @return bool
     */
    public function areAllApprovalsCompleted(string $workflowType, Employee $employee, array $completedLevels): bool
    {
        $workflow = \App\Models\Workflow::where('type', $workflowType)
            ->where('is_active', true)
            ->first();

        if (!$workflow) {
            return false;
        }

        $requiredSteps = $workflow->steps()
            ->where('is_required', true)
            ->pluck('step_order')
            ->toArray();

        // التحقق من أن جميع الخطوات المطلوبة مكتملة
        foreach ($requiredSteps as $stepOrder) {
            if (!in_array($stepOrder, $completedLevels)) {
                return false;
            }
        }

        return true;
    }

    /**
     * الحصول على التسلسل الهرمي الكامل للموظف
     * 
     * @param Employee $employee
     * @return array ['direct_manager' => Employee|null, 'department_manager' => User|null, 'chain' => array]
     */
    public function getEmployeeHierarchy(Employee $employee): array
    {
        $hierarchy = [
            'direct_manager' => null,
            'department_manager' => null,
            'chain' => [],
        ];

        // المدير المباشر
        if ($employee->manager_id) {
            $directManager = Employee::find($employee->manager_id);
            if ($directManager) {
                $hierarchy['direct_manager'] = $directManager;
                $hierarchy['chain'][] = [
                    'type' => 'direct_manager',
                    'employee' => $directManager,
                    'user' => $directManager->user,
                ];
            }
        }

        // مدير القسم
        $deptManager = $this->getDepartmentManager($employee);
        if ($deptManager) {
            $hierarchy['department_manager'] = $deptManager;
            $hierarchy['chain'][] = [
                'type' => 'department_manager',
                'user' => $deptManager,
            ];
        }

        // التسلسل الهرمي الكامل (حتى المدير العام)
        $currentManager = $hierarchy['direct_manager'];
        while ($currentManager && $currentManager->manager_id) {
            $nextManager = Employee::find($currentManager->manager_id);
            if ($nextManager) {
                $hierarchy['chain'][] = [
                    'type' => 'hierarchy_manager',
                    'level' => count($hierarchy['chain']) + 1,
                    'employee' => $nextManager,
                    'user' => $nextManager->user,
                ];
                $currentManager = $nextManager;
            } else {
                break;
            }
        }

        return $hierarchy;
    }
}
