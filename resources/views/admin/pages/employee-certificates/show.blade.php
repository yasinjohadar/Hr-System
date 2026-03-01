@extends('admin.layouts.master')

@section('page-title')
    تفاصيل الشهادة
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">تفاصيل الشهادة</h5>
                </div>
                <div>
                    <a href="{{ route('admin.employee-certificates.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-right me-2"></i>العودة للقائمة
                    </a>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">{{ $certificate->certificate_name_ar ?? $certificate->certificate_name }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">الموظف:</label>
                                    <p class="form-control-plaintext">{{ $certificate->employee->full_name }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">الجهة المانحة:</label>
                                    <p class="form-control-plaintext">{{ $certificate->issuing_organization }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">رقم الشهادة:</label>
                                    <p class="form-control-plaintext">{{ $certificate->certificate_number ?? '-' }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">تاريخ الإصدار:</label>
                                    <p class="form-control-plaintext">{{ $certificate->issue_date->format('Y-m-d') }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">تاريخ انتهاء الصلاحية:</label>
                                    <p class="form-control-plaintext">
                                        @if ($certificate->does_not_expire)
                                            <span class="text-muted">لا تنتهي</span>
                                        @elseif ($certificate->expiry_date)
                                            <span class="{{ $certificate->isExpired() ? 'text-danger' : '' }}">
                                                {{ $certificate->expiry_date->format('Y-m-d') }}
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">الحالة:</label>
                                    <p class="form-control-plaintext">
                                        <span class="badge bg-{{ $certificate->status == 'active' ? 'success' : 'danger' }}">
                                            {{ $certificate->status == 'active' ? 'نشط' : 'منتهي' }}
                                        </span>
                                    </p>
                                </div>
                                @if ($certificate->description)
                                <div class="col-12 mb-3">
                                    <label class="form-label fw-bold">الوصف:</label>
                                    <p class="form-control-plaintext">{{ $certificate->description }}</p>
                                </div>
                                @endif
                            </div>
                            <div class="mt-3">
                                @can('employee-certificate-edit')
                                <a href="{{ route('admin.employee-certificates.edit', $certificate->id) }}" class="btn btn-warning">
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


