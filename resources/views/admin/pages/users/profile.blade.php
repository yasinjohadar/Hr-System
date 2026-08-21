@extends('admin.layouts.master')

@section('page-title')
    ملف المستخدم — {{ $user->name }}
@stop

@php
    $statusMeta = match ($user->status) {
        'active' => ['label' => 'حساب مفعل', 'color' => '#059669'],
        'inactive' => ['label' => 'موقوف', 'color' => '#d97706'],
        default => ['label' => 'محظور', 'color' => '#dc2626'],
    };

    $employmentStatusLabels = [
        'active' => ['label' => 'نشط وظيفياً', 'class' => 'success'],
        'on_leave' => ['label' => 'في إجازة', 'class' => 'warning'],
        'terminated' => ['label' => 'منتهي', 'class' => 'danger'],
        'resigned' => ['label' => 'استقال', 'class' => 'secondary'],
    ];
    $employmentTypeLabels = [
        'full_time' => 'دوام كامل',
        'part_time' => 'دوام جزئي',
        'contract' => 'عقد',
        'intern' => 'متدرب',
    ];
    $maritalLabels = [
        'single' => 'أعزب',
        'married' => 'متزوج',
        'divorced' => 'مطلق',
        'widowed' => 'أرمل',
    ];

    $employee = $user->employee;
    if ($employee) {
        $empStatus = $employmentStatusLabels[$employee->employment_status] ?? ['label' => $employee->employment_status ?? '—', 'class' => 'secondary'];
        $empStatusBadge = '<span class="admin-badge admin-badge-' . ($empStatus['class'] === 'secondary' ? 'muted' : $empStatus['class']) . '">' . e($empStatus['label']) . '</span>';
    }
@endphp

