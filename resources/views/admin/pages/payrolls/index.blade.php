@extends('admin.layouts.master')

@section('page-title')
    كشوف الرواتب
@stop

@section('content')
    @if (\Session::has('success'))
        <div class="alert alert-success">
            <ul>
                <li>{!! \Session::get('success') !!}</li>
            </ul>
        </div>
    @endif

    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">كشوف الرواتب</h5>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header align-items-center d-flex gap-3">
                            @can('payroll-create')
                            <a href="{{ route('admin.payrolls.create') }}" class="btn btn-primary btn-sm">إنشاء كشف راتب جديد</a>
                            @endcan
                            @can('payroll-list')
                            <a href="{{ route('admin.payrolls.export-bank-file') }}" class="btn btn-success btn-sm">تصدير للبنك</a>
                            @endcan

                            <div class="flex-shrink-0">
                                <form action="{{ route('admin.payrolls.index') }}" method="GET" class="d-flex align-items-center gap-2">
                                    <input type="text" name="search" class="form-control" placeholder="بحث..." value="{{ request('search') }}" style="width: 200px;">
                                    
                                    <select name="employee_id" class="form-select" style="width: 200px;">
                                        <option value="">كل الموظفين</option>
                                        @foreach ($employees as $employee)
                                            <option value="{{ $employee->id }}" {{ request('employee_id') == $employee->id ? 'selected' : '' }}>
                                                {{ $employee->full_name }}
                                            </option>
                                        @endforeach
                                    </select>

                                    <select name="status" class="form-select" style="width: 150px;">
                                        <option value="">كل الحالات</option>
                                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>مسودة</option>
                                        <option value="calculated" {{ request('status') == 'calculated' ? 'selected' : '' }}>محسوب</option>
                                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>موافق عليه</option>
                                        <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>مدفوع</option>
                                    </select>

                                    <select name="month" class="form-select" style="width: 120px;">
                                        <option value="">كل الأشهر</option>
                                        @for ($i = 1; $i <= 12; $i++)
                                            <option value="{{ $i }}" {{ request('month') == $i ? 'selected' : '' }}>
                                                {{ ['', 'يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'][$i] }}
                                            </option>
                                        @endfor
                                    </select>

                                    <input type="number" name="year" class="form-control" placeholder="السنة" value="{{ request('year') }}" style="width: 100px;">

                                    <button type="submit" class="btn btn-primary btn-sm">بحث</button>
                                    <a href="{{ route('admin.payrolls.index') }}" class="btn btn-secondary btn-sm">إعادة تعيين</a>
                                </form>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered text-nowrap border-bottom">
                                    <thead>
                                        <tr>
                                            <th>كود الكشف</th>
                                            <th>الموظف</th>
                                            <th>الشهر/السنة</th>
                                            <th>الراتب الأساسي</th>
                                            <th>البدلات</th>
                                            <th>الخصومات</th>
                                            <th>الراتب الصافي</th>
                                            <th>الحالة</th>
                                            <th>الإجراءات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($payrolls as $payroll)
                                            <tr>
                                                <td>{{ $payroll->payroll_code }}</td>
                                                <td>{{ $payroll->employee->full_name }}</td>
                                                <td>{{ $payroll->month_name }} / {{ $payroll->payroll_year }}</td>
                                                <td>{{ number_format($payroll->base_salary, 2) }} {{ $payroll->currency->code ?? '' }}</td>
                                                <td>{{ number_format($payroll->total_allowances, 2) }}</td>
                                                <td>{{ number_format($payroll->total_deductions, 2) }}</td>
                                                <td><strong>{{ number_format($payroll->net_salary, 2) }}</strong></td>
                                                <td>
                                                    <span class="badge bg-{{ match($payroll->status) {
                                                        'draft' => 'secondary',
                                                        'calculated' => 'info',
                                                        'approved' => 'warning',
                                                        'paid' => 'success',
                                                        'cancelled' => 'danger',
                                                        default => 'secondary'
                                                    } }}">
                                                        {{ $payroll->status_name_ar }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="d-flex gap-2">
                                                        @can('payroll-show')
                                                        <a href="{{ route('admin.payrolls.show', $payroll->id) }}" class="btn btn-sm btn-info">عرض</a>
                                                        @endcan
                                                        @can('payroll-edit')
                                                        @if($payroll->status != 'paid')
                                                        <a href="{{ route('admin.payrolls.edit', $payroll->id) }}" class="btn btn-sm btn-primary">تعديل</a>
                                                        @endif
                                                        @endcan
                                                        @if($payroll->status == 'draft')
                                                        <form action="{{ route('admin.payrolls.calculate', $payroll->id) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-success">حساب</button>
                                                        </form>
                                                        @endif
                                                        @if($payroll->status == 'calculated')
                                                        <form action="{{ route('admin.payrolls.approve', $payroll->id) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-warning">موافقة</button>
                                                        </form>
                                                        @endif
                                                        @can('payroll-show')
                                                        <a href="{{ route('admin.payrolls.payslip', $payroll->id) }}" class="btn btn-sm btn-secondary" target="_blank">كشف راتب</a>
                                                        @if(in_array($payroll->status, ['calculated', 'approved', 'paid']))
                                                        <a href="{{ route('admin.payrolls.payslip.pdf', $payroll->id) }}" class="btn btn-sm btn-danger" target="_blank">تحميل PDF</a>
                                                        @endif
                                                        @endcan
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="9" class="text-center">لا توجد كشوف رواتب</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <div class="d-flex justify-content-center mt-3">
                                {{ $payrolls->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

