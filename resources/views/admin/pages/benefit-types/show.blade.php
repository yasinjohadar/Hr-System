@extends('admin.layouts.master')

@section('page-title')
    تفاصيل نوع الميزة
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">تفاصيل نوع الميزة: {{ $benefitType->name_ar ?? $benefitType->name }}</h5>
                </div>
                <div>
                    <a href="{{ route('admin.benefit-types.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-right me-2"></i>العودة للقائمة
                    </a>
                    @can('benefit-type-edit')
                    <a href="{{ route('admin.benefit-types.edit', $benefitType->id) }}" class="btn btn-primary">
                        <i class="fas fa-edit me-2"></i>تعديل
                    </a>
                    @endcan
                </div>
            </div>

            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">معلومات نوع الميزة</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">اسم الميزة:</label>
                                    <p class="form-control-plaintext">
                                        <strong>{{ $benefitType->name_ar ?? $benefitType->name }}</strong>
                                        @if ($benefitType->name_ar && $benefitType->name)
                                            <br><small class="text-muted">{{ $benefitType->name }}</small>
                                        @endif
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">كود الميزة:</label>
                                    <p class="form-control-plaintext"><span class="badge bg-info">{{ $benefitType->code }}</span></p>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">النوع:</label>
                                    <p class="form-control-plaintext"><span class="badge bg-secondary">{{ $benefitType->type_name_ar }}</span></p>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">القيمة الافتراضية:</label>
                                    <p class="form-control-plaintext">
                                        @if ($benefitType->default_value)
                                            {{ number_format($benefitType->default_value, 2) }}
                                            @if ($benefitType->currency)
                                                {{ $benefitType->currency->symbol_ar ?? $benefitType->currency->symbol }}
                                            @endif
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </p>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">عدد الموظفين:</label>
                                    <p class="form-control-plaintext">
                                        <span class="badge bg-success">{{ $benefitType->employee_benefits_count ?? 0 }}</span>
                                    </p>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label fw-bold">خاضع للضريبة:</label>
                                    <p class="form-control-plaintext">
                                        <span class="badge bg-{{ $benefitType->is_taxable ? 'warning' : 'success' }}">
                                            {{ $benefitType->is_taxable ? 'نعم' : 'لا' }}
                                        </span>
                                    </p>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label fw-bold">إلزامي:</label>
                                    <p class="form-control-plaintext">
                                        <span class="badge bg-{{ $benefitType->is_mandatory ? 'info' : 'secondary' }}">
                                            {{ $benefitType->is_mandatory ? 'نعم' : 'لا' }}
                                        </span>
                                    </p>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label fw-bold">يتطلب موافقة:</label>
                                    <p class="form-control-plaintext">
                                        <span class="badge bg-{{ $benefitType->requires_approval ? 'warning' : 'success' }}">
                                            {{ $benefitType->requires_approval ? 'نعم' : 'لا' }}
                                        </span>
                                    </p>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label fw-bold">الحالة:</label>
                                    <p class="form-control-plaintext">
                                        <span class="badge bg-{{ $benefitType->is_active ? 'success' : 'danger' }}">
                                            {{ $benefitType->is_active ? 'نشط' : 'غير نشط' }}
                                        </span>
                                    </p>
                                </div>
                                @if ($benefitType->description || $benefitType->description_ar)
                                <div class="col-12 mb-3">
                                    <label class="form-label fw-bold">الوصف:</label>
                                    <p class="form-control-plaintext">{{ $benefitType->description_ar ?? $benefitType->description }}</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop


