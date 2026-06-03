@php
    $roleColorClasses = ['primary', 'success', 'info', 'warning', 'secondary', 'danger', 'teal'];
@endphp

<div class="users-table-wrapper">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 users-table">
            <thead>
                <tr>
                    <th scope="col" style="width: 48px;">#</th>
                    <th scope="col">المستخدم</th>
                    <th scope="col">الموظف</th>
                    <th scope="col">الأقسام المُدارة</th>
                    <th scope="col">حجم الفريق</th>
                    <th scope="col">الأدوار</th>
                    <th scope="col" style="width: 120px;">الحالة</th>
                    <th scope="col" class="text-center" style="width: 70px;">العمليات</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($heads as $head)
                    @php
                        $roleNames = $head->roles->pluck('name');
                    @endphp
                    <tr>
                        <td class="text-muted">{{ $heads->firstItem() + $loop->index }}</td>
                        <td>
                            <div class="user-cell">
                                @if ($head->photo)
                                    <img src="{{ asset('storage/' . $head->photo) }}" alt="" class="user-avatar">
                                @else
                                    <span class="user-avatar-placeholder">{{ mb_substr($head->name, 0, 1) }}</span>
                                @endif
                                @can('user-show')
                                <a href="{{ route('users.show', $head->id) }}" class="fw-semibold text-decoration-none text-truncate">
                                    {{ $head->name }}
                                </a>
                                @else
                                <span class="fw-semibold">{{ $head->name }}</span>
                                @endcan
                            </div>
                            @if ($head->email)
                                <small class="text-muted d-block mt-1">{{ $head->email }}</small>
                            @endif
                        </td>
                        <td>
                            @if ($head->employee)
                                <span class="small">{{ $head->employee->full_name }}</span>
                                @if ($head->employee->employee_code)
                                    <br><span class="badge bg-light text-dark">{{ $head->employee->employee_code }}</span>
                                @endif
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            @if ($head->managed_departments_list->isNotEmpty())
                                @foreach ($head->managed_departments_list->take(3) as $dept)
                                    <span class="badge bg-primary-transparent text-primary me-1 mb-1">{{ $dept->name }}</span>
                                @endforeach
                                @if ($head->managed_departments_list->count() > 3)
                                    <span class="badge bg-secondary-transparent">+{{ $head->managed_departments_list->count() - 3 }}</span>
                                @endif
                            @else
                                <span class="badge bg-warning-transparent text-warning">بدون قسم</span>
                            @endif
                        </td>
                        <td><span class="badge bg-info-transparent text-info">{{ $head->managed_team_count }}</span></td>
                        <td>
                            @forelse ($roleNames->take(2) as $index => $role)
                                <span class="badge bg-{{ $roleColorClasses[$index % count($roleColorClasses)] }}-transparent me-1">{{ $role }}</span>
                            @empty
                                <span class="text-muted small">—</span>
                            @endforelse
                            @if ($roleNames->count() > 2)
                                <span class="badge bg-secondary-transparent">+{{ $roleNames->count() - 2 }}</span>
                            @endif
                        </td>
                        <td>
                            @can('user-toggle-status')
                            <div class="form-check form-switch user-status-switch-wrap mb-0">
                                <input type="checkbox"
                                    class="form-check-input user-status-switch"
                                    role="switch"
                                    id="head-status-{{ $head->id }}"
                                    data-user-id="{{ $head->id }}"
                                    {{ $head->is_active ? 'checked' : '' }}
                                    @disabled($head->id === auth()->id())>
                                <label class="form-check-label small user-status-label" for="head-status-{{ $head->id }}">
                                    {{ $head->is_active ? 'مفعّل' : 'معطّل' }}
                                </label>
                            </div>
                            @else
                                @if ($head->is_active)
                                    <span class="badge bg-success">نشط</span>
                                @else
                                    <span class="badge bg-danger">معطّل</span>
                                @endif
                            @endcan
                        </td>
                        <td class="actions-cell">
                            <div class="dropdown">
                                <button class="btn actions-dropdown-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="ri-more-2-fill text-muted"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-actions shadow-sm">
                                    @can('user-show')
                                    <li>
                                        <a class="dropdown-item" href="{{ route('users.show', $head->id) }}">
                                            <i class="ri-eye-line text-info me-2"></i>عرض المستخدم
                                        </a>
                                    </li>
                                    @endcan
                                    @can('user-edit')
                                    <li>
                                        <a class="dropdown-item" href="{{ route('users.edit', $head->id) }}">
                                            <i class="ri-edit-line text-primary me-2"></i>تعديل
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
                                        <a class="dropdown-item" href="{{ route('admin.department-heads.edit', $head->id) }}">
                                            <i class="ri-building-line text-primary me-2"></i>تعديل الأقسام
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('admin.department-heads.show', $head->id) }}">
                                            <i class="ri-file-user-line text-info me-2"></i>ملف رئيس القسم
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
                                    @can('user-delete')
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <a href="#" class="dropdown-item text-danger" data-bs-toggle="modal" data-bs-target="#delete{{ $head->id }}">
                                            <i class="ri-delete-bin-line me-2"></i>حذف المستخدم
                                        </a>
                                    </li>
                                    @endcan
                                    @can('department-head-manage')
                                    @cannot('user-delete')
                                    <li><hr class="dropdown-divider"></li>
                                    @endcannot
                                    <li>
                                        <form action="{{ route('admin.department-heads.destroy', $head->id) }}" method="POST" class="d-inline"
                                              onsubmit="return confirm('إلغاء تعيين رئيس القسم من جميع الأقسام؟');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-warning">
                                                <i class="ri-user-unfollow-line me-2"></i>إلغاء تعيين رئيس القسم
                                            </button>
                                        </form>
                                    </li>
                                    @endcan
                                </ul>
                            </div>
                        </td>
                    </tr>

                    @can('user-change-password')
                        @include('admin.pages.users.change_password', ['user' => $head])
                    @endcan
                    @can('user-delete')
                        @include('admin.pages.users.delete', ['user' => $head])
                    @endcan
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-5">
                            <i class="ri-user-search-line fs-1 d-block mb-2 opacity-50"></i>
                            لا يوجد رؤساء أقسام معيّنون بعد.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
    <small class="text-muted">إجمالي النتائج: <strong>{{ $heads->total() }}</strong></small>
    {{ $heads->withQueryString()->links() }}
</div>
