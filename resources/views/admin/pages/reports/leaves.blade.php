@extends('admin.layouts.master')

@section('page-title')
    تقرير الإجازات
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">تقرير الإجازات</h5>
                </div>
                <div>
                    <a href="{{ route('admin.reports.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-right me-2"></i>العودة للتقارير
                    </a>
                </div>
            </div>

            <!-- فلترة -->
            <div class="card mb-3">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.reports.leaves') }}" class="row g-3">
                        <div class="col-md-3">
                            <select name="employee_id" class="form-select">
                                <option value="">كل الموظفين</option>
                                @foreach ($employees as $emp)
                                    <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>
                                        {{ $emp->full_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="leave_type_id" class="form-select">
                                <option value="">كل الأنواع</option>
                                @foreach ($leaveTypes as $type)
                                    <option value="{{ $type->id }}" {{ request('leave_type_id') == $type->id ? 'selected' : '' }}>
                                        {{ $type->name_ar ?? $type->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                        </div>
                        <div class="col-md-2">
                            <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">تطبيق</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- الإحصائيات -->
            <div class="row mb-3">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h6 class="card-title">إجمالي الطلبات</h6>
                            <h2 class="mb-0">{{ $stats['total_requests'] }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h6 class="card-title">مقبولة</h6>
                            <h2 class="mb-0">{{ $stats['approved'] }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <h6 class="card-title">قيد الانتظار</h6>
                            <h2 class="mb-0">{{ $stats['pending'] }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <h6 class="card-title">إجمالي الأيام</h6>
                            <h2 class="mb-0">{{ $stats['total_days'] }}</h2>
                        </div>
                    </div>
                </div>
            </div>

            <!-- جدول الإجازات -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">طلبات الإجازات ({{ $leaveRequests->count() }})</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>الموظف</th>
                                    <th>نوع الإجازة</th>
                                    <th>من تاريخ</th>
                                    <th>إلى تاريخ</th>
                                    <th>عدد الأيام</th>
                                    <th>الحالة</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($leaveRequests as $request)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td><strong>{{ $request->employee->full_name }}</strong></td>
                                        <td>{{ $request->leaveType->name_ar ?? $request->leaveType->name }}</td>
                                        <td>{{ $request->start_date->format('Y-m-d') }}</td>
                                        <td>{{ $request->end_date->format('Y-m-d') }}</td>
                                        <td><span class="badge bg-info">{{ $request->number_of_days }} يوم</span></td>
                                        <td>
                                            <span class="badge bg-{{ $request->status == 'approved' ? 'success' : ($request->status == 'rejected' ? 'danger' : 'warning') }}">
                                                {{ $request->status_name_ar }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop


