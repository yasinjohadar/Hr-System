@extends('admin.layouts.master')

@section('page-title')
    تقرير الإجازات
@stop

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin-reports.css') }}">
@endpush

@section('content')
    <div class="main-content app-content admin-reports-page">
        <div class="container-fluid pt-4">

            <div class="card page-hero mb-4">
                <div class="card-body py-4">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="page-hero-icon">
                                <i class="ri-sun-line"></i>
                            </div>
                            <div>
                                <h4 class="mb-1 page-hero-title fw-bold">تقرير الإجازات</h4>
                                <p class="mb-0 page-hero-subtitle">تحليل طلبات الإجازات حسب الموظف والفترة</p>
                            </div>
                        </div>
                        <a href="{{ route('admin.reports.index') }}" class="btn btn-hero-outline btn-sm">
                            <i class="ri-arrow-right-line me-1"></i>العودة للتقارير
                        </a>
                    </div>
                </div>
            </div>

            <form method="GET" action="{{ route('admin.reports.leaves') }}" class="filters-panel">
                <div class="row g-3 align-items-end">
                    <div class="col-md-6 col-lg-3">
                        <label class="form-label">الموظف</label>
                        <select name="employee_id" class="form-select">
                            <option value="">كل الموظفين</option>
                            @foreach ($employees as $emp)
                                <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>
                                    {{ $emp->full_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <label class="form-label">نوع الإجازة</label>
                        <select name="leave_type_id" class="form-select">
                            <option value="">كل الأنواع</option>
                            @foreach ($leaveTypes as $type)
                                <option value="{{ $type->id }}" {{ request('leave_type_id') == $type->id ? 'selected' : '' }}>
                                    {{ $type->name_ar ?? $type->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6 col-lg-2">
                        <label class="form-label">من تاريخ</label>
                        <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-6 col-lg-2">
                        <label class="form-label">إلى تاريخ</label>
                        <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                    </div>
                    <div class="col-6 col-lg-auto">
                        <label class="form-label d-block">&nbsp;</label>
                        <button type="submit" class="btn btn-filter-submit">تطبيق</button>
                    </div>
                    <div class="col-6 col-lg-auto">
                        <label class="form-label d-block">&nbsp;</label>
                        <a href="{{ route('admin.reports.leaves') }}" class="btn btn-filter-clear">مسح</a>
                    </div>
                </div>
            </form>

            <div class="row g-3 mb-4">
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-value">{{ $stats['total_requests'] }}</div>
                                <div class="stat-label">إجمالي الطلبات</div>
                            </div>
                            <div class="stat-icon stat-icon--primary"><i class="ri-file-list-3-line"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-value">{{ $stats['approved'] }}</div>
                                <div class="stat-label">مقبولة</div>
                            </div>
                            <div class="stat-icon stat-icon--success"><i class="ri-checkbox-circle-line"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-value">{{ $stats['pending'] }}</div>
                                <div class="stat-label">قيد الانتظار</div>
                            </div>
                            <div class="stat-icon stat-icon--warning"><i class="ri-time-line"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-value">{{ $stats['total_days'] }}</div>
                                <div class="stat-label">إجمالي الأيام</div>
                            </div>
                            <div class="stat-icon stat-icon--info"><i class="ri-calendar-line"></i></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="content-panel">
                <div class="content-panel-header">طلبات الإجازات ({{ $leaveRequests->count() }})</div>

                @if ($leaveRequests->isNotEmpty())
                    <div class="report-table-scroll">
                        <div class="report-table-header">
                            <span>#</span>
                            <span>الموظف</span>
                            <span>نوع الإجازة</span>
                            <span>من تاريخ</span>
                            <span>إلى تاريخ</span>
                            <span>الأيام</span>
                            <span>الحالة</span>
                        </div>

                        @foreach ($leaveRequests as $leave)
                            <div class="report-table-row">
                                <span class="row-index">{{ $loop->iteration }}</span>

                                <div class="report-mobile-field">
                                    <span class="report-mobile-label">الموظف</span>
                                    <span class="cell-name">{{ $leave->employee->full_name }}</span>
                                </div>

                                <div class="report-mobile-field">
                                    <span class="report-mobile-label">نوع الإجازة</span>
                                    <span class="type-pill">{{ $leave->leaveType->name_ar ?? $leave->leaveType->name }}</span>
                                </div>

                                <div class="report-mobile-field">
                                    <span class="report-mobile-label">من تاريخ</span>
                                    <span>{{ $leave->start_date->format('Y/m/d') }}</span>
                                </div>

                                <div class="report-mobile-field">
                                    <span class="report-mobile-label">إلى تاريخ</span>
                                    <span>{{ $leave->end_date->format('Y/m/d') }}</span>
                                </div>

                                <div class="report-mobile-field">
                                    <span class="report-mobile-label">عدد الأيام</span>
                                    <span class="days-pill">{{ $leave->days_count }} يوم</span>
                                </div>

                                <div class="report-mobile-field">
                                    <span class="report-mobile-label">الحالة</span>
                                    <span class="status-pill status-pill--{{ $leave->status }}">{{ $leave->status_name_ar }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state">لا توجد طلبات إجازة للفلاتر المحددة</div>
                @endif
            </div>

        </div>
    </div>
@stop
