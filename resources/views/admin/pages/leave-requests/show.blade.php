@extends('admin.layouts.master')

@section('page-title')
    تفاصيل طلب الإجازة
@stop

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin-leave-requests.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/workflow-approval-timeline.css') }}">
@endpush

@section('content')
    @php
        $employeeName = $leaveRequest->employee->full_name
            ?? trim($leaveRequest->employee->first_name . ' ' . $leaveRequest->employee->last_name);
        $canApproveNow = $canApproveNow ?? false;
        $displayBadge = $workflowProgress['badge_ar'] ?? $leaveRequest->status_name_ar;
        $displayVariant = $workflowProgress['badge_variant'] ?? $leaveRequest->status;
    @endphp

    <div class="main-content app-content admin-leave-requests-page">
        <div class="container-fluid pt-4">

            <div class="card page-hero mb-4">
                <div class="card-body py-4">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="page-hero-icon">
                                <i class="ri-file-text-line"></i>
                            </div>
                            <div>
                                <h4 class="mb-1 page-hero-title fw-bold">تفاصيل طلب الإجازة #{{ $leaveRequest->id }}</h4>
                                <p class="mb-0 page-hero-subtitle">{{ $employeeName }}</p>
                                <span class="status-pill status-pill--{{ $displayVariant }} hero-status-pill">{{ $displayBadge }}</span>
                            </div>
                        </div>
                        <div class="d-flex flex-wrap gap-2">
                            <a href="{{ route('admin.leave-requests.index') }}" class="btn btn-hero-outline btn-sm">
                                <i class="ri-arrow-right-line me-1"></i>العودة للقائمة
                            </a>
                            @if ($leaveRequest->status == 'pending' && $canApproveNow)
                                <button type="button" class="btn btn-hero-success btn-sm" data-bs-toggle="modal" data-bs-target="#approve{{ $leaveRequest->id }}">
                                    <i class="ri-check-line me-1"></i>موافقة
                                </button>
                                <button type="button" class="btn btn-hero-danger btn-sm" data-bs-toggle="modal" data-bs-target="#reject{{ $leaveRequest->id }}">
                                    <i class="ri-close-line me-1"></i>رفض
                                </button>
                            @endif
                            @if ($leaveRequest->status == 'pending')
                                @can('leave-request-edit')
                                    <a href="{{ route('admin.leave-requests.edit', $leaveRequest->id) }}" class="btn btn-hero-primary btn-sm">
                                        <i class="ri-pencil-line me-1"></i>تعديل
                                    </a>
                                @endcan
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="detail-panel">
                <div class="detail-panel-header">معلومات طلب الإجازة</div>

                <div class="detail-summary">
                    <div class="detail-summary-item">
                        <div class="detail-summary-label">الموظف</div>
                        <div class="detail-summary-value">{{ $employeeName }}</div>
                        @if ($leaveRequest->employee->employee_code ?? null)
                            <div class="detail-summary-value--muted">{{ $leaveRequest->employee->employee_code }}</div>
                        @endif
                    </div>
                    <div class="detail-summary-item">
                        <div class="detail-summary-label">نوع الإجازة</div>
                        <div class="detail-summary-value">
                            <span class="type-pill">{{ $leaveRequest->leaveType->name_ar ?? $leaveRequest->leaveType->name }}</span>
                        </div>
                    </div>
                    <div class="detail-summary-item">
                        <div class="detail-summary-label">من تاريخ</div>
                        <div class="detail-summary-value">{{ $leaveRequest->start_date->format('Y/m/d') }}</div>
                    </div>
                    <div class="detail-summary-item">
                        <div class="detail-summary-label">إلى تاريخ</div>
                        <div class="detail-summary-value">{{ $leaveRequest->end_date->format('Y/m/d') }}</div>
                    </div>
                    <div class="detail-summary-item">
                        <div class="detail-summary-label">عدد الأيام</div>
                        <div class="detail-summary-value">
                            <span class="days-pill">{{ $leaveRequest->days_count }} يوم</span>
                        </div>
                    </div>
                    <div class="detail-summary-item">
                        <div class="detail-summary-label">الحالة</div>
                        <div class="detail-summary-value">
                            <span class="status-pill status-pill--{{ $displayVariant }}">{{ $displayBadge }}</span>
                        </div>
                    </div>
                    <div class="detail-summary-item">
                        <div class="detail-summary-label">رقم الطلب</div>
                        <div class="detail-summary-value">#{{ $leaveRequest->id }}</div>
                    </div>
                </div>

                <ul class="detail-meta-list">
                    @if ($leaveRequest->reason)
                        <li class="detail-meta-item">
                            <span class="detail-meta-key">سبب الإجازة</span>
                            <span class="detail-meta-value">{{ $leaveRequest->reason }}</span>
                        </li>
                    @endif
                    @if ($leaveRequest->notes)
                        <li class="detail-meta-item">
                            <span class="detail-meta-key">ملاحظات</span>
                            <span class="detail-meta-value">{{ $leaveRequest->notes }}</span>
                        </li>
                    @endif
                    @if ($leaveRequest->approved_by)
                        <li class="detail-meta-item">
                            <span class="detail-meta-key">وافق عليه</span>
                            <span class="detail-meta-value">
                                {{ $leaveRequest->approver->name ?? '—' }}
                                @if ($leaveRequest->approved_at)
                                    <span class="d-block text-muted fs-12 mt-1">{{ $leaveRequest->approved_at->format('Y/m/d H:i') }}</span>
                                @endif
                            </span>
                        </li>
                    @endif
                    @if ($leaveRequest->rejection_reason)
                        <li class="detail-meta-item">
                            <span class="detail-meta-key">سبب الرفض</span>
                            <span class="detail-meta-value detail-meta-value--danger">{{ $leaveRequest->rejection_reason }}</span>
                        </li>
                    @endif
                    <li class="detail-meta-item">
                        <span class="detail-meta-key">تاريخ الإنشاء</span>
                        <span class="detail-meta-value">{{ $leaveRequest->created_at->format('Y/m/d H:i') }}</span>
                    </li>
                    <li class="detail-meta-item">
                        <span class="detail-meta-key">أنشأ بواسطة</span>
                        <span class="detail-meta-value">{{ $leaveRequest->creator->name ?? '—' }}</span>
                    </li>
                </ul>
            </div>

            @if ($workflowProgress ?? null)
                <div class="card custom-card mb-4">
                    <div class="card-body">
                        <x-workflow-approval-timeline :workflow-progress="$workflowProgress" />
                    </div>
                </div>
            @endif

            @if ($leaveRequest->status == 'pending' && $canApproveNow)
                @include('admin.pages.leave-requests.approve', ['request' => $leaveRequest])
                @include('admin.pages.leave-requests.reject', ['request' => $leaveRequest])
            @endif

        </div>
    </div>
@stop
