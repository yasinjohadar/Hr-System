<div class="users-table-wrapper">
    <div class="table-responsive">
    <table class="table table-striped table-hover align-middle table-nowrap mb-0">
        <thead class="table-light">
            <tr>
                <th scope="col" style="width: 40px;">#</th>
                <th scope="col" style="min-width: 150px;">اسم المستخدم</th>
                <th scope="col" style="min-width: 200px;">البريد</th>
                <th scope="col" style="min-width: 120px;">الهاتف</th>
                <th scope="col" style="min-width: 130px;">اخر دخول</th>
                <th scope="col" style="min-width: 150px;">الأدوار</th>
                <th scope="col" style="min-width: 110px;">الحالة</th>
                <th scope="col" style="min-width: 120px;">الحالة النشطة</th>
                <th scope="col" style="width: 60px;">العمليات</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($users as $user)
                @php
                    $userSessions = $sessions->get($user->id);
                    $lastSession = $userSessions ? $userSessions->first() : null;
                @endphp
                <tr>
                    <th scope="row">{{ $loop->iteration + (($users->currentPage() - 1) * $users->perPage()) }}</th>

                    <td>
                        <a href="{{ route('users.show', $user->id) }}" class="text-decoration-none">
                            {{ $user->name }}
                        </a>
                    </td>

                    <td>
                        @if ($user->email)
                            <div class="d-flex align-items-center gap-1">
                                <a href="mailto:{{ $user->email }}" class="text-primary text-decoration-none" title="إرسال بريد إلكتروني">
                                    {{ $user->email }}
                                </a>
                                <button class="btn btn-link btn-sm p-0 text-secondary copy-email-btn"
                                        data-email="{{ $user->email }}" title="نسخ البريد">
                                    <i class="far fa-copy"></i>
                                </button>
                            </div>
                        @else
                            -
                        @endif
                    </td>

                    <td>
                        @if ($user->phone)
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $user->phone) }}" target="_blank"
                                class="text-success text-decoration-none me-1" title="فتح WhatsApp">
                                <i class="fab fa-whatsapp"></i>
                            </a>
                            {{ $user->phone }}
                        @else
                            -
                        @endif
                    </td>

                    <td>
                        @if ($lastSession)
                            {{ \Carbon\Carbon::createFromTimestamp($lastSession->last_activity)->diffForHumans() }}
                        @else
                            لا توجد جلسات
                        @endif
                    </td>

                    <td>
                        @foreach ($user->getRoleNames() as $role)
                            <span class="badge bg-primary me-1">{{ $role }}</span>
                        @endforeach
                    </td>

                    <td>
                        @if ($user->status === 'active')
                            <span class="badge bg-success">مفعل</span>
                        @elseif($user->status === 'inactive')
                            <span class="badge bg-warning text-dark">موقوف</span>
                        @elseif($user->status === 'banned')
                            <span class="badge bg-danger">محظور</span>
                        @else
                            <span class="badge bg-secondary">غير معروف</span>
                        @endif
                    </td>

                    <td>
                        <button class="btn btn-sm toggle-status-btn {{ $user->is_active ? 'btn-success' : 'btn-secondary' }}"
                                data-user-id="{{ $user->id }}"
                                data-user-name="{{ $user->name }}"
                                data-is-active="{{ $user->is_active ? '1' : '0' }}"
                                type="button"
                                style="min-width:90px;border-radius:5px;">
                            {{ $user->is_active ? 'مفعل' : 'غير مفعل' }}
                        </button>
                    </td>

                    <td class="actions-cell">
                        <div class="dropdown">
                            <button class="btn btn-light btn-sm border actions-dropdown-btn" type="button"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-ellipsis-vertical text-muted"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 dropdown-menu-actions" style="border-radius:5px;">
                                <li>
                                    <a class="dropdown-item" href="{{ route('users.show', $user->id) }}">
                                        <i class="fas fa-eye text-info me-2"></i>عرض المستخدم
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('users.edit', $user->id) }}">
                                        <i class="fas fa-pen-to-square text-primary me-2"></i>تعديل
                                    </a>
                                </li>
                                <li>
                                    <a href="#" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#change_password{{ $user->id }}">
                                        <i class="fas fa-key text-warning me-2"></i>تغيير كلمة المرور
                                    </a>
                                </li>
                                <li>
                                    <a href="#" class="dropdown-item login-code-btn" data-user-id="{{ $user->id }}" data-user-name="{{ $user->name }}">
                                        <i class="fas fa-link text-success me-2"></i>كود تسجيل الدخول
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a href="#" class="dropdown-item text-danger" data-bs-toggle="modal" data-bs-target="#delete{{ $user->id }}">
                                        <i class="fas fa-trash-can text-danger me-2"></i>حذف المستخدم
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
                    <td colspan="9" class="text-center text-muted py-4">
                        <i class="fas fa-search mb-2 d-block fs-3"></i>
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
