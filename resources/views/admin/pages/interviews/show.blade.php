@extends('admin.layouts.master')

@section('page-title')
    تفاصيل المقابلة
@stop

@section('css')
    <style>
        .interview-show-hero {
            background: linear-gradient(145deg, var(--bs-primary) 0%, rgba(var(--bs-primary-rgb), 0.88) 55%, rgb(15, 76, 129) 100%);
            color: #fff;
            border: none;
        }
        .interview-show-hero .text-white-75 { color: rgba(255,255,255,.85) !important; }
    </style>
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">تفاصيل المقابلة</h5>
                    <p class="text-muted small mb-0">{{ $interview->candidate->full_name }} — {{ $interview->jobVacancy->title_ar ?? $interview->jobVacancy->title }}</p>
                </div>
                <div class="d-flex flex-wrap gap-2 mt-2 mt-md-0">
                    <a href="{{ route('admin.interviews.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-right me-1"></i>العودة للقائمة
                    </a>
                    @can('interview-edit')
                        <a href="{{ route('admin.interviews.edit', $interview->id) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit me-1"></i>تعديل
                        </a>
                    @endcan
                </div>
            </div>

            <div class="row g-3">
                <div class="col-lg-4">
                    <div class="card interview-show-hero shadow-sm h-100">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex align-items-start gap-3 mb-3">
                                <span class="avatar avatar-md bg-white bg-opacity-25 rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width:3rem;height:3rem;">
                                    <i class="fas fa-handshake fs-4"></i>
                                </span>
                                <div class="min-w-0">
                                    <div class="text-white-75 small mb-1">المقابلة</div>
                                    <div class="fw-semibold fs-6 text-truncate">{{ $interview->candidate->full_name }}</div>
                                    <div class="small text-white-75">{{ $interview->jobVacancy->title_ar ?? $interview->jobVacancy->title }}</div>
                                </div>
                            </div>
                            <div class="mb-3 pb-3 border-bottom border-white border-opacity-25">
                                <div class="text-white-75 small mb-1"><i class="fas fa-calendar me-1"></i>التاريخ والوقت</div>
                                <div class="fw-semibold">
                                    {{ $interview->interview_date->format('Y-m-d') }}
                                    @if ($interview->interview_time)
                                        — {{ $interview->interview_time->format('H:i') }}
                                    @endif
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="text-white-75 small mb-2">الحالة</div>
                                <span class="badge bg-{{ $interview->status == 'completed' ? 'success' : ($interview->status == 'cancelled' ? 'danger' : 'warning') }} fs-14 px-3 py-2">
                                    {{ $interview->status_name_ar }}
                                </span>
                            </div>
                            <div class="mt-auto pt-3 border-top border-white border-opacity-25">
                                @if ($interview->recommendation_status)
                                <div class="text-white-75 small mb-1">التوصية</div>
                                <div class="fs-5 fw-semibold">
                                    {{ $interview->recommendation_status == 'hire' ? 'توظيف' : ($interview->recommendation_status == 'reject' ? 'رفض' : 'قيد المراجعة') }}
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-light py-3 border-bottom">
                            <h6 class="mb-0 fw-semibold">
                                <i class="fas fa-circle-info text-primary me-2"></i>تفاصيل المقابلة
                            </h6>
                            <small class="text-muted">نوع المقابلة والجولة والتقييم</small>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <tbody>
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle" style="width:35%">
                                                <i class="fas fa-tag text-muted me-2"></i>نوع المقابلة
                                            </th>
                                            <td class="pe-4 py-3 align-middle">
                                                <span class="badge bg-info-subtle text-dark border">{{ $interview->type_name_ar }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-layer-group text-muted me-2"></i>جولة المقابلة
                                            </th>
                                            <td class="pe-4 py-3 align-middle">
                                                <span class="badge bg-secondary-subtle text-dark border">{{ $interview->round_name_ar }}</span>
                                            </td>
                                        </tr>
                                        @if ($interview->location)
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-location-dot text-muted me-2"></i>المكان
                                            </th>
                                            <td class="pe-4 py-3 align-middle fw-semibold">{{ $interview->location }}</td>
                                        </tr>
                                        @endif
                                        @if ($interview->overall_rating)
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-star text-muted me-2"></i>التقييم الإجمالي
                                            </th>
                                            <td class="pe-4 py-3 align-middle">
                                                <span class="badge bg-success fs-14">{{ $interview->overall_rating }}/5</span>
                                            </td>
                                        </tr>
                                        @endif
                                        @if ($interview->recommendation_status)
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-thumbs-up text-muted me-2"></i>التوصية
                                            </th>
                                            <td class="pe-4 py-3 align-middle">
                                                <span class="badge bg-{{ $interview->recommendation_status == 'hire' ? 'success' : ($interview->recommendation_status == 'reject' ? 'danger' : 'warning') }} fs-14">
                                                    {{ $interview->recommendation_status == 'hire' ? 'توظيف' : ($interview->recommendation_status == 'reject' ? 'رفض' : 'قيد المراجعة') }}
                                                </span>
                                            </td>
                                        </tr>
                                        @endif
                                        @if ($interview->interview_notes)
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-sticky-note text-muted me-2"></i>ملاحظات المقابلة
                                            </th>
                                            <td class="pe-4 py-3 align-middle">{{ $interview->interview_notes }}</td>
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
                            <h6 class="mb-0 fw-semibold"><i class="fas fa-clock-rotate-left text-primary me-2"></i>بيانات السجل</h6>
                        </div>
                        <div class="card-body">
                            <div class="row row-cols-1 row-cols-md-2 g-0">
                                <div class="col border-bottom border-end-md p-3">
                                    <div class="small text-muted mb-1"><i class="far fa-clock me-1"></i>تاريخ الإنشاء</div>
                                    <div class="fw-semibold font-monospace small">{{ $interview->created_at->format('Y-m-d H:i') }}</div>
                                </div>
                                <div class="col border-bottom p-3">
                                    <div class="small text-muted mb-1"><i class="fas fa-pen-to-square me-1"></i>آخر تحديث</div>
                                    <div class="fw-semibold font-monospace small">{{ $interview->updated_at->format('Y-m-d H:i') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
