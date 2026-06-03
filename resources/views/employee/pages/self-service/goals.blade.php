@extends('employee.layouts.master')

@section('page-title')
    الأهداف
@stop

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/employee-goals.css') }}">
@endpush

@section('content')
    <div class="main-content app-content employee-goals-page">
        <div class="container-fluid pt-4">

            <div class="card page-hero mb-4">
                <div class="card-body py-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="page-hero-icon">
                            <i class="ri-focus-3-line"></i>
                        </div>
                        <div>
                            <h4 class="mb-1 page-hero-title fw-bold">أهدافي</h4>
                            <p class="mb-0 page-hero-subtitle">أهدافك الوظيفية والشخصية ومتابعة التقدم</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-value">{{ $stats['total'] }}</div>
                                <div class="stat-label">إجمالي الأهداف</div>
                            </div>
                            <div class="stat-icon stat-icon--primary"><i class="ri-flag-line"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-value">{{ $stats['in_progress'] }}</div>
                                <div class="stat-label">قيد التنفيذ</div>
                            </div>
                            <div class="stat-icon stat-icon--warning"><i class="ri-loader-4-line"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-value">{{ $stats['completed'] }}</div>
                                <div class="stat-label">مكتمل</div>
                            </div>
                            <div class="stat-icon stat-icon--success"><i class="ri-checkbox-circle-line"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-value">{{ $stats['overdue'] }}</div>
                                <div class="stat-label">متأخر</div>
                            </div>
                            <div class="stat-icon stat-icon--danger"><i class="ri-alarm-warning-line"></i></div>
                        </div>
                    </div>
                </div>
            </div>

            @if ($goals->isNotEmpty())
                <div class="filter-pills" role="group">
                    <button type="button" class="filter-pill active" data-goal-filter="all">الكل</button>
                    <button type="button" class="filter-pill" data-goal-filter="in_progress">قيد التنفيذ</button>
                    <button type="button" class="filter-pill" data-goal-filter="completed">مكتمل</button>
                    <button type="button" class="filter-pill" data-goal-filter="overdue">متأخر</button>
                    <button type="button" class="filter-pill" data-goal-filter="not_started">لم يبدأ</button>
                </div>

                <div class="row g-3">
                    @foreach ($goals as $goal)
                        @php
                            $pct = min(100, max(0, (int) $goal->progress_percentage));
                            $isOverdue = $goal->target_date->isPast()
                                && ! in_array($goal->status, ['completed', 'cancelled']);
                            $filterState = $isOverdue ? 'overdue' : $goal->status;
                            $statusClass = $isOverdue ? 'overdue' : $goal->status;
                            $statusLabel = $isOverdue ? 'متأخر' : $goal->status_name_ar;
                            $progressBarClass = $goal->status === 'completed'
                                ? 'goal-progress-bar--complete'
                                : ($isOverdue ? 'goal-progress-bar--overdue' : '');
                        @endphp
                        <div class="col-md-6 col-xl-4 goal-card-item" data-filter-state="{{ $filterState }}">
                            <div class="goal-card {{ $isOverdue ? 'is-overdue' : '' }}">
                                <div class="goal-card-header">
                                    <div class="goal-title">{{ $goal->title }}</div>
                                    <div class="goal-badges">
                                        <span class="type-pill">{{ $goal->type_name_ar }}</span>
                                        <span class="priority-pill priority-pill--{{ $goal->priority }}">{{ $goal->priority_name_ar }}</span>
                                    </div>
                                </div>

                                <div class="goal-date">
                                    <i class="ri-calendar-line me-1"></i>
                                    تاريخ الهدف: {{ $goal->target_date->format('d/m/Y') }}
                                </div>

                                <div class="goal-progress-label">
                                    <span>التقدم</span>
                                    <span class="progress-pct">{{ $pct }}%</span>
                                </div>
                                <div class="goal-progress">
                                    <div class="goal-progress-bar {{ $progressBarClass }}" style="width: {{ $pct }}%"></div>
                                </div>

                                <div class="goal-card-footer">
                                    <span class="status-pill status-pill--{{ $statusClass }}">{{ $statusLabel }}</span>
                                    @if ($goal->description)
                                        <span class="text-muted fs-12 text-truncate" style="max-width: 50%"
                                            title="{{ $goal->description }}">
                                            {{ \Illuminate\Support\Str::limit($goal->description, 40) }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state">
                    <div class="empty-icon"><i class="ri-focus-3-line"></i></div>
                    <h5 class="fw-semibold text-dark mb-2">لا توجد أهداف مسجلة</h5>
                    <p class="text-muted mb-0">ستظهر أهدافك هنا بعد إضافتها من الإدارة أو تقييم الأداء</p>
                </div>
            @endif
        </div>
    </div>
@stop

@push('scripts')
    <script src="{{ asset('assets/js/employee-goals.js') }}"></script>
@endpush
