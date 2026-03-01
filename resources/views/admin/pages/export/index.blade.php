@extends('admin.layouts.master')

@section('page-title')
    تصدير البيانات
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">تصدير البيانات إلى Excel</h5>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">اختر الجدول للتصدير</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <!-- إدارة الموارد البشرية -->
                                <div class="col-md-6 col-lg-4">
                                    <div class="card border">
                                        <div class="card-body">
                                            <h6 class="card-title text-primary mb-3">إدارة الموارد البشرية</h6>
                                            <div class="d-grid gap-2">
                                                @can('export-data')
                                                <a href="{{ route('admin.export.employees') }}" class="btn btn-outline-success btn-sm">
                                                    <i class="fe fe-download"></i> الموظفين
                                                </a>
                                                <a href="{{ route('admin.export.departments') }}" class="btn btn-outline-success btn-sm">
                                                    <i class="fe fe-download"></i> الأقسام
                                                </a>
                                                <a href="{{ route('admin.export.branches') }}" class="btn btn-outline-success btn-sm">
                                                    <i class="fe fe-download"></i> الفروع
                                                </a>
                                                <a href="{{ route('admin.export.positions') }}" class="btn btn-outline-success btn-sm">
                                                    <i class="fe fe-download"></i> المناصب
                                                </a>
                                                @endcan
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- الرواتب والرواتب -->
                                <div class="col-md-6 col-lg-4">
                                    <div class="card border">
                                        <div class="card-body">
                                            <h6 class="card-title text-primary mb-3">الرواتب والرواتب</h6>
                                            <div class="d-grid gap-2">
                                                @can('export-data')
                                                <a href="{{ route('admin.export.salaries') }}" class="btn btn-outline-success btn-sm">
                                                    <i class="fe fe-download"></i> الرواتب
                                                </a>
                                                <a href="{{ route('admin.export.payrolls') }}" class="btn btn-outline-success btn-sm">
                                                    <i class="fe fe-download"></i> كشوف الرواتب
                                                </a>
                                                <a href="{{ route('admin.export.payroll-payments') }}" class="btn btn-outline-success btn-sm">
                                                    <i class="fe fe-download"></i> سجلات الدفع
                                                </a>
                                                <a href="{{ route('admin.export.salary-components') }}" class="btn btn-outline-success btn-sm">
                                                    <i class="fe fe-download"></i> مكونات الراتب
                                                </a>
                                                <a href="{{ route('admin.export.tax-settings') }}" class="btn btn-outline-success btn-sm">
                                                    <i class="fe fe-download"></i> إعدادات الضرائب
                                                </a>
                                                <a href="{{ route('admin.export.bank-accounts') }}" class="btn btn-outline-success btn-sm">
                                                    <i class="fe fe-download"></i> الحسابات البنكية
                                                </a>
                                                @endcan
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- الإجازات -->
                                <div class="col-md-6 col-lg-4">
                                    <div class="card border">
                                        <div class="card-body">
                                            <h6 class="card-title text-primary mb-3">الإجازات</h6>
                                            <div class="d-grid gap-2">
                                                @can('export-data')
                                                <a href="{{ route('admin.export.leave-types') }}" class="btn btn-outline-success btn-sm">
                                                    <i class="fe fe-download"></i> أنواع الإجازات
                                                </a>
                                                <a href="{{ route('admin.export.leave-requests') }}" class="btn btn-outline-success btn-sm">
                                                    <i class="fe fe-download"></i> طلبات الإجازات
                                                </a>
                                                <a href="{{ route('admin.export.leave-balances') }}" class="btn btn-outline-success btn-sm">
                                                    <i class="fe fe-download"></i> أرصدة الإجازات
                                                </a>
                                                @endcan
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- الحضور -->
                                <div class="col-md-6 col-lg-4">
                                    <div class="card border">
                                        <div class="card-body">
                                            <h6 class="card-title text-primary mb-3">الحضور</h6>
                                            <div class="d-grid gap-2">
                                                @can('export-data')
                                                <a href="{{ route('admin.export.attendances') }}" class="btn btn-outline-success btn-sm">
                                                    <i class="fe fe-download"></i> سجلات الحضور
                                                </a>
                                                <a href="{{ route('admin.export.shifts') }}" class="btn btn-outline-success btn-sm">
                                                    <i class="fe fe-download"></i> المناوبات
                                                </a>
                                                <a href="{{ route('admin.export.overtimes') }}" class="btn btn-outline-success btn-sm">
                                                    <i class="fe fe-download"></i> الساعات الإضافية
                                                </a>
                                                @endcan
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- التدريب والتقييم -->
                                <div class="col-md-6 col-lg-4">
                                    <div class="card border">
                                        <div class="card-body">
                                            <h6 class="card-title text-primary mb-3">التدريب والتقييم</h6>
                                            <div class="d-grid gap-2">
                                                @can('export-data')
                                                <a href="{{ route('admin.export.trainings') }}" class="btn btn-outline-success btn-sm">
                                                    <i class="fe fe-download"></i> التدريبات
                                                </a>
                                                <a href="{{ route('admin.export.training-records') }}" class="btn btn-outline-success btn-sm">
                                                    <i class="fe fe-download"></i> سجلات التدريب
                                                </a>
                                                <a href="{{ route('admin.export.performance-reviews') }}" class="btn btn-outline-success btn-sm">
                                                    <i class="fe fe-download"></i> التقييمات
                                                </a>
                                                @endcan
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- التوظيف -->
                                <div class="col-md-6 col-lg-4">
                                    <div class="card border">
                                        <div class="card-body">
                                            <h6 class="card-title text-primary mb-3">التوظيف</h6>
                                            <div class="d-grid gap-2">
                                                @can('export-data')
                                                <a href="{{ route('admin.export.job-vacancies') }}" class="btn btn-outline-success btn-sm">
                                                    <i class="fe fe-download"></i> الوظائف الشاغرة
                                                </a>
                                                <a href="{{ route('admin.export.candidates') }}" class="btn btn-outline-success btn-sm">
                                                    <i class="fe fe-download"></i> المرشحين
                                                </a>
                                                <a href="{{ route('admin.export.job-applications') }}" class="btn btn-outline-success btn-sm">
                                                    <i class="fe fe-download"></i> طلبات التوظيف
                                                </a>
                                                <a href="{{ route('admin.export.interviews') }}" class="btn btn-outline-success btn-sm">
                                                    <i class="fe fe-download"></i> المقابلات
                                                </a>
                                                @endcan
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- المزايا والتعويضات -->
                                <div class="col-md-6 col-lg-4">
                                    <div class="card border">
                                        <div class="card-body">
                                            <h6 class="card-title text-primary mb-3">المزايا والتعويضات</h6>
                                            <div class="d-grid gap-2">
                                                @can('export-data')
                                                <a href="{{ route('admin.export.benefit-types') }}" class="btn btn-outline-success btn-sm">
                                                    <i class="fe fe-download"></i> أنواع المزايا
                                                </a>
                                                <a href="{{ route('admin.export.employee-benefits') }}" class="btn btn-outline-success btn-sm">
                                                    <i class="fe fe-download"></i> مزايا الموظفين
                                                </a>
                                                @endcan
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- المصروفات -->
                                <div class="col-md-6 col-lg-4">
                                    <div class="card border">
                                        <div class="card-body">
                                            <h6 class="card-title text-primary mb-3">المصروفات</h6>
                                            <div class="d-grid gap-2">
                                                @can('export-data')
                                                <a href="{{ route('admin.export.expense-categories') }}" class="btn btn-outline-success btn-sm">
                                                    <i class="fe fe-download"></i> تصنيفات المصروفات
                                                </a>
                                                <a href="{{ route('admin.export.expense-requests') }}" class="btn btn-outline-success btn-sm">
                                                    <i class="fe fe-download"></i> طلبات المصروفات
                                                </a>
                                                @endcan
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- الأصول -->
                                <div class="col-md-6 col-lg-4">
                                    <div class="card border">
                                        <div class="card-body">
                                            <h6 class="card-title text-primary mb-3">الأصول</h6>
                                            <div class="d-grid gap-2">
                                                @can('export-data')
                                                <a href="{{ route('admin.export.assets') }}" class="btn btn-outline-success btn-sm">
                                                    <i class="fe fe-download"></i> الأصول
                                                </a>
                                                <a href="{{ route('admin.export.asset-assignments') }}" class="btn btn-outline-success btn-sm">
                                                    <i class="fe fe-download"></i> توزيعات الأصول
                                                </a>
                                                <a href="{{ route('admin.export.asset-maintenances') }}" class="btn btn-outline-success btn-sm">
                                                    <i class="fe fe-download"></i> صيانة الأصول
                                                </a>
                                                @endcan
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- الإجراءات التأديبية -->
                                <div class="col-md-6 col-lg-4">
                                    <div class="card border">
                                        <div class="card-body">
                                            <h6 class="card-title text-primary mb-3">الإجراءات التأديبية</h6>
                                            <div class="d-grid gap-2">
                                                @can('export-data')
                                                <a href="{{ route('admin.export.violation-types') }}" class="btn btn-outline-success btn-sm">
                                                    <i class="fe fe-download"></i> أنواع المخالفات
                                                </a>
                                                <a href="{{ route('admin.export.disciplinary-actions') }}" class="btn btn-outline-success btn-sm">
                                                    <i class="fe fe-download"></i> الإجراءات التأديبية
                                                </a>
                                                <a href="{{ route('admin.export.employee-violations') }}" class="btn btn-outline-success btn-sm">
                                                    <i class="fe fe-download"></i> مخالفات الموظفين
                                                </a>
                                                @endcan
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- المهام والمشاريع -->
                                <div class="col-md-6 col-lg-4">
                                    <div class="card border">
                                        <div class="card-body">
                                            <h6 class="card-title text-primary mb-3">المهام والمشاريع</h6>
                                            <div class="d-grid gap-2">
                                                @can('export-data')
                                                <a href="{{ route('admin.export.projects') }}" class="btn btn-outline-success btn-sm">
                                                    <i class="fe fe-download"></i> المشاريع
                                                </a>
                                                <a href="{{ route('admin.export.tasks') }}" class="btn btn-outline-success btn-sm">
                                                    <i class="fe fe-download"></i> المهام
                                                </a>
                                                @endcan
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- أخرى -->
                                <div class="col-md-6 col-lg-4">
                                    <div class="card border">
                                        <div class="card-body">
                                            <h6 class="card-title text-primary mb-3">أخرى</h6>
                                            <div class="d-grid gap-2">
                                                @can('export-data')
                                                <a href="{{ route('admin.export.tickets') }}" class="btn btn-outline-success btn-sm">
                                                    <i class="fe fe-download"></i> التذاكر
                                                </a>
                                                <a href="{{ route('admin.export.meetings') }}" class="btn btn-outline-success btn-sm">
                                                    <i class="fe fe-download"></i> الاجتماعات
                                                </a>
                                                <a href="{{ route('admin.export.calendar-events') }}" class="btn btn-outline-success btn-sm">
                                                    <i class="fe fe-download"></i> أحداث التقويم
                                                </a>
                                                @endcan
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

