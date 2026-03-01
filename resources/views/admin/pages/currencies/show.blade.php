@extends('admin.layouts.master')

@section('page-title')
    تفاصيل العملة
@stop

@section('css')
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">تفاصيل العملة: {{ $currency->name_ar ?? $currency->name }}</h5>
                </div>
                <div>
                    <a href="{{ route('admin.currencies.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-right me-2"></i>العودة للقائمة
                    </a>
                    @can('currency-edit')
                    <a href="{{ route('admin.currencies.edit', $currency->id) }}" class="btn btn-primary">
                        <i class="fas fa-edit me-2"></i>تعديل
                    </a>
                    @endcan
                </div>
            </div>

            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">معلومات العملة</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">اسم العملة (إنجليزي):</label>
                                    <p class="form-control-plaintext">{{ $currency->name }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">اسم العملة (عربي):</label>
                                    <p class="form-control-plaintext">{{ $currency->name_ar ?? '-' }}</p>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">كود العملة:</label>
                                    <p class="form-control-plaintext"><span class="badge bg-info">{{ $currency->code }}</span></p>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">الرمز (إنجليزي):</label>
                                    <p class="form-control-plaintext">{{ $currency->symbol ?? '-' }}</p>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">الرمز (عربي):</label>
                                    <p class="form-control-plaintext">{{ $currency->symbol_ar ?? '-' }}</p>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">عدد الأرقام العشرية:</label>
                                    <p class="form-control-plaintext">{{ $currency->decimal_places }}</p>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">سعر الصرف:</label>
                                    <p class="form-control-plaintext">{{ number_format($currency->exchange_rate, 4) }}</p>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">ترتيب العرض:</label>
                                    <p class="form-control-plaintext">{{ $currency->sort_order }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">عملة أساسية:</label>
                                    <p class="form-control-plaintext">
                                        @if ($currency->is_base_currency)
                                            <span class="badge bg-warning">نعم - عملة أساسية</span>
                                        @else
                                            <span class="badge bg-secondary">لا</span>
                                        @endif
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">الحالة:</label>
                                    <p class="form-control-plaintext">
                                        @if ($currency->is_active)
                                            <span class="badge bg-success">نشط</span>
                                        @else
                                            <span class="badge bg-danger">غير نشط</span>
                                        @endif
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

