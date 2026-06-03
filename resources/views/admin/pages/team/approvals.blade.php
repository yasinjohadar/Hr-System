@extends('admin.layouts.master')

@section('page-title')
    الموافقات
@stop

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin-team-approvals.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('assets/js/admin-team-approvals.js') }}"></script>
@endpush

@section('content')
    <div class="main-content app-content admin-team-approvals-page">
        <div class="container-fluid pt-4">

            <div class="card page-hero mb-4">
                <div class="card-body py-4">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="page-hero-icon">
                                <i class="ri-checkbox-circle-line"></i>
                            </div>
                            <div>
                                <h4 class="mb-1 page-hero-title fw-bold">الموافقات</h4>
                                <p class="mb-0 page-hero-subtitle">إدارة طلبات الموافقة لفريقك</p>
                            </div>
                        </div>
                        <a href="{{ route('admin.team.dashboard') }}" class="btn btn-hero-outline btn-sm">
                            <i class="ri-arrow-right-line me-1"></i>العودة للوحة التحكم
                        </a>
                    </div>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-sm-6 col-xl-4">
                    <div class="stat-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-value stat-value--warning">{{ count($pendingApprovals) }}</div>
                                <div class="stat-label">طلبات معلقة</div>
                            </div>
                            <div class="stat-icon stat-icon--warning"><i class="ri-time-line"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-4">
                    <div class="stat-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-value stat-value--success">{{ $approvedCount }}</div>
                                <div class="stat-label">تمت الموافقة هذا الشهر</div>
                            </div>
                            <div class="stat-icon stat-icon--success"><i class="ri-checkbox-circle-line"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-4">
                    <div class="stat-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-value stat-value--danger">{{ $rejectedCount }}</div>
                                <div class="stat-label">تم الرفض هذا الشهر</div>
                            </div>
                            <div class="stat-icon stat-icon--danger"><i class="ri-close-circle-line"></i></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="content-panel">
                <div class="content-panel-header">
                    <div>
                        <h5 class="fw-bold">الطلبات المعلقة</h5>
                        <p class="text-muted fs-13 mb-0">{{ count($pendingApprovals) }} طلب بانتظار إجراءك</p>
                    </div>
                    @if (count($pendingApprovals) > 0)
                        <div class="filter-pills" role="group">
                            <button type="button" class="filter-pill active" data-approval-filter="all">الكل</button>
                            @foreach ($approvalTypeFilters as $filter)
                                <button type="button" class="filter-pill" data-approval-filter="{{ $filter['key'] }}">{{ $filter['label'] }}</button>
                            @endforeach
                        </div>
                    @endif
                </div>

                @forelse($pendingApprovals as $approval)
                    @php
                        $req = $approval['request'];
                        $iconClass = match ($approval['type']) {
                            'leave' => ['leave', 'sun'],
                            'expense' => ['expense', 'money-dollar-circle'],
                            'job_change' => ['job_change', 'user-settings'],
                            'ticket' => ['ticket', 'customer-service-2'],
                            'project_time' => ['project_time', 'timer'],
                            default => ['default', 'file-list'],
                        };
                    @endphp
                    <div class="approval-card"
                         data-approval-card
                         data-approval-type="{{ $approval['type'] }}">
                        <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
                            <div class="d-flex align-items-start gap-3 flex-grow-1 min-w-0">
                                <div class="approval-icon approval-icon--{{ $iconClass[0] }}">
                                    <i class="ri-{{ $iconClass[1] }}-line"></i>
                                </div>
                                <div class="min-w-0">
                                    <div class="approval-title">{{ $req->employee->full_name }}</div>
                                    <div class="approval-meta mt-1">
                                        {{ $req->employee->department->name ?? '—' }}
                                        · {{ $req->employee->position->title ?? '—' }}
                                    </div>
                                    <div class="approval-meta mt-1">
                                        <span class="type-pill">{{ $approval['label_ar'] }}</span>
                                        @if (!empty($approval['step']->name_ar))
                                            · <i class="ri-git-commit-line me-1"></i>{{ $approval['step']->name_ar }}
                                        @endif
                                    </div>
                                    <div class="approval-body mt-2">
                                        @if ($approval['type'] === 'leave')
                                            <span class="type-pill type-pill--leave">{{ $req->leaveType->name_ar ?? $req->leaveType->name }}</span>
                                            {{ $req->start_date->format('Y/m/d') }} — {{ $req->end_date->format('Y/m/d') }}
                                            ({{ $req->days_count }} يوم)
                                            @if ($req->reason)
                                                <div class="approval-meta mt-2">
                                                    <i class="ri-chat-quote-line me-1"></i>{{ Str::limit($req->reason, 120) }}
                                                </div>
                                            @endif
                                        @elseif ($approval['type'] === 'expense')
                                            <span class="type-pill type-pill--expense">{{ $req->category->name_ar ?? $req->category->name }}</span>
                                            {{ number_format($req->amount, 2) }} {{ $req->currency->code ?? 'ر.س' }}
                                            @if ($req->description)
                                                <div class="approval-meta mt-2">
                                                    <i class="ri-chat-quote-line me-1"></i>{{ Str::limit($req->description, 120) }}
                                                </div>
                                            @endif
                                        @elseif ($approval['type'] === 'job_change')
                                            {{ $req->change_type_label ?? $req->change_type ?? 'تغيير وظيفي' }}
                                            @if ($req->effective_date)
                                                · {{ $req->effective_date->format('Y/m/d') }}
                                            @endif
                                        @elseif ($approval['type'] === 'ticket')
                                            <strong>{{ $req->title }}</strong>
                                            @if ($req->description)
                                                <div class="approval-meta mt-2">{{ Str::limit($req->description, 120) }}</div>
                                            @endif
                                        @elseif ($approval['type'] === 'project_time')
                                            {{ $req->project->name ?? 'مشروع' }} · {{ $req->hours }} ساعة · {{ $req->worked_date->format('Y/m/d') }}
                                        @else
                                            {{ $approval['label_ar'] }}
                                        @endif
                                    </div>
                                    <div class="approval-meta mt-2">
                                        <i class="ri-time-line me-1"></i>{{ $req->created_at->diffForHumans() }}
                                    </div>
                                </div>
                            </div>
                            <div class="flex-shrink-0">
                                @if ($approval['show_route'])
                                    <a href="{{ route($approval['show_route'], $req->id) }}" class="btn-view-details">
                                        <i class="ri-eye-line me-1"></i>عرض التفاصيل
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="empty-state">
                        <div class="empty-icon"><i class="ri-checkbox-circle-line"></i></div>
                        <h5>لا توجد طلبات معلقة</h5>
                        <p>جميع الطلبات تمت معالجتها</p>
                    </div>
                @endforelse

                <div class="empty-filtered d-none" data-empty-filtered>
                    لا توجد طلبات من هذا النوع
                </div>
            </div>

        </div>
    </div>
@stop
