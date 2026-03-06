@extends('admin.layouts.master')

@section('page-title')
    لوحة المؤشرات الإدارية
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">لوحة مؤشرات الإدارة العليا</h5>
                </div>
                <a href="{{ route('admin.reports.index') }}" class="btn btn-secondary btn-sm">عودة للتقارير</a>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h6 class="card-title">الموظفون النشطون</h6>
                            <h2 class="mb-0">{{ $kpis['active_employees'] }}</h2>
                            <a href="{{ route('admin.reports.employees') }}" class="text-white small">تفاصيل</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <h6 class="card-title">منتهية الخدمة (السنة الحالية)</h6>
                            <h2 class="mb-0">{{ $kpis['exits_this_year'] }}</h2>
                            <a href="{{ route('admin.reports.turnover') }}" class="text-white small">تقرير الدوران</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <h6 class="card-title">معدل الدوران %</h6>
                            <h2 class="mb-0">{{ $kpis['turnover_rate'] }}%</h2>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h6 class="card-title">إجمالي رواتب الشهر (معتمدة/مدفوعة)</h6>
                            <h2 class="mb-0">{{ number_format($kpis['payroll_total'], 0) }} ر.س</h2>
                            <a href="{{ route('admin.reports.salaries') }}" class="text-white small">تقارير الرواتب</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-danger text-white">
                        <div class="card-body">
                            <h6 class="card-title">طلبات إجازة معلقة</h6>
                            <h2 class="mb-0">{{ $kpis['pending_leaves'] }}</h2>
                            <a href="{{ route('admin.reports.leaves') }}" class="text-white small">تقارير الإجازات</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-secondary text-white">
                        <div class="card-body">
                            <h6 class="card-title">شواغر منشورة</h6>
                            <h2 class="mb-0">{{ $kpis['published_vacancies'] }}</h2>
                            <a href="{{ route('admin.reports.recruitment') }}" class="text-white small">تقارير التوظيف</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <div class="card bg-teal text-white">
                        <div class="card-body">
                            <h6 class="card-title">المعينون (السنة الحالية)</h6>
                            <h2 class="mb-0">{{ $kpis['hired_this_year'] }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-indigo text-white">
                        <div class="card-body">
                            <h6 class="card-title">سجلات تدريب مكتملة (السنة)</h6>
                            <h2 class="mb-0">{{ $kpis['training_completed'] }}</h2>
                            <a href="{{ route('admin.reports.training-effectiveness') }}" class="text-white small">فعالية التدريب</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-purple text-white">
                        <div class="card-body">
                            <h6 class="card-title">مشاركون في التدريب حالياً</h6>
                            <h2 class="mb-0">{{ $kpis['training_participants'] }}</h2>
                            <a href="{{ route('admin.reports.training') }}" class="text-white small">تقارير التدريب</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">روابط سريعة</h5>
                </div>
                <div class="card-body">
                    <a href="{{ route('admin.reports.turnover') }}" class="btn btn-outline-primary me-2">معدل دوران الموظفين</a>
                    <a href="{{ route('admin.reports.training-effectiveness') }}" class="btn btn-outline-success me-2">فعالية التدريب</a>
                    <a href="{{ route('admin.reports.dashboard') }}" class="btn btn-outline-secondary">التقرير الشامل</a>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .bg-teal { background-color: #20c997 !important; }
        .bg-indigo { background-color: #6610f2 !important; }
        .bg-purple { background-color: #6f42c1 !important; }
    </style>
@stop
