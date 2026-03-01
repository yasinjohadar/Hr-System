@extends('admin.layouts.master')

@section('page-title')
    تقرير الرواتب
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">تقرير الرواتب</h5>
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
                    <form method="GET" action="{{ route('admin.reports.salaries') }}" class="row g-3">
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
                        <div class="col-md-2">
                            <select name="month" class="form-select">
                                <option value="">كل الأشهر</option>
                                @for ($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i }}" {{ request('month') == $i ? 'selected' : '' }}>
                                        {{ \Carbon\Carbon::create()->month($i)->locale('ar')->monthName }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="number" name="year" class="form-control" placeholder="السنة" value="{{ request('year', date('Y')) }}">
                        </div>
                        <div class="col-md-2">
                            <select name="payment_status" class="form-select">
                                <option value="">كل الحالات</option>
                                <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>مدفوع</option>
                                <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary w-100">تطبيق الفلترة</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- الإحصائيات -->
            <div class="row mb-3">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h6 class="card-title">إجمالي الرواتب</h6>
                            <h2 class="mb-0">{{ number_format($stats['total_salaries']) }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h6 class="card-title">إجمالي المبلغ</h6>
                            <h2 class="mb-0">{{ number_format($stats['total_amount'], 2) }} ر.س</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <h6 class="card-title">إجمالي البدلات</h6>
                            <h2 class="mb-0">{{ number_format($stats['total_allowances'], 2) }} ر.س</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <h6 class="card-title">إجمالي الخصومات</h6>
                            <h2 class="mb-0">{{ number_format($stats['total_deductions'], 2) }} ر.س</h2>
                        </div>
                    </div>
                </div>
            </div>

            <!-- جدول الرواتب -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">سجلات الرواتب ({{ $salaries->count() }})</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>الموظف</th>
                                    <th>الشهر/السنة</th>
                                    <th>الراتب الأساسي</th>
                                    <th>البدلات</th>
                                    <th>المكافآت</th>
                                    <th>الخصومات</th>
                                    <th>الإجمالي</th>
                                    <th>حالة الدفع</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($salaries as $salary)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td><strong>{{ $salary->employee->full_name }}</strong></td>
                                        <td>{{ $salary->salary_month }}/{{ $salary->salary_year }}</td>
                                        <td>{{ number_format($salary->base_salary, 2) }} ر.س</td>
                                        <td>{{ number_format($salary->allowances, 2) }} ر.س</td>
                                        <td>{{ number_format($salary->bonuses, 2) }} ر.س</td>
                                        <td>{{ number_format($salary->deductions, 2) }} ر.س</td>
                                        <td><strong>{{ number_format($salary->total_salary, 2) }} ر.س</strong></td>
                                        <td>
                                            <span class="badge bg-{{ $salary->payment_status == 'paid' ? 'success' : 'warning' }}">
                                                {{ $salary->payment_status == 'paid' ? 'مدفوع' : 'قيد الانتظار' }}
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


