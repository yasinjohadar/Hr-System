@extends('employee.layouts.master')

@section('page-title')
    التسلسل الإداري
@stop

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/employee-hierarchy.css') }}">
@endpush

@section('content')
    <div class="main-content app-content employee-hierarchy-page">
        <div class="container-fluid pt-4">

            <div class="card page-hero mb-4">
                <div class="card-body py-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="page-hero-icon">
                            <i class="ri-organization-chart"></i>
                        </div>
                        <div>
                            <h4 class="mb-1 page-hero-title fw-bold">التسلسل الإداري</h4>
                            <p class="mb-0 page-hero-subtitle">هيكل الإدارة والتابعية داخل المؤسسة</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-sm-4">
                    <div class="stat-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-value">{{ $stats['manager_levels'] }}</div>
                                <div class="stat-label">مستويات إدارية</div>
                            </div>
                            <div class="stat-icon stat-icon--primary"><i class="ri-user-star-line"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="stat-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-value">{{ $stats['departments'] }}</div>
                                <div class="stat-label">أقسام في التسلسل</div>
                            </div>
                            <div class="stat-icon stat-icon--success"><i class="ri-building-line"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="stat-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-value">{{ $stats['has_direct_manager'] ? 'نعم' : '—' }}</div>
                                <div class="stat-label">مدير مباشر</div>
                            </div>
                            <div class="stat-icon stat-icon--info"><i class="ri-arrow-up-line"></i></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-xl-4">
                    <div class="section-card">
                        <div class="section-card-header">
                            <i class="ri-user-line me-1 text-primary"></i>معلوماتي
                        </div>
                        <div class="section-card-body text-center">
                            @if ($employee->photo)
                                <img src="{{ asset('storage/' . $employee->photo) }}" alt="" class="profile-avatar mb-3">
                            @else
                                <div class="profile-avatar--placeholder mb-3 mx-auto">
                                    {{ substr($employee->first_name, 0, 1) }}
                                </div>
                            @endif
                            <h5 class="fw-bold mb-1">{{ $employee->full_name }}</h5>
                            <p class="text-muted fs-13 mb-2 font-monospace">{{ $employee->employee_code }}</p>
                            <span class="badge bg-primary-transparent text-primary mb-3">{{ $employee->position->title ?? '—' }}</span>
                            <div class="text-start mt-2">
                                <div class="profile-detail-row">
                                    <span class="profile-detail-label">القسم</span>
                                    <span class="profile-detail-value">{{ $employee->department->name ?? '—' }}</span>
                                </div>
                                <div class="profile-detail-row">
                                    <span class="profile-detail-label">الفرع</span>
                                    <span class="profile-detail-value">{{ $employee->branch->name ?? '—' }}</span>
                                </div>
                                <div class="profile-detail-row">
                                    <span class="profile-detail-label">تاريخ التوظيف</span>
                                    <span class="profile-detail-value">{{ $employee->hire_date->format('Y/m/d') }}</span>
                                </div>
                                <div class="profile-detail-row">
                                    <span class="profile-detail-label">سنوات الخدمة</span>
                                    <span class="profile-detail-value">
                                        {{ $yearsOfService }} {{ $yearsOfService === 1 ? 'سنة' : 'سنوات' }}
                                        @if ($monthsOfService > 0)
                                            و {{ $monthsOfService }} {{ $monthsOfService === 1 ? 'شهر' : 'أشهر' }}
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-4">
                    <div class="section-card">
                        <div class="section-card-header">
                            <i class="ri-git-branch-line me-1 text-primary"></i>التسلسل الإداري
                        </div>
                        <div class="section-card-body py-2 px-3">
                            <div class="chain-item">
                                <div class="chain-avatar chain-avatar--self">{{ substr($employee->first_name, 0, 1) }}</div>
                                <div>
                                    <div class="chain-role">أنت</div>
                                    <div class="chain-name">{{ $employee->full_name }}</div>
                                    <div class="chain-meta">{{ $employee->position->title ?? '—' }}</div>
                                </div>
                            </div>

                            @if ($directManager)
                                <div class="chain-item">
                                    <div class="chain-avatar chain-avatar--manager">{{ substr($directManager->first_name, 0, 1) }}</div>
                                    <div>
                                        <div class="chain-role">المدير المباشر</div>
                                        <div class="chain-name">{{ $directManager->full_name }}</div>
                                        <div class="chain-meta">{{ $directManager->position->title ?? '—' }}</div>
                                    </div>
                                </div>
                            @endif

                            @if ($departmentManager && (! $directManager || $departmentManager->id !== $directManager->user_id))
                                <div class="chain-item">
                                    <div class="chain-avatar chain-avatar--dept">{{ mb_substr($departmentManager->name, 0, 1) }}</div>
                                    <div>
                                        <div class="chain-role">رئيس القسم</div>
                                        <div class="chain-name">{{ $departmentManager->name }}</div>
                                        <div class="chain-meta">{{ $departmentManager->email ?? '—' }}</div>
                                    </div>
                                </div>
                            @endif

                            @if (count($managerChain) > 1)
                                @foreach ($managerChain as $index => $manager)
                                    @if ($manager->id !== $directManager?->id)
                                        <div class="chain-item">
                                            <div class="chain-avatar chain-avatar--other">{{ substr($manager->first_name, 0, 1) }}</div>
                                            <div>
                                                <div class="chain-role">
                                                    @if ($index === 0) مدير
                                                    @else مستوى {{ $index + 1 }}
                                                    @endif
                                                </div>
                                                <div class="chain-name">{{ $manager->full_name }}</div>
                                                <div class="chain-meta">{{ $manager->position->title ?? '—' }}</div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            @endif

                            @if (! $directManager && ! $departmentManager && count($managerChain) <= 1)
                                <div class="chain-empty">
                                    <i class="ri-information-line d-block mb-2 fs-20"></i>
                                    لا يوجد مدير مباشر محدد
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-xl-4">
                    <div class="section-card mb-4">
                        <div class="section-card-header">
                            <i class="ri-building-4-line me-1 text-primary"></i>هيكل القسم
                        </div>
                        <div class="section-card-body py-2 px-3">
                            @forelse ($departmentHierarchy as $index => $dept)
                                @php
                                    $isLast = $index === count($departmentHierarchy) - 1;
                                @endphp
                                <div class="dept-tree-item" data-level="{{ min($index, 3) }}">
                                    @if ($index > 0)
                                        <i class="ri-corner-down-right-line text-muted"></i>
                                    @endif
                                    <div class="dept-icon {{ $isLast ? 'dept-icon--current' : '' }}">
                                        <i class="ri-building-line"></i>
                                    </div>
                                    <div>
                                        <div class="fw-semibold fs-13">{{ $dept['name'] }}</div>
                                        <small class="text-muted">{{ $dept['code'] }}</small>
                                        @if ($dept['manager'])
                                            <br><small class="text-primary">رئيس: {{ $dept['manager']->name }}</small>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <div class="chain-empty">لا توجد معلومات عن القسم</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
