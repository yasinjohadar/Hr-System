<?php

namespace App\Services;

use App\Models\Department;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class DepartmentScopeService
{
    public function forUser(?User $user = null): self
    {
        $this->user = $user ?? Auth::user();

        return $this;
    }

    protected ?User $user = null;

    protected function user(): ?User
    {
        return $this->user ?? Auth::user();
    }

    public function shouldScope(): bool
    {
        $user = $this->user();

        return $user && $user->isDepartmentHead();
    }

    public function managedDepartmentIds(): array
    {
        $user = $this->user();
        if (! $user || ! $user->isDepartmentHead()) {
            return [];
        }

        return $user->getManagedDepartmentIds();
    }

    public function managedEmployeeIds(): array
    {
        $user = $this->user();
        if (! $user || ! $user->isDepartmentHead()) {
            return [];
        }

        return $user->getManagedEmployeeIds();
    }

    public function scopeEmployees(Builder $query, string $departmentColumn = 'department_id'): Builder
    {
        if (! $this->shouldScope()) {
            return $query;
        }

        $departmentIds = $this->managedDepartmentIds();
        if (empty($departmentIds)) {
            return $query->whereRaw('1 = 0');
        }

        return $query->whereIn($departmentColumn, $departmentIds);
    }

    public function scopeByEmployeeId(Builder $query, string $column = 'employee_id'): Builder
    {
        if (! $this->shouldScope()) {
            return $query;
        }

        $employeeIds = $this->managedEmployeeIds();
        if (empty($employeeIds)) {
            return $query->whereRaw('1 = 0');
        }

        return $query->whereIn($column, $employeeIds);
    }

    public function scopeDepartments(Builder $query): Builder
    {
        if (! $this->shouldScope()) {
            return $query;
        }

        $departmentIds = $this->managedDepartmentIds();
        if (empty($departmentIds)) {
            return $query->whereRaw('1 = 0');
        }

        return $query->whereIn('id', $departmentIds);
    }

    public function authorizeEmployeeId(int $employeeId, ?string $message = null): void
    {
        if (! $this->shouldScope()) {
            return;
        }

        if (! in_array($employeeId, $this->managedEmployeeIds(), true)) {
            abort(403, $message ?? 'غير مصرح لك بالوصول إلى هذا الموظف.');
        }
    }

    public function authorizeEmployee(?Employee $employee, ?string $message = null): void
    {
        if (! $employee) {
            abort(404);
        }

        $this->authorizeEmployeeId((int) $employee->id, $message);
    }

    public function authorizeDepartmentId(int $departmentId, ?string $message = null): void
    {
        if (! $this->shouldScope()) {
            return;
        }

        if (! in_array($departmentId, $this->managedDepartmentIds(), true)) {
            abort(403, $message ?? 'غير مصرح لك بالوصول إلى هذا القسم.');
        }
    }

    public function filterEmployeeCollection(iterable $employees): array
    {
        if (! $this->shouldScope()) {
            return is_array($employees) ? $employees : iterator_to_array($employees);
        }

        $allowed = $this->managedEmployeeIds();

        return collect($employees)->filter(fn ($e) => in_array($e->id, $allowed, true))->values()->all();
    }
}
