@extends('admin.layouts.master')

@section('page-title')
    تفاصيل نوع الإجازة
@stop

@section('css')
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">تفاصيل نوع الإجازة: {{ $leaveType->name_ar ?? $leaveType->name }}</h5>
                </div>
                <div>
                    <a href="{{ route('admin.leave-types.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-right me-2"></i>العودة للقائمة
                    </a>
                    @can('leave-type-edit')
                    <a href="{{ route('admin.leave-types.edit', $leaveType->id) }}" class="btn btn-primary">
                        <i class="fas fa-edit me-2"></i>تعديل
                    </a>
                    @endcan
                </div>
            </div>

            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">معلومات نوع الإجازة</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">اسم نوع الإجازة (إنجليزي):</label>
                                    <p class="form-control-plaintext">{{ $leaveType->name }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">اسم نوع الإجازة (عربي):</label>
                                    <p class="form-control-plaintext">{{ $leaveType->name_ar ?? '-' }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">كود نوع الإجازة:</label>
                                    <p class="form-control-plaintext"><span class="badge bg-info">{{ $leaveType->code }}</span></p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">الحد الأقصى للأيام:</label>
                                    <p class="form-control-plaintext">{{ $leaveType->max_days ?? 'غير محدد' }} يوم</p>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">إجازة مدفوعة:</label>
                                    <p class="form-control-plaintext">
                                        @if ($leaveType->is_paid)
                                            <span class="badge bg-success">نعم</span>
                                        @else
                                            <span class="badge bg-danger">لا</span>
                                        @endif
                                    </p>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">تحتاج موافقة:</label>
                                    <p class="form-control-plaintext">
                                        @if ($leaveType->requires_approval)
                                            <span class="badge bg-warning">نعم</span>
                                        @else
                                            <span class="badge bg-secondary">لا</span>
                                        @endif
                                    </p>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">يمكن ترحيلها:</label>
                                    <p class="form-control-plaintext">
                                        @if ($leaveType->carry_forward)
                                            <span class="badge bg-info">نعم</span>
                                        @else
                                            <span class="badge bg-secondary">لا</span>
                                        @endif
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">عدد الطلبات:</label>
                                    <p class="form-control-plaintext">
                                        <span class="badge bg-primary">{{ $leaveType->leave_requests_count ?? 0 }}</span>
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">الحالة:</label>
                                    <p class="form-control-plaintext">
                                        @if ($leaveType->is_active)
                                            <span class="badge bg-success">نشط</span>
                                        @else
                                            <span class="badge bg-danger">غير نشط</span>
                                        @endif
                                    </p>
                                </div>
                                @if ($leaveType->description)
                                <div class="col-12 mb-3">
                                    <label class="form-label fw-bold">الوصف:</label>
                                    <p class="form-control-plaintext">{{ $leaveType->description }}</p>
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

@section('js')
@stop