@section('content')
    <div class="main-content app-content admin-users admin-employees-show">
        <div class="container-fluid admin-page-shell">
            @include('admin.pages.users.partials.alerts')

            <div class="admin-page-banner">
                <div class="admin-page-banner-main">
                    <span class="admin-page-banner-icon admin-page-banner-icon--avatar">
                        @if ($user->photo)
                            <img src="{{ asset('storage/' . $user->photo) }}" alt="">
                        @else
                            <span class="admin-page-banner-avatar-letter">{{ mb_substr($user->name, 0, 1) }}</span>
                        @endif
                    </span>
                    <div class="admin-page-banner-text">
                        <h1>{{ $user->name }}</h1>
                        <p>{{ $user->email }}</p>
                    </div>
                </div>
                <div class="admin-page-banner-actions">
                    @can('user-show')
                        <button type="button" class="admin-btn admin-btn-light login-code-btn"
                                data-user-id="{{ $user->id }}" data-user-name="{{ $user->name }}">
                            <i class="ri-link"></i>
                            كود دخول
                        </button>
                    @endcan
                    @can('user-edit')
                        <a href="{{ route('users.edit', $user->id) }}" class="admin-btn admin-btn-light">
                            <i class="ri-edit-line"></i>
                            تعديل
                        </a>
                    @endcan
                    <a href="{{ route('users.index') }}" class="admin-btn admin-btn-secondary">
                        <i class="ri-arrow-right-line"></i>
                        القائمة
                    </a>
                </div>
            </div>

            <div class="admin-report-stats admin-report-stats-4 mb-4">
                <div class="admin-report-stat admin-report-stat-static admin-report-stat-blue">
                    <span class="admin-report-stat-icon"><i class="ri-shield-check-line"></i></span>
                    <span class="admin-report-stat-label">حالة الحساب</span>
                    <span class="admin-report-stat-value" style="color:{{ $statusMeta['color'] }};">{{ $statusMeta['label'] }}</span>
                </div>
                <div class="admin-report-stat admin-report-stat-static admin-report-stat-green">
                    <span class="admin-report-stat-icon"><i class="ri-login-circle-line"></i></span>
                    <span class="admin-report-stat-label">تفعيل الدخول</span>
                    <span class="admin-report-stat-value" style="color:{{ $user->is_active ? '#059669' : '#94a3b8' }};">
                        {{ $user->is_active ? 'مفعّل' : 'معطّل' }}
                    </span>
                </div>
                <div class="admin-report-stat admin-report-stat-static admin-report-stat-amber">
                    <span class="admin-report-stat-icon"><i class="ri-shield-user-line"></i></span>
                    <span class="admin-report-stat-label">الأدوار</span>
                    <span class="admin-report-stat-value" style="color:#d97706;">{{ $user->roles->count() }}</span>
                </div>
                <div class="admin-report-stat admin-report-stat-static admin-report-stat-cyan">
                    <span class="admin-report-stat-icon"><i class="ri-flashlight-line"></i></span>
                    <span class="admin-report-stat-label">آخر نشاط</span>
                    <span class="admin-report-stat-value" style="color:#0891b2; font-size:0.95rem;">
                        @if (! $lastSession)
                            لا نشاط مسجّل
                        @elseif ($lastSession->last_activity >= now()->subMinutes(5)->timestamp)
                            متصل الآن
                        @else
                            {{ \Carbon\Carbon::createFromTimestamp($lastSession->last_activity)->diffForHumans() }}
                        @endif
                    </span>
                </div>
            </div>

            <ul class="admin-profile-tabs" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-info" type="button">
                        <i class="ri-information-line"></i>البيانات
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-roles" type="button">
                        <i class="ri-shield-user-line"></i>الأدوار
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-security" type="button">
                        <i class="ri-shield-keyhole-line"></i>الأمان
                    </button>
                </li>
                @if ($employee)
                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-employee" type="button">
                            <i class="ri-briefcase-line"></i>بيانات الموظف
                        </button>
                    </li>
                @endif
            </ul>

            <div class="tab-content">
                {{-- البيانات --}}
                <div class="tab-pane fade show active" id="tab-info">
                    @include('admin.pages.employees.partials.show-info-card', [
                        'icon' => 'ri-user-settings-line',
                        'title' => 'بيانات الحساب',
                        'rows' => [
                            ['icon' => 'ri-at-line', 'label' => 'اسم المستخدم', 'value' => e($user->username ?? '—')],
                            ['icon' => 'ri-whatsapp-line', 'label' => 'الهاتف', 'value' => $user->phone
                                ? '<a href="https://wa.me/' . preg_replace('/[^0-9]/', '', $user->phone) . '" target="_blank" rel="noopener" class="text-success">' . e($user->phone) . '</a>'
                                : '—'],
                            ['icon' => 'ri-calendar-check-line', 'label' => 'تاريخ الإنشاء', 'value' => '<span class="font-monospace small">' . e($user->created_at?->format('Y-m-d H:i') ?? '—') . '</span>'],
                            ['icon' => 'ri-refresh-line', 'label' => 'آخر تحديث', 'value' => '<span class="font-monospace small">' . e($user->updated_at?->format('Y-m-d H:i') ?? '—') . '</span>'],
                        ],
                    ])
                </div>

                {{-- الأدوار --}}
                <div class="tab-pane fade" id="tab-roles">
                    <div class="admin-page-card">
                        <div class="admin-form-body">
                            @forelse ($user->roles as $role)
                                <span class="admin-badge admin-badge-role fs-13 me-2 mb-2 px-3 py-2">{{ $role->name }}</span>
                            @empty
                                <div class="admin-empty-state">
                                    <i class="ri-shield-user-line"></i>
                                    لا توجد أدوار معيّنة لهذا المستخدم
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- الأمان --}}
                <div class="tab-pane fade" id="tab-security">
                    <div class="row g-3">
                        <div class="col-lg-5">
                            @include('admin.pages.employees.partials.show-info-card', [
                                'icon' => 'ri-shield-keyhole-line',
                                'title' => 'ملخّص الأمان',
                                'rows' => [
                                    ['icon' => 'ri-time-line', 'label' => 'آخر نشاط', 'value' => $lastSession
                                        ? e(\Carbon\Carbon::createFromTimestamp($lastSession->last_activity)->format('Y-m-d H:i')) . ' <small class="text-muted">(' . e(\Carbon\Carbon::createFromTimestamp($lastSession->last_activity)->diffForHumans()) . ')</small>'
                                        : 'لا توجد جلسات مسجّلة'],
                                    ['icon' => 'ri-global-line', 'label' => 'عنوان IP', 'value' => '<span class="font-monospace">' . e($lastSession->ip_address ?? '—') . '</span>'],
                                    ['icon' => 'ri-key-2-line', 'label' => 'كلمة المرور', 'value' => 'محدَّثة آخر مرّة مع تعديل الحساب — <span class="text-muted">غير معروضة لأسباب أمنية</span>'],
                                ],
                            ])
                        </div>
                        <div class="col-lg-7">
                            <div class="admin-page-card">
                                <div class="card-toolbar">
                                    <h6 class="mb-0 fw-bold">
                                        <i class="ri-history-line text-primary me-2"></i>سجل الجلسات الأخيرة
                                    </h6>
                                    <small class="text-muted">
                                        المتصفّح ونظام التشغيل مُستخلَصان من بيانات الجلسة الفعلية
                                    </small>
                                </div>
                                <div class="admin-table-wrap">
                                    <div class="table-responsive">
                                        <table class="admin-data-table admin-data-table-sm">
                                            <thead>
                                                <tr>
                                                    <th>الحالة</th>
                                                    <th>المتصفّح</th>
                                                    <th>نظام التشغيل</th>
                                                    <th>عنوان IP</th>
                                                    <th>آخر نشاط</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($recentSessions as $session)
                                                    <tr class="{{ $session->is_latest ? 'admin-session-current-row' : '' }}">
                                                        <td>
                                                            @if ($session->is_latest && $session->is_online_now)
                                                                <span class="admin-session-live-badge">متصل الآن</span>
                                                            @elseif ($session->is_latest)
                                                                <span class="admin-badge admin-badge-muted">آخر جلسة</span>
                                                            @else
                                                                <span class="text-muted small">—</span>
                                                            @endif
                                                        </td>
                                                        <td>{{ $session->browser }}</td>
                                                        <td>{{ $session->os }}</td>
                                                        <td class="font-monospace small">{{ $session->ip_address ?? '—' }}</td>
                                                        <td class="small text-muted">
                                                            {{ \Carbon\Carbon::createFromTimestamp($session->last_activity)->diffForHumans() }}
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="5">
                                                            <div class="admin-empty-state">
                                                                <i class="ri-shield-keyhole-line"></i>
                                                                لا توجد جلسات مسجّلة لهذا المستخدم
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- بيانات الموظف --}}
                @if ($employee)
                    <div class="tab-pane fade" id="tab-employee">
                        <div class="d-flex justify-content-end mb-3">
                            @can('employee-show')
                                <a href="{{ route('admin.employees.show', $employee->id) }}" class="admin-btn admin-btn-primary">
                                    <i class="ri-external-link-line"></i>
                                    الملف الكامل وسجل التغييرات الوظيفية
                                </a>
                            @endcan
                        </div>

                        <div class="row g-3">
                            <div class="col-lg-8">
                                @include('admin.pages.employees.partials.show-info-card', [
                                    'icon' => 'ri-briefcase-line',
                                    'title' => 'معلومات الوظيفة',
                                    'subtitle' => 'القسم، المنصب، الفرع، والراتب',
                                    'rows' => [
                                        ['icon' => 'ri-hashtag', 'label' => 'الكود الوظيفي', 'value' => '<span class="font-monospace">' . e($employee->employee_code) . '</span>'],
                                        ['icon' => 'ri-building-line', 'label' => 'القسم', 'value' => e($employee->department->name ?? '—')],
                                        ['icon' => 'ri-user-star-line', 'label' => 'المنصب', 'value' => e($employee->position->title ?? '—')],
                                        ['icon' => 'ri-git-branch-line', 'label' => 'الفرع', 'value' => e($employee->branch->name ?? '—')],
                                        ['icon' => 'ri-user-follow-line', 'label' => 'المدير المباشر', 'value' => e($employee->manager->full_name ?? '—')],
                                        ['icon' => 'ri-calendar-check-line', 'label' => 'تاريخ التوظيف', 'value' => e($employee->hire_date?->format('Y-m-d') ?? '—')],
                                        ['icon' => 'ri-time-line', 'label' => 'نوع التوظيف', 'value' => e($employmentTypeLabels[$employee->employment_type] ?? $employee->employment_type ?? '—')],
                                        ['icon' => 'ri-money-dollar-circle-line', 'label' => 'الراتب الأساسي', 'value' => $employee->salary ? number_format($employee->salary, 2) . ' ر.س' : '—'],
                                        ['icon' => 'ri-flag-line', 'label' => 'الحالة الوظيفية', 'value' => $empStatusBadge],
                                        ['icon' => 'ri-map-pin-line', 'label' => 'مكان العمل', 'value' => e($employee->work_location ?? '—')],
                                    ],
                                ])
                            </div>
                            <div class="col-lg-4">
                                @include('admin.pages.employees.partials.show-info-card', [
                                    'icon' => 'ri-contacts-line',
                                    'title' => 'معلومات الاتصال',
                                    'rows' => [
                                        ['icon' => 'ri-mail-line', 'label' => 'البريد الشخصي', 'value' => $employee->personal_email ? '<a href="mailto:' . e($employee->personal_email) . '">' . e($employee->personal_email) . '</a>' : '—'],
                                        ['icon' => 'ri-mail-send-line', 'label' => 'بريد العمل', 'value' => e($employee->work_email ?? $user->email ?? '—')],
                                        ['icon' => 'ri-phone-line', 'label' => 'الهاتف الشخصي', 'value' => e($employee->personal_phone ?? '—')],
                                        ['icon' => 'ri-phone-fill', 'label' => 'هاتف العمل', 'value' => e($employee->work_phone ?? '—')],
                                        ['icon' => 'ri-map-pin-2-line', 'label' => 'العنوان', 'value' => e(trim(($employee->address ?? '') . ($employee->city ? '، ' . $employee->city : '')) ?: '—')],
                                    ],
                                ])
                            </div>
                        </div>

                        <div class="row g-3 mt-1">
                            <div class="col-md-6">
                                @include('admin.pages.employees.partials.show-info-card', [
                                    'icon' => 'ri-id-card-line',
                                    'title' => 'المعلومات الشخصية',
                                    'rows' => [
                                        ['icon' => 'ri-cake-2-line', 'label' => 'تاريخ الميلاد', 'value' => e($employee->date_of_birth?->format('Y-m-d') ?? '—')],
                                        ['icon' => 'ri-men-line', 'label' => 'الجنس', 'value' => e($employee->gender === 'male' ? 'ذكر' : ($employee->gender === 'female' ? 'أنثى' : '—'))],
                                        ['icon' => 'ri-heart-line', 'label' => 'الحالة الاجتماعية', 'value' => e($maritalLabels[$employee->marital_status] ?? $employee->marital_status ?? '—')],
                                        ['icon' => 'ri-fingerprint-line', 'label' => 'رقم الهوية', 'value' => '<span class="font-monospace">' . e($employee->national_id ?? '—') . '</span>'],
                                    ],
                                ])
                            </div>
                            @if ($employee->emergency_contact_name)
                                <div class="col-md-6">
                                    @include('admin.pages.employees.partials.show-info-card', [
                                        'icon' => 'ri-alarm-warning-line',
                                        'title' => 'جهة الاتصال في حالات الطوارئ',
                                        'rows' => [
                                            ['icon' => 'ri-user-line', 'label' => 'الاسم', 'value' => e($employee->emergency_contact_name)],
                                            ['icon' => 'ri-phone-line', 'label' => 'الهاتف', 'value' => e($employee->emergency_contact_phone ?? '—')],
                                            ['icon' => 'ri-group-line', 'label' => 'العلاقة', 'value' => e($employee->emergency_contact_relation ?? '—')],
                                        ],
                                    ])
                                </div>
                            @endif
                        </div>

                        <div class="row g-3 mt-1">
                            <div class="col-12">
                                @include('admin.pages.employees.partials.show-info-card', [
                                    'icon' => 'ri-history-line',
                                    'title' => 'بيانات سجل الموظف',
                                    'rows' => [
                                        ['icon' => 'ri-user-settings-line', 'label' => 'أنشأ بواسطة', 'value' => e($employee->creator->name ?? '—')],
                                        ['icon' => 'ri-calendar-line', 'label' => 'تاريخ الإنشاء', 'value' => '<span class="font-monospace small">' . e($employee->created_at->format('Y-m-d H:i')) . '</span>'],
                                        ['icon' => 'ri-edit-line', 'label' => 'آخر تحديث', 'value' => '<span class="font-monospace small">' . e($employee->updated_at->format('Y-m-d H:i')) . '</span>'],
                                    ],
                                ])
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
    <link rel="stylesheet" href="{{ asset('assets/css/admin-employees.css') }}">
@endpush

@push('scripts')
    <script>
        window.adminUsersConfig = {
            loginCodeUrlTemplate: @json(route('admin.users.login-code', ['user' => '__ID__'])),
        };
    </script>
    <script src="{{ asset('assets/js/admin-users.js') }}"></script>
@endpush
