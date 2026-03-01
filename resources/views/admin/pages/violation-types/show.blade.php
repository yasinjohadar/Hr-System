@extends('admin.layouts.master')

@section('page-title')
    تفاصيل نوع المخالفة
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">تفاصيل نوع المخالفة</h5>
                </div>
                <div>
                    <a href="{{ route('admin.violation-types.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-right me-2"></i>العودة للقائمة
                    </a>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">{{ $violationType->name_ar ?? $violationType->name }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">الاسم:</label>
                                    <p class="form-control-plaintext">{{ $violationType->name }}</p>
                                </div>
                                @if ($violationType->name_ar)
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">الاسم (عربي):</label>
                                    <p class="form-control-plaintext">{{ $violationType->name_ar }}</p>
                                </div>
                                @endif
                                @if ($violationType->code)
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">الكود:</label>
                                    <p class="form-control-plaintext">{{ $violationType->code }}</p>
                                </div>
                                @endif
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">مستوى الخطورة:</label>
                                    <p class="form-control-plaintext">
                                        <span class="badge bg-{{ $violationType->severity_level >= 4 ? 'danger' : ($violationType->severity_level >= 3 ? 'warning' : 'info') }}">
                                            {{ $violationType->severity_level_name_ar }}
                                        </span>
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">يتطلب تحذير:</label>
                                    <p class="form-control-plaintext">
                                        <span class="badge bg-{{ $violationType->requires_warning ? 'warning' : 'secondary' }}">
                                            {{ $violationType->requires_warning ? 'نعم' : 'لا' }}
                                        </span>
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">الحالة:</label>
                                    <p class="form-control-plaintext">
                                        <span class="badge bg-{{ $violationType->is_active ? 'success' : 'danger' }}">
                                            {{ $violationType->is_active ? 'نشط' : 'غير نشط' }}
                                        </span>
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">عدد المخالفات:</label>
                                    <p class="form-control-plaintext">{{ $violationType->employeeViolations->count() }}</p>
                                </div>
                                @if ($violationType->description)
                                <div class="col-12 mb-3">
                                    <label class="form-label fw-bold">الوصف:</label>
                                    <p class="form-control-plaintext">{{ $violationType->description }}</p>
                                </div>
                                @endif
                            </div>
                            <div class="mt-3">
                                @can('violation-type-edit')
                                <a href="{{ route('admin.violation-types.edit', $violationType->id) }}" class="btn btn-info">
                                    <i class="fas fa-edit me-2"></i>تعديل
                                </a>
                                @endcan
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

