@extends('admin.layouts.master')

@section('page-title')
    الموافقات
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div>
                    <h5 class="page-title fs-21 mb-1">الموافقات</h5>
                    <p class="text-muted fs-13 mb-0">إدارة طلبات الموافقة</p>
                </div>
                <a href="{{ route('admin.team.dashboard') }}" class="btn btn-outline-primary btn-sm">
                    <i class="ri-arrow-left-line me-1"></i>العودة للوحة التحكم
                </a>
            </div>

            <!-- Stats -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card custom-card bg-warning text-white">
                        <div class="card-body text-center">
                            <h3 class="mb-1 fw-semibold">{{ count($pendingApprovals) }}</h3>
                            <p class="mb-0">طلبات معلقة</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card custom-card bg-success text-white">
                        <div class="card-body text-center">
                            <h3 class="mb-1 fw-semibold">{{ $approvedCount }}</h3>
                            <p class="mb-0">تمت الموافقة هذا الشهر</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card custom-card bg-danger text-white">
                        <div class="card-body text-center">
                            <h3 class="mb-1 fw-semibold">{{ $rejectedCount }}</h3>
                            <p class="mb-0">تم الرفض هذا الشهر</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pending Approvals -->
            <div class="card custom-card">
                <div class="card-header">
                    <h6 class="card-title fw-semibold">الطلبات المعلقة</h6>
                </div>
                <div class="card-body p-0">
                    @forelse($pendingApprovals as $approval)
                        <div class="p-4 border-bottom">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <div class="d-flex align-items-start">
                                        <div class="avatar avatar-md bg-{{ $approval['type'] === 'leave' ? 'success' : 'info' }}-transparent avatar-rounded me-3">
                                            <i class="ri-{{ $approval['type'] === 'leave' ? 'sun' : 'money-dollar-circle' }}-line text-{{ $approval['type'] === 'leave' ? 'success' : 'info' }}"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-1 fw-semibold">{{ $approval['request']->employee->full_name }}</h6>
                                            <p class="text-muted mb-1 fs-13">
                                                {{ $approval['request']->employee->department->name ?? '-' }} - {{ $approval['request']->employee->position->title ?? '-' }}
                                            </p>
                                            @if($approval['type'] === 'leave')
                                                <p class="mb-1">
                                                    <span class="badge bg-success-transparent me-1">{{ $approval['request']->leaveType->name_ar ?? $approval['request']->leaveType->name }}</span>
                                                    {{ $approval['request']->start_date->format('Y/m/d') }} إلى {{ $approval['request']->end_date->format('Y/m/d') }}
                                                    ({{ $approval['request']->days_count }} يوم)
                                                </p>
                                                @if($approval['request']->reason)
                                                    <p class="text-muted fs-13 mb-0">
                                                        <i class="ri-chat-quote-line me-1"></i>{{ Str::limit($approval['request']->reason, 100) }}
                                                    </p>
                                                @endif
                                            @else
                                                <p class="mb-1">
                                                    <span class="badge bg-info-transparent me-1">{{ $approval['request']->category->name_ar ?? $approval['request']->category->name }}</span>
                                                    {{ number_format($approval['request']->amount, 2) }} {{ $approval['request']->currency->code ?? 'ر.س' }}
                                                </p>
                                                <p class="text-muted fs-13 mb-0">
                                                    <i class="ri-chat-quote-line me-1"></i>{{ Str::limit($approval['request']->description, 100) }}
                                                </p>
                                            @endif
                                            <small class="text-muted mt-2 d-block">
                                                <i class="ri-time-line me-1"></i>
                                                {{ $approval['request']->created_at->diffForHumans() }}
                                            </small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                                    @if($approval['type'] === 'leave')
                                        <a href="{{ route('admin.leave-requests.show', $approval['request']->id) }}" class="btn btn-primary btn-sm">
                                            <i class="ri-eye-line me-1"></i>عرض التفاصيل
                                        </a>
                                    @else
                                        <a href="{{ route('admin.expense-requests.show', $approval['request']->id) }}" class="btn btn-primary btn-sm">
                                            <i class="ri-eye-line me-1"></i>عرض التفاصيل
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted py-5">
                            <i class="ri-checkbox-circle-line fs-40 d-block mb-3 text-success"></i>
                            <h5>لا توجد طلبات معلقة</h5>
                            <p class="fs-14">جميع الطلبات تمت معالجتها</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@stop
