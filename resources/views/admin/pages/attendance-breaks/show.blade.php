@extends('admin.layouts.master')

@section('page-title')
    تفاصيل الاستراحة
@stop

@section('css')
    <style>
        .break-show-hero {
            background: linear-gradient(145deg, var(--bs-primary) 0%, rgba(var(--bs-primary-rgb), 0.88) 55%, rgb(15, 76, 129) 100%);
            color: #fff;
            border: none;
        }
        .break-show-hero .text-white-75 { color: rgba(255,255,255,.85) !important; }
    </style>
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">تفاصيل الاستراحة</h5>
                    <p class="text-muted small mb-0">{{ $break->break_type_name_ar }} — {{ $break->attendance->employee->full_name ?? '—' }}</p>
                </div>
                <div class="d-flex flex-wrap gap-2 mt-2 mt-md-0">
                    <a href="{{ route('admin.attendance-breaks.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-right me-1"></i>العودة للقائمة
                    </a>
                    @can('attendance-break-edit')
                        <a href="{{ route('admin.attendance-breaks.edit', $break->id) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit me-1"></i>تعديل
                        </a>
                    @endcan
                </div>
            </div>

            <div class="row g-3">
                <div class="col-lg-4">
                    <div class="card break-show-hero shadow-sm h-100">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex align-items-start gap-3 mb-3">
                                <span class="avatar avatar-md bg-white bg-opacity-25 rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width:3rem;height:3rem;">
                                    <i class="fas fa-mug-hot fs-4"></i>
                                </span>
                                <div class="min-w-0">
                                    <div class="text-white-75 small mb-1">الاستراحة</div>
                                    <div class="fw-semibold fs-6 text-truncate">{{ $break->break_type_name_ar }}</div>
                                    <div class="small text-white-75">{{ $break->attendance->attendance_date }}</div>
                                </div>
                            </div>
                            <div class="mb-3 pb-3 border-bottom border-white border-opacity-25">
                                <div class="text-white-75 small mb-1"><i class="fas fa-user me-1"></i>الموظف</div>
                                <div class="fw-semibold">{{ $break->attendance->employee->full_name ?? '—' }}</div>
                                <div class="small text-white-75 font-monospace">{{ $break->attendance->employee->employee_code ?? '—' }}</div>
                            </div>
                            <div class="mt-auto pt-3 border-top border-white border-opacity-25">
                                <div class="text-white-75 small mb-1">المدة</div>
                                <div class="display-6 fw-bold lh-1">{{ $break->duration_minutes }}</div>
                                <div class="small text-white-75 mt-1">دقيقة</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-light py-3 border-bottom">
                            <h6 class="mb-0 fw-semibold">
                                <i class="fas fa-clock text-primary me-2"></i>تفاصيل أوقات الاستراحة
                            </h6>
                            <small class="text-muted">وقت البدء والانتهاء والمدة</small>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <tbody>
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle" style="width:40%">
                                                <i class="fas fa-tag text-muted me-2"></i>نوع الاستراحة
                                            </th>
                                            <td class="pe-4 py-3 align-middle">
                                                <span class="badge bg-info-subtle text-dark border">{{ $break->break_type_name_ar }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-play text-success me-2"></i>وقت البدء
                                            </th>
                                            <td class="pe-4 py-3 align-middle">
                                                <span class="badge bg-success-subtle text-success border fs-14">{{ $break->break_start }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-stop text-danger me-2"></i>وقت الانتهاء
                                            </th>
                                            <td class="pe-4 py-3 align-middle">
                                                @if($break->break_end)
                                                    <span class="badge bg-danger-subtle text-danger border fs-14">{{ $break->break_end }}</span>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-hourglass-half text-muted me-2"></i>المدة
                                            </th>
                                            <td class="pe-4 py-3 align-middle fw-semibold">{{ $break->duration_minutes }} <span class="text-muted small">دقيقة</span></td>
                                        </tr>
                                        @if($break->notes)
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-sticky-note text-muted me-2"></i>ملاحظات
                                            </th>
                                            <td class="pe-4 py-3 align-middle">{{ $break->notes }}</td>
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
                            <div class="row row-cols-1 g-0">
                                <div class="col p-3">
                                    <div class="small text-muted mb-1"><i class="far fa-clock me-1"></i>تاريخ الإنشاء</div>
                                    <div class="fw-semibold font-monospace small">{{ $break->created_at->format('Y-m-d H:i') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
