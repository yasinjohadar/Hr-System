<?php

namespace App\Services;

use App\Models\Department;
use App\Models\User;
use Illuminate\Support\Collection;

class DepartmentHeadCapabilitiesService
{
    public function build(User $head): array
    {
        $head->loadMissing(['roles.permissions', 'permissions', 'employee']);

        $grantedNames = $head->getAllPermissions()->pluck('name')->unique()->values();
        $grantedSet = $grantedNames->flip();

        $rolePermissionNames = $head->roles
            ->flatMap(fn ($role) => $role->permissions->pluck('name'))
            ->unique()
            ->values();

        $directPermissionNames = $head->permissions->pluck('name')->unique()->values();

        $templatePermissions = collect(config('role-templates.department_head.permissions', []));
        $managedDepartmentIds = $head->getManagedDepartmentIds();
        $managedDepartments = Department::whereIn('id', $managedDepartmentIds ?: [0])
            ->with('parent')
            ->orderBy('name')
            ->get();

        $directManaged = Department::where('manager_id', $head->id)->orderBy('name')->get();

        $groups = $this->buildActionGroups($head, $grantedSet);
        $portal = $this->buildPortalFeatures($head);
        $extraPermissions = $this->extraPermissionsGrouped($grantedNames, $groups);

        $templateGranted = $templatePermissions->filter(fn ($p) => $grantedSet->has($p));
        $templateMissing = $templatePermissions->diff($grantedNames);

        return [
            'granted_names' => $grantedNames,
            'role_permission_names' => $rolePermissionNames,
            'direct_permission_names' => $directPermissionNames,
            'template_permissions' => $templatePermissions,
            'template_granted_count' => $templateGranted->count(),
            'template_missing' => $templateMissing->values(),
            'managed_department_ids' => $managedDepartmentIds,
            'managed_departments' => $managedDepartments,
            'direct_managed_departments' => $directManaged,
            'managed_team_count' => count($head->getManagedEmployeeIds()),
            'groups' => $groups,
            'portal' => $portal,
            'extra_permissions' => $extraPermissions,
            'limitations' => config('department-head-capabilities.limitations', []),
            'scope' => config('department-head-capabilities.scope', []),
            'has_department_head_role' => $head->hasRole('department_head'),
            'can_access_admin' => $head->hasAnyRole(['admin', 'department_head']),
        ];
    }

    protected function buildActionGroups(User $head, Collection $grantedSet): array
    {
        $groups = [];

        foreach (config('department-head-capabilities.groups', []) as $group) {
            $actions = [];
            foreach ($group['actions'] ?? [] as $action) {
                $permission = $action['permission'] ?? null;
                $granted = $permission ? $grantedSet->has($permission) : false;
                $actions[] = [
                    'label' => $action['label'],
                    'permission' => $permission,
                    'permission_label' => $permission ? $this->permissionLabel($permission) : null,
                    'granted' => $granted,
                ];
            }

            $grantedCount = collect($actions)->where('granted', true)->count();
            $totalCount = count($actions);

            $groups[] = [
                'title' => $group['title'],
                'icon' => $group['icon'] ?? 'ri-checkbox-circle-line',
                'summary' => $group['summary'] ?? '',
                'actions' => $actions,
                'granted_count' => $grantedCount,
                'total_count' => $totalCount,
                'fully_granted' => $totalCount > 0 && $grantedCount === $totalCount,
            ];
        }

        return $groups;
    }

    protected function buildPortalFeatures(User $head): array
    {
        $features = [];

        foreach (config('department-head-capabilities.portal', []) as $item) {
            $role = $item['requires_role'] ?? null;
            $available = $role ? $head->hasRole($role) : true;

            $features[] = [
                'label' => $item['label'],
                'description' => $item['description'] ?? '',
                'route' => $item['route'] ?? null,
                'url' => isset($item['route']) && $item['route'] ? route($item['route']) : null,
                'available' => $available,
            ];
        }

        return $features;
    }

    /**
     * صلاحيات ممنوحة وغير مذكورة في مجموعات الشرح.
     */
    protected function extraPermissionsGrouped(Collection $grantedNames, array $groups): array
    {
        $covered = collect($groups)
            ->flatMap(fn ($g) => collect($g['actions'])->pluck('permission'))
            ->filter()
            ->unique();

        $extra = $grantedNames->diff($covered)->sort()->values();

        $prefixes = [
            'الرواتب' => ['salary-', 'payroll-'],
            'المستخدمون' => ['user-', 'role-'],
            'الأقسام والهيكل' => ['department-', 'position-', 'branch-'],
            'أخرى' => [],
        ];

        $grouped = [];
        foreach ($prefixes as $title => $prefixList) {
            if ($title === 'أخرى') {
                continue;
            }
            $items = $extra->filter(function ($name) use ($prefixList) {
                foreach ($prefixList as $prefix) {
                    if (str_starts_with($name, $prefix)) {
                        return true;
                    }
                }

                return false;
            });
            if ($items->isNotEmpty()) {
                $grouped[$title] = $items->map(fn ($p) => [
                    'name' => $p,
                    'label' => $this->permissionLabel($p),
                ])->values()->all();
            }
        }

        $placed = collect($grouped)->flatMap(fn ($items) => collect($items)->pluck('name'));
        $remaining = $extra->diff($placed);
        if ($remaining->isNotEmpty()) {
            $grouped['صلاحيات إضافية'] = $remaining->map(fn ($p) => [
                'name' => $p,
                'label' => $this->permissionLabel($p),
            ])->values()->all();
        }

        return $grouped;
    }

    protected function permissionLabel(string $permission): string
    {
        $label = __('permissions.' . $permission, [], 'ar');

        return $label !== 'permissions.' . $permission ? $label : $permission;
    }
}
