@extends('employee.layouts.master')

@section('page-title')
    لوحة تحكم الموظف
@stop

@section('content')
    <div class="main-content app-content employee-dashboard" id="employee-dashboard">
        <div class="container-fluid">
            @include('employee.pages.self-service.dashboard.partials.header')

            <ul class="nav nav-tabs nav-tabs-dashboard mb-4" id="dashboardTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="tab-overview-btn" data-bs-toggle="tab" data-bs-target="#tab-overview" type="button" role="tab" data-tab-key="overview">
                        <i class="ri-dashboard-line me-1"></i>نظرة عامة
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tab-attendance-btn" data-bs-toggle="tab" data-bs-target="#tab-attendance" type="button" role="tab" data-tab-key="attendance">
                        <i class="ri-calendar-check-line me-1"></i>الحضور
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tab-leaves-btn" data-bs-toggle="tab" data-bs-target="#tab-leaves" type="button" role="tab" data-tab-key="leaves">
                        <i class="ri-sun-line me-1"></i>الإجازات والمالية
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tab-activity-btn" data-bs-toggle="tab" data-bs-target="#tab-activity" type="button" role="tab" data-tab-key="activity">
                        <i class="ri-pulse-line me-1"></i>النشاط
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="dashboardTabContent">
                <div class="tab-pane fade show active" id="tab-overview" role="tabpanel">
                    @include('employee.pages.self-service.dashboard.partials.tab-overview')
                </div>
                <div class="tab-pane fade" id="tab-attendance" role="tabpanel">
                    @include('employee.pages.self-service.dashboard.partials.tab-attendance')
                </div>
                <div class="tab-pane fade" id="tab-leaves" role="tabpanel">
                    @include('employee.pages.self-service.dashboard.partials.tab-leaves-payroll')
                </div>
                <div class="tab-pane fade" id="tab-activity" role="tabpanel">
                    @include('employee.pages.self-service.dashboard.partials.tab-activity')
                </div>
            </div>
        </div>
    </div>
@stop

@php
    $daysInMonth = now()->daysInMonth;
    $chartCategories = [];
    $chartData = [];
    $chartColors = [];
    $colorMap = ['present' => '#22c55e', 'late' => '#f59e0b', 'absent' => '#ef4444', 'none' => '#94a3b8'];
    $valueMap = ['present' => 3, 'late' => 2, 'absent' => 1, 'none' => 0];
    for ($d = 1; $d <= $daysInMonth; $d++) {
        $dateKey = now()->copy()->day($d)->format('Y-m-d');
        $chartCategories[] = (string) $d;
        $record = $attendanceByDay->get($dateKey);
        $status = $record['status'] ?? 'none';
        if (! isset($valueMap[$status])) {
            $status = 'none';
        }
        $chartData[] = $valueMap[$status];
        $chartColors[] = $colorMap[$status];
    }
    $attendanceChartInitial = [
        'categories' => $chartCategories,
        'series' => [['name' => 'الحضور', 'data' => $chartData]],
        'colors' => $chartColors,
        'monthLabel' => now()->translatedFormat('F Y'),
    ];
@endphp

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/employee-dashboard.css') }}">
@endpush

@push('scripts')
    <script>
        window.employeeDashboardConfig = {
            refreshUrl: @json(route('employee.dashboard.refresh')),
            widgetUrlTemplate: @json(route('employee.dashboard.widget', ['widget' => '__WIDGET__'])),
            attendanceChart: @json($attendanceChartInitial),
            csrfToken: @json(csrf_token()),
        };
    </script>
    <script src="{{ asset('assets/js/employee-dashboard.js') }}"></script>
@endpush
