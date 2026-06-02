@extends('admin.layouts.master')

@section('page-title')
    تفاصيل الموظف — {{ $employee->full_name }}
@stop

@php
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
    $empStatus = $employmentStatusLabels[$employee->employment_status] ?? ['label' => $employee->employment_status ?? '—', 'class' => 'secondary'];
    $empStatusBadge = '<span class="badge bg-' . $empStatus['class'] . '-transparent">' . e($empStatus['label']) . '</span>';
@endphp

@section('content')
    <div class="main-content app-content admin-employees admin-employees-show">
        <div class="container-fluid">
            @include('admin.pages.employees.partials.alerts')

            <div class="card custom-card profile-hero-card bg-primary-gradient mb-4">
                <div class="card-body py-4">
                    <div class="d-flex flex-column flex-lg-row align-items-lg-center gap-4">
                        <div class="text-center text-lg-start flex-shrink-0">
                            @if ($employee->photo)
                                <img src="{{ asset('storage/' . $employee->photo) }}" alt="" class="profile-avatar-lg">
                            @else
                                <span class="profile-avatar-lg-placeholder">{{ mb_substr($employee->full_name, 0, 1) }}</span>
                            @endif
                        </div>
                        <div class="flex-grow-1 text-white text-center text-lg-start">
                            <h3 class="text-white mb-1">{{ $employee->full_name }}</h3>
                            <p class="mb-2 op-8 font-monospace">{{ $employee->employee_code }}</p>
                            <div class="d-flex flex-wrap gap-2 justify-content-center justify-content-lg-start mb-3">
                                {!! $empStatusBadge !!}
                                @if ($employee->department)
                                    <span class="badge bg-light text-dark">{{ $employee->department->name }}</span>
                                @endif
                                @if ($employee->position)
                                    <span class="badge bg-light text-dark">{{ $employee->position->title }}</span>
                                @endif
                            </div>
                            <div class="employee-hero-status d-inline-flex align-items-center gap-2 bg-white bg-opacity-10 rounded px-3 py-2">
                                <span class="small op-8">تفعيل السجل:</span>
                                @can('employee-edit')
                                    <div class="form-check form-switch employee-status-switch-wrap mb-0">
                                        <input type="checkbox"
                                            class="form-check-input employee-status-switch employee-status-switch-light"
                                            role="switch"
                                            id="employee-show-status"
                                            data-employee-id="{{ $employee->id }}"
                                            {{ $employee->is_active ? 'checked' : '' }}>
                                        <label class="form-check-label small text-white employee-status-label mb-0" for="employee-show-status">
                                            {{ $employee->is_active ? 'مفعّل' : 'معطّل' }}
                                        </label>
                                    </div>
                                @else
                                    <span class="badge bg-{{ $employee->is_active ? 'success' : 'secondary' }}">
                                        {{ $employee->is_active ? 'مفعّل' : 'معطّل' }}
                                    </span>
                                @endcan
                            </div>
                        </div>
                        <div class="d-flex flex-wrap gap-2 justify-content-center">
                            @can('employee-edit')
                                <a href="{{ route('admin.employees.edit', $employee->id) }}" class="btn btn-light btn-sm">
                                    <i class="ri-edit-line me-1"></i>تعديل
                                </a>
                            @endcan
                            @if ($employee->user_id && $employee->user && $employee->user->is_active)
                                @can('employee-show')
                                    <form action="{{ route('admin.employees.login-as', $employee) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-light btn-sm">
                                            <i class="ri-login-box-line me-1"></i>الدخول كموظف
                                        </button>
                                    </form>
                                    <button type="button" class="btn btn-outline-light btn-sm employee-login-code-btn"
                                        data-employee-id="{{ $employee->id }}"
                                        data-employee-name="{{ $employee->full_name }}">
                                        <i class="ri-link me-1"></i>كود دخول
                                    </button>
                                @endcan
                            @endif
                            <a href="{{ route('admin.employees.index') }}" class="btn btn-outline-light btn-sm">
                                <i class="ri-arrow-right-line me-1"></i>القائمة
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-lg-8">
                    @include('admin.pages.employees.partials.show-info-card', [
                        'icon' => 'ri-briefcase-line',
                        'title' => 'معلومات الوظيفة',
                        'subtitle' => 'القسم، المنصب، الفرع، والراتب',
                        'rows' => [
                            ['icon' => 'ri-building-line', 'label' => 'القسم', 'value' => e($employee->department->name ?? '—')],
                            ['icon' => 'ri-user-star-line', 'label' => 'المنصب', 'value' => e($employee->position->title ?? '—')],
                            ['icon' => 'ri-git-branch-line', 'label' => 'الفرع', 'value' => e($employee->branch->name ?? '—')],
                            ['icon' => 'ri-user-follow-line', 'label' => 'المدير المباشر', 'value' => e($employee->manager->full_name ?? '—')],
                            ['icon' => 'ri-calendar-check-line', 'label' => 'تاريخ التوظيف', 'value' => e($employee->hire_date ? $employee->hire_date->format('Y-m-d') : '—')],
                            ['icon' => 'ri-time-line', 'label' => 'نوع التوظيف', 'value' => e($employmentTypeLabels[$employee->employment_type] ?? $employee->employment_type ?? '—')],
                            ['icon' => 'ri-money-dollar-circle-line', 'label' => 'الراتب الأساسي', 'value' => $employee->salary ? number_format($employee->salary, 2) . ' ر.س' : '—'],
                            ['icon' => 'ri-flag-line', 'label' => 'الحالة الوظيفية', 'value' => $empStatusBadge],
                            ['icon' => 'ri-map-pin-line', 'label' => 'مكان العمل', 'value' => e($employee->work_location ?? '—')],
                            ['icon' => 'ri-shield-user-line', 'label' => 'حساب الدخول', 'value' => $employee->user ? '<a href="' . route('users.show', $employee->user_id) . '" class="text-primary">' . e($employee->user->email) . '</a>' : '—'],
                        ],
                    ])
                </div>
                <div class="col-lg-4">
                    @include('admin.pages.employees.partials.show-info-card', [
                        'icon' => 'ri-contacts-line',
                        'title' => 'معلومات الاتصال',
                        'rows' => [
                            ['icon' => 'ri-mail-line', 'label' => 'البريد الشخصي', 'value' => $employee->personal_email ? '<a href="mailto:' . e($employee->personal_email) . '">' . e($employee->personal_email) . '</a>' : '—'],
                            ['icon' => 'ri-mail-send-line', 'label' => 'بريد العمل', 'value' => e($employee->work_email ?? ($employee->user->email ?? '—'))],
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
                            ['icon' => 'ri-cake-2-line', 'label' => 'تاريخ الميلاد', 'value' => e($employee->date_of_birth ? $employee->date_of_birth->format('Y-m-d') : '—')],
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
                    <div class="card custom-card employee-detail-card">
                        <div class="card-header border-bottom py-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <div>
                                <h6 class="mb-0 fw-semibold">
                                    <i class="ri-arrow-left-right-line text-primary me-2"></i>سجل التغييرات الوظيفية
                                </h6>
                                <small class="text-muted">آخر التغييرات الوظيفية المعتمدة</small>
                            </div>
                            <a href="{{ route('admin.employee-job-changes.index', ['employee_id' => $employee->id]) }}" class="btn btn-outline-primary btn-sm">
                                عرض الكل
                            </a>
                        </div>
                        <div class="card-body p-0">
                            @if (isset($jobChangeHistory) && $jobChangeHistory->isNotEmpty())
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0 employees-table">
                                        <thead>
                                            <tr>
                                                <th class="ps-4">التاريخ الفعال</th>
                                                <th>نوع التغيير</th>
                                                <th>ملخص التغيير</th>
                                                <th class="pe-4 text-end"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($jobChangeHistory as $change)
                                                <tr>
                                                    <td class="ps-4 small text-muted">{{ $change->effective_date->format('Y-m-d') }}</td>
                                                    <td><span class="badge bg-info-transparent">{{ $change->change_type_label }}</span></td>
                                                    <td class="small">
                                                        @php
                                                            $parts = [];
                                                            if ($change->new_department_id && $change->old_department_id != $change->new_department_id) {
                                                                $parts[] = 'القسم: ' . ($change->oldDepartment->name ?? '-') . ' → ' . ($change->newDepartment->name ?? '-');
                                                            }
                                                            if ($change->new_position_id && $change->old_position_id != $change->new_position_id) {
                                                                $parts[] = 'المنصب: ' . ($change->oldPosition->title ?? '-') . ' → ' . ($change->newPosition->title ?? '-');
                                                            }
                                                            if ($change->new_branch_id && $change->old_branch_id != $change->new_branch_id) {
                                                                $parts[] = 'الفرع: ' . ($change->oldBranch->name ?? '-') . ' → ' . ($change->newBranch->name ?? '-');
                                                            }
                                                            if ($change->new_manager_id && $change->old_manager_id != $change->new_manager_id) {
                                                                $parts[] = 'المدير: ' . ($change->oldManager->full_name ?? '-') . ' → ' . ($change->newManager->full_name ?? '-');
                                                            }
                                                            if ($change->new_salary !== null && (string) $change->old_salary !== (string) $change->new_salary) {
                                                                $parts[] = 'الراتب: ' . number_format($change->old_salary ?? 0, 2) . ' → ' . number_format($change->new_salary, 2) . ' ر.س';
                                                            }
                                                        @endphp
                                                        {{ count($parts) > 0 ? implode(' | ', $parts) : '—' }}
                                                    </td>
                                                    <td class="pe-4 text-end">
                                                        <a href="{{ route('admin.employee-job-changes.show', $change) }}" class="btn btn-sm btn-light" title="عرض">
                                                            <i class="ri-eye-line"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-muted text-center py-4 mb-0">لا يوجد سجل تغييرات وظيفية.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-3 mt-1 mb-4">
                <div class="col-12">
                    @include('admin.pages.employees.partials.show-info-card', [
                        'icon' => 'ri-history-line',
                        'title' => 'بيانات السجل',
                        'rows' => [
                            ['icon' => 'ri-user-settings-line', 'label' => 'أنشأ بواسطة', 'value' => e($employee->creator->name ?? '—')],
                            ['icon' => 'ri-calendar-line', 'label' => 'تاريخ الإنشاء', 'value' => '<span class="font-monospace small">' . e($employee->created_at->format('Y-m-d H:i')) . '</span>'],
                            ['icon' => 'ri-edit-line', 'label' => 'آخر تحديث', 'value' => '<span class="font-monospace small">' . e($employee->updated_at->format('Y-m-d H:i')) . '</span>'],
                        ],
                    ])
                </div>
            </div>
        </div>
    </div>

    @include('admin.pages.employees.partials.modals')
@stop

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin-employees.css') }}">
@endpush

@push('scripts')
    <script>
        window.adminEmployeesConfig = {
            toggleUrlTemplate: @json(route('admin.employees.toggle-active', ['employee' => '__ID__'])),
            loginCodeUrlTemplate: @json(route('admin.employees.login-code', ['employee' => '__ID__'])),
        };
    </script>
    <script src="{{ asset('assets/js/admin-employees.js') }}"></script>
@endpush
