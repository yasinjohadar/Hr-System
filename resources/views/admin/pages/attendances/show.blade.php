@extends('admin.layouts.master')

@section('page-title')
    تفاصيل سجل الحضور
@stop

@section('css')
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">تفاصيل سجل الحضور</h5>
                </div>
                <div>
                    <a href="{{ route('admin.attendances.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-right me-2"></i>العودة للقائمة
                    </a>
                    @can('attendance-edit')
                    <a href="{{ route('admin.attendances.edit', $attendance->id) }}" class="btn btn-primary">
                        <i class="fas fa-edit me-2"></i>تعديل
                    </a>
                    @endcan
                </div>
            </div>

            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">معلومات سجل الحضور</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">الموظف:</label>
                                    <p class="form-control-plaintext">
                                        <strong>{{ $attendance->employee->full_name ?? $attendance->employee->first_name . ' ' . $attendance->employee->last_name }}</strong>
                                        <br><small class="text-muted">{{ $attendance->employee->employee_code ?? '' }}</small>
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">تاريخ الحضور:</label>
                                    <p class="form-control-plaintext">{{ $attendance->attendance_date->format('Y-m-d') }}</p>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label fw-bold">وقت الدخول:</label>
                                    <p class="form-control-plaintext">
                                        @if ($attendance->check_in)
                                            <span class="badge bg-success">{{ is_string($attendance->check_in) ? $attendance->check_in : $attendance->check_in->format('H:i') }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </p>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label fw-bold">وقت الخروج:</label>
                                    <p class="form-control-plaintext">
                                        @if ($attendance->check_out)
                                            <span class="badge bg-info">{{ is_string($attendance->check_out) ? $attendance->check_out : $attendance->check_out->format('H:i') }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </p>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label fw-bold">وقت الدخول المتوقع:</label>
                                    <p class="form-control-plaintext">{{ $attendance->expected_check_in ?? '09:00' }}</p>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label fw-bold">وقت الخروج المتوقع:</label>
                                    <p class="form-control-plaintext">{{ $attendance->expected_check_out ?? '17:00' }}</p>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label fw-bold">ساعات العمل:</label>
                                    <p class="form-control-plaintext">
                                        @if ($attendance->hours_worked > 0)
                                            <span class="badge bg-primary">{{ $attendance->hours_worked_formatted }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </p>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label fw-bold">دقائق التأخير:</label>
                                    <p class="form-control-plaintext">
                                        @if ($attendance->late_minutes > 0)
                                            <span class="badge bg-warning">{{ $attendance->late_minutes }} دقيقة</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </p>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label fw-bold">ساعات إضافية:</label>
                                    <p class="form-control-plaintext">
                                        @if ($attendance->overtime_minutes > 0)
                                            <span class="badge bg-success">{{ floor($attendance->overtime_minutes / 60) }}:{{ str_pad($attendance->overtime_minutes % 60, 2, '0', STR_PAD_LEFT) }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </p>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label fw-bold">انصراف مبكر:</label>
                                    <p class="form-control-plaintext">
                                        @if ($attendance->early_leave_minutes > 0)
                                            <span class="badge bg-danger">{{ $attendance->early_leave_minutes }} دقيقة</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">الحالة:</label>
                                    <p class="form-control-plaintext">
                                        @if ($attendance->status == 'present')
                                            <span class="badge bg-success">حاضر</span>
                                        @elseif ($attendance->status == 'absent')
                                            <span class="badge bg-danger">غائب</span>
                                        @elseif ($attendance->status == 'late')
                                            <span class="badge bg-warning">متأخر</span>
                                        @elseif ($attendance->status == 'half_day')
                                            <span class="badge bg-info">نصف يوم</span>
                                        @elseif ($attendance->status == 'on_leave')
                                            <span class="badge bg-secondary">في إجازة</span>
                                        @else
                                            <span class="badge bg-primary">عطلة</span>
                                        @endif
                                    </p>
                                </div>
                                @if ($attendance->notes)
                                <div class="col-12 mb-3">
                                    <label class="form-label fw-bold">ملاحظات:</label>
                                    <p class="form-control-plaintext">{{ $attendance->notes }}</p>
                                </div>
                                @endif
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">تاريخ الإنشاء:</label>
                                    <p class="form-control-plaintext">{{ $attendance->created_at->format('Y-m-d H:i') }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">أنشأ بواسطة:</label>
                                    <p class="form-control-plaintext">{{ $attendance->creator->name ?? '-' }}</p>
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

