@extends('admin.layouts.master')

@section('page-title')
    تفاصيل سجل التدريب
@stop

@section('css')
    <style>
        .record-show-hero {
            background: linear-gradient(145deg, var(--bs-primary) 0%, rgba(var(--bs-primary-rgb), 0.88) 55%, rgb(15, 76, 129) 100%);
            color: #fff;
            border: none;
        }
        .record-show-hero .text-white-75 { color: rgba(255,255,255,.85) !important; }
    </style>
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">تفاصيل سجل التدريب</h5>
                    <p class="text-muted small mb-0">{{ $record->employee->full_name ?? '' }} — {{ $record->training->title_ar ?? $record->training->title }}</p>
                </div>
                <div class="d-flex flex-wrap gap-2 mt-2 mt-md-0">
                    <a href="{{ route('admin.training-records.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-right me-1"></i>العودة للقائمة
                    </a>
                    @can('training-record-edit')
                        <a href="{{ route('admin.training-records.edit', $record->id) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit me-1"></i>تعديل
                        </a>
                    @endcan
                </div>
            </div>

            <div class="row g-3">
                <div class="col-lg-4">
                    <div class="card record-show-hero shadow-sm h-100">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex align-items-start gap-3 mb-3">
                                <span class="avatar avatar-md bg-white bg-opacity-25 rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width:3rem;height:3rem;">
                                    <i class="fas fa-graduation-cap fs-4"></i>
                                </span>
                                <div class="min-w-0">
                                    <div class="text-white-75 small mb-1">سجل التدريب</div>
                                    <div class="fw-semibold fs-6 text-truncate">{{ $record->employee->full_name ?? $record->employee->first_name . ' ' . $record->employee->last_name }}</div>
                                    <div class="small text-white-75 font-monospace">{{ $record->employee->employee_code ?? '—' }}</div>
                                </div>
                            </div>
                            <div class="mb-3 pb-3 border-bottom border-white border-opacity-25">
                                <div class="text-white-75 small mb-1"><i class="fas fa-bookmark me-1"></i>الدورة التدريبية</div>
                                <div class="fw-semibold">{{ $record->training->title_ar ?? $record->training->title }}</div>
                                <div class="small text-white-75 font-monospace">{{ $record->training->code }}</div>
                            </div>
                            <div class="mb-3">
                                <div class="text-white-75 small mb-2">الحالة</div>
                                @switch($record->status)
                                    @case('completed')
                                        <span class="badge bg-success fs-14 px-3 py-2">مكتمل</span>
                                        @break
                                    @case('attending')
                                        <span class="badge bg-primary fs-14 px-3 py-2">يحضر</span>
                                        @break
                                    @case('registered')
                                        <span class="badge bg-info fs-14 px-3 py-2">مسجل</span>
                                        @break
                                    @case('failed')
                                        <span class="badge bg-danger fs-14 px-3 py-2">فاشل</span>
                                        @break
                                    @default
                                        <span class="badge bg-secondary fs-14 px-3 py-2">ملغي</span>
                                @endswitch
                            </div>
                            <div class="mt-auto pt-3 border-top border-white border-opacity-25">
                                <div class="text-white-75 small mb-1">النتيجة</div>
                                @if ($record->score)
                                    <div class="display-6 fw-bold lh-1">{{ number_format($record->score, 2) }}%</div>
                                    <div class="small text-white-75 mt-1">{{ $record->score_rating }}</div>
                                @else
                                    <div class="display-6 fw-bold lh-1">—</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-light py-3 border-bottom">
                            <h6 class="mb-0 fw-semibold">
                                <i class="fas fa-circle-info text-primary me-2"></i>تفاصيل السجل
                            </h6>
                            <small class="text-muted">التواريخ والشهادة والتقييم</small>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <tbody>
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle" style="width:35%">
                                                <i class="fas fa-calendar-plus text-muted me-2"></i>تاريخ التسجيل
                                            </th>
                                            <td class="pe-4 py-3 align-middle fw-semibold">
                                                {{ $record->registration_date ? $record->registration_date->format('Y-m-d') : '—' }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-calendar-check text-muted me-2"></i>تاريخ الإتمام
                                            </th>
                                            <td class="pe-4 py-3 align-middle fw-semibold {{ $record->status == 'completed' ? 'text-success' : '' }}">
                                                {{ $record->completion_date ? $record->completion_date->format('Y-m-d') : '—' }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-certificate text-muted me-2"></i>الشهادة
                                            </th>
                                            <td class="pe-4 py-3 align-middle">
                                                @if ($record->certificate_issued)
                                                    <span class="badge bg-success-subtle text-success border">تم الإصدار</span>
                                                    @if ($record->certificate_date)
                                                        <small class="text-muted ms-2">{{ $record->certificate_date->format('Y-m-d') }}</small>
                                                    @endif
                                                @else
                                                    <span class="badge bg-secondary-subtle text-dark border">لم يتم الإصدار</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @if ($record->feedback)
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-comment-dots text-muted me-2"></i>ملاحظات الموظف
                                            </th>
                                            <td class="pe-4 py-3 align-middle">{{ $record->feedback }}</td>
                                        </tr>
                                        @endif
                                        @if ($record->evaluation)
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-clipboard-check text-muted me-2"></i>تقييم المدرب
                                            </th>
                                            <td class="pe-4 py-3 align-middle">{{ $record->evaluation }}</td>
                                        </tr>
                                        @endif
                                        @if ($record->notes)
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-sticky-note text-muted me-2"></i>ملاحظات إضافية
                                            </th>
                                            <td class="pe-4 py-3 align-middle">{{ $record->notes }}</td>
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
                                    <div class="small text-muted mb-1"><i class="far fa-clock me-1"></i>تاريخ الإنشاء</div>
                                    <div class="fw-semibold font-monospace small">{{ $record->created_at->format('Y-m-d H:i') }}</div>
                                </div>
                                <div class="col border-bottom p-3">
                                    <div class="small text-muted mb-1"><i class="fas fa-pen-to-square me-1"></i>آخر تحديث</div>
                                    <div class="fw-semibold font-monospace small">{{ $record->updated_at->format('Y-m-d H:i') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
