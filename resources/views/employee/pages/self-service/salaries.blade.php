@extends('employee.layouts.master')

@section('page-title')
    الرواتب
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">كشف الرواتب</h5>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">سجلات الرواتب ({{ $salaries->total() }})</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
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
                                @forelse ($salaries as $salary)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $salary->salary_month }}/{{ $salary->salary_year }}</td>
                                        <td>{{ number_format($salary->base_salary, 2) }} {{ $salary->currency->symbol_ar ?? $salary->currency->symbol ?? 'ر.س' }}</td>
                                        <td>{{ number_format($salary->allowances, 2) }} {{ $salary->currency->symbol_ar ?? $salary->currency->symbol ?? 'ر.س' }}</td>
                                        <td>{{ number_format($salary->bonuses, 2) }} {{ $salary->currency->symbol_ar ?? $salary->currency->symbol ?? 'ر.س' }}</td>
                                        <td>{{ number_format($salary->deductions, 2) }} {{ $salary->currency->symbol_ar ?? $salary->currency->symbol ?? 'ر.س' }}</td>
                                        <td><strong>{{ number_format($salary->total_salary, 2) }} {{ $salary->currency->symbol_ar ?? $salary->currency->symbol ?? 'ر.س' }}</strong></td>
                                        <td>
                                            <span class="badge bg-{{ $salary->payment_status == 'paid' ? 'success' : 'warning' }}">
                                                {{ $salary->payment_status == 'paid' ? 'مدفوع' : 'قيد الانتظار' }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">لا توجد سجلات رواتب</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $salaries->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop


