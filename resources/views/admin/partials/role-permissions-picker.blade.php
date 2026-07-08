@php
    $selected = $selectedPermissions ?? old('permissions', []);
    $groups = $permissionsGrouped ?? [];
    $cardThemes = ['purple', 'blue', 'teal', 'amber', 'rose', 'cyan', 'indigo', 'emerald'];

    $isChecked = function (string $name) use ($selected): bool {
        if ($selected === null || $selected === []) {
            return false;
        }
        if (array_is_list($selected)) {
            return in_array($name, $selected, true);
        }

        return isset($selected[$name]);
    };
@endphp

<div id="rolePermissionsPicker" class="role-perms" data-permission-picker>
    <div class="role-perms__toolbar">
        <div class="search-input-wrap role-perms__search">
            <i class="ri-search-line"></i>
            <input type="text" class="form-control" placeholder="بحث في الصلاحيات..."
                   data-permission-search autocomplete="off">
        </div>
        <button type="button" class="admin-btn admin-btn-secondary" data-permission-select-all>
            <i class="ri-checkbox-multiple-line"></i>
            تحديد الظاهر
        </button>
        <button type="button" class="admin-btn admin-btn-secondary" data-permission-deselect-all>
            <i class="ri-close-circle-line"></i>
            إلغاء الظاهر
        </button>
    </div>

    @if (empty($groups))
        <p class="role-perms__empty">لا توجد صلاحيات في النظام.</p>
    @else
        <div class="role-perms__cards">
            @foreach ($groups as $index => $permissions)
                @php
                    $categoryName = is_string($index) ? $index : 'أخرى';
                    $permissionList = $permissions instanceof \Illuminate\Support\Collection ? $permissions : collect($permissions);
                    $selectedInGroup = $permissionList->filter(fn ($p) => $isChecked($p->name))->count();
                    $totalInGroup = $permissionList->count();
                    $groupKey = \Illuminate\Support\Str::slug($categoryName);
                    $theme = $cardThemes[$loop->index % count($cardThemes)];
                @endphp
                <article class="role-perms__card role-perms__card--{{ $theme }}" data-permission-group>
                    <header class="role-perms__card-head">
                        <div class="role-perms__card-head-main">
                            <span class="role-perms__card-icon" aria-hidden="true">
                                <i class="ri-folder-shield-line"></i>
                            </span>
                            <div class="role-perms__card-titles">
                                <h4 class="role-perms__card-title">{{ $categoryName }}</h4>
                                <p class="role-perms__card-sub">{{ $totalInGroup }} صلاحية في هذه المجموعة</p>
                            </div>
                        </div>
                        <div class="role-perms__card-meta">
                            <span class="role-perms__badge" data-group-selected>{{ $selectedInGroup }}/{{ $totalInGroup }}</span>
                        </div>
                    </header>

                    <div class="role-perms__card-actions">
                        <button type="button" class="role-perms__card-action" data-permission-group-select>
                            <i class="ri-checkbox-line"></i>
                            تحديد الكل
                        </button>
                        <button type="button" class="role-perms__card-action" data-permission-group-deselect>
                            <i class="ri-checkbox-blank-line"></i>
                            إلغاء الكل
                        </button>
                    </div>

                    <div class="role-perms__card-body">
                        <div class="role-perms__list">
                            @foreach ($permissionList as $permission)
                                @php
                                    $permId = 'perm-' . $groupKey . '-' . $permission->id;
                                    $label = permission_label($permission->name);
                                @endphp
                                <label class="role-perms__item"
                                       data-permission-item
                                       data-name="{{ $permission->name }}"
                                       data-label="{{ $label }}">
                                    <input class="role-perms__checkbox"
                                           type="checkbox"
                                           name="permissions[{{ $permission->name }}]"
                                           value="{{ $permission->name }}"
                                           id="{{ $permId }}"
                                           {{ $isChecked($permission->name) ? 'checked' : '' }}>
                                    <span class="role-perms__text">
                                        <span class="role-perms__name">{{ $label }}</span>
                                        <span class="role-perms__key">{{ $permission->name }}</span>
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    @endif

    <p class="role-perms__empty" data-permission-empty style="display: none;">
        لا توجد صلاحيات مطابقة للبحث
    </p>
</div>
