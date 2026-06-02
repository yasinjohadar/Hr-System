@extends('admin.layouts.master')

@section('page-title')
    لوحة التحكم
@stop

@section('content')
    <div class="main-content app-content admin-dashboard">
        <div class="container-fluid">
            @include('admin.dashboard.partials.hero')

            <div class="dashboard-single-page">
                @include('admin.dashboard.partials.kpi-primary')
                @include('admin.dashboard.partials.kpi-secondary')
                @include('admin.dashboard.partials.mena-kpis')
                @include('admin.dashboard.partials.quick-actions')

                <div class="dashboard-section">
                    <div class="card custom-card">
                        <div class="card-header">
                            <h6 class="card-title fw-semibold mb-0">
                                <i class="ri-line-chart-line me-1"></i>إحصائيات الحضور (آخر 6 أشهر)
                            </h6>
                        </div>
                        <div class="card-body">
                            <div id="admin-attendance-chart"></div>
                        </div>
                    </div>
                </div>

                <div class="dashboard-section">
                    @include('admin.dashboard.partials.urgent-tasks')
                    @include('admin.dashboard.partials.alerts')
                </div>

                @if(isset($announcements) && $announcements->isNotEmpty())
                    <div class="dashboard-section">
                        <div class="card custom-card">
                            <div class="card-header d-flex justify-content-between align-items-center card-header-accent">
                                <h6 class="card-title fw-semibold mb-0">
                                    <i class="ri-megaphone-line me-1 text-primary"></i>إعلانات الشركة
                                </h6>
                                <a href="{{ route('admin.announcements.index') }}" class="btn btn-sm btn-outline-primary">عرض الكل</a>
                            </div>
                            <div class="card-body">
                                @foreach($announcements as $announcement)
                                    <div class="announcement-item">
                                        <h6 class="mb-1 fw-semibold">{{ $announcement->title }}</h6>
                                        @if($announcement->content)
                                            <p class="mb-1 text-muted fs-13">{{ Str::limit(strip_tags($announcement->content), 120) }}</p>
                                        @endif
                                        <small class="text-muted">
                                            {{ $announcement->publish_date?->format('Y/m/d') ?? $announcement->created_at->format('Y/m/d') }}
                                            @if($announcement->expiry_date)
                                                — حتى {{ $announcement->expiry_date->format('Y/m/d') }}
                                            @endif
                                        </small>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@stop

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin-dashboard.css') }}">
@endpush

@push('scripts')
    <script>
        window.adminDashboardConfig = {
            attendanceChart: @json($chartData['attendance'] ?? []),
            refreshUrl: @json(route('admin.dashboard', ['refresh' => 1])),
        };
    </script>
    <script src="{{ asset('assets/js/admin-dashboard.js') }}"></script>
@endpush
