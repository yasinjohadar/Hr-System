@extends('employee.layouts.master')

@section('page-title')
    الحضور والانصراف
@stop

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/employee-attendance.css') }}">
@endpush

@section('content')
    <div class="main-content app-content employee-attendance-page">
        <div class="container-fluid pt-4">

            <div class="card page-hero mb-4">
                <div class="card-body py-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="page-hero-icon">
                            <i class="ri-calendar-check-line"></i>
                        </div>
                        <div>
                            <h4 class="mb-1 page-hero-title fw-bold">سجل الحضور والانصراف</h4>
                            <p class="mb-0 page-hero-subtitle">ملخص شهر {{ $monthLabel }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-sm-6 col-xl">
                    <div class="stat-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-value">{{ $stats['present'] }}</div>
                                <div class="stat-label">حاضر (هذا الشهر)</div>
                            </div>
                            <div class="stat-icon stat-icon--success"><i class="ri-checkbox-circle-line"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl">
                    <div class="stat-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-value">{{ $stats['late'] }}</div>
                                <div class="stat-label">متأخر</div>
                            </div>
                            <div class="stat-icon stat-icon--warning"><i class="ri-time-line"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl">
                    <div class="stat-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-value">{{ $stats['absent'] }}</div>
                                <div class="stat-label">غائب</div>
                            </div>
                            <div class="stat-icon stat-icon--danger"><i class="ri-close-circle-line"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl">
                    <div class="stat-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-value">{{ $stats['hours_month'] }}</div>
                                <div class="stat-label">ساعات عمل</div>
                            </div>
                            <div class="stat-icon stat-icon--primary"><i class="ri-timer-line"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl">
                    <div class="stat-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-value">{{ $stats['overtime_month'] }}</div>
                                <div class="stat-label">ساعات إضافية</div>
                            </div>
                            <div class="stat-icon stat-icon--info"><i class="ri-add-circle-line"></i></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="content-panel mb-4">
                <div class="content-panel-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                    <div>
                        <h5 class="fw-bold mb-1 text-dark">سجلات الحضور</h5>
                        <p class="text-muted fs-13 mb-0">{{ $attendances->total() }} سجل</p>
                    </div>
                    <div class="filter-pills" role="group">
                        <button type="button" class="filter-pill active" data-attendance-filter="all">الكل</button>
                        <button type="button" class="filter-pill" data-attendance-filter="present">حاضر</button>
                        <button type="button" class="filter-pill" data-attendance-filter="late">متأخر</button>
                        <button type="button" class="filter-pill" data-attendance-filter="absent">غائب</button>
                    </div>
                </div>

                <div id="attendance-list">
                    @forelse ($attendances as $attendance)
                        @php
                            $statusClass = match ($attendance->status) {
                                'present' => 'present',
                                'absent' => 'absent',
                                'late' => 'late',
                                'half_day' => 'half_day',
                                'on_leave' => 'on_leave',
                                'holiday' => 'holiday',
                                default => 'present',
                            };
                            $hours = round($attendance->hours_worked / 60, 2);
                            $overtime = round($attendance->overtime_minutes / 60, 2);
                        @endphp
                        <article class="attendance-row" data-status="{{ $attendance->status }}">
                            <div class="att-date-badge">
                                <div class="att-date-day">{{ $attendance->attendance_date->format('d') }}</div>
                                <div class="att-date-month">{{ $attendance->attendance_date->translatedFormat('M') }}</div>
                            </div>
                            <div>
                                <div class="fw-semibold text-dark mb-2 fs-13">
                                    {{ $attendance->attendance_date->translatedFormat('l، d F Y') }}
                                </div>
                                <div class="att-times">
                                    <div class="att-time-block">
                                        <span class="label">دخول</span>
                                        <span class="value">
                                            {{ $attendance->check_in ? \Carbon\Carbon::parse($attendance->check_in)->format('H:i') : '—' }}
                                        </span>
                                    </div>
                                    <div class="att-time-block">
                                        <span class="label">خروج</span>
                                        <span class="value">
                                            {{ $attendance->check_out ? \Carbon\Carbon::parse($attendance->check_out)->format('H:i') : '—' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="attendance-row-side">
                                <span class="att-hours-chip">{{ $hours }} س</span>
                                @if ($overtime > 0)
                                    <span class="att-overtime-chip">+{{ $overtime }} إضافي</span>
                                @endif
                                <span class="status-pill status-pill--{{ $statusClass }}">{{ $attendance->status_ar }}</span>
                            </div>
                        </article>
                    @empty
                        <div class="empty-state">
                            <div class="empty-icon"><i class="ri-calendar-todo-line"></i></div>
                            <h5 class="fw-semibold text-dark mb-2">لا توجد سجلات حضور</h5>
                            <p class="text-muted mb-0">ستظهر سجلاتك هنا عند تسجيل الحضور</p>
                        </div>
                    @endforelse
                </div>

                @if ($attendances->hasPages())
                    <div class="p-3 border-top">
                        {{ $attendances->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@stop

@push('scripts')
    <script src="{{ asset('assets/js/employee-attendance.js') }}"></script>
@endpush
