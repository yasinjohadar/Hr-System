@extends('admin.layouts.master')

@section('page-title')
    التقرير الشامل
@stop

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin-reports.css') }}">
@endpush

@section('content')
    @php
        $overviewStats = [
            ['key' => 'total_employees', 'label' => 'إجمالي الموظفين', 'icon' => 'ri-team-line', 'tone' => 'primary'],
            ['key' => 'total_departments', 'label' => 'الأقسام', 'icon' => 'ri-building-line', 'tone' => 'success'],
            ['key' => 'total_positions', 'label' => 'المناصب', 'icon' => 'ri-briefcase-line', 'tone' => 'info'],
            ['key' => 'total_branches', 'label' => 'الفروع', 'icon' => 'ri-map-pin-line', 'tone' => 'warning'],
        ];
        $attendanceStats = [
            ['key' => 'today_attendance', 'label' => 'حضور اليوم', 'icon' => 'ri-user-check-line', 'tone' => 'success'],
            ['key' => 'today_absent', 'label' => 'غياب اليوم', 'icon' => 'ri-user-unfollow-line', 'tone' => 'danger'],
            ['key' => 'approved_leaves', 'label' => 'في إجازة حالياً', 'icon' => 'ri-sun-line', 'tone' => 'info'],
        ];
        $hrStats = [
            ['key' => 'active_vacancies', 'label' => 'وظائف شاغرة', 'icon' => 'ri-user-search-line', 'tone' => 'primary'],
            ['key' => 'pending_applications', 'label' => 'طلبات قيد المراجعة', 'icon' => 'ri-time-line', 'tone' => 'warning'],
            ['key' => 'ongoing_trainings', 'label' => 'دورات قيد التنفيذ', 'icon' => 'ri-book-open-line', 'tone' => 'info'],
            ['key' => 'training_participants', 'label' => 'مشاركون في التدريب', 'icon' => 'ri-graduation-cap-line', 'tone' => 'muted'],
        ];
        $quickLinks = [
            ['route' => 'admin.reports.employees', 'label' => 'تقارير الموظفين', 'icon' => 'ri-team-line', 'tone' => 'primary'],
            ['route' => 'admin.reports.attendance', 'label' => 'تقارير الحضور', 'icon' => 'ri-calendar-check-line', 'tone' => 'success'],
            ['route' => 'admin.reports.salaries', 'label' => 'تقارير الرواتب', 'icon' => 'ri-wallet-3-line', 'tone' => 'warning'],
            ['route' => 'admin.reports.leaves', 'label' => 'تقارير الإجازات', 'icon' => 'ri-sun-line', 'tone' => 'info'],
            ['route' => 'admin.reports.performance', 'label' => 'تقارير التقييمات', 'icon' => 'ri-star-line', 'tone' => 'purple'],
            ['route' => 'admin.reports.training', 'label' => 'تقارير التدريب', 'icon' => 'ri-graduation-cap-line', 'tone' => 'secondary'],
            ['route' => 'admin.reports.recruitment', 'label' => 'تقارير التوظيف', 'icon' => 'ri-briefcase-line', 'tone' => 'danger'],
            ['route' => 'admin.reports.benefits', 'label' => 'تقارير المزايا', 'icon' => 'ri-gift-line', 'tone' => 'teal'],
        ];
        $salaryTotal = $monthlySalaries?->total ?? 0;
        $salaryBase = $monthlySalaries?->base ?? 0;
        $salaryCount = $monthlySalaries?->count ?? 0;
    @endphp

    <div class="main-content app-content admin-reports-page">
        <div class="container-fluid pt-4">

            <div class="card page-hero mb-4">
                <div class="card-body py-4">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="page-hero-icon">
                                <i class="ri-dashboard-3-line"></i>
                            </div>
                            <div>
                                <h4 class="mb-1 page-hero-title fw-bold">التقرير الشامل</h4>
                                <p class="mb-0 page-hero-subtitle">لوحة معلومات سريعة لأهم مؤشرات الموارد البشرية</p>
                            </div>
                        </div>
                        <a href="{{ route('admin.reports.index') }}" class="btn btn-hero-outline btn-sm">
                            <i class="ri-arrow-right-line me-1"></i>العودة للتقارير
                        </a>
                    </div>
                </div>
            </div>

            <p class="section-heading">نظرة عامة</p>
            <div class="row g-3 mb-4">
                @foreach ($overviewStats as $item)
                    <div class="col-sm-6 col-xl-3">
                        <div class="stat-card">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <div class="stat-value">{{ $stats[$item['key']] }}</div>
                                    <div class="stat-label">{{ $item['label'] }}</div>
                                </div>
                                <div class="stat-icon stat-icon--{{ $item['tone'] }}"><i class="{{ $item['icon'] }}"></i></div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <p class="section-heading">الحضور والإجازات اليوم</p>
            <div class="row g-3 mb-4">
                @foreach ($attendanceStats as $item)
                    <div class="col-md-4">
                        <div class="stat-card">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <div class="stat-value">{{ $stats[$item['key']] }}</div>
                                    <div class="stat-label">{{ $item['label'] }}</div>
                                </div>
                                <div class="stat-icon stat-icon--{{ $item['tone'] }}"><i class="{{ $item['icon'] }}"></i></div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <p class="section-heading">التوظيف والتدريب</p>
            <div class="row g-3 mb-4">
                @foreach ($hrStats as $item)
                    <div class="col-sm-6 col-xl-3">
                        <div class="stat-card">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <div class="stat-value">{{ $stats[$item['key']] }}</div>
                                    <div class="stat-label">{{ $item['label'] }}</div>
                                </div>
                                <div class="stat-icon stat-icon--{{ $item['tone'] }}"><i class="{{ $item['icon'] }}"></i></div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="content-panel mb-4">
                <div class="content-panel-header">إحصائيات رواتب الشهر الحالي</div>
                <div class="content-panel-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="salary-stat">
                                <div class="salary-stat-label">إجمالي الرواتب</div>
                                <div class="salary-stat-value salary-stat-value--primary">{{ number_format($salaryTotal, 2) }} ر.س</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="salary-stat">
                                <div class="salary-stat-label">إجمالي الأساسي</div>
                                <div class="salary-stat-value salary-stat-value--success">{{ number_format($salaryBase, 2) }} ر.س</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="salary-stat">
                                <div class="salary-stat-label">عدد الرواتب</div>
                                <div class="salary-stat-value salary-stat-value--info">{{ $salaryCount }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="content-panel">
                <div class="content-panel-header">روابط سريعة للتقارير التفصيلية</div>
                <div class="content-panel-body">
                    <div class="quick-links-grid">
                        @foreach ($quickLinks as $link)
                            <a href="{{ route($link['route']) }}" class="quick-link-chip quick-link-chip--{{ $link['tone'] }}">
                                <i class="{{ $link['icon'] }}"></i>
                                <span>{{ $link['label'] }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>

        </div>
    </div>
@stop
