@extends('admin.layouts.master')

@section('page-title')
    قائمة الرواتب
@stop

@section('css')
@stop

@section('content')
    @if (\Session::has('success'))
        <div class="alert alert-success">
            <ul>
                <li>{!! \Session::get('success') !!}</li>
            </ul>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">كافة الرواتب</h5>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header align-items-center d-flex gap-3">
                            @can('salary-create')
                            <a href="{{ route('admin.salaries.create') }}" class="btn btn-primary btn-sm">إضافة راتب جديد</a>
                            @endcan

                            <div class="flex-shrink-0">
                                <form action="{{ route('admin.salaries.index') }}" method="GET" class="d-flex align-items-center gap-2">
                                    <select name="employee_id" class="form-select" style="width: 200px;">
                                        <option value="">كل الموظفين</option>
                                        @foreach ($employees as $employee)
                                            <option value="{{ $employee->id }}" {{ request('employee_id') == $employee->id ? 'selected' : '' }}>
                                                {{ $employee->full_name ?? $employee->first_name . ' ' . $employee->last_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <select name="salary_month" class="form-select" style="width: 150px;">
                                        <option value="">كل الأشهر</option>
                                        @for ($i = 1; $i <= 12; $i++)
                                            <option value="{{ $i }}" {{ request('salary_month', $currentMonth) == $i ? 'selected' : '' }}>
                                                {{ ['', 'يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'][$i] }}
                                            </option>
                                        @endfor
                                    </select>
                                    <select name="salary_year" class="form-select" style="width: 120px;">
                                        @if ($years->isEmpty())
                                            <option value="{{ date('Y') }}" {{ request('salary_year', $currentYear) == date('Y') ? 'selected' : '' }}>{{ date('Y') }}</option>
                                        @else
                                            @foreach ($years as $year)
                                                <option value="{{ $year }}" {{ request('salary_year', $currentYear) == $year ? 'selected' : '' }}>{{ $year }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    <select name="payment_status" class="form-select" style="width: 150px;">
                                        <option value="">كل الحالات</option>
                                        <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                                        <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>مدفوع</option>
                                        <option value="cancelled" {{ request('payment_status') == 'cancelled' ? 'selected' : '' }}>ملغي</option>
                                    </select>
                                    <button type="submit" class="btn btn-secondary">بحث</button>
                                    <a href="{{ route('admin.salaries.index') }}" class="btn btn-danger">مسح</a>
                                </form>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover align-middle table-nowrap mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>الموظف</th>
                                            <th>الشهر</th>
                                            <th>الراتب الأساسي</th>
                                            <th>البدلات</th>
                                            <th>المكافآت</th>
                                            <th>الخصومات</th>
                                            <th>الإجمالي</th>
                                            <th>حالة الدفع</th>
                                            <th>تاريخ الدفع</th>
                                            <th>العمليات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($salaries as $salary)
                                            <tr>
                                                <th>{{ $loop->iteration }}</th>
                                                <td>
                                                    <strong>{{ $salary->employee->full_name ?? $salary->employee->first_name . ' ' . $salary->employee->last_name }}</strong>
                                                    <br><small class="text-muted">{{ $salary->employee->employee_code ?? '' }}</small>
                                                </td>
                                                <td>
                                                    {{ $salary->month_name }} {{ $salary->salary_year }}
                                                </td>
                                                <td>{{ number_format($salary->base_salary, 2) }} {{ $salary->currency->symbol_ar ?? $salary->currency->symbol ?? 'ر.س' }}</td>
                                                <td>{{ number_format($salary->allowances, 2) }}</td>
                                                <td>{{ number_format($salary->bonuses, 2) }}</td>
                                                <td class="text-danger">-{{ number_format($salary->deductions, 2) }}</td>
                                                <td>
                                                    <strong class="text-success">{{ number_format($salary->total_salary, 2) }}</strong>
                                                </td>
                                                <td>
                                                    @if ($salary->payment_status == 'paid')
                                                        <span class="badge bg-success">مدفوع</span>
                                                    @elseif ($salary->payment_status == 'pending')
                                                        <span class="badge bg-warning">قيد الانتظار</span>
                                                    @else
                                                        <span class="badge bg-danger">ملغي</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    {{ $salary->payment_date ? $salary->payment_date->format('Y-m-d') : '-' }}
                                                </td>
                                                <td>
                                                    @can('salary-show')
                                                    <a class="btn btn-info btn-sm me-1" href="{{ route('admin.salaries.show', $salary->id) }}" title="عرض">
                                                        <i class="fa-solid fa-eye"></i>
                                                    </a>
                                                    @endcan
                                                    @can('salary-edit')
                                                    <a class="btn btn-warning btn-sm me-1" href="{{ route('admin.salaries.edit', $salary->id) }}" title="تعديل">
                                                        <i class="fa-solid fa-pen-to-square"></i>
                                                    </a>
                                                    @endcan
                                                    @can('salary-delete')
                                                    <a class="btn btn-danger btn-sm me-1" data-bs-toggle="modal" data-bs-target="#delete{{ $salary->id }}" title="حذف">
                                                        <i class="fa-solid fa-trash-can"></i>
                                                    </a>
                                                    @endcan
                                                </td>
                                            </tr>
                                            @include('admin.pages.salaries.delete')
                                        @empty
                                            <tr>
                                                <td colspan="11" class="text-center text-danger fw-bold">لا توجد بيانات متاحة</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>

                                <div class="mt-3">
                                    {{ $salaries->withQueryString()->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
@stop

