<?php

namespace App\Http\Controllers\Admin\Concerns;

use App\Models\Employee;
use App\Services\DepartmentScopeService;
use Illuminate\Database\Eloquent\Builder;

trait ScopesByDepartment
{
    protected function departmentScope(): DepartmentScopeService
    {
        return app(DepartmentScopeService::class)->forUser(auth()->user());
    }

    protected function scopeEmployeesQuery(Builder $query, string $departmentColumn = 'department_id'): Builder
    {
        return $this->departmentScope()->scopeEmployees($query, $departmentColumn);
    }

    protected function scopeByEmployeeQuery(Builder $query, string $column = 'employee_id'): Builder
    {
        return $this->departmentScope()->scopeByEmployeeId($query, $column);
    }

    protected function authorizeManagedEmployeeId(int $employeeId, ?string $message = null): void
    {
        $this->departmentScope()->authorizeEmployeeId($employeeId, $message);
    }

    protected function authorizeManagedEmployee(?Employee $employee, ?string $message = null): void
    {
        $this->departmentScope()->authorizeEmployee($employee, $message);
    }

    protected function scopeDepartmentsQuery(Builder $query): Builder
    {
        return $this->departmentScope()->scopeDepartments($query);
    }
}
