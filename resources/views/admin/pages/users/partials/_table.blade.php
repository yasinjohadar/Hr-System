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
                    <th scope="col">البريد</th>
                    <th scope="col">الهاتف</th>
                    <th scope="col">آخر دخول</th>
                    <th scope="col">الأدوار</th>
                    <th scope="col" style="width: 120px;">الحالة</th>
                    <th scope="col" class="text-center" style="width: 70px;">العمليات</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                    @php
                        $sessionRow = $sessions->get($user->id);
                        $lastActivity = $sessionRow->last_activity ?? null;
                        $roleNames = $user->roles->pluck('name');
                    @endphp
                    <tr>
                        <td class="text-muted">{{ $loop->iteration + (($users->currentPage() - 1) * $users->perPage()) }}</td>
                        <td>
                            <div class="user-cell">
                                @if ($user->photo)
                                    <img src="{{ asset('storage/' . $user->photo) }}" alt="" class="user-avatar">
                                @else
                                    <span class="user-avatar-placeholder">{{ mb_substr($user->name, 0, 1) }}</span>
                                @endif
                                <a href="{{ route('users.show', $user->id) }}" class="fw-semibold text-decoration-none text-truncate">
                                    {{ $user->name }}
                                </a>
                            </div>
                        </td>
                        <td>
                            @if ($user->email)
                                <div class="d-flex align-items-center gap-1">
                                    <a href="mailto:{{ $user->email }}" class="text-primary text-decoration-none small text-truncate" style="max-width: 180px;">
                                        {{ $user->email }}
                                    </a>
                                    <button type="button" class="btn btn-link btn-sm p-0 text-muted copy-email-btn" data-email="{{ $user->email }}" title="نسخ">
                                        <i class="ri-file-copy-line"></i>
                                    </button>
                                </div>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            @if ($user->phone)
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $user->phone) }}" target="_blank" rel="noopener"
                                    class="text-success text-decoration-none">
                                    <i class="ri-whatsapp-line me-1"></i>{{ $user->phone }}
                                </a>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td class="small text-muted">
                            @if ($lastActivity)
                                {{ \Carbon\Carbon::createFromTimestamp($lastActivity)->diffForHumans() }}
                            @else
                                لا توجد جلسات
                            @endif
                        </td>
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
                            <div class="form-check form-switch user-status-switch-wrap mb-0">
                                <input type="checkbox"
                                    class="form-check-input user-status-switch"
                                    role="switch"
                                    id="user-status-{{ $user->id }}"
                                    data-user-id="{{ $user->id }}"
                                    {{ $user->is_active ? 'checked' : '' }}
                                    @disabled($user->id === auth()->id())>
                                <label class="form-check-label small user-status-label" for="user-status-{{ $user->id }}">
                                    {{ $user->is_active ? 'مفعّل' : 'معطّل' }}
                                </label>
                            </div>
                        </td>
                        <td class="actions-cell">
                            <div class="dropdown">
                                <button class="btn actions-dropdown-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="ri-more-2-fill text-muted"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-actions shadow-sm">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('users.show', $user->id) }}">
                                            <i class="ri-eye-line text-info me-2"></i>عرض المستخدم
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('users.edit', $user->id) }}">
                                            <i class="ri-edit-line text-primary me-2"></i>تعديل
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#change_password{{ $user->id }}">
                                            <i class="ri-key-line text-warning me-2"></i>تغيير كلمة المرور
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#" class="dropdown-item login-code-btn" data-user-id="{{ $user->id }}" data-user-name="{{ $user->name }}">
                                            <i class="ri-link text-success me-2"></i>كود تسجيل الدخول
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <a href="#" class="dropdown-item text-danger" data-bs-toggle="modal" data-bs-target="#delete{{ $user->id }}">
                                            <i class="ri-delete-bin-line me-2"></i>حذف المستخدم
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>

                    @include('admin.pages.users.delete')
                    @include('admin.pages.users.change_password')
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-5">
                            <i class="ri-user-search-line fs-1 d-block mb-2 opacity-50"></i>
                            لا توجد نتائج تطابق بحثك
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
    <small class="text-muted">إجمالي النتائج: <strong>{{ $users->total() }}</strong></small>
    {{ $users->withQueryString()->links() }}
</div>
