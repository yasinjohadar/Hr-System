@extends('admin.layouts.master')

@section('page-title')
    تفاصيل الدولة
@stop

@section('css')
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">تفاصيل الدولة: {{ $country->name_ar ?? $country->name }}</h5>
                </div>
                <div>
                    <a href="{{ route('admin.countries.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-right me-2"></i>العودة للقائمة
                    </a>
                    @can('country-edit')
                    <a href="{{ route('admin.countries.edit', $country->id) }}" class="btn btn-primary">
                        <i class="fas fa-edit me-2"></i>تعديل
                    </a>
                    @endcan
                </div>
            </div>

            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">معلومات الدولة</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">اسم الدولة (إنجليزي):</label>
                                    <p class="form-control-plaintext">{{ $country->name }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">اسم الدولة (عربي):</label>
                                    <p class="form-control-plaintext">{{ $country->name_ar ?? '-' }}</p>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">كود الدولة (2 أحرف):</label>
                                    <p class="form-control-plaintext"><span class="badge bg-info">{{ $country->code }}</span></p>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">كود الدولة (3 أحرف):</label>
                                    <p class="form-control-plaintext">{{ $country->code3 ?? '-' }}</p>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">رمز الهاتف:</label>
                                    <p class="form-control-plaintext">{{ $country->phone_code ?? '-' }}</p>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">رمز العملة:</label>
                                    <p class="form-control-plaintext">{{ $country->currency_code ?? '-' }}</p>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">العلم:</label>
                                    <p class="form-control-plaintext">
                                        @if ($country->flag)
                                            <span style="font-size: 32px;">{{ $country->flag }}</span>
                                        @else
                                            <span class="badge bg-secondary">{{ $country->code }}</span>
                                        @endif
                                    </p>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">ترتيب العرض:</label>
                                    <p class="form-control-plaintext">{{ $country->sort_order }}</p>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">الحالة:</label>
                                    <p class="form-control-plaintext">
                                        @if ($country->is_active)
                                            <span class="badge bg-success">نشط</span>
                                        @else
                                            <span class="badge bg-danger">غير نشط</span>
                                        @endif
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">عدد الموظفين:</label>
                                    <p class="form-control-plaintext">
                                        <span class="badge bg-primary">{{ $country->employees_count ?? 0 }}</span>
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">عدد الفروع:</label>
                                    <p class="form-control-plaintext">
                                        <span class="badge bg-primary">{{ $country->branches_count ?? 0 }}</span>
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

