@extends('admin.layouts.master')

@section('page-title')
    لوحة التحكم
@stop

@section('content')
    <div class="main-content app-content admin-dashboard">
        <div class="container-fluid">

            <div class="dashboard-welcome d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                <div>
                    <h4>مرحباً، {{ auth()->user()->name }}!</h4>
                    <p class="mb-0 text-muted">لوحة تحكم شاملة لإدارة الموارد البشرية</p>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    @if(auth()->user()->hasRole('department_head') && auth()->user()->canAccessEmployeePortal())
                        <a href="{{ route('employee.dashboard') }}" target="_blank" rel="noopener noreferrer"
                            class="btn btn-primary btn-sm">
                            <i class="ri-user-3-line me-1"></i>بوابة الموظف
                        </a>
                    @endif
                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="refreshAdminDashboard(event)">
                        <i class="ri-refresh-line me-1"></i>تحديث
                    </button>
                    <a href="{{ route('admin.employees.create') }}" class="btn btn-outline-primary btn-sm">
                        <i class="ri-user-add-line me-1"></i>موظف جديد
                    </a>
                    <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-primary btn-sm">
                        <i class="ri-bar-chart-box-line me-1"></i>التقارير
                    </a>
                </div>
            </div>

            <div class="row g-3 mb-4">
                @foreach ($dashboardWidgets as $index => $widget)
                    <div class="col-xl-3 col-lg-6 col-md-6">
                        <a href="{{ route($widget['route'], $widget['route_params'] ?? []) }}"
                           class="dashboard-stat-link"
                           style="--card-delay: {{ $index * 0.1 }}s">
                            <div class="dashboard-stat-card dashboard-stat-{{ $widget['theme'] }}">
                                <div class="stat-card-shine"></div>
                                <div class="stat-card-mesh"></div>
                                <div class="stat-card-bubble stat-card-bubble-1"></div>
                                <div class="stat-card-bubble stat-card-bubble-2"></div>
                                <div class="stat-card-bubble stat-card-bubble-3"></div>
                                <div class="stat-card-glow"></div>
                                <div class="stat-card-body">
                                    <div class="stat-card-content">
                                        <span class="stat-label">{{ $widget['title'] }}</span>
                                        <span class="stat-value"
                                              data-count="{{ $widget['value'] }}"
                                              @if(!empty($widget['suffix'])) data-suffix="{{ $widget['suffix'] }}" @endif>0</span>
                                        <span class="stat-subtext">{{ $widget['subtext'] }}</span>
                                    </div>
                                    <div class="stat-icon-wrap">
                                        <span class="stat-icon-ring"></span>
                                        <span class="stat-icon-circle">
                                            <i class="{{ $widget['icon'] }}"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>

            <div class="row g-3 mb-4">
                @foreach ($secondaryWidgets as $widget)
                    <div class="col-xl-3 col-lg-4 col-md-6">
                        <a href="{{ route($widget['route'], $widget['route_params'] ?? []) }}"
                           class="dashboard-secondary-link text-decoration-none">
                            <div class="card custom-card mb-0 h-100 dashboard-secondary-card">
                                <div class="card-body d-flex align-items-center justify-content-between">
                                    <div>
                                        <p class="text-muted mb-1 fs-12">{{ $widget['title'] }}</p>
                                        <h4 class="mb-0 fw-bold">{{ $widget['value'] }}</h4>
                                    </div>
                                    <span class="menu-icon-box menu-icon-{{ $widget['color'] }}">
                                        <i class="{{ $widget['icon'] }}"></i>
                                    </span>
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>

            <div class="shortcuts-section mb-4">
                <div class="shortcuts-section-header">
                    <span class="shortcuts-section-icon"><i class="ri-flashlight-line"></i></span>
                    <h5 class="dashboard-section-title mb-0">إجراءات سريعة</h5>
                </div>
                <div class="row g-3 shortcuts-grid">
                    @foreach ($quickShortcuts as $index => $shortcut)
                        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-6">
                            <a href="{{ route($shortcut['route']) }}"
                               class="shortcut-card shortcut-theme-{{ $shortcut['color'] }}"
                               style="--shortcut-delay: {{ $index * 0.05 }}s">
                                <span class="shortcut-shine"></span>
                                <span class="shortcut-accent"></span>
                                <span class="shortcut-icon-wrap">
                                    <span class="shortcut-icon-ring"></span>
                                    <span class="shortcut-icon">
                                        <i class="{{ $shortcut['icon'] }}"></i>
                                    </span>
                                </span>
                                <span class="shortcut-title">{{ $shortcut['title'] }}</span>
                                <span class="shortcut-desc">{{ $shortcut['desc'] }}</span>
                                <span class="shortcut-arrow"><i class="ri-arrow-left-s-line"></i></span>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-xl-8 col-lg-7">
                    <div class="card custom-card h-100">
                        <div class="card-header">
                            <h6 class="card-title mb-0">
                                <i class="ri-line-chart-line me-1"></i>إحصائيات الحضور (آخر 6 أشهر)
                            </h6>
                        </div>
                        <div class="card-body">
                            <div id="admin-attendance-chart"></div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-lg-5">
                    <div class="card custom-card h-100">
                        <div class="card-header">
                            <h6 class="card-title mb-0">ملخص اليوم</h6>
                        </div>
                        <div class="card-body pt-2">
                            @foreach ($todaySummary as $item)
                                <div class="today-summary-item">
                                    <div class="d-flex align-items-center">
                                        <span class="summary-icon menu-icon-box menu-icon-{{ $item['color'] }} menu-icon-sm">
                                            <i class="{{ $item['icon'] }}"></i>
                                        </span>
                                        <span class="summary-label">{{ $item['label'] }}</span>
                                    </div>
                                    <span class="summary-value">{{ $item['value'] }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            @include('admin.dashboard.partials.urgent-tasks')
            @include('admin.dashboard.partials.alerts')

            @if(isset($announcements) && $announcements->isNotEmpty())
                <div class="card custom-card mt-3">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="card-title fw-semibold mb-0">
                            <i class="ri-megaphone-line me-1 text-primary"></i>إعلانات الشركة
                        </h6>
                        <a href="{{ route('admin.announcements.index') }}" class="btn btn-sm btn-outline-primary">عرض الكل</a>
                    </div>
                    <div class="card-body">
                        @foreach($announcements as $announcement)
                            <div class="announcement-item {{ !$loop->last ? 'border-bottom pb-3 mb-3' : '' }}">
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
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.stat-value[data-count]').forEach(function (el) {
                const target = parseInt(el.getAttribute('data-count'), 10) || 0;
                const suffix = el.getAttribute('data-suffix') || '';
                const duration = 1400;
                const start = performance.now();

                function easeOutExpo(t) {
                    return t === 1 ? 1 : 1 - Math.pow(2, -10 * t);
                }

                function tick(now) {
                    const progress = Math.min((now - start) / duration, 1);
                    const value = Math.round(easeOutExpo(progress) * target);
                    el.textContent = value.toLocaleString('en-US') + suffix;
                    if (progress < 1) {
                        requestAnimationFrame(tick);
                    } else {
                        el.classList.add('stat-value-done');
                    }
                }

                requestAnimationFrame(tick);
            });

            document.querySelectorAll('.shortcut-card').forEach(function (card) {
                card.addEventListener('click', function (e) {
                    const rect = card.getBoundingClientRect();
                    const size = Math.max(rect.width, rect.height);
                    const ripple = document.createElement('span');
                    ripple.className = 'shortcut-ripple';
                    ripple.style.width = ripple.style.height = size + 'px';
                    ripple.style.left = (e.clientX - rect.left - size / 2) + 'px';
                    ripple.style.top = (e.clientY - rect.top - size / 2) + 'px';
                    card.appendChild(ripple);
                    ripple.addEventListener('animationend', function () { ripple.remove(); });
                });
            });
        });
    </script>
@endpush
