@extends('admin.layouts.master')

@section('page-title')
    لوحة المؤشرات الإدارية
@stop

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin-reports.css') }}">
@endpush

@section('content')
    @php
        $kpiCards = [
            [
                'label' => 'الموظفون النشطون',
                'value' => $kpis['active_employees'],
                'icon' => 'ri-team-line',
                'tone' => 'primary',
                'link' => ['route' => 'admin.reports.employees', 'text' => 'تفاصيل'],
            ],
            [
                'label' => 'منتهية الخدمة (السنة الحالية)',
                'value' => $kpis['exits_this_year'],
                'icon' => 'ri-user-unfollow-line',
                'tone' => 'info',
                'link' => ['route' => 'admin.reports.turnover', 'text' => 'تقرير الدوران'],
            ],
            [
                'label' => 'معدل الدوران',
                'value' => $kpis['turnover_rate'] . '%',
                'icon' => 'ri-exchange-line',
                'tone' => 'warning',
            ],
            [
                'label' => 'إجمالي رواتب الشهر (معتمدة/مدفوعة)',
                'value' => number_format($kpis['payroll_total'], 0) . ' ر.س',
                'icon' => 'ri-wallet-3-line',
                'tone' => 'success',
                'link' => ['route' => 'admin.reports.salaries', 'text' => 'تقارير الرواتب'],
            ],
            [
                'label' => 'طلبات إجازة معلقة',
                'value' => $kpis['pending_leaves'],
                'icon' => 'ri-time-line',
                'tone' => 'danger',
                'link' => ['route' => 'admin.reports.leaves', 'text' => 'تقارير الإجازات'],
            ],
            [
                'label' => 'شواغر منشورة',
                'value' => $kpis['published_vacancies'],
                'icon' => 'ri-briefcase-line',
                'tone' => 'secondary',
                'link' => ['route' => 'admin.reports.recruitment', 'text' => 'تقارير التوظيف'],
            ],
            [
                'label' => 'المعينون (السنة الحالية)',
                'value' => $kpis['hired_this_year'],
                'icon' => 'ri-user-add-line',
                'tone' => 'teal',
            ],
            [
                'label' => 'سجلات تدريب مكتملة (السنة)',
                'value' => $kpis['training_completed'],
                'icon' => 'ri-award-line',
                'tone' => 'indigo',
                'link' => ['route' => 'admin.reports.training-effectiveness', 'text' => 'فعالية التدريب'],
            ],
            [
                'label' => 'مشاركون في التدريب حالياً',
                'value' => $kpis['training_participants'],
                'icon' => 'ri-graduation-cap-line',
                'tone' => 'purple',
                'link' => ['route' => 'admin.reports.training', 'text' => 'تقارير التدريب'],
            ],
        ];

        $quickLinks = [
            ['route' => 'admin.reports.turnover', 'label' => 'معدل دوران الموظفين', 'icon' => 'ri-exchange-line', 'tone' => 'warning'],
            ['route' => 'admin.reports.training-effectiveness', 'label' => 'فعالية التدريب', 'icon' => 'ri-award-line', 'tone' => 'success'],
            ['route' => 'admin.reports.dashboard', 'label' => 'التقرير الشامل', 'icon' => 'ri-dashboard-3-line', 'tone' => 'primary'],
        ];
    @endphp

    <div class="main-content app-content admin-reports-page">
        <div class="container-fluid pt-4">

            <div class="card page-hero mb-4">
                <div class="card-body py-4">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="page-hero-icon">
                                <i class="ri-bar-chart-grouped-line"></i>
                            </div>
                            <div>
                                <h4 class="mb-1 page-hero-title fw-bold">لوحة مؤشرات الإدارة العليا</h4>
                                <p class="mb-0 page-hero-subtitle">مؤشرات رئيسية للموارد البشرية والتشغيل</p>
                            </div>
                        </div>
                        <a href="{{ route('admin.reports.index') }}" class="btn btn-hero-outline btn-sm">
                            <i class="ri-arrow-right-line me-1"></i>عودة للتقارير
                        </a>
                    </div>
                </div>
            </div>

            <div class="row g-3 mb-4">
                @foreach ($kpiCards as $card)
                    <div class="col-sm-6 col-xl-4">
                        <div class="kpi-card kpi-card--{{ $card['tone'] }}">
                            <div class="kpi-card-top">
                                <span class="kpi-card-label">{{ $card['label'] }}</span>
                                <span class="kpi-card-icon"><i class="{{ $card['icon'] }}"></i></span>
                            </div>
                            <div class="kpi-card-value">{{ $card['value'] }}</div>
                            @if (!empty($card['link']))
                                <div class="kpi-card-footer">
                                    <a href="{{ route($card['link']['route']) }}" class="kpi-card-link">
                                        {{ $card['link']['text'] }}
                                        <i class="ri-arrow-left-s-line"></i>
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="content-panel">
                <div class="content-panel-header">روابط سريعة</div>
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
