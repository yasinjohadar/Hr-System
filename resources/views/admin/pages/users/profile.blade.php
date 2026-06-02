@extends('admin.layouts.master')

@section('page-title')
    ملف المستخدم — {{ $user->name }}
@stop

@section('content')
    <div class="main-content app-content admin-users">
        <div class="container-fluid">
            @include('admin.pages.users.partials.alerts')

            <div class="card custom-card profile-hero-card bg-primary-gradient mb-4">
                <div class="card-body py-4">
                    <div class="d-flex flex-column flex-md-row align-items-md-center gap-4">
                        <div class="text-center text-md-start">
                            @if ($user->photo)
                                <img src="{{ asset('storage/' . $user->photo) }}" alt="" class="profile-avatar-lg">
                            @else
                                <span class="profile-avatar-lg-placeholder">{{ mb_substr($user->name, 0, 1) }}</span>
                            @endif
                        </div>
                        <div class="flex-grow-1 text-white">
                            <h3 class="text-white mb-2">{{ $user->name }}</h3>
                            <p class="mb-2 op-8">{{ $user->email }}</p>
                            <div class="d-flex flex-wrap gap-2 mb-2">
                                @if ($user->status === 'active')
                                    <span class="badge bg-success-transparent">حساب مفعل</span>
                                @elseif($user->status === 'inactive')
                                    <span class="badge bg-warning-transparent">موقوف</span>
                                @else
                                    <span class="badge bg-danger-transparent">محظور</span>
                                @endif
                                <span class="badge bg-{{ $user->is_active ? 'success' : 'secondary' }}-transparent">
                                    دخول: {{ $user->is_active ? 'مفعّل' : 'معطّل' }}
                                </span>
                            </div>
                            @foreach ($user->roles as $role)
                                <span class="badge bg-light text-dark me-1">{{ $role->name }}</span>
                            @endforeach
                        </div>
                        <div class="d-flex flex-wrap gap-2">
                            <a href="{{ route('users.edit', $user->id) }}" class="btn btn-light btn-sm">
                                <i class="ri-edit-line me-1"></i>تعديل
                            </a>
                            <button type="button" class="btn btn-outline-light btn-sm login-code-btn"
                                data-user-id="{{ $user->id }}" data-user-name="{{ $user->name }}">
                                <i class="ri-link me-1"></i>كود دخول
                            </button>
                            <a href="{{ route('users.index') }}" class="btn btn-outline-light btn-sm">
                                <i class="ri-arrow-right-line me-1"></i>القائمة
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <ul class="nav nav-tabs nav-tabs-profile mb-4" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-info" type="button">
                        <i class="ri-information-line me-1"></i>البيانات
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-roles" type="button">
                        <i class="ri-shield-user-line me-1"></i>الأدوار
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-security" type="button">
                        <i class="ri-shield-keyhole-line me-1"></i>الأمان
                    </button>
                </li>
                @if ($user->employee)
                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-employee" type="button">
                            <i class="ri-briefcase-line me-1"></i>الموظف
                        </button>
                    </li>
                @endif
            </ul>

            <div class="tab-content">
                <div class="tab-pane fade show active" id="tab-info">
                    <div class="card custom-card">
                        <div class="card-body">
                            <dl class="row info-list mb-0">
                                <div class="col-md-6">
                                    <dt>اسم المستخدم</dt>
                                    <dd>{{ $user->username ?? '—' }}</dd>
                                </div>
                                <div class="col-md-6">
                                    <dt>الهاتف</dt>
                                    <dd>
                                        @if ($user->phone)
                                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $user->phone) }}" target="_blank" class="text-success">{{ $user->phone }}</a>
                                        @else
                                            —
                                        @endif
                                    </dd>
                                </div>
                                <div class="col-md-6">
                                    <dt>تاريخ الإنشاء</dt>
                                    <dd>{{ $user->created_at?->format('Y/m/d H:i') ?? '—' }}</dd>
                                </div>
                                <div class="col-md-6">
                                    <dt>آخر تحديث</dt>
                                    <dd>{{ $user->updated_at?->format('Y/m/d H:i') ?? '—' }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="tab-roles">
                    <div class="card custom-card">
                        <div class="card-body">
                            @forelse ($user->roles as $role)
                                <span class="badge bg-primary-transparent fs-13 me-2 mb-2 px-3 py-2">{{ $role->name }}</span>
                            @empty
                                <p class="text-muted mb-0">لا توجد أدوار معينة لهذا المستخدم.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="tab-security">
                    <div class="card custom-card">
                        <div class="card-body">
                            <dl class="row info-list mb-0">
                                <div class="col-md-6">
                                    <dt>آخر دخول (من الجلسات)</dt>
                                    <dd>
                                        @if ($lastSession?->last_activity)
                                            {{ \Carbon\Carbon::createFromTimestamp($lastSession->last_activity)->format('Y/m/d H:i') }}
                                            <small class="text-muted">({{ \Carbon\Carbon::createFromTimestamp($lastSession->last_activity)->diffForHumans() }})</small>
                                        @else
                                            لا توجد جلسات نشطة
                                        @endif
                                    </dd>
                                </div>
                                <div class="col-md-6">
                                    <dt>آخر دخول (مسجّل)</dt>
                                    <dd>{{ $user->last_login_at?->format('Y/m/d H:i') ?? '—' }}</dd>
                                </div>
                                <div class="col-md-6">
                                    <dt>عنوان IP</dt>
                                    <dd>{{ $user->last_login_ip ?? '—' }}</dd>
                                </div>
                                <div class="col-12">
                                    <dt>وكيل المتصفح</dt>
                                    <dd class="small text-break">{{ $user->last_login_user_agent ?? '—' }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>

                @if ($user->employee)
                    <div class="tab-pane fade" id="tab-employee">
                        <div class="card custom-card">
                            <div class="card-body d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <div>
                                    <h6 class="mb-1">{{ $user->employee->full_name ?? $user->name }}</h6>
                                    <p class="text-muted mb-0 small">كود: {{ $user->employee->employee_code ?? '—' }}</p>
                                </div>
                                <a href="{{ route('admin.employees.show', $user->employee->id) }}" class="btn btn-primary btn-sm">
                                    <i class="ri-external-link-line me-1"></i>عرض ملف الموظف
                                </a>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @include('admin.pages.users.partials.modals')
@stop

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin-users.css') }}">
@endpush

@push('scripts')
    <script>
        window.adminUsersConfig = {
            loginCodeUrlTemplate: @json(route('admin.users.login-code', ['user' => '__ID__'])),
        };
    </script>
    <script src="{{ asset('assets/js/admin-users.js') }}"></script>
@endpush
