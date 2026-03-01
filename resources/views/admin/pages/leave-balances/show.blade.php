@extends('admin.layouts.master')

@section('page-title')
    تفاصيل رصيد الإجازة
@stop

@section('css')
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">تفاصيل رصيد الإجازة</h5>
                </div>
                <div>
                    <a href="{{ route('admin.leave-balances.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-right me-2"></i>العودة للقائمة
                    </a>
                    @can('leave-balance-edit')
                    <a href="{{ route('admin.leave-balances.edit', $leaveBalance->id) }}" class="btn btn-primary">
                        <i class="fas fa-edit me-2"></i>تعديل
                    </a>
                    @endcan
                </div>
            </div>

            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">معلومات رصيد الإجازة</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">الموظف:</label>
                                    <p class="form-control-plaintext">
                                        <strong>{{ $leaveBalance->employee->full_name ?? $leaveBalance->employee->first_name . ' ' . $leaveBalance->employee->last_name }}</strong>
                                        <br><small class="text-muted">{{ $leaveBalance->employee->employee_code ?? '' }}</small>
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">نوع الإجازة:</label>
                                    <p class="form-control-plaintext">
                                        <span class="badge bg-info">{{ $leaveBalance->leaveType->name_ar ?? $leaveBalance->leaveType->name }}</span>
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">السنة:</label>
                                    <p class="form-control-plaintext">{{ $leaveBalance->year }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">إجمالي الأيام:</label>
                                    <p class="form-control-plaintext">
                                        <span class="badge bg-primary">{{ $leaveBalance->total_days }} يوم</span>
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">الأيام المستخدمة:</label>
                                    <p class="form-control-plaintext">
                                        <span class="badge bg-warning">{{ $leaveBalance->used_days }} يوم</span>
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">الأيام المتبقية:</label>
                                    <p class="form-control-plaintext">
                                        @if ($leaveBalance->remaining_days > 0)
                                            <span class="badge bg-success">{{ $leaveBalance->remaining_days }} يوم</span>
                                        @else
                                            <span class="badge bg-danger">{{ $leaveBalance->remaining_days }} يوم</span>
                                        @endif
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">الأيام المحمولة من العام السابق:</label>
                                    <p class="form-control-plaintext">
                                        <span class="badge bg-info">{{ $leaveBalance->carried_forward }} يوم</span>
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


