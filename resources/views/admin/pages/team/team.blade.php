@extends('admin.layouts.master')

@section('page-title')
    فريق العمل
@stop

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin-team-members.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('assets/js/admin-team-members.js') }}"></script>
@endpush

@section('content')
    <div class="main-content app-content admin-team-members-page">
        <div class="container-fluid pt-4">

            <div class="card page-hero mb-4">
                <div class="card-body py-4">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="page-hero-icon">
                                <i class="ri-team-line"></i>
                            </div>
                            <div>
                                <h4 class="mb-1 page-hero-title fw-bold">فريق العمل</h4>
                                <p class="mb-0 page-hero-subtitle">إدارة موظفي القسم</p>
                            </div>
                        </div>
                        <a href="{{ route('admin.team.dashboard') }}" class="btn btn-hero-outline btn-sm">
                            <i class="ri-arrow-right-line me-1"></i>العودة للوحة التحكم
                        </a>
                    </div>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-6 col-md-4 col-xl-2">
                    <div class="stat-card">
                        <div class="stat-value stat-value--primary">{{ $teamStats['total'] }}</div>
                        <div class="stat-label">إجمالي الموظفين</div>
                    </div>
                </div>
                <div class="col-6 col-md-4 col-xl-2">
                    <div class="stat-card">
                        <div class="stat-value stat-value--success">{{ $teamStats['present'] }}</div>
                        <div class="stat-label">حاضر اليوم</div>
                    </div>
                </div>
                <div class="col-6 col-md-4 col-xl-2">
                    <div class="stat-card">
                        <div class="stat-value stat-value--danger">{{ $teamStats['absent'] }}</div>
                        <div class="stat-label">غائب اليوم</div>
                    </div>
                </div>
                <div class="col-6 col-md-4 col-xl-2">
                    <div class="stat-card">
                        <div class="stat-value stat-value--warning">{{ $teamStats['late'] }}</div>
                        <div class="stat-label">متأخر اليوم</div>
                    </div>
                </div>
                <div class="col-6 col-md-4 col-xl-2">
                    <div class="stat-card">
                        <div class="stat-value stat-value--info">{{ $teamStats['on_leave'] }}</div>
                        <div class="stat-label">في إجازة</div>
                    </div>
                </div>
                <div class="col-6 col-md-4 col-xl-2">
                    <div class="stat-card">
                        <div class="stat-value stat-value--secondary">{{ count($departments) }}</div>
                        <div class="stat-label">الأقسام</div>
                    </div>
                </div>
            </div>

            <div class="content-panel">
                <div class="content-panel-header">
                    <div>
                        <h5 class="fw-bold mb-1">قائمة الموظفين</h5>
                        <p class="text-muted fs-13 mb-0">{{ $employees->count() }} موظف</p>
                    </div>
                </div>

                @if ($employees->isNotEmpty())
                    <div class="content-panel-toolbar">
                        <div class="search-box">
                            <i class="ri-search-line"></i>
                            <input type="search" data-team-search placeholder="بحث بالاسم أو الرقم أو البريد..." autocomplete="off">
                        </div>
                        @if ($departments->count() > 1)
                            <div class="filter-pills" role="group">
                                <button type="button" class="filter-pill active" data-dept-filter="all">الكل</button>
                                @foreach ($departments as $dept)
                                    <button type="button" class="filter-pill" data-dept-filter="{{ $dept->id }}">{{ $dept->name }}</button>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <div class="members-table-scroll" data-employee-list>
                        <div class="members-table-header">
                            <span>الموظف</span>
                            <span>القسم</span>
                            <span>المنصب</span>
                            <span>المدير المباشر</span>
                            <span>البريد الإلكتروني</span>
                            <span>الهاتف</span>
                            <span>تاريخ التوظيف</span>
                            <span class="text-center">إجراء</span>
                        </div>

                    @foreach ($employees as $emp)
                        @php
                            $searchBlob = mb_strtolower(implode(' ', array_filter([
                                $emp->full_name,
                                $emp->employee_code,
                                $emp->work_email,
                                $emp->personal_email,
                                $emp->work_phone,
                                $emp->personal_phone,
                                $emp->department->name ?? '',
                                $emp->position->title ?? '',
                                $emp->manager?->full_name,
                            ])));
                            $email = $emp->work_email ?? $emp->personal_email;
                            $phone = $emp->work_phone ?? $emp->personal_phone;
                            $initial = mb_substr($emp->first_name ?? $emp->full_name ?? '?', 0, 1);
                        @endphp
                        <div class="members-table-row"
                             data-employee-card
                             data-department-id="{{ $emp->department_id }}"
                             data-search="{{ $searchBlob }}">
                            <div class="col-employee">
                                <div class="employee-avatar-lg">{{ $initial }}</div>
                                <div class="min-w-0">
                                    <div class="employee-name" title="{{ $emp->full_name }}">{{ $emp->full_name }}</div>
                                    <div class="employee-code">{{ $emp->employee_code }}</div>
                                </div>
                            </div>

                            <div class="members-mobile-field">
                                <span class="members-mobile-label">القسم</span>
                                <span class="cell-text">
                                    @if ($emp->department)
                                        <span class="dept-pill" title="{{ $emp->department->name }}">{{ $emp->department->name }}</span>
                                    @else
                                        —
                                    @endif
                                </span>
                            </div>

                            <div class="members-mobile-field">
                                <span class="members-mobile-label">المنصب</span>
                                <span class="cell-text" title="{{ $emp->position->title ?? '' }}">{{ $emp->position->title ?? '—' }}</span>
                            </div>

                            <div class="members-mobile-field">
                                <span class="members-mobile-label">المدير المباشر</span>
                                <span class="cell-text" title="{{ $emp->manager?->full_name }}">{{ $emp->manager?->full_name ?? '—' }}</span>
                            </div>

                            <div class="members-mobile-field">
                                <span class="members-mobile-label">البريد الإلكتروني</span>
                                <span class="cell-text" title="{{ $email }}">
                                    @if ($email)
                                        <a href="mailto:{{ $email }}">{{ $email }}</a>
                                    @else
                                        —
                                    @endif
                                </span>
                            </div>

                            <div class="members-mobile-field">
                                <span class="members-mobile-label">الهاتف</span>
                                <span class="cell-text">{{ $phone ?? '—' }}</span>
                            </div>

                            <div class="members-mobile-field">
                                <span class="members-mobile-label">تاريخ التوظيف</span>
                                <span class="cell-text">{{ $emp->hire_date?->format('Y/m/d') ?? '—' }}</span>
                            </div>

                            <div class="col-actions">
                                @can('view', $emp)
                                    <a href="{{ route('admin.employees.show', $emp) }}" class="btn-view-sm" title="عرض الملف">
                                        <i class="ri-eye-line"></i>
                                    </a>
                                @endcan
                            </div>
                        </div>
                    @endforeach
                    </div>

                    <div class="empty-state d-none" data-empty-filtered>
                        <i class="ri-search-line"></i>
                        <p>لا توجد نتائج مطابقة للبحث أو الفلتر</p>
                    </div>
                @else
                    <div class="empty-state">
                        <i class="ri-team-line"></i>
                        <p>لا يوجد موظفين في القسم</p>
                    </div>
                @endif
            </div>

        </div>
    </div>
@stop
