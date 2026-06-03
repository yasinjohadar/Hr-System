@extends('admin.layouts.master')

@section('page-title')
    التقارير
@stop

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin-reports.css') }}">
@endpush

@section('content')
    @php
        $reportCards = [
            [
                'route' => 'admin.reports.employees',
                'title' => 'تقارير الموظفين',
                'desc' => 'تقارير تفصيلية عن الموظفين والأقسام',
                'icon' => 'ri-team-line',
                'tone' => 'primary',
            ],
            [
                'route' => 'admin.reports.attendance',
                'title' => 'تقارير الحضور',
                'desc' => 'تقارير الحضور والانصراف والتأخير',
                'icon' => 'ri-calendar-check-line',
                'tone' => 'success',
            ],
            [
                'route' => 'admin.reports.salaries',
                'title' => 'تقارير الرواتب',
                'desc' => 'تقارير الرواتب والمدفوعات',
                'icon' => 'ri-wallet-3-line',
                'tone' => 'warning',
            ],
            [
                'route' => 'admin.reports.leaves',
                'title' => 'تقارير الإجازات',
                'desc' => 'تقارير الإجازات والأرصدة',
                'icon' => 'ri-sun-line',
                'tone' => 'info',
            ],
            [
                'route' => 'admin.reports.performance',
                'title' => 'تقارير التقييمات',
                'desc' => 'تقارير أداء الموظفين',
                'icon' => 'ri-star-line',
                'tone' => 'purple',
            ],
            [
                'route' => 'admin.reports.training',
                'title' => 'تقارير التدريب',
                'desc' => 'تقارير الدورات التدريبية',
                'icon' => 'ri-graduation-cap-line',
                'tone' => 'secondary',
            ],
            [
                'route' => 'admin.reports.recruitment',
                'title' => 'تقارير التوظيف',
                'desc' => 'تقارير الوظائف والمرشحين',
                'icon' => 'ri-briefcase-line',
                'tone' => 'danger',
            ],
            [
                'route' => 'admin.reports.benefits',
                'title' => 'تقارير المزايا',
                'desc' => 'تقارير المزايا والتعويضات',
                'icon' => 'ri-gift-line',
                'tone' => 'teal',
            ],
            [
                'route' => 'admin.reports.turnover',
                'title' => 'معدل دوران الموظفين',
                'desc' => 'نسبة المنتهية خدمتهم لفترة محددة',
                'icon' => 'ri-exchange-line',
                'tone' => 'orange',
            ],
            [
                'route' => 'admin.reports.training-effectiveness',
                'title' => 'فعالية التدريب',
                'desc' => 'معدل الإكمال والموظفين المدربين',
                'icon' => 'ri-award-line',
                'tone' => 'cyan',
            ],
        ];
    @endphp

    <div class="main-content app-content admin-reports-page">
        <div class="container-fluid pt-4">

            <div class="card page-hero mb-4">
                <div class="card-body py-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="page-hero-icon">
                            <i class="ri-pie-chart-2-line"></i>
                        </div>
                        <div>
                            <h4 class="mb-1 page-hero-title fw-bold">التقارير الشاملة</h4>
                            <p class="mb-0 page-hero-subtitle">اختر نوع التقرير لعرض البيانات والتحليلات</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-3 mb-4">
                @foreach ($reportCards as $card)
                    <div class="col-sm-6 col-xl-4 col-xxl-3">
                        <a href="{{ route($card['route']) }}" class="report-card report-card--{{ $card['tone'] }}">
                            <div class="report-card-icon">
                                <i class="{{ $card['icon'] }}"></i>
                            </div>
                            <div class="report-card-title">{{ $card['title'] }}</div>
                            <p class="report-card-desc mb-0">{{ $card['desc'] }}</p>
                            <span class="report-card-link">
                                عرض التقرير
                                <i class="ri-arrow-left-line"></i>
                            </span>
                        </a>
                    </div>
                @endforeach
            </div>

            <div class="report-featured">
                <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="report-featured-icon">
                            <i class="ri-dashboard-3-line"></i>
                        </div>
                        <div>
                            <h4 class="report-featured-title">التقرير الشامل</h4>
                            <p class="report-featured-desc">نظرة شاملة على جميع جوانب النظام والمؤشرات الرئيسية</p>
                        </div>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('admin.reports.dashboard') }}" class="btn btn-featured-primary">
                            <i class="ri-dashboard-line me-1"></i>عرض التقرير الشامل
                        </a>
                        <a href="{{ route('admin.reports.kpis') }}" class="btn btn-featured-outline">
                            <i class="ri-bar-chart-grouped-line me-1"></i>لوحة المؤشرات الإدارية
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
@stop
