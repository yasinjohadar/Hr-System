@extends('admin.layouts.master')

@section('page-title')
    تفاصيل سجل الدفع
@stop

@section('css')
    <style>
        .payment-show-hero {
            background: linear-gradient(145deg, var(--bs-primary) 0%, rgba(var(--bs-primary-rgb), 0.88) 55%, rgb(15, 76, 129) 100%);
            color: #fff;
            border: none;
        }
        .payment-show-hero .text-white-75 { color: rgba(255,255,255,.85) !important; }
    </style>
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">تفاصيل سجل الدفع</h5>
                    <p class="text-muted small mb-0">{{ $payment->payment_code }} — {{ $payment->payroll->employee->full_name ?? '—' }}</p>
                </div>
                <div class="d-flex flex-wrap gap-2 mt-2 mt-md-0">
                    <a href="{{ route('admin.payroll-payments.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-right me-1"></i>العودة للقائمة
                    </a>
                    @can('payroll-payment-edit')
                        @if($payment->status !== 'completed')
                            <a href="{{ route('admin.payroll-payments.edit', $payment->id) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit me-1"></i>تعديل
                            </a>
                        @endif
                    @endcan
                </div>
            </div>

            <div class="row g-3">
                <div class="col-lg-4">
                    <div class="card payment-show-hero shadow-sm h-100">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex align-items-start gap-3 mb-3">
                                <span class="avatar avatar-md bg-white bg-opacity-25 rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width:3rem;height:3rem;">
                                    <i class="fas fa-credit-card fs-4"></i>
                                </span>
                                <div class="min-w-0">
                                    <div class="text-white-75 small mb-1">سجل الدفع</div>
                                    <div class="fw-semibold fs-6 text-truncate">{{ $payment->payment_code }}</div>
                                    <div class="small text-white-75">{{ $payment->payment_method_name_ar }}</div>
                                </div>
                            </div>
                            <div class="mb-3 pb-3 border-bottom border-white border-opacity-25">
                                <div class="text-white-75 small mb-1"><i class="fas fa-user me-1"></i>الموظف</div>
                                <div class="fs-5 fw-semibold">{{ $payment->payroll->employee->full_name ?? '—' }}</div>
                                <div class="small text-white-75 font-monospace">{{ $payment->payroll->payroll_code }}</div>
                            </div>
                            <div class="mb-3">
                                <div class="text-white-75 small mb-2">حالة الدفع</div>
                                @switch($payment->status)
                                    @case('completed')
                                        <span class="badge bg-success fs-14 px-3 py-2">مكتمل</span>
                                        @break
                                    @case('processing')
                                        <span class="badge bg-warning text-dark fs-14 px-3 py-2">قيد المعالجة</span>
                                        @break
                                    @case('pending')
                                        <span class="badge bg-info fs-14 px-3 py-2">قيد الانتظار</span>
                                        @break
                                    @case('failed')
                                        <span class="badge bg-danger fs-14 px-3 py-2">فشل</span>
                                        @break
                                    @case('cancelled')
                                        <span class="badge bg-secondary fs-14 px-3 py-2">ملغي</span>
                                        @break
                                    @default
                                        <span class="badge bg-secondary fs-14 px-3 py-2">{{ $payment->status_name_ar }}</span>
                                @endswitch
                            </div>
                            <div class="mt-auto pt-3 border-top border-white border-opacity-25">
                                <div class="text-white-75 small mb-1">المبلغ</div>
                                <div class="display-6 fw-bold lh-1">{{ number_format($payment->amount, 2) }}</div>
                                <div class="small text-white-75 mt-1">{{ $payment->currency->code ?? '' }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-light py-3 border-bottom">
                            <h6 class="mb-0 fw-semibold">
                                <i class="fas fa-circle-info text-primary me-2"></i>تفاصيل الدفع
                            </h6>
                            <small class="text-muted">معلومات الدفع والتحويل والمراجع</small>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <tbody>
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle" style="width:40%">
                                                <i class="fas fa-file-invoice text-muted me-2"></i>كشف الراتب
                                            </th>
                                            <td class="pe-4 py-3 align-middle">
                                                <span class="badge bg-primary-subtle text-primary border">{{ $payment->payroll->payroll_code }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-wallet text-muted me-2"></i>طريقة الدفع
                                            </th>
                                            <td class="pe-4 py-3 align-middle fw-semibold">{{ $payment->payment_method_name_ar }}</td>
                                        </tr>
                                        @if($payment->reference_number)
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-hashtag text-muted me-2"></i>رقم المرجع
                                            </th>
                                            <td class="pe-4 py-3 align-middle font-monospace fw-semibold">{{ $payment->reference_number }}</td>
                                        </tr>
                                        @endif
                                        @if($payment->bankAccount)
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-university text-muted me-2"></i>الحساب البنكي
                                            </th>
                                            <td class="pe-4 py-3 align-middle">
                                                <div class="fw-semibold">{{ $payment->bankAccount->bank_name }}</div>
                                                <small class="text-muted font-monospace">{{ $payment->bankAccount->account_number }}</small>
                                            </td>
                                        </tr>
                                        @endif
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-calendar-check text-muted me-2"></i>تاريخ الدفع
                                            </th>
                                            <td class="pe-4 py-3 align-middle fw-semibold">{{ $payment->payment_date->format('Y-m-d') }}</td>
                                        </tr>
                                        @if($payment->processed_at)
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-clock-check text-muted me-2"></i>تاريخ المعالجة
                                            </th>
                                            <td class="pe-4 py-3 align-middle fw-semibold">{{ $payment->processed_at->format('Y-m-d H:i') }}</td>
                                        </tr>
                                        @endif
                                        @if($payment->processedBy)
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-user-check text-muted me-2"></i>معالج بواسطة
                                            </th>
                                            <td class="pe-4 py-3 align-middle fw-semibold">{{ $payment->processedBy->name }}</td>
                                        </tr>
                                        @endif
                                        @if($payment->failure_reason)
                                        <tr class="table-danger bg-danger bg-opacity-10">
                                            <th scope="row" class="ps-4 py-3 align-middle border-danger border-opacity-25">
                                                <i class="fas fa-triangle-exclamation text-danger me-2"></i>سبب الفشل
                                            </th>
                                            <td class="pe-4 py-3 align-middle text-danger fw-semibold">{{ $payment->failure_reason }}</td>
                                        </tr>
                                        @endif
                                        @if($payment->payment_notes)
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-sticky-note text-muted me-2"></i>ملاحظات
                                            </th>
                                            <td class="pe-4 py-3 align-middle">{{ $payment->payment_notes }}</td>
                                        </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-3 mt-1 mb-4">
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-light py-3">
                            <h6 class="mb-0 fw-semibold">
                                <i class="fas fa-clock-rotate-left text-primary me-2"></i>بيانات السجل
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-0">
                                @if($payment->creator)
                                <div class="col border-bottom border-end-md p-3">
                                    <div class="small text-muted mb-1"><i class="fas fa-user-pen me-1"></i>أنشأ بواسطة</div>
                                    <div class="fw-semibold">{{ $payment->creator->name }}</div>
                                </div>
                                @endif
                                <div class="col border-bottom border-end-md p-3">
                                    <div class="small text-muted mb-1"><i class="far fa-clock me-1"></i>تاريخ الإنشاء</div>
                                    <div class="fw-semibold font-monospace small">{{ $payment->created_at->format('Y-m-d H:i') }}</div>
                                </div>
                                <div class="col border-bottom p-3">
                                    <div class="small text-muted mb-1"><i class="fas fa-pen-to-square me-1"></i>آخر تحديث</div>
                                    <div class="fw-semibold font-monospace small">{{ $payment->updated_at->format('Y-m-d H:i') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
