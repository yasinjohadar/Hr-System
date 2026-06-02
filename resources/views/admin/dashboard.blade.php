@extends('admin.layouts.master')

@section('page-title')
    لوحة التحكم
@stop

@section('content')
    <div class="main-content app-content admin-dashboard">
        <div class="container-fluid">
            @include('admin.dashboard.partials.hero')

            <ul class="nav nav-tabs nav-tabs-dashboard mb-4" id="adminDashboardTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="tab-overview-btn" data-bs-toggle="tab" data-bs-target="#tab-overview" type="button" role="tab" data-tab-key="overview">
                        <i class="ri-dashboard-line me-1"></i>نظرة عامة
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tab-followup-btn" data-bs-toggle="tab" data-bs-target="#tab-followup" type="button" role="tab" data-tab-key="followup">
                        <i class="ri-alarm-line me-1"></i>المتابعة والتنبيهات
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tab-analytics-btn" data-bs-toggle="tab" data-bs-target="#tab-analytics" type="button" role="tab" data-tab-key="analytics">
                        <i class="ri-bar-chart-grouped-line me-1"></i>التحليلات والنشاط
                    </button>
                </li>
            </ul>

            <div class="tab-content">
                {{-- نظرة عامة --}}
                <div class="tab-pane fade show active" id="tab-overview" role="tabpanel">
                    @include('admin.dashboard.partials.kpi-primary')
                    @include('admin.dashboard.partials.kpi-secondary')
                    @include('admin.dashboard.partials.quick-actions')
                </div>

                {{-- المتابعة --}}
                <div class="tab-pane fade" id="tab-followup" role="tabpanel">
                    @include('admin.dashboard.partials.mena-kpis')
                    @include('admin.dashboard.partials.urgent-tasks')
                    @include('admin.dashboard.partials.alerts')

                    @if(isset($announcements) && $announcements->isNotEmpty())
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
                    @endif
                </div>

                {{-- التحليلات --}}
                <div class="tab-pane fade" id="tab-analytics" role="tabpanel">
                    <div class="row g-4">
                        <div class="col-lg-8">
                            <div class="card custom-card h-100">
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
                        <div class="col-lg-4">
                            <div class="card custom-card h-100">
                                <div class="card-header">
                                    <h6 class="card-title fw-semibold mb-0">
                                        <i class="ri-history-line me-1"></i>آخر الأنشطة
                                    </h6>
                                </div>
                                <div class="card-body pt-0">
                                    @forelse($recentActivities as $activity)
                                        <div class="activity-timeline-item d-flex gap-3">
                                            <span class="activity-icon bg-{{ $activity['color'] ?? 'primary' }}-transparent text-{{ $activity['color'] ?? 'primary' }}">
                                                @php
                                                    $riMap = [
                                                        'fas fa-user-plus' => 'ri-user-add-line',
                                                        'fas fa-calendar' => 'ri-calendar-line',
                                                        'fas fa-ticket-alt' => 'ri-coupon-line',
                                                    ];
                                                    $icon = $riMap[$activity['icon'] ?? ''] ?? 'ri-record-circle-line';
                                                @endphp
                                                <i class="{{ $icon }}"></i>
                                            </span>
                                            <div class="flex-grow-1 min-w-0">
                                                <h6 class="mb-1 fs-14 fw-semibold">{{ $activity['title'] ?? '' }}</h6>
                                                <p class="mb-1 text-muted fs-12 text-truncate">{{ $activity['description'] ?? '' }}</p>
                                                <small class="text-muted">{{ $activity['time']->diffForHumans() ?? '' }}</small>
                                            </div>
                                        </div>
                                    @empty
                                        <p class="text-muted text-center py-4 mb-0">لا توجد أنشطة حديثة</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
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
