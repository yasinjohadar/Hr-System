@extends('admin.layouts.master')

@section('page-title')
    تفاصيل كشف الراتب
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="page-header d-flex justify-content-between align-items-center my-4">
                <h5 class="page-title mb-0">تفاصيل كشف الراتب - {{ $payroll->payroll_code }}</h5>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.payrolls.index') }}" class="btn btn-secondary btn-sm">رجوع</a>
                    @if($payroll->status == 'calculated' || $payroll->status == 'approved')
                    <a href="{{ route('admin.payrolls.payslip', $payroll->id) }}" class="btn btn-info btn-sm" target="_blank">طباعة كشف الراتب</a>
                    @endif
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">معلومات الموظف</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <strong>الموظف:</strong> {{ $payroll->employee->full_name }}
                                </div>
                                <div class="col-md-3">
                                    <strong>كود الموظف:</strong> {{ $payroll->employee->employee_code }}
                                </div>
                                <div class="col-md-3">
                                    <strong>الفترة:</strong> {{ $payroll->month_name }} / {{ $payroll->payroll_year }}
                                </div>
                                <div class="col-md-3">
                                    <strong>الحالة:</strong>
                                    <span class="badge bg-{{ match($payroll->status) {
                                        'draft' => 'secondary',
                                        'calculated' => 'info',
                                        'approved' => 'warning',
                                        'paid' => 'success',
                                        default => 'secondary'
                                    } }}">
                                        {{ $payroll->status_name_ar }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">تفاصيل الراتب</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>البند</th>
                                        <th>القيمة</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><strong>الراتب الأساسي</strong></td>
                                        <td>{{ number_format($payroll->base_salary, 2) }} {{ $payroll->currency->code ?? '' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>إجمالي البدلات</strong></td>
                                        <td class="text-success">+ {{ number_format($payroll->total_allowances, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>المكافآت</strong></td>
                                        <td class="text-success">+ {{ number_format($payroll->bonuses, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>الساعات الإضافية</strong></td>
                                        <td class="text-success">+ {{ number_format($payroll->overtime_amount, 2) }} ({{ number_format($payroll->overtime_hours, 2) }} ساعة)</td>
                                    </tr>
                                    <tr>
                                        <td><strong>إجمالي الخصومات</strong></td>
                                        <td class="text-danger">- {{ number_format($payroll->total_deductions, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>خصم الإجازات</strong></td>
                                        <td class="text-danger">- {{ number_format($payroll->leave_deduction, 2) }} ({{ $payroll->leave_days }} يوم)</td>
                                    </tr>
                                    <tr>
                                        <td><strong>خصم التأخير</strong></td>
                                        <td class="text-danger">- {{ number_format($payroll->late_deduction, 2) }} ({{ $payroll->late_days }} يوم)</td>
                                    </tr>
                                    <tr class="table-primary">
                                        <td><strong>الراتب الإجمالي</strong></td>
                                        <td><strong>{{ number_format($payroll->gross_salary, 2) }}</strong></td>
                                    </tr>
                                    <tr class="table-success">
                                        <td><strong>الراتب الصافي</strong></td>
                                        <td><strong>{{ number_format($payroll->net_salary, 2) }} {{ $payroll->currency->code ?? '' }}</strong></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    @if($payroll->items->count() > 0)
                    <div class="card mt-3">
                        <div class="card-header">
                            <h5 class="card-title mb-0">بنود الراتب</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>النوع</th>
                                        <th>اسم البند</th>
                                        <th>القيمة</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($payroll->items as $item)
                                        <tr>
                                            <td><span class="badge bg-{{ $item->item_type == 'allowance' ? 'success' : ($item->item_type == 'deduction' ? 'danger' : 'info') }}">{{ $item->item_type_name_ar }}</span></td>
                                            <td>{{ $item->item_name_ar ?? $item->item_name }}</td>
                                            <td>{{ number_format($item->amount, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">معلومات الحضور</h5>
                        </div>
                        <div class="card-body">
                            <p><strong>أيام العمل:</strong> {{ $payroll->working_days }}</p>
                            <p><strong>أيام الحضور:</strong> {{ $payroll->present_days }}</p>
                            <p><strong>أيام الغياب:</strong> {{ $payroll->absent_days }}</p>
                            <p><strong>أيام التأخير:</strong> {{ $payroll->late_days }}</p>
                        </div>
                    </div>

                    <div class="card mt-3">
                        <div class="card-header">
                            <h5 class="card-title mb-0">الإجراءات</h5>
                        </div>
                        <div class="card-body">
                            @if($payroll->status == 'draft')
                            <form action="{{ route('admin.payrolls.calculate', $payroll->id) }}" method="POST" class="mb-2">
                                @csrf
                                <button type="submit" class="btn btn-success w-100">حساب الراتب تلقائياً</button>
                            </form>
                            @endif

                            @if($payroll->status == 'calculated')
                            <form action="{{ route('admin.payrolls.approve', $payroll->id) }}" method="POST" class="mb-2">
                                @csrf
                                <button type="submit" class="btn btn-warning w-100">الموافقة على الراتب</button>
                            </form>
                            @endif

                            @can('payroll-edit')
                            @if($payroll->status != 'paid')
                            <a href="{{ route('admin.payrolls.edit', $payroll->id) }}" class="btn btn-primary w-100 mb-2">تعديل</a>
                            @endif
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

