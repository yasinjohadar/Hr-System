@extends('admin.layouts.master')

@section('page-title')
    تفاصيل سجل الحضور
@stop

@section('css')
    <style>
        .attendance-show-hero {
            background: linear-gradient(145deg, var(--bs-primary) 0%, rgba(var(--bs-primary-rgb), 0.88) 55%, rgb(15, 76, 129) 100%);
            color: #fff;
            border: none;
        }
        .attendance-show-hero .text-white-75 { color: rgba(255,255,255,.85) !important; }
    </style>
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">تفاصيل سجل الحضور</h5>
                    <p class="text-muted small mb-0">{{ $attendance->attendance_date->format('Y-m-d') }} — {{ $attendance->employee->full_name ?? $attendance->employee->first_name . ' ' . $attendance->employee->last_name }}</p>
                </div>
                <div class="d-flex flex-wrap gap-2 mt-2 mt-md-0">
                    <a href="{{ route('admin.attendances.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-right me-1"></i>العودة للقائمة
                    </a>
                    @can('attendance-edit')
                        <a href="{{ route('admin.attendances.edit', $attendance->id) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit me-1"></i>تعديل
                        </a>
                    @endcan
                </div>
            </div>

            <div class="row g-3">
                <div class="col-lg-4">
                    <div class="card attendance-show-hero shadow-sm h-100">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex align-items-start gap-3 mb-3">
                                <span class="avatar avatar-md bg-white bg-opacity-25 rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width:3rem;height:3rem;">
                                    <i class="fas fa-user-check fs-4"></i>
                                </span>
                                <div class="min-w-0">
                                    <div class="text-white-75 small mb-1">الموظف</div>
                                    <div class="fw-semibold fs-6 text-truncate">{{ $attendance->employee->full_name ?? $attendance->employee->first_name . ' ' . $attendance->employee->last_name }}</div>
                                    <div class="small text-white-75 font-monospace">{{ $attendance->employee->employee_code ?? '—' }}</div>
                                </div>
                            </div>
                            <div class="mb-3 pb-3 border-bottom border-white border-opacity-25">
                                <div class="text-white-75 small mb-1"><i class="far fa-calendar me-1"></i>تاريخ الحضور</div>
                                <div class="fs-5 fw-semibold">{{ $attendance->attendance_date->format('Y-m-d') }}</div>
                            </div>
                            <div class="mb-3">
                                <div class="text-white-75 small mb-2">الحالة</div>
                                @switch($attendance->status)
                                    @case('present')
                                        <span class="badge bg-success fs-14 px-3 py-2">حاضر</span>
                                        @break
                                    @case('absent')
                                        <span class="badge bg-danger fs-14 px-3 py-2">غائب</span>
                                        @break
                                    @case('late')
                                        <span class="badge bg-warning text-dark fs-14 px-3 py-2">متأخر</span>
                                        @break
                                    @case('half_day')
                                        <span class="badge bg-info fs-14 px-3 py-2">نصف يوم</span>
                                        @break
                                    @case('on_leave')
                                        <span class="badge bg-secondary fs-14 px-3 py-2">في إجازة</span>
                                        @break
                                    @default
                                        <span class="badge bg-primary fs-14 px-3 py-2">عطلة</span>
                                @endswitch
                            </div>
                            <div class="mt-auto pt-3 border-top border-white border-opacity-25">
                                <div class="text-white-75 small mb-1">ساعات العمل</div>
                                @if ($attendance->hours_worked > 0)
                                    <div class="fs-3 fw-bold lh-1">{{ $attendance->hours_worked_formatted }}</div>
                                @else
                                    <div class="fs-3 fw-bold lh-1">—</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-light py-3 border-bottom">
                            <h6 class="mb-0 fw-semibold">
                                <i class="fas fa-clock text-primary me-2"></i>تفاصيل أوقات الحضور والانصراف
                            </h6>
                            <small class="text-muted">أوقات الدخول والخروج والإضافي والتأخير</small>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <tbody>
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle" style="width:40%">
                                                <i class="fas fa-sign-in-alt text-success me-2"></i>وقت الدخول
                                            </th>
                                            <td class="pe-4 py-3 align-middle">
                                                @if ($attendance->check_in)
                                                    <span class="badge bg-success-subtle text-success border fs-14">{{ is_string($attendance->check_in) ? $attendance->check_in : $attendance->check_in->format('H:i') }}</span>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-sign-out-alt text-info me-2"></i>وقت الخروج
                                            </th>
                                            <td class="pe-4 py-3 align-middle">
                                                @if ($attendance->check_out)
                                                    <span class="badge bg-info-subtle text-dark border fs-14">{{ is_string($attendance->check_out) ? $attendance->check_out : $attendance->check_out->format('H:i') }}</span>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-clock text-muted me-2"></i>الوقت المتوقع
                                            </th>
                                            <td class="pe-4 py-3 align-middle fw-semibold">
                                                {{ $attendance->expected_check_in ?? '09:00' }}
                                                <span class="text-muted mx-1">—</span>
                                                {{ $attendance->expected_check_out ?? '17:00' }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-hourglass-half text-warning me-2"></i>دقائق التأخير
                                            </th>
                                            <td class="pe-4 py-3 align-middle">
                                                @if ($attendance->late_minutes > 0)
                                                    <span class="badge bg-warning text-dark fs-14">{{ $attendance->late_minutes }} دقيقة</span>
                                                @else
                                                    <span class="text-success fw-semibold">لا يوجد</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-plus-circle text-success me-2"></i>ساعات إضافية
                                            </th>
                                            <td class="pe-4 py-3 align-middle">
                                                @if ($attendance->overtime_minutes > 0)
                                                    <span class="badge bg-success fs-14">{{ floor($attendance->overtime_minutes / 60) }}:{{ str_pad($attendance->overtime_minutes % 60, 2, '0', STR_PAD_LEFT) }}</span>
                                                @else
                                                    <span class="text-muted">لا يوجد</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-minus-circle text-danger me-2"></i>انصراف مبكر
                                            </th>
                                            <td class="pe-4 py-3 align-middle">
                                                @if ($attendance->early_leave_minutes > 0)
                                                    <span class="badge bg-danger fs-14">{{ $attendance->early_leave_minutes }} دقيقة</span>
                                                @else
                                                    <span class="text-success fw-semibold">لا يوجد</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @if ($attendance->notes)
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-sticky-note text-muted me-2"></i>ملاحظات
                                            </th>
                                            <td class="pe-4 py-3 align-middle">{{ $attendance->notes }}</td>
                                        </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-3 mt-1 mb-4">
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-light py-3">
                            <h6 class="mb-0 fw-semibold">
                                <i class="fas fa-clock-rotate-left text-primary me-2"></i>بيانات السجل
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row row-cols-1 row-cols-md-2 g-0">
                                <div class="col border-bottom border-end-md p-3">
                                    <div class="small text-muted mb-1"><i class="fas fa-user-pen me-1"></i>أنشأ بواسطة</div>
                                    <div class="fw-semibold">{{ $attendance->creator->name ?? '—' }}</div>
                                </div>
                                <div class="col border-bottom p-3">
                                    <div class="small text-muted mb-1"><i class="far fa-clock me-1"></i>تاريخ الإنشاء</div>
                                    <div class="fw-semibold font-monospace small">{{ $attendance->created_at->format('Y-m-d H:i') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
