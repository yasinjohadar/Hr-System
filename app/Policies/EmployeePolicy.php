<?php

namespace App\Policies;

use App\Models\Employee;
use App\Models\User;
use App\Services\DepartmentScopeService;

class EmployeePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('employee-list') || $user->can('employee-show');
    }

    public function view(User $user, Employee $employee): bool
    {
        if (! $user->can('employee-show')) {
            return false;
        }

        if ($user->isDepartmentHead()) {
            return in_array($employee->id, app(DepartmentScopeService::class)->forUser($user)->managedEmployeeIds(), true);
        }

        return true;
    }

    public function update(User $user, Employee $employee): bool
    {
        if (! $user->can('employee-edit')) {
            return false;
        }

        if ($user->isDepartmentHead()) {
            return in_array($employee->id, app(DepartmentScopeService::class)->forUser($user)->managedEmployeeIds(), true);
        }

        return true;
    }
}
