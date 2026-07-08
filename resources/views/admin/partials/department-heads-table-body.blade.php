@php
    $roleBadgeClasses = ['admin-badge-role', 'admin-badge-success', 'admin-badge-warning', 'admin-badge-muted'];
@endphp

@forelse ($heads as $head)
    @php
        $roleNames = $head->roles->pluck('name');
    @endphp
    <tr>
        <th scope="row" class="row-number">{{ $heads->firstItem() + $loop->index }}</th>
        <td>
            <div class="admin-user-cell">
                @if ($head->photo)
                    <img src="{{ asset('storage/' . $head->photo) }}" alt="" class="admin-avatar-img">
                @else
                    <span class="admin-avatar-initial">{{ mb_substr($head->name, 0, 1) }}</span>
                @endif
                @can('user-show')
                    <a href="{{ route('users.show', $head->id) }}" class="admin-user-link">{{ $head->name }}</a>
                @else
                    <span class="admin-user-link">{{ $head->name }}</span>
                @endcan
            </div>
            @if ($head->email)
                <small class="text-muted d-block mt-1 ps-1">{{ $head->email }}</small>
            @endif
        </td>
        <td>
            @if ($head->employee)
                <span class="fw-semibold small">{{ $head->employee->full_name }}</span>
                @if ($head->employee->employee_code)
                    <span class="admin-badge admin-badge-muted d-inline-block mt-1">{{ $head->employee->employee_code }}</span>
                @endif
            @else
                <span class="text-muted">—</span>
            @endif
        </td>
        <td>
            @if ($head->managed_departments_list->isNotEmpty())
                <div class="d-flex flex-wrap gap-1">
                    @foreach ($head->managed_departments_list->take(3) as $dept)
                        <span class="admin-badge admin-badge-success">{{ $dept->name }}</span>
                    @endforeach
                    @if ($head->managed_departments_list->count() > 3)
                        <span class="admin-badge admin-badge-muted">+{{ $head->managed_departments_list->count() - 3 }}</span>
                    @endif
                </div>
            @else
                <span class="admin-badge admin-badge-warning">بدون قسم</span>
            @endif
        </td>
        <td>
            <span class="admin-badge admin-badge-muted">{{ $head->managed_team_count }}</span>
        </td>
        <td>
            @forelse ($roleNames->take(2) as $index => $role)
                <span class="admin-badge {{ $roleBadgeClasses[$index % count($roleBadgeClasses)] }}">{{ $role }}</span>
            @empty
                <span class="text-muted">—</span>
            @endforelse
            @if ($roleNames->count() > 2)
                <span class="admin-badge admin-badge-muted">+{{ $roleNames->count() - 2 }}</span>
            @endif
        </td>
        <td>
            @can('user-toggle-status')
                <div class="admin-status-switch">
                    <input class="form-check-input toggle-status" type="checkbox"
                           data-user-id="{{ $head->id }}"
                           {{ $head->is_active ? 'checked' : '' }}
                           @disabled($head->id === auth()->id())>
                    <span class="status-label {{ $head->is_active ? 'is-active' : '' }}">
                        {{ $head->is_active ? 'مفعّل' : 'معطّل' }}
                    </span>
                </div>
            @else
                @if ($head->is_active)
                    <span class="admin-badge admin-badge-success">نشط</span>
                @else
                    <span class="admin-badge admin-badge-danger">معطّل</span>
                @endif
            @endcan
        </td>
        <td>
            <div class="admin-row-actions dropdown">
                <button class="admin-kebab-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="ri-more-2-fill"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    @can('user-show')
                        <li>
                            <a class="dropdown-item" href="{{ route('users.show', $head->id) }}">
                                <i class="ri-eye-line text-info me-2"></i>عرض المستخدم
                            </a>
                        </li>
                    @endcan
                    @can('department-head-list')
                        <li>
                            <a class="dropdown-item" href="{{ route('admin.department-heads.capabilities', $head->id) }}">
                                <i class="ri-shield-user-line text-info me-2"></i>الصلاحيات والقدرات
                            </a>
                        </li>
                    @endcan
                    @can('department-head-manage')
                        <li>
                            <a class="dropdown-item" href="{{ route('admin.department-heads.show', $head->id) }}">
                                <i class="ri-file-user-line text-primary me-2"></i>ملف رئيس القسم
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('admin.department-heads.edit', $head->id) }}">
                                <i class="ri-building-line text-primary me-2"></i>تعديل الأقسام
                            </a>
                        </li>
                    @endcan
                    @can('user-change-password')
                        <li>
                            <a href="#" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#change_password{{ $head->id }}">
                                <i class="ri-key-line text-warning me-2"></i>تغيير كلمة المرور
                            </a>
                        </li>
                    @endcan
                    @can('user-show')
                        <li>
                            <a href="#" class="dropdown-item login-code-btn" data-user-id="{{ $head->id }}" data-user-name="{{ $head->name }}">
                                <i class="ri-link text-success me-2"></i>كود تسجيل الدخول
                            </a>
                        </li>
                    @endcan
                    @can('department-head-manage')
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <button type="button" class="dropdown-item text-warning border-0 bg-transparent w-100 text-start"
                                    data-delete-url="{{ route('admin.department-heads.destroy', $head->id) }}"
                                    data-delete-title="إلغاء تعيين رئيس القسم"
                                    data-delete-message="هل تريد إلغاء تعيين <strong>{{ $head->name }}</strong> من جميع الأقسام؟"
                                    data-delete-hint="سيُزال من الأقسام المُدارة وقد يُلغى دور رئيس القسم."
                                    data-delete-confirm="إلغاء التعيين">
                                <i class="ri-user-unfollow-line me-2"></i>إلغاء تعيين رئيس القسم
                            </button>
                        </li>
                    @endcan
                    @can('user-delete')
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <button type="button" class="dropdown-item text-danger border-0 bg-transparent w-100 text-start"
                                    data-delete-url="{{ route('users.destroy', $head->id) }}"
                                    data-delete-message="هل أنت متأكد من حذف المستخدم <strong>{{ $head->name }}</strong>؟">
                                <i class="ri-delete-bin-line me-2"></i>حذف المستخدم
                            </button>
                        </li>
                    @endcan
                </ul>
            </div>
        </td>
    </tr>

    @can('user-change-password')
        @include('admin.pages.users.change_password', ['user' => $head])
    @endcan
@empty
    <tr>
        <td colspan="8">
            <div class="admin-empty-state">
                <i class="ri-user-search-line"></i>
                لا يوجد رؤساء أقسام معيّنون بعد
            </div>
        </td>
    </tr>
@endforelse
