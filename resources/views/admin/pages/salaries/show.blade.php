@extends('admin.layouts.master')

@section('page-title')
    تفاصيل الراتب
@stop

@section('css')
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">تفاصيل الراتب: {{ $salary->employee->full_name ?? $salary->employee->first_name . ' ' . $salary->employee->last_name }}</h5>
                </div>
                <div>
                    <a href="{{ route('admin.salaries.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-right me-2"></i>العودة للقائمة
                    </a>
                    @can('salary-edit')
                    <a href="{{ route('admin.salaries.edit', $salary->id) }}" class="btn btn-primary">
                        <i class="fas fa-edit me-2"></i>تعديل
                    </a>
                    @endcan
                </div>
            </div>

            <div class="row">
                <div class="col-xl-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">معلومات الراتب</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">الموظف:</label>
                                    <p class="form-control-plaintext">
                                        <strong>{{ $salary->employee->full_name ?? $salary->employee->first_name . ' ' . $salary->employee->last_name }}</strong>
                                        <br><small class="text-muted">{{ $salary->employee->employee_code ?? '' }}</small>
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">الفترة:</label>
                                    <p class="form-control-plaintext">
                                        {{ $salary->month_name }} {{ $salary->salary_year }}
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">الراتب الأساسي:</label>
                                    <p class="form-control-plaintext">
                                        <strong>{{ number_format($salary->base_salary, 2) }}</strong> 
                                        {{ $salary->currency->symbol_ar ?? $salary->currency->symbol ?? 'ر.س' }}
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">البدلات:</label>
                                    <p class="form-control-plaintext text-success">
                                        +{{ number_format($salary->allowances, 2) }}
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">المكافآت:</label>
                                    <p class="form-control-plaintext text-success">
                                        +{{ number_format($salary->bonuses, 2) }}
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">ساعات إضافية:</label>
                                    <p class="form-control-plaintext text-success">
                                        +{{ number_format($salary->overtime, 2) }}
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">الخصومات:</label>
                                    <p class="form-control-plaintext text-danger">
                                        -{{ number_format($salary->deductions, 2) }}
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">الراتب الإجمالي:</label>
                                    <p class="form-control-plaintext">
                                        <strong class="text-success fs-5">{{ number_format($salary->total_salary, 2) }}</strong>
                                        {{ $salary->currency->symbol_ar ?? $salary->currency->symbol ?? 'ر.س' }}
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">حالة الدفع:</label>
                                    <p class="form-control-plaintext">
                                        @if ($salary->payment_status == 'paid')
                                            <span class="badge bg-success">مدفوع</span>
                                        @elseif ($salary->payment_status == 'pending')
                                            <span class="badge bg-warning">قيد الانتظار</span>
                                        @else
                                            <span class="badge bg-danger">ملغي</span>
                                        @endif
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">تاريخ الدفع:</label>
                                    <p class="form-control-plaintext">
                                        {{ $salary->payment_date ? $salary->payment_date->format('Y-m-d') : '-' }}
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">العملة:</label>
                                    <p class="form-control-plaintext">
                                        {{ $salary->currency ? ($salary->currency->name_ar ?? $salary->currency->name) . ' (' . $salary->currency->code . ')' : '-' }}
                                    </p>
                                </div>
                                @if ($salary->notes)
                                <div class="col-12 mb-3">
                                    <label class="form-label fw-bold">ملاحظات:</label>
                                    <p class="form-control-plaintext">{{ $salary->notes }}</p>
                                </div>
                                @endif
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">تاريخ الإنشاء:</label>
                                    <p class="form-control-plaintext">
                                        {{ $salary->created_at->format('Y-m-d H:i') }}
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">أنشأ بواسطة:</label>
                                    <p class="form-control-plaintext">
                                        {{ $salary->creator->name ?? '-' }}
                                    </p>
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

