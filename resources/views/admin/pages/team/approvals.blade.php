@extends('admin.layouts.master')

@section('page-title')
    الموافقات
@stop

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin-team-approvals.css') }}?v=2">
@endpush

@push('scripts')
    <script src="{{ asset('assets/js/admin-team-approvals.js') }}"></script>
@endpush

@section('content')
    <div class="main-content app-content admin-team-approvals-page">
        <div class="container-fluid admin-page-shell">

            <div class="admin-page-banner">
                <div class="admin-page-banner-main">
                    <span class="admin-page-banner-icon"><i class="ri-checkbox-circle-line"></i></span>
                    <div class="admin-page-banner-text">
                        <h1>الموافقات</h1>
                        <p>إدارة طلبات الموافقة لفريقك ومراجعتها</p>
                    </div>
                </div>
                <div class="admin-page-banner-actions">
                    <a href="{{ route('admin.team.dashboard') }}" class="admin-btn admin-btn-light">
                        <i class="ri-arrow-right-line"></i>
                        العودة للوحة التحكم
                    </a>
                </div>
            </div>

            <div class="admin-report-stats admin-report-stats-4 team-approvals-stats mb-4">
                <div class="admin-report-stat admin-report-stat-static admin-report-stat-amber">
                    <span class="admin-report-stat-icon"><i class="ri-time-line"></i></span>
                    <span class="admin-report-stat-label">طلبات معلقة</span>
                    <span class="admin-report-stat-value" style="color:#d97706;">{{ count($pendingApprovals) }}</span>
                </div>
                <div class="admin-report-stat admin-report-stat-static admin-report-stat-green">
                    <span class="admin-report-stat-icon"><i class="ri-checkbox-circle-line"></i></span>
                    <span class="admin-report-stat-label">تمت الموافقة هذا الشهر</span>
                    <span class="admin-report-stat-value" style="color:#059669;">{{ $approvedCount }}</span>
                </div>
                <div class="admin-report-stat admin-report-stat-static admin-report-stat-rose">
                    <span class="admin-report-stat-icon"><i class="ri-close-circle-line"></i></span>
                    <span class="admin-report-stat-label">تم الرفض هذا الشهر</span>
                    <span class="admin-report-stat-value" style="color:#e11d48;">{{ $rejectedCount }}</span>
                </div>
                <div class="admin-report-stat admin-report-stat-static admin-report-stat-cyan">
                    <span class="admin-report-stat-icon"><i class="ri-filter-3-line"></i></span>
                    <span class="admin-report-stat-label">أنواع الطلبات</span>
                    <span class="admin-report-stat-value" style="color:#0891b2;">{{ count($approvalTypeFilters) }}</span>
                </div>
            </div>

            <div class="admin-page-card team-approvals-panel">
                <div class="card-toolbar team-approvals-toolbar">
                    <div>
                        <h2 class="team-approvals-toolbar__title">الطلبات المعلقة</h2>
                        <p class="team-approvals-toolbar__subtitle mb-0">{{ count($pendingApprovals) }} طلب بانتظار إجراءك</p>
                    </div>
                    @if (count($pendingApprovals) > 0)
                        <div class="team-approval-filters" role="group">
                            <button type="button" class="team-approval-filter active" data-approval-filter="all">الكل</button>
                            @foreach ($approvalTypeFilters as $filter)
                                <button type="button" class="team-approval-filter" data-approval-filter="{{ $filter['key'] }}">{{ $filter['label'] }}</button>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="team-approvals-list">
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
                        <article class="team-approval-card"
                                 data-approval-card
                                 data-approval-type="{{ $approval['type'] }}">
                            <div class="team-approval-card__main">
                                <div class="approval-icon approval-icon--{{ $iconClass[0] }}">
                                    <i class="ri-{{ $iconClass[1] }}-line"></i>
                                </div>
                                <div class="team-approval-card__body">
                                    <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                                        <h3 class="team-approval-card__name">{{ $req->employee->full_name }}</h3>
                                        <span class="type-pill">{{ $approval['label_ar'] }}</span>
                                    </div>
                                    <div class="approval-meta">
                                        {{ $req->employee->department->name ?? '—' }}
                                        · {{ $req->employee->position->title ?? '—' }}
                                        @if (!empty($approval['step']->name_ar))
                                            · <i class="ri-git-commit-line"></i> {{ $approval['step']->name_ar }}
                                        @endif
                                    </div>
                                    <div class="approval-body">
                                        @if ($approval['type'] === 'leave')
                                            <span class="type-pill type-pill--leave">{{ $req->leaveType->name_ar ?? $req->leaveType->name }}</span>
                                            {{ $req->start_date->format('Y/m/d') }} — {{ $req->end_date->format('Y/m/d') }}
                                            ({{ $req->days_count }} يوم)
                                            @if ($req->reason)
                                                <div class="approval-meta mt-2">
                                                    <i class="ri-chat-quote-line"></i> {{ Str::limit($req->reason, 120) }}
                                                </div>
                                            @endif
                                        @elseif ($approval['type'] === 'expense')
                                            <span class="type-pill type-pill--expense">{{ $req->category->name_ar ?? $req->category->name }}</span>
                                            {{ number_format($req->amount, 2) }} {{ $req->currency->code ?? 'ر.س' }}
                                            @if ($req->description)
                                                <div class="approval-meta mt-2">
                                                    <i class="ri-chat-quote-line"></i> {{ Str::limit($req->description, 120) }}
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
                                        <i class="ri-time-line"></i> {{ $req->created_at->diffForHumans() }}
                                    </div>
                                </div>
                            </div>
                            @if ($approval['show_route'])
                                <div class="team-approval-card__actions">
                                    <a href="{{ route($approval['show_route'], $req->id) }}" class="admin-btn admin-btn-primary admin-btn-sm">
                                        <i class="ri-eye-line"></i>
                                        عرض التفاصيل
                                    </a>
                                </div>
                            @endif
                        </article>
                    @empty
                        <div class="admin-empty-state team-approvals-empty">
                            <i class="ri-checkbox-circle-line"></i>
                            <strong>لا توجد طلبات معلقة</strong>
                            <span>جميع الطلبات تمت معالجتها</span>
                        </div>
                    @endforelse

                    <div class="team-approvals-empty-filtered d-none" data-empty-filtered>
                        لا توجد طلبات من هذا النوع
                    </div>
                </div>
            </div>

        </div>
    </div>
@stop
