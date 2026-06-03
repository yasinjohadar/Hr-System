@extends('admin.layouts.master')

@section('page-title')
    لوحة رئيس القسم
@stop

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin-team-dashboard.css') }}">
@endpush

@section('content')
    @php
        $attendancePct = $stats['total_employees'] > 0
            ? min(100, (int) round(($stats['present_today'] / $stats['total_employees']) * 100))
            : 0;
    @endphp

    <div class="main-content app-content admin-team-dashboard-page">
        <div class="container-fluid pt-4">

            <div class="card page-hero mb-4">
                <div class="card-body py-4">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="page-hero-icon">
                                <i class="ri-dashboard-3-line"></i>
                            </div>
                            <div>
                                <h4 class="mb-1 page-hero-title fw-bold">لوحة رئيس القسم</h4>
                                <p class="mb-0 page-hero-subtitle">إدارة الفريق والموافقات</p>
                            </div>
                        </div>
                        <div class="d-flex flex-wrap gap-2">
                            <a href="{{ route('admin.team.members') }}" class="btn btn-hero-primary btn-sm">
                                <i class="ri-team-line me-1"></i>فريق العمل
                            </a>
                            <a href="{{ route('admin.team.approvals') }}" class="btn btn-hero-outline btn-sm">
                                <i class="ri-checkbox-circle-line me-1"></i>الموافقات
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-value">{{ $stats['total_employees'] }}</div>
                                <div class="stat-label">موظفو القسم</div>
                            </div>
                            <div class="stat-icon stat-icon--primary"><i class="ri-team-line"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1 pe-2">
                                <div class="stat-value">
                                    {{ $stats['present_today'] }}
                                    <span class="fs-6 fw-normal text-muted">/ {{ $stats['total_employees'] }}</span>
                                </div>
                                <div class="stat-label">حضور اليوم</div>
                                <div class="stat-progress">
                                    <div class="stat-progress-bar" style="width: {{ $attendancePct }}%"></div>
                                </div>
                                <div class="stat-mini-pills">
                                    @if($stats['late_today'] > 0)
                                        <span class="mini-pill mini-pill--warning">{{ $stats['late_today'] }} متأخر</span>
                                    @endif
                                    @if($stats['absent_today'] > 0)
                                        <span class="mini-pill mini-pill--danger">{{ $stats['absent_today'] }} غائب</span>
                                    @endif
                                    @if($stats['on_leave_today'] > 0)
                                        <span class="mini-pill mini-pill--info">{{ $stats['on_leave_today'] }} إجازة</span>
                                    @endif
                                </div>
                            </div>
                            <div class="stat-icon stat-icon--success"><i class="ri-user-check-line"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-value">{{ $stats['my_pending_approvals'] }}</div>
                                <div class="stat-label">طلبات معلقة</div>
                                <div class="stat-mini-pills">
                                    <span class="mini-pill mini-pill--success">{{ $stats['pending_leaves'] }} إجازة</span>
                                    <span class="mini-pill mini-pill--info">{{ $stats['pending_expenses'] }} مصروفات</span>
                                </div>
                            </div>
                            <div class="stat-icon stat-icon--warning"><i class="ri-time-line"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-value">{{ $stats['month_attendance'] }}</div>
                                <div class="stat-label">حضور الشهر (يوم)</div>
                            </div>
                            <div class="stat-icon stat-icon--info"><i class="ri-calendar-check-line"></i></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-xl-8 col-lg-7">
                    <div class="content-panel">
                        <div class="content-panel-header">
                            <h5><i class="ri-checkbox-circle-line me-1 text-primary"></i>طلبات الموافقة المعلقة</h5>
                            <a href="{{ route('admin.team.approvals') }}" class="btn-panel-link">عرض الكل</a>
                        </div>
                        @forelse($pendingApprovals as $approval)
                            <div class="approval-item">
                                <div class="d-flex justify-content-between align-items-start gap-2">
                                    <div class="d-flex align-items-start gap-3 flex-grow-1">
                                        <div class="item-icon item-icon--{{ $approval['type'] === 'leave' ? 'leave' : 'expense' }}">
                                            <i class="ri-{{ $approval['type'] === 'leave' ? 'sun' : 'money-dollar-circle' }}-line"></i>
                                        </div>
                                        <div class="min-w-0">
                                            <div class="item-title">{{ $approval['request']->employee->full_name }}</div>
                                            <div class="item-meta mt-1">
                                                @if($approval['type'] === 'leave')
                                                    <span class="type-pill type-pill--leave">{{ $approval['request']->leaveType->name_ar ?? $approval['request']->leaveType->name }}</span>
                                                    {{ $approval['request']->start_date->format('Y/m/d') }} — {{ $approval['request']->end_date->format('Y/m/d') }}
                                                    ({{ $approval['request']->days_count }} يوم)
                                                @else
                                                    <span class="type-pill type-pill--expense">{{ $approval['request']->category->name_ar ?? $approval['request']->category->name }}</span>
                                                    {{ number_format($approval['request']->amount, 2) }} {{ $approval['request']->currency->code ?? 'ر.س' }}
                                                @endif
                                            </div>
                                            <div class="item-meta mt-1">
                                                <i class="ri-time-line me-1"></i>{{ $approval['request']->created_at->diffForHumans() }}
                                            </div>
                                        </div>
                                    </div>
                                    @if($approval['type'] === 'leave')
                                        <a href="{{ route('admin.leave-requests.show', $approval['request']->id) }}" class="btn-view-sm" title="عرض">
                                            <i class="ri-eye-line"></i>
                                        </a>
                                    @else
                                        <a href="{{ route('admin.expense-requests.show', $approval['request']->id) }}" class="btn-view-sm" title="عرض">
                                            <i class="ri-eye-line"></i>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="empty-state">
                                <div class="empty-icon"><i class="ri-checkbox-circle-line"></i></div>
                                <h6>لا توجد طلبات معلقة</h6>
                                <p>جميع الطلبات تمت معالجتها</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="col-xl-4 col-lg-5 d-flex flex-column gap-3">
                    <div class="content-panel">
                        <div class="content-panel-header">
                            <h5><i class="ri-building-line me-1 text-primary"></i>الأقسام المُدارة</h5>
                        </div>
                        @forelse($departments as $dept)
                            <div class="dept-item">
                                <div class="d-flex justify-content-between align-items-center gap-2">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="item-icon item-icon--dept">
                                            <i class="ri-building-2-line"></i>
                                        </div>
                                        <div>
                                            <div class="item-title">{{ $dept->name }}</div>
                                            <div class="item-meta">{{ $dept->employees_count }} موظف</div>
                                        </div>
                                    </div>
                                    <span class="status-pill status-pill--{{ $dept->is_active ? 'active' : 'inactive' }}">
                                        {{ $dept->is_active ? 'نشط' : 'غير نشط' }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="empty-state py-4">
                                <p class="mb-0">لا توجد أقسام مُدارة</p>
                            </div>
                        @endforelse
                    </div>

                    @if($upcomingMeetings->isNotEmpty())
                        <div class="content-panel">
                            <div class="content-panel-header">
                                <h5><i class="ri-calendar-event-line me-1 text-primary"></i>اجتماعات قادمة</h5>
                            </div>
                            @foreach($upcomingMeetings->take(3) as $meeting)
                                <div class="meeting-item">
                                    <div class="item-title">{{ $meeting->title }}</div>
                                    <div class="item-meta mt-1">
                                        <i class="ri-time-line me-1"></i>
                                        {{ \Carbon\Carbon::parse($meeting->start_time)->format('Y/m/d H:i') }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <div class="content-panel mb-4">
                <div class="content-panel-header">
                    <h5><i class="ri-calendar-check-line me-1 text-primary"></i>حضور اليوم — {{ today()->format('Y/m/d') }}</h5>
                    <div class="attendance-legend">
                        <span class="legend-chip mini-pill mini-pill--success">{{ $stats['present_today'] }} حاضر</span>
                        <span class="legend-chip mini-pill mini-pill--warning">{{ $stats['late_today'] }} متأخر</span>
                        <span class="legend-chip mini-pill mini-pill--danger">{{ $stats['absent_today'] }} غائب</span>
                        <span class="legend-chip mini-pill mini-pill--info">{{ $stats['on_leave_today'] }} إجازة</span>
                    </div>
                </div>
                <div class="attendance-table-wrap">
                    <div class="table-header-row">
                        <span>الموظف</span>
                        <span>القسم</span>
                        <span>المنصب</span>
                        <span>الحالة</span>
                        <span>دخول</span>
                        <span>خروج</span>
                    </div>
                    @forelse($todayAttendance as $attendance)
                        @php
                            $statusClass = match($attendance->status) {
                                'present' => 'present',
                                'absent' => 'absent',
                                'late' => 'late',
                                'on_leave' => 'on_leave',
                                default => 'inactive',
                            };
                        @endphp
                        <div class="attendance-row">
                            <div class="d-flex align-items-center gap-2">
                                <div class="employee-avatar">
                                    {{ mb_substr($attendance->employee->first_name ?? '?', 0, 1) }}
                                </div>
                                <span class="item-title">{{ $attendance->employee->full_name }}</span>
                            </div>
                            <span class="item-meta">{{ $attendance->employee->department->name ?? '—' }}</span>
                            <span class="item-meta">{{ $attendance->employee->position->title ?? '—' }}</span>
                            <span class="status-pill status-pill--{{ $statusClass }}">{{ $attendance->status_ar }}</span>
                            <span class="item-meta">{{ $attendance->check_in ? \Carbon\Carbon::parse($attendance->check_in)->format('H:i') : '—' }}</span>
                            <span class="item-meta">{{ $attendance->check_out ? \Carbon\Carbon::parse($attendance->check_out)->format('H:i') : '—' }}</span>
                        </div>
                    @empty
                        <div class="empty-state">
                            <p class="mb-0">لا توجد سجلات حضور اليوم</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="content-panel">
                <div class="content-panel-header">
                    <h5><i class="ri-sun-line me-1 text-primary"></i>آخر طلبات الإجازة</h5>
                </div>
                <div class="leave-table-wrap">
                    <div class="table-header-row">
                        <span>الموظف</span>
                        <span>نوع الإجازة</span>
                        <span>من</span>
                        <span>إلى</span>
                        <span>الأيام</span>
                        <span>الحالة</span>
                        <span>تاريخ الطلب</span>
                    </div>
                    @forelse($recentLeaves as $leave)
                        @php
                            $leaveStatusClass = match($leave->status) {
                                'approved' => 'approved',
                                'rejected' => 'rejected',
                                default => 'pending',
                            };
                        @endphp
                        <div class="leave-row">
                            <span class="item-title">{{ $leave->employee->full_name }}</span>
                            <span class="item-meta">{{ $leave->leaveType->name_ar ?? $leave->leaveType->name }}</span>
                            <span class="item-meta">{{ $leave->start_date->format('Y/m/d') }}</span>
                            <span class="item-meta">{{ $leave->end_date->format('Y/m/d') }}</span>
                            <span class="item-meta">{{ $leave->days_count }}</span>
                            <span class="status-pill status-pill--{{ $leaveStatusClass }}">{{ $leave->status_name_ar }}</span>
                            <span class="item-meta">{{ $leave->created_at->format('Y/m/d H:i') }}</span>
                        </div>
                    @empty
                        <div class="empty-state">
                            <p class="mb-0">لا توجد طلبات إجازة</p>
                        </div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
@stop
