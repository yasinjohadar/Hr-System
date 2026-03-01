@extends('admin.layouts.master')

@section('page-title')
    تفاصيل طلب الإجازة
@stop

@section('css')
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">تفاصيل طلب الإجازة</h5>
                </div>
                <div>
                    <a href="{{ route('admin.leave-requests.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-right me-2"></i>العودة للقائمة
                    </a>
                    @if ($leaveRequest->status == 'pending')
                        @can('leave-request-approve')
                        <form action="{{ route('admin.leave-requests.approve', $leaveRequest->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-success" onclick="return confirm('هل أنت متأكد من الموافقة على هذا الطلب؟')">
                                <i class="fas fa-check me-2"></i>موافقة
                            </button>
                        </form>
                        @endcan
                        @can('leave-request-edit')
                        <a href="{{ route('admin.leave-requests.edit', $leaveRequest->id) }}" class="btn btn-primary">
                            <i class="fas fa-edit me-2"></i>تعديل
                        </a>
                        @endcan
                    @endif
                </div>
            </div>

            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">معلومات طلب الإجازة</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">الموظف:</label>
                                    <p class="form-control-plaintext">
                                        <strong>{{ $leaveRequest->employee->full_name ?? $leaveRequest->employee->first_name . ' ' . $leaveRequest->employee->last_name }}</strong>
                                        <br><small class="text-muted">{{ $leaveRequest->employee->employee_code ?? '' }}</small>
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">نوع الإجازة:</label>
                                    <p class="form-control-plaintext">
                                        <span class="badge bg-info">{{ $leaveRequest->leaveType->name_ar ?? $leaveRequest->leaveType->name }}</span>
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">من تاريخ:</label>
                                    <p class="form-control-plaintext">{{ $leaveRequest->start_date->format('Y-m-d') }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">إلى تاريخ:</label>
                                    <p class="form-control-plaintext">{{ $leaveRequest->end_date->format('Y-m-d') }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">عدد الأيام:</label>
                                    <p class="form-control-plaintext">
                                        <span class="badge bg-primary">{{ $leaveRequest->days_count }} يوم</span>
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">الحالة:</label>
                                    <p class="form-control-plaintext">
                                        @if ($leaveRequest->status == 'approved')
                                            <span class="badge bg-success">موافق عليه</span>
                                        @elseif ($leaveRequest->status == 'pending')
                                            <span class="badge bg-warning">قيد الانتظار</span>
                                        @elseif ($leaveRequest->status == 'rejected')
                                            <span class="badge bg-danger">مرفوض</span>
                                        @else
                                            <span class="badge bg-secondary">ملغي</span>
                                        @endif
                                    </p>
                                </div>
                                @if ($leaveRequest->approved_by)
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">وافق عليه:</label>
                                    <p class="form-control-plaintext">
                                        {{ $leaveRequest->approver->name ?? '-' }}
                                        @if ($leaveRequest->approved_at)
                                            <br><small class="text-muted">{{ $leaveRequest->approved_at->format('Y-m-d H:i') }}</small>
                                        @endif
                                    </p>
                                </div>
                                @endif
                                @if ($leaveRequest->rejection_reason)
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">سبب الرفض:</label>
                                    <p class="form-control-plaintext text-danger">{{ $leaveRequest->rejection_reason }}</p>
                                </div>
                                @endif
                                @if ($leaveRequest->reason)
                                <div class="col-12 mb-3">
                                    <label class="form-label fw-bold">سبب الإجازة:</label>
                                    <p class="form-control-plaintext">{{ $leaveRequest->reason }}</p>
                                </div>
                                @endif
                                @if ($leaveRequest->notes)
                                <div class="col-12 mb-3">
                                    <label class="form-label fw-bold">ملاحظات:</label>
                                    <p class="form-control-plaintext">{{ $leaveRequest->notes }}</p>
                                </div>
                                @endif
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">تاريخ الإنشاء:</label>
                                    <p class="form-control-plaintext">{{ $leaveRequest->created_at->format('Y-m-d H:i') }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">أنشأ بواسطة:</label>
                                    <p class="form-control-plaintext">{{ $leaveRequest->creator->name ?? '-' }}</p>
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


