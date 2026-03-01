@extends('admin.layouts.master')

@section('page-title')
    تفاصيل مكافأة الموظف
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">تفاصيل مكافأة الموظف</h5>
                </div>
                <div>
                    <a href="{{ route('admin.employee-rewards.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-right me-2"></i>العودة للقائمة
                    </a>
                    @can('employee-reward-edit')
                    <a href="{{ route('admin.employee-rewards.edit', $reward->id) }}" class="btn btn-primary">
                        <i class="fas fa-edit me-2"></i>تعديل
                    </a>
                    @endcan
                </div>
            </div>

            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">معلومات المكافأة</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">كود المكافأة:</label>
                                    <p class="form-control-plaintext"><strong>{{ $reward->reward_code }}</strong></p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">الموظف:</label>
                                    <p class="form-control-plaintext">
                                        <strong>
                                            <a href="{{ route('admin.employees.show', $reward->employee_id) }}">
                                                {{ $reward->employee->full_name }}
                                            </a>
                                        </strong>
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">نوع المكافأة:</label>
                                    <p class="form-control-plaintext">
                                        <strong>{{ $reward->rewardType->name_ar ?? $reward->rewardType->name }}</strong>
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">العنوان:</label>
                                    <p class="form-control-plaintext"><strong>{{ $reward->title }}</strong></p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">تاريخ المكافأة:</label>
                                    <p class="form-control-plaintext">{{ $reward->reward_date->format('Y-m-d') }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">السبب:</label>
                                    <p class="form-control-plaintext">
                                        <span class="badge bg-info">{{ $reward->reason_name_ar }}</span>
                                    </p>
                                </div>
                                @if ($reward->monetary_value)
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">القيمة النقدية:</label>
                                    <p class="form-control-plaintext">
                                        <strong>
                                            {{ number_format($reward->monetary_value, 2) }}
                                            @if ($reward->currency)
                                                {{ $reward->currency->symbol_ar ?? $reward->currency->symbol }}
                                            @endif
                                        </strong>
                                    </p>
                                </div>
                                @endif
                                @if ($reward->points)
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">النقاط:</label>
                                    <p class="form-control-plaintext"><strong>{{ $reward->points }}</strong></p>
                                </div>
                                @endif
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">الحالة:</label>
                                    <p class="form-control-plaintext">
                                        <span class="badge bg-{{ $reward->status == 'awarded' ? 'success' : ($reward->status == 'approved' ? 'primary' : ($reward->status == 'cancelled' ? 'danger' : 'warning')) }}">
                                            {{ $reward->status_name_ar }}
                                        </span>
                                    </p>
                                </div>
                                @if ($reward->awardedBy)
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">تم منحها بواسطة:</label>
                                    <p class="form-control-plaintext">{{ $reward->awardedBy->name }}</p>
                                </div>
                                @endif
                                @if ($reward->awarded_at)
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">تاريخ المنح:</label>
                                    <p class="form-control-plaintext">{{ $reward->awarded_at->format('Y-m-d H:i') }}</p>
                                </div>
                                @endif
                                @if ($reward->description)
                                <div class="col-12 mb-3">
                                    <label class="form-label fw-bold">الوصف:</label>
                                    <p class="form-control-plaintext">{{ $reward->description }}</p>
                                </div>
                                @endif
                                @if ($reward->notes)
                                <div class="col-12 mb-3">
                                    <label class="form-label fw-bold">ملاحظات:</label>
                                    <p class="form-control-plaintext">{{ $reward->notes }}</p>
                                </div>
                                @endif
                                @if ($reward->creator)
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">تم الإنشاء بواسطة:</label>
                                    <p class="form-control-plaintext">{{ $reward->creator->name }}</p>
                                </div>
                                @endif
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">تاريخ الإنشاء:</label>
                                    <p class="form-control-plaintext">{{ $reward->created_at->format('Y-m-d H:i') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

