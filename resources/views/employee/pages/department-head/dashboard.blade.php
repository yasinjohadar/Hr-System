@extends('employee.layouts.master')

@section('page-title')
    لوحة رئيس القسم
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <!-- Page Header -->
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div>
                    <h5 class="page-title fs-21 mb-1">لوحة رئيس القسم</h5>
                    <p class="text-muted fs-13 mb-0">إدارة الفريق والموافقات</p>
                </div>
                <div class="d-flex gap-2 mt-2 mt-md-0">
                    <a href="{{ route('employee.department-head.team') }}" class="btn btn-primary btn-sm">
                        <i class="ri-team-line me-1"></i>فريق العمل
                    </a>
                    <a href="{{ route('employee.department-head.approvals') }}" class="btn btn-outline-primary btn-sm">
                        <i class="ri-checkbox-circle-line me-1"></i>الموافقات
                    </a>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="row mb-4">
                <div class="col-xl-3 col-md-6">
                    <div class="card custom-card overflow-hidden">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-xl bg-primary-transparent avatar-rounded me-3">
                                    <i class="ri-team-line fs-20 text-primary"></i>
                                </div>
                                <div>
                                    <p class="text-muted mb-0 fs-13">موظفو القسم</p>
                                    <h4 class="mb-0 fw-semibold">{{ $stats['total_employees'] }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="card custom-card overflow-hidden">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-xl bg-success-transparent avatar-rounded me-3">
                                    <i class="ri-user-check-line fs-20 text-success"></i>
                                </div>
                                <div>
                                    <p class="text-muted mb-0 fs-13">حضور اليوم</p>
                                    <h4 class="mb-0 fw-semibold">{{ $stats['present_today'] }} <span class="fs-14 text-muted">/ {{ $stats['total_employees'] }}</span></h4>
                                </div>
                            </div>
                            <div class="mt-2">
                                @if($stats['late_today'] > 0)
                                    <span class="badge bg-warning-transparent">{{ $stats['late_today'] }} متأخر</span>
                                @endif
                                @if($stats['absent_today'] > 0)
                                    <span class="badge bg-danger-transparent ms-1">{{ $stats['absent_today'] }} غائب</span>
                                @endif
                                @if($stats['on_leave_today'] > 0)
                                    <span class="badge bg-info-transparent ms-1">{{ $stats['on_leave_today'] }} إجازة</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="card custom-card overflow-hidden">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-xl bg-warning-transparent avatar-rounded me-3">
                                    <i class="ri-time-line fs-20 text-warning"></i>
                                </div>
                                <div>
                                    <p class="text-muted mb-0 fs-13">طلبات معلقة</p>
                                    <h4 class="mb-0 fw-semibold">{{ $stats['my_pending_approvals'] }}</h4>
                                </div>
                            </div>
                            <div class="mt-2">
                                <span class="badge bg-success-transparent">{{ $stats['pending_leaves'] }} إجازة</span>
                                <span class="badge bg-info-transparent ms-1">{{ $stats['pending_expenses'] }} مصروفات</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="card custom-card overflow-hidden">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-xl bg-info-transparent avatar-rounded me-3">
                                    <i class="ri-calendar-check-line fs-20 text-info"></i>
                                </div>
                                <div>
                                    <p class="text-muted mb-0 fs-13">حضور الشهر</p>
                                    <h4 class="mb-0 fw-semibold">{{ $stats['month_attendance'] }} <span class="fs-14 text-muted">يوم</span></h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content Row -->
            <div class="row mb-4">
                <!-- Pending Approvals -->
                <div class="col-xl-8 col-lg-7">
                    <div class="card custom-card">
                        <div class="card-header justify-content-between">
                            <h6 class="card-title fw-semibold">
                                <i class="ri-checkbox-circle-line me-1"></i>طلبات الموافقة المعلقة
                            </h6>
                            <a href="{{ route('employee.department-head.approvals') }}" class="btn btn-sm btn-outline-primary">عرض الكل</a>
                        </div>
                        <div class="card-body p-0">
                            @forelse($pendingApprovals as $approval)
                                <div class="p-3 border-bottom">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="d-flex align-items-start">
                                            <div class="avatar avatar-sm bg-{{ $approval['type'] === 'leave' ? 'success' : 'info' }}-transparent avatar-rounded me-3 mt-1">
                                                <i class="ri-{{ $approval['type'] === 'leave' ? 'sun' : 'money-dollar-circle' }}-line text-{{ $approval['type'] === 'leave' ? 'success' : 'info' }}"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-1 fs-14 fw-semibold">
                                                    {{ $approval['request']->employee->full_name }}
                                                </h6>
                                                <p class="text-muted fs-13 mb-1">
                                                    @if($approval['type'] === 'leave')
                                                        <span class="badge bg-success-transparent me-1">{{ $approval['request']->leaveType->name_ar ?? $approval['request']->leaveType->name }}</span>
                                                        {{ $approval['request']->start_date->format('Y/m/d') }} - {{ $approval['request']->end_date->format('Y/m/d') }}
                                                        ({{ $approval['request']->days_count }} يوم)
                                                    @else
                                                        <span class="badge bg-info-transparent me-1">{{ $approval['request']->category->name_ar ?? $approval['request']->category->name }}</span>
                                                        {{ number_format($approval['request']->amount, 2) }} {{ $approval['request']->currency->code ?? 'ر.س' }}
                                                    @endif
                                                </p>
                                                <small class="text-muted">
                                                    <i class="ri-time-line me-1"></i>
                                                    {{ $approval['request']->created_at->diffForHumans() }}
                                                </small>
                                            </div>
                                        </div>
                                        <div class="d-flex gap-2">
                                            @if($approval['type'] === 'leave')
                                                <a href="{{ route('admin.leave-requests.show', $approval['request']->id) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="ri-eye-line"></i>
                                                </a>
                                            @else
                                                <a href="{{ route('admin.expense-requests.show', $approval['request']->id) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="ri-eye-line"></i>
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center text-muted py-5">
                                    <i class="ri-checkbox-circle-line fs-32 d-block mb-3 text-success"></i>
                                    <h6>لا توجد طلبات معلقة</h6>
                                    <p class="fs-13">جميع الطلبات تمت معالجتها</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Departments & Quick Info -->
                <div class="col-xl-4 col-lg-5">
                    <!-- Departments -->
                    <div class="card custom-card mb-4">
                        <div class="card-header">
                            <h6 class="card-title fw-semibold">
                                <i class="ri-building-line me-1"></i>الأقسام المُدارة
                            </h6>
                        </div>
                        <div class="card-body p-0">
                            @forelse($departments as $dept)
                                <div class="p-3 border-bottom">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1 fs-14 fw-semibold">{{ $dept->name }}</h6>
                                            <small class="text-muted">{{ $dept->employees_count }} موظف</small>
                                        </div>
                                        <span class="badge bg-{{ $dept->is_active ? 'success' : 'danger' }}-transparent">
                                            {{ $dept->is_active ? 'نشط' : 'غير نشط' }}
                                        </span>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center text-muted py-3">
                                    لا توجد أقسام مُدارة
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Upcoming Meetings -->
                    @if($upcomingMeetings->isNotEmpty())
                        <div class="card custom-card">
                            <div class="card-header">
                                <h6 class="card-title fw-semibold">
                                    <i class="ri-calendar-event-line me-1"></i>اجتماعات قادمة
                                </h6>
                            </div>
                            <div class="card-body p-0">
                                @foreach($upcomingMeetings->take(3) as $meeting)
                                    <div class="p-3 border-bottom">
                                        <h6 class="mb-1 fs-14 fw-semibold">{{ $meeting->title }}</h6>
                                        <p class="text-muted fs-13 mb-0">
                                            <i class="ri-time-line me-1"></i>
                                            {{ \Carbon\Carbon::parse($meeting->start_time)->format('Y/m/d H:i') }}
                                        </p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Today Attendance -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card custom-card">
                        <div class="card-header justify-content-between">
                            <h6 class="card-title fw-semibold">
                                <i class="ri-calendar-check-line me-1"></i>حضور اليوم - {{ today()->format('Y/m/d') }}
                            </h6>
                            <div>
                                <span class="badge bg-success-transparent me-2">
                                    <i class="ri-check-line me-1"></i>{{ $stats['present_today'] }} حاضر
                                </span>
                                <span class="badge bg-warning-transparent me-2">
                                    <i class="ri-time-line me-1"></i>{{ $stats['late_today'] }} متأخر
                                </span>
                                <span class="badge bg-danger-transparent me-2">
                                    <i class="ri-close-line me-1"></i>{{ $stats['absent_today'] }} غائب
                                </span>
                                <span class="badge bg-info-transparent">
                                    <i class="ri-sun-line me-1"></i>{{ $stats['on_leave_today'] }} إجازة
                                </span>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover text-nowrap mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>الموظف</th>
                                            <th>القسم</th>
                                            <th>المنصب</th>
                                            <th>الحالة</th>
                                            <th>دخول</th>
                                            <th>خروج</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($todayAttendance as $attendance)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar avatar-sm bg-primary-transparent avatar-rounded me-2">
                                                            {{ substr($attendance->employee->first_name, 0, 1) }}
                                                        </div>
                                                        <span class="fw-medium">{{ $attendance->employee->full_name }}</span>
                                                    </div>
                                                </td>
                                                <td>{{ $attendance->employee->department->name ?? '-' }}</td>
                                                <td>{{ $attendance->employee->position->title ?? '-' }}</td>
                                                <td>
                                                    <span class="badge bg-{{ $attendance->status === 'present' ? 'success' : ($attendance->status === 'absent' ? 'danger' : ($attendance->status === 'late' ? 'warning' : 'info')) }}-transparent">
                                                        {{ $attendance->status_name_ar }}
                                                    </span>
                                                </td>
                                                <td>{{ $attendance->check_in ? \Carbon\Carbon::parse($attendance->check_in)->format('H:i') : '-' }}</td>
                                                <td>{{ $attendance->check_out ? \Carbon\Carbon::parse($attendance->check_out)->format('H:i') : '-' }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center text-muted py-4">
                                                    لا توجد سجلات حضور اليوم
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Leaves -->
            <div class="row">
                <div class="col-12">
                    <div class="card custom-card">
                        <div class="card-header">
                            <h6 class="card-title fw-semibold">
                                <i class="ri-sun-line me-1"></i>آخر طلبات الإجازة
                            </h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover text-nowrap mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>الموظف</th>
                                            <th>نوع الإجازة</th>
                                            <th>من</th>
                                            <th>إلى</th>
                                            <th>الأيام</th>
                                            <th>الحالة</th>
                                            <th>تاريخ الطلب</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($recentLeaves as $leave)
                                            <tr>
                                                <td>{{ $leave->employee->full_name }}</td>
                                                <td>{{ $leave->leaveType->name_ar ?? $leave->leaveType->name }}</td>
                                                <td>{{ $leave->start_date->format('Y/m/d') }}</td>
                                                <td>{{ $leave->end_date->format('Y/m/d') }}</td>
                                                <td>{{ $leave->days_count }}</td>
                                                <td>
                                                    <span class="badge bg-{{ $leave->status === 'approved' ? 'success' : ($leave->status === 'rejected' ? 'danger' : 'warning') }}-transparent">
                                                        {{ $leave->status_name_ar }}
                                                    </span>
                                                </td>
                                                <td>{{ $leave->created_at->format('Y/m/d H:i') }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center text-muted py-4">
                                                    لا توجد طلبات إجازة
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@stop
