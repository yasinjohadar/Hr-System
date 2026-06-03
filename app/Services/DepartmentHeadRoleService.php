<?php

namespace App\Services;

use App\Models\Department;
use App\Models\User;
use Spatie\Permission\Models\Role;

class DepartmentHeadRoleService
{
    public function ensureRole(User $user): void
    {
        if ($user->hasRole('admin')) {
            return;
        }

        $role = Role::firstOrCreate(['name' => 'department_head']);
        if (! $user->hasRole('department_head')) {
            $user->assignRole($role);
        }
    }

    public function revokeRoleIfNotManagingAnyDepartment(?User $user): void
    {
        if (! $user) {
            return;
        }

        $stillManager = Department::where('manager_id', $user->id)->exists();
        if (! $stillManager && $user->hasRole('department_head')) {
            $user->removeRole('department_head');
        }
    }

    public function assignDepartments(User $user, array $departmentIds): void
    {
        $departmentIds = array_filter(array_map('intval', $departmentIds));

        foreach ($departmentIds as $departmentId) {
            $department = Department::find($departmentId);
            if (! $department) {
                continue;
            }

            $previousManagerId = $department->manager_id;
            $department->manager_id = $user->id;
            $department->save();

            if ($previousManagerId && (int) $previousManagerId !== (int) $user->id) {
                $this->revokeRoleIfNotManagingAnyDepartment(User::find($previousManagerId));
            }
        }

        if (! empty($departmentIds)) {
            $this->ensureRole($user);
        } else {
            $this->revokeRoleIfNotManagingAnyDepartment($user);
        }
    }

    public function removeFromDepartment(User $user, Department $department): void
    {
        if ((int) $department->manager_id === (int) $user->id) {
            $department->manager_id = null;
            $department->save();
        }

        $this->revokeRoleIfNotManagingAnyDepartment($user);
    }

    public function removeFromAllDepartments(User $user): void
    {
        Department::where('manager_id', $user->id)->update(['manager_id' => null]);
        $this->revokeRoleIfNotManagingAnyDepartment($user);
    }
}
