@extends('admin.layouts.master')

@section('page-title')
    التقارير
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">التقارير الشاملة</h5>
                </div>
            </div>

            <div class="row">
                <!-- تقرير الموظفين -->
                <div class="col-xl-3 col-lg-6 col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="avatar avatar-md bg-primary text-white rounded">
                                        <i class="fas fa-users fa-lg"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1">تقارير الموظفين</h6>
                                    <p class="mb-0 text-muted">تقارير تفصيلية عن الموظفين</p>
                                </div>
                            </div>
                            <div class="mt-3">
                                <a href="{{ route('admin.reports.employees') }}" class="btn btn-primary btn-sm w-100">
                                    <i class="fas fa-chart-bar me-2"></i>عرض التقرير
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- تقرير الحضور -->
                <div class="col-xl-3 col-lg-6 col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="avatar avatar-md bg-success text-white rounded">
                                        <i class="fas fa-calendar-check fa-lg"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1">تقارير الحضور</h6>
                                    <p class="mb-0 text-muted">تقارير الحضور والانصراف</p>
                                </div>
                            </div>
                            <div class="mt-3">
                                <a href="{{ route('admin.reports.attendance') }}" class="btn btn-success btn-sm w-100">
                                    <i class="fas fa-chart-line me-2"></i>عرض التقرير
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- تقرير الرواتب -->
                <div class="col-xl-3 col-lg-6 col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="avatar avatar-md bg-warning text-white rounded">
                                        <i class="fas fa-money-bill-wave fa-lg"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1">تقارير الرواتب</h6>
                                    <p class="mb-0 text-muted">تقارير الرواتب والمدفوعات</p>
                                </div>
                            </div>
                            <div class="mt-3">
                                <a href="{{ route('admin.reports.salaries') }}" class="btn btn-warning btn-sm w-100">
                                    <i class="fas fa-chart-pie me-2"></i>عرض التقرير
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- تقرير الإجازات -->
                <div class="col-xl-3 col-lg-6 col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="avatar avatar-md bg-info text-white rounded">
                                        <i class="fas fa-calendar-times fa-lg"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1">تقارير الإجازات</h6>
                                    <p class="mb-0 text-muted">تقارير الإجازات والأرصدة</p>
                                </div>
                            </div>
                            <div class="mt-3">
                                <a href="{{ route('admin.reports.leaves') }}" class="btn btn-info btn-sm w-100">
                                    <i class="fas fa-chart-area me-2"></i>عرض التقرير
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- تقرير التقييمات -->
                <div class="col-xl-3 col-lg-6 col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="avatar avatar-md bg-purple text-white rounded">
                                        <i class="fas fa-star fa-lg"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1">تقارير التقييمات</h6>
                                    <p class="mb-0 text-muted">تقارير أداء الموظفين</p>
                                </div>
                            </div>
                            <div class="mt-3">
                                <a href="{{ route('admin.reports.performance') }}" class="btn btn-purple btn-sm w-100">
                                    <i class="fas fa-chart-bar me-2"></i>عرض التقرير
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- تقرير التدريب -->
                <div class="col-xl-3 col-lg-6 col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="avatar avatar-md bg-secondary text-white rounded">
                                        <i class="fas fa-graduation-cap fa-lg"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1">تقارير التدريب</h6>
                                    <p class="mb-0 text-muted">تقارير الدورات التدريبية</p>
                                </div>
                            </div>
                            <div class="mt-3">
                                <a href="{{ route('admin.reports.training') }}" class="btn btn-secondary btn-sm w-100">
                                    <i class="fas fa-chart-line me-2"></i>عرض التقرير
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- تقرير التوظيف -->
                <div class="col-xl-3 col-lg-6 col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="avatar avatar-md bg-danger text-white rounded">
                                        <i class="fas fa-briefcase fa-lg"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1">تقارير التوظيف</h6>
                                    <p class="mb-0 text-muted">تقارير الوظائف والمرشحين</p>
                                </div>
                            </div>
                            <div class="mt-3">
                                <a href="{{ route('admin.reports.recruitment') }}" class="btn btn-danger btn-sm w-100">
                                    <i class="fas fa-chart-pie me-2"></i>عرض التقرير
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- تقرير المزايا -->
                <div class="col-xl-3 col-lg-6 col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="avatar avatar-md bg-teal text-white rounded">
                                        <i class="fas fa-gift fa-lg"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1">تقارير المزايا</h6>
                                    <p class="mb-0 text-muted">تقارير المزايا والتعويضات</p>
                                </div>
                            </div>
                            <div class="mt-3">
                                <a href="{{ route('admin.reports.benefits') }}" class="btn btn-teal btn-sm w-100">
                                    <i class="fas fa-chart-bar me-2"></i>عرض التقرير
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- معدل دوران الموظفين -->
                <div class="col-xl-3 col-lg-6 col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="avatar avatar-md bg-orange text-white rounded">
                                        <i class="fas fa-exchange-alt fa-lg"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1">معدل دوران الموظفين</h6>
                                    <p class="mb-0 text-muted">نسبة المنتهية خدمتهم لفترة محددة</p>
                                </div>
                            </div>
                            <div class="mt-3">
                                <a href="{{ route('admin.reports.turnover') }}" class="btn btn-orange btn-sm w-100">
                                    <i class="fas fa-chart-line me-2"></i>عرض التقرير
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- فعالية التدريب -->
                <div class="col-xl-3 col-lg-6 col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="avatar avatar-md bg-cyan text-white rounded">
                                        <i class="fas fa-award fa-lg"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1">فعالية التدريب</h6>
                                    <p class="mb-0 text-muted">معدل الإكمال والموظفين المدربين</p>
                                </div>
                            </div>
                            <div class="mt-3">
                                <a href="{{ route('admin.reports.training-effectiveness') }}" class="btn btn-cyan btn-sm w-100">
                                    <i class="fas fa-chart-pie me-2"></i>عرض التقرير
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- التقرير الشامل / لوحة المؤشرات -->
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="avatar avatar-lg bg-primary text-white rounded">
                                        <i class="fas fa-tachometer-alt fa-2x"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h4 class="mb-1">التقرير الشامل</h4>
                                    <p class="mb-0 text-muted">نظرة شاملة على جميع جوانب النظام</p>
                                </div>
                            </div>
                            <div class="mt-3 d-flex gap-2 flex-wrap">
                                <a href="{{ route('admin.reports.dashboard') }}" class="btn btn-primary btn-lg">
                                    <i class="fas fa-dashboard me-2"></i>عرض التقرير الشامل
                                </a>
                                <a href="{{ route('admin.reports.kpis') }}" class="btn btn-outline-primary btn-lg">
                                    <i class="fas fa-chart-bar me-2"></i>لوحة المؤشرات الإدارية
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .avatar {
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .btn-purple {
            background-color: #6f42c1;
            border-color: #6f42c1;
            color: white;
        }
        .btn-teal {
            background-color: #20c997;
            border-color: #20c997;
            color: white;
        }
        .btn-orange {
            background-color: #fd7e14;
            border-color: #fd7e14;
            color: white;
        }
        .btn-cyan {
            background-color: #0dcaf0;
            border-color: #0dcaf0;
            color: white;
        }
    </style>
@stop


