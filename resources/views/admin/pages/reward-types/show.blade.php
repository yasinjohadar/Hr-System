@extends('admin.layouts.master')

@section('page-title')
    تفاصيل نوع المكافأة
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">تفاصيل نوع المكافأة</h5>
                </div>
                <div>
                    <a href="{{ route('admin.reward-types.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-right me-2"></i>العودة للقائمة
                    </a>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">{{ $rewardType->name_ar ?? $rewardType->name }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">الاسم:</label>
                                    <p class="form-control-plaintext">{{ $rewardType->name }}</p>
                                </div>
                                @if ($rewardType->name_ar)
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">الاسم (عربي):</label>
                                    <p class="form-control-plaintext">{{ $rewardType->name_ar }}</p>
                                </div>
                                @endif
                                @if ($rewardType->code)
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">الكود:</label>
                                    <p class="form-control-plaintext">{{ $rewardType->code }}</p>
                                </div>
                                @endif
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">نوع المكافأة:</label>
                                    <p class="form-control-plaintext">
                                        <span class="badge bg-primary">{{ $rewardType->type_name_ar }}</span>
                                    </p>
                                </div>
                                @if ($rewardType->default_value)
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">القيمة الافتراضية:</label>
                                    <p class="form-control-plaintext">{{ number_format($rewardType->default_value, 2) }}</p>
                                </div>
                                @endif
                                @if ($rewardType->default_points)
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">النقاط الافتراضية:</label>
                                    <p class="form-control-plaintext">{{ $rewardType->default_points }}</p>
                                </div>
                                @endif
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">الحالة:</label>
                                    <p class="form-control-plaintext">
                                        <span class="badge bg-{{ $rewardType->is_active ? 'success' : 'danger' }}">
                                            {{ $rewardType->is_active ? 'نشط' : 'غير نشط' }}
                                        </span>
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">عدد المكافآت:</label>
                                    <p class="form-control-plaintext">{{ $rewardType->employee_rewards_count ?? 0 }}</p>
                                </div>
                                @if ($rewardType->description)
                                <div class="col-12 mb-3">
                                    <label class="form-label fw-bold">الوصف:</label>
                                    <p class="form-control-plaintext">{{ $rewardType->description }}</p>
                                </div>
                                @endif
                                @if ($rewardType->creator)
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">تم الإنشاء بواسطة:</label>
                                    <p class="form-control-plaintext">{{ $rewardType->creator->name }}</p>
                                </div>
                                @endif
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">تاريخ الإنشاء:</label>
                                    <p class="form-control-plaintext">{{ $rewardType->created_at->format('Y-m-d H:i') }}</p>
                                </div>
                            </div>
                            <div class="mt-3">
                                @can('reward-type-edit')
                                <a href="{{ route('admin.reward-types.edit', $rewardType->id) }}" class="btn btn-info">
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

