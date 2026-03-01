@extends('employee.layouts.master')

@section('page-title')
    لوحة تحكم الموظف
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">لوحة تحكم الموظف</h5>
                </div>
            </div>

            <!-- إحصائيات سريعة -->
            <div class="row mb-3">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h6 class="card-title">طلبات إجازة قيد الانتظار</h6>
                            <h2 class="mb-0">{{ $stats['pending_leaves'] }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h6 class="card-title">إجازات مقبولة</h6>
                            <h2 class="mb-0">{{ $stats['approved_leaves'] }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <h6 class="card-title">حضور الشهر الحالي</h6>
                            <h2 class="mb-0">{{ $stats['total_attendance'] }} يوم</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <h6 class="card-title">أهداف قيد التنفيذ</h6>
                            <h2 class="mb-0">{{ $stats['pending_goals'] }}</h2>
                        </div>
                    </div>
                </div>
            </div>

            <!-- معلومات الموظف -->
            <div class="row mb-3">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body text-center">
                            @if ($employee->photo)
                                <img src="{{ asset('storage/' . $employee->photo) }}" alt="صورة الموظف" class="rounded-circle mb-3" style="width: 120px; height: 120px; object-fit: cover;">
                            @else
                                <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 120px; height: 120px; font-size: 48px;">
                                    {{ substr($employee->first_name, 0, 1) }}
                                </div>
                            @endif
                            <h5 class="mb-1">{{ $employee->full_name }}</h5>
                            <p class="text-muted mb-1">{{ $employee->employee_code }}</p>
                            <p class="text-muted mb-0">{{ $employee->position->title ?? '-' }} - {{ $employee->department->name ?? '-' }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">معلومات سريعة</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">البريد الإلكتروني:</label>
                                    <p class="form-control-plaintext">{{ $employee->work_email ?? $employee->personal_email ?? '-' }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">رقم الهاتف:</label>
                                    <p class="form-control-plaintext">{{ $employee->work_phone ?? $employee->personal_phone ?? '-' }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">تاريخ التوظيف:</label>
                                    <p class="form-control-plaintext">{{ $employee->hire_date->format('Y-m-d') }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">الحالة:</label>
                                    <p class="form-control-plaintext">
                                        <span class="badge bg-{{ $employee->is_active ? 'success' : 'danger' }}">
                                            {{ $employee->is_active ? 'نشط' : 'غير نشط' }}
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- آخر الإجازات -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">آخر طلبات الإجازة</h5>
                        </div>
                        <div class="card-body">
                            @forelse ($recentLeaves as $leave)
                                <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                    <div>
                                        <strong>{{ $leave->leaveType->name_ar ?? $leave->leaveType->name }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $leave->start_date->format('Y-m-d') }} - {{ $leave->end_date->format('Y-m-d') }}</small>
                                    </div>
                                    <span class="badge bg-{{ $leave->status == 'approved' ? 'success' : ($leave->status == 'rejected' ? 'danger' : 'warning') }}">
                                        {{ $leave->status_name_ar }}
                                    </span>
                                </div>
                            @empty
                                <p class="text-muted text-center">لا توجد طلبات إجازة</p>
                            @endforelse
                            <div class="text-center mt-3">
                                <a href="{{ route('employee.leaves') }}" class="btn btn-sm btn-primary">عرض الكل</a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- آخر الحضور -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">آخر سجلات الحضور</h5>
                        </div>
                        <div class="card-body">
                            @forelse ($recentAttendance as $attendance)
                                <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                    <div>
                                        <strong>{{ $attendance->attendance_date->format('Y-m-d') }}</strong>
                                        <br>
                                        <small class="text-muted">
                                            {{ $attendance->check_in ? \Carbon\Carbon::parse($attendance->check_in)->format('H:i') : '-' }} - 
                                            {{ $attendance->check_out ? \Carbon\Carbon::parse($attendance->check_out)->format('H:i') : '-' }}
                                        </small>
                                    </div>
                                    <span class="badge bg-{{ $attendance->status == 'present' ? 'success' : ($attendance->status == 'absent' ? 'danger' : 'warning') }}">
                                        {{ $attendance->status_name_ar }}
                                    </span>
                                </div>
                            @empty
                                <p class="text-muted text-center">لا توجد سجلات حضور</p>
                            @endforelse
                            <div class="text-center mt-3">
                                <a href="{{ route('employee.attendance') }}" class="btn btn-sm btn-primary">عرض الكل</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- روابط سريعة -->
            <div class="row">
                <div class="col-md-3 mb-2">
                    <a href="{{ route('employee.profile') }}" class="btn btn-outline-primary w-100">
                        <i class="fas fa-user me-2"></i>الملف الشخصي
                    </a>
                </div>
                <div class="col-md-3 mb-2">
                    <a href="{{ route('employee.leaves') }}" class="btn btn-outline-success w-100">
                        <i class="fas fa-calendar-times me-2"></i>الإجازات
                    </a>
                </div>
                <div class="col-md-3 mb-2">
                    <a href="{{ route('employee.attendance') }}" class="btn btn-outline-info w-100">
                        <i class="fas fa-calendar-check me-2"></i>الحضور
                    </a>
                </div>
                <div class="col-md-3 mb-2">
                    <a href="{{ route('employee.salaries') }}" class="btn btn-outline-warning w-100">
                        <i class="fas fa-money-bill-wave me-2"></i>الرواتب
                    </a>
                </div>
            </div>
        </div>
    </div>
@stop


