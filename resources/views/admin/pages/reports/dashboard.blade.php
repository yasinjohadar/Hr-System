@extends('admin.layouts.master')

@section('page-title')
    التقرير الشامل
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">التقرير الشامل - لوحة المعلومات</h5>
                </div>
                <div>
                    <a href="{{ route('admin.reports.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-right me-2"></i>العودة للتقارير
                    </a>
                </div>
            </div>

            <!-- إحصائيات عامة -->
            <div class="row mb-3">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h6 class="card-title">إجمالي الموظفين</h6>
                            <h2 class="mb-0">{{ $stats['total_employees'] }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h6 class="card-title">الأقسام</h6>
                            <h2 class="mb-0">{{ $stats['total_departments'] }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <h6 class="card-title">المناصب</h6>
                            <h2 class="mb-0">{{ $stats['total_positions'] }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <h6 class="card-title">الفروع</h6>
                            <h2 class="mb-0">{{ $stats['total_branches'] }}</h2>
                        </div>
                    </div>
                </div>
            </div>

            <!-- إحصائيات الحضور -->
            <div class="row mb-3">
                <div class="col-md-4">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h6 class="card-title">حضور اليوم</h6>
                            <h2 class="mb-0">{{ $stats['today_attendance'] }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-danger text-white">
                        <div class="card-body">
                            <h6 class="card-title">غياب اليوم</h6>
                            <h2 class="mb-0">{{ $stats['today_absent'] }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <h6 class="card-title">في إجازة حالياً</h6>
                            <h2 class="mb-0">{{ $stats['approved_leaves'] }}</h2>
                        </div>
                    </div>
                </div>
            </div>

            <!-- إحصائيات التوظيف والتدريب -->
            <div class="row mb-3">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h6 class="card-title">وظائف شاغرة</h6>
                            <h2 class="mb-0">{{ $stats['active_vacancies'] }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <h6 class="card-title">طلبات قيد المراجعة</h6>
                            <h2 class="mb-0">{{ $stats['pending_applications'] }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <h6 class="card-title">دورات قيد التنفيذ</h6>
                            <h2 class="mb-0">{{ $stats['ongoing_trainings'] }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-secondary text-white">
                        <div class="card-body">
                            <h6 class="card-title">مشاركون في التدريب</h6>
                            <h2 class="mb-0">{{ $stats['training_participants'] }}</h2>
                        </div>
                    </div>
                </div>
            </div>

            <!-- إحصائيات الرواتب الشهرية -->
            @if ($monthlySalaries)
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">إحصائيات رواتب الشهر الحالي</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <h6>إجمالي الرواتب</h6>
                            <h3 class="text-primary">{{ number_format($monthlySalaries->total, 2) }} ر.س</h3>
                        </div>
                        <div class="col-md-4">
                            <h6>إجمالي الأساسي</h6>
                            <h3 class="text-success">{{ number_format($monthlySalaries->base, 2) }} ر.س</h3>
                        </div>
                        <div class="col-md-4">
                            <h6>عدد الرواتب</h6>
                            <h3 class="text-info">{{ $monthlySalaries->count }}</h3>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- روابط سريعة للتقارير -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">روابط سريعة للتقارير التفصيلية</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-2">
                            <a href="{{ route('admin.reports.employees') }}" class="btn btn-outline-primary w-100">
                                <i class="fas fa-users me-2"></i>تقارير الموظفين
                            </a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="{{ route('admin.reports.attendance') }}" class="btn btn-outline-success w-100">
                                <i class="fas fa-calendar-check me-2"></i>تقارير الحضور
                            </a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="{{ route('admin.reports.salaries') }}" class="btn btn-outline-warning w-100">
                                <i class="fas fa-money-bill-wave me-2"></i>تقارير الرواتب
                            </a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="{{ route('admin.reports.leaves') }}" class="btn btn-outline-info w-100">
                                <i class="fas fa-calendar-times me-2"></i>تقارير الإجازات
                            </a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="{{ route('admin.reports.performance') }}" class="btn btn-outline-purple w-100">
                                <i class="fas fa-star me-2"></i>تقارير التقييمات
                            </a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="{{ route('admin.reports.training') }}" class="btn btn-outline-secondary w-100">
                                <i class="fas fa-graduation-cap me-2"></i>تقارير التدريب
                            </a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="{{ route('admin.reports.recruitment') }}" class="btn btn-outline-danger w-100">
                                <i class="fas fa-briefcase me-2"></i>تقارير التوظيف
                            </a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="{{ route('admin.reports.benefits') }}" class="btn btn-outline-teal w-100">
                                <i class="fas fa-gift me-2"></i>تقارير المزايا
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .btn-outline-purple {
            border-color: #6f42c1;
            color: #6f42c1;
        }
        .btn-outline-purple:hover {
            background-color: #6f42c1;
            color: white;
        }
        .btn-outline-teal {
            border-color: #20c997;
            color: #20c997;
        }
        .btn-outline-teal:hover {
            background-color: #20c997;
            color: white;
        }
    </style>
@stop


