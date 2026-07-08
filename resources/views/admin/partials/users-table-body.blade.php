@php
    $roleBadgeClasses = ['admin-badge-role', 'admin-badge-success', 'admin-badge-warning', 'admin-badge-muted'];
@endphp

@forelse ($users as $user)
    @php
        $sessionRow = $sessions->get($user->id);
        $lastActivity = $sessionRow->last_activity ?? null;
        $roleNames = $user->roles->pluck('name');
    @endphp
    <tr>
        <th scope="row" class="row-number">{{ $users->firstItem() + $loop->index }}</th>
        <td>
            <div class="admin-user-cell">
                @if ($user->photo)
                    <img src="{{ asset('storage/' . $user->photo) }}" alt="" class="admin-avatar-img">
                @else
                    <span class="admin-avatar-initial">{{ mb_substr($user->name, 0, 1) }}</span>
                @endif
                <a href="{{ route('users.show', $user->id) }}" class="admin-user-link">{{ $user->name }}</a>
            </div>
        </td>
        <td>
            @if ($user->email)
                <div class="admin-email-cell">
                    <a href="mailto:{{ $user->email }}" class="admin-email-link">{{ $user->email }}</a>
                    <button type="button" class="admin-copy-btn" data-copy-email="{{ $user->email }}" title="نسخ البريد">
                        <i class="ri-file-copy-line"></i>
                    </button>
                </div>
            @else
                <span class="text-muted">—</span>
            @endif
        </td>
        <td>
            @if ($user->phone)
                <span class="admin-phone-cell">
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $user->phone) }}" target="_blank" rel="noopener" title="واتساب">
                        <i class="ri-whatsapp-line"></i>
                    </a>
                    {{ $user->phone }}
                </span>
            @else
                <span class="text-muted">—</span>
            @endif
        </td>
        <td>
            @if ($lastActivity)
                <span class="text-muted">{{ \Carbon\Carbon::createFromTimestamp($lastActivity)->diffForHumans() }}</span>
            @else
                <span class="text-muted">لا توجد جلسات</span>
            @endif
        </td>
        <td>
            @forelse ($roleNames as $index => $role)
                <span class="admin-badge {{ $roleBadgeClasses[$index % count($roleBadgeClasses)] }}">{{ $role }}</span>
            @empty
                <span class="text-muted">—</span>
            @endforelse
        </td>
        <td>
            @if ($user->status === 'active')
                <span class="admin-badge admin-badge-success">مفعل</span>
            @elseif($user->status === 'inactive')
                <span class="admin-badge admin-badge-warning">موقوف</span>
            @elseif($user->status === 'banned')
                <span class="admin-badge admin-badge-danger">محظور</span>
            @else
                <span class="admin-badge admin-badge-muted">غير معروف</span>
            @endif
        </td>
        <td>
            <div class="admin-status-switch">
                <input class="form-check-input toggle-status" type="checkbox"
                       data-user-id="{{ $user->id }}"
                       {{ $user->is_active ? 'checked' : '' }}
                       @disabled($user->id === auth()->id())>
                <span class="status-label {{ $user->is_active ? 'is-active' : '' }}">
                    {{ $user->is_active ? 'مفعّل' : 'معطّل' }}
                </span>
            </div>
        </td>
        <td>
            <div class="admin-row-actions dropdown">
                <button class="admin-kebab-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="ri-more-2-fill"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    @can('user-show')
                        <li>
                            <a class="dropdown-item" href="{{ route('users.show', $user->id) }}">
                                <i class="ri-eye-line text-info me-2"></i>عرض المستخدم
                            </a>
                        </li>
                    @endcan
                    @can('user-edit')
                        <li>
                            <a class="dropdown-item" href="{{ route('users.edit', $user->id) }}">
                                <i class="ri-edit-line text-primary me-2"></i>تعديل
                            </a>
                        </li>
                    @endcan
                    @can('user-change-password')
                        <li>
                            <a href="#" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#change_password{{ $user->id }}">
                                <i class="ri-key-line text-warning me-2"></i>تغيير كلمة المرور
                            </a>
                        </li>
                    @endcan
                    @can('user-show')
                        <li>
                            <a href="#" class="dropdown-item login-code-btn" data-user-id="{{ $user->id }}" data-user-name="{{ $user->name }}">
                                <i class="ri-link text-success me-2"></i>كود تسجيل الدخول
                            </a>
                        </li>
                    @endcan
                    @can('user-delete')
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <button type="button" class="dropdown-item text-danger border-0 bg-transparent w-100 text-start"
                                data-delete-url="{{ route('users.destroy', $user->id) }}"
                                data-delete-title="حذف المستخدم"
                                data-delete-message="هل أنت متأكد من حذف المستخدم {{ $user->name }}؟"
                                data-delete-hint="لا يمكن التراجع عن هذا الإجراء بعد الحذف."
                                data-delete-confirm="حذف المستخدم">
                                <i class="ri-delete-bin-line me-2"></i>حذف المستخدم
                            </button>
                        </li>
                    @endcan
                </ul>
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="9">
            <div class="admin-empty-state">
                <i class="ri-user-search-line"></i>
                لا توجد نتائج مطابقة
            </div>
        </td>
    </tr>
@endforelse
