@extends('admin.layouts.master')

@section('page-title')
    تفاصيل سجل الدفع
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="page-header d-flex justify-content-between align-items-center my-4">
                <h5 class="page-title mb-0">تفاصيل سجل الدفع</h5>
                <div>
                    @can('payroll-payment-edit')
                    @if($payment->status !== 'completed')
                    <a href="{{ route('admin.payroll-payments.edit', $payment->id) }}" class="btn btn-primary btn-sm">تعديل</a>
                    @endif
                    @endcan
                    <a href="{{ route('admin.payroll-payments.index') }}" class="btn btn-secondary btn-sm">رجوع</a>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">معلومات سجل الدفع</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">كود الدفع:</label>
                                    <p>{{ $payment->payment_code }}</p>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold">الموظف:</label>
                                    <p>{{ $payment->payroll->employee->full_name }}</p>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold">كشف الراتب:</label>
                                    <p>{{ $payment->payroll->payroll_code }}</p>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold">المبلغ:</label>
                                    <p>{{ number_format($payment->amount, 2) }} {{ $payment->currency->code ?? '' }}</p>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold">طريقة الدفع:</label>
                                    <p>
                                        <span class="badge bg-info">{{ $payment->payment_method_name_ar }}</span>
                                    </p>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold">تاريخ الدفع:</label>
                                    <p>{{ $payment->payment_date->format('Y-m-d') }}</p>
                                </div>

                                @if($payment->reference_number)
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">رقم المرجع:</label>
                                    <p>{{ $payment->reference_number }}</p>
                                </div>
                                @endif

                                @if($payment->bankAccount)
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">الحساب البنكي:</label>
                                    <p>{{ $payment->bankAccount->bank_name }} - {{ $payment->bankAccount->account_number }}</p>
                                </div>
                                @endif

                                <div class="col-md-6">
                                    <label class="form-label fw-bold">الحالة:</label>
                                    <p>
                                        <span class="badge bg-{{ match($payment->status) {
                                            'completed' => 'success',
                                            'processing' => 'warning',
                                            'pending' => 'info',
                                            'failed' => 'danger',
                                            'cancelled' => 'secondary',
                                            default => 'secondary'
                                        } }}">
                                            {{ $payment->status_name_ar }}
                                        </span>
                                    </p>
                                </div>

                                @if($payment->processed_at)
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">تاريخ المعالجة:</label>
                                    <p>{{ $payment->processed_at->format('Y-m-d H:i') }}</p>
                                </div>
                                @endif

                                @if($payment->processedBy)
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">معالج بواسطة:</label>
                                    <p>{{ $payment->processedBy->name }}</p>
                                </div>
                                @endif

                                @if($payment->failure_reason)
                                <div class="col-md-12">
                                    <label class="form-label fw-bold">سبب الفشل:</label>
                                    <p class="text-danger">{{ $payment->failure_reason }}</p>
                                </div>
                                @endif

                                @if($payment->payment_notes)
                                <div class="col-md-12">
                                    <label class="form-label fw-bold">ملاحظات:</label>
                                    <p>{{ $payment->payment_notes }}</p>
                                </div>
                                @endif

                                @if($payment->creator)
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">أنشئ بواسطة:</label>
                                    <p>{{ $payment->creator->name }}</p>
                                </div>
                                @endif

                                <div class="col-md-6">
                                    <label class="form-label fw-bold">تاريخ الإنشاء:</label>
                                    <p>{{ $payment->created_at->format('Y-m-d H:i') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

