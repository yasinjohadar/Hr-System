@extends('admin.layouts.master')

@section('page-title')
    الموافقة على طلب المصروف
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">الموافقة على طلب المصروف</h5>
                </div>
                <div>
                    <a href="{{ route('admin.expense-requests.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-right me-2"></i>العودة للقائمة
                    </a>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">معلومات الطلب</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">رقم الطلب:</label>
                            <p class="form-control-plaintext"><strong>{{ $expenseRequest->request_code }}</strong></p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">الموظف:</label>
                            <p class="form-control-plaintext">{{ $expenseRequest->employee->full_name }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">التصنيف:</label>
                            <p class="form-control-plaintext">{{ $expenseRequest->category->name_ar ?? $expenseRequest->category->name }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">المبلغ:</label>
                            <p class="form-control-plaintext">
                                <strong>{{ number_format($expenseRequest->amount, 2) }}</strong>
                                @if ($expenseRequest->currency)
                                    <small class="text-muted">{{ $expenseRequest->currency->code }}</small>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">تاريخ المصروف:</label>
                            <p class="form-control-plaintext">{{ $expenseRequest->expense_date->format('Y-m-d') }}</p>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold">الوصف:</label>
                            <p class="form-control-plaintext">{{ $expenseRequest->description }}</p>
                        </div>
                        @if ($expenseRequest->receipt_path)
                        <div class="col-12">
                            <label class="form-label fw-bold">الإيصال:</label>
                            <p class="form-control-plaintext">
                                <a href="{{ asset('storage/' . $expenseRequest->receipt_path) }}" target="_blank" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye me-1"></i>عرض الإيصال
                                </a>
                            </p>
                        </div>
                        @endif
                    </div>

                    <form method="POST" action="{{ route('admin.expense-requests.approve', $expenseRequest->id) }}">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">تعليقات الموافقة</label>
                            <textarea name="comments" class="form-control" rows="3" placeholder="تعليقات اختيارية..."></textarea>
                        </div>

                        <div class="text-end">
                            <a href="{{ route('admin.expense-requests.index') }}" class="btn btn-secondary px-4 me-2">إلغاء</a>
                            <button type="submit" class="btn btn-success px-4">
                                <i class="fas fa-check me-2"></i>موافقة
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

