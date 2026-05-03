@extends('admin.layouts.master')

@section('page-title')
    تفاصيل التقييم
@stop

@section('css')
    <style>
        .review-show-hero {
            background: linear-gradient(145deg, var(--bs-primary) 0%, rgba(var(--bs-primary-rgb), 0.88) 55%, rgb(15, 76, 129) 100%);
            color: #fff;
            border: none;
        }
        .review-show-hero .text-white-75 { color: rgba(255,255,255,.85) !important; }
    </style>
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">تفاصيل التقييم</h5>
                    <p class="text-muted small mb-0">{{ $review->employee->full_name ?? '' }} — {{ $review->review_date->format('Y-m-d') }}</p>
                </div>
                <div class="d-flex flex-wrap gap-2 mt-2 mt-md-0">
                    <a href="{{ route('admin.performance-reviews.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-right me-1"></i>العودة للقائمة
                    </a>
                    @if ($review->status != 'approved')
                        @can('performance-review-edit')
                            <a href="{{ route('admin.performance-reviews.edit', $review->id) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit me-1"></i>تعديل
                            </a>
                        @endcan
                    @endif
                    @if ($review->status == 'completed')
                        @can('performance-review-approve')
                            <form action="{{ route('admin.performance-reviews.approve', $review->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('هل أنت متأكد من الموافقة على هذا التقييم؟')">
                                    <i class="fas fa-check me-1"></i>موافقة
                                </button>
                            </form>
                        @endcan
                    @endif
                </div>
            </div>

            <div class="row g-3">
                <div class="col-lg-4">
                    <div class="card review-show-hero shadow-sm h-100">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex align-items-start gap-3 mb-3">
                                <span class="avatar avatar-md bg-white bg-opacity-25 rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width:3rem;height:3rem;">
                                    <i class="fas fa-star fs-4"></i>
                                </span>
                                <div class="min-w-0">
                                    <div class="text-white-75 small mb-1">التقييم</div>
                                    <div class="fw-semibold fs-6 text-truncate">{{ $review->employee->full_name ?? '' }}</div>
                                    <div class="small text-white-75 font-monospace">{{ $review->employee->employee_code ?? '—' }}</div>
                                </div>
                            </div>
                            <div class="mb-3 pb-3 border-bottom border-white border-opacity-25">
                                <div class="text-white-75 small mb-1"><i class="fas fa-user-tie me-1"></i>المقيّم</div>
                                <div class="fw-semibold">{{ $review->reviewer->full_name ?? $review->reviewer->first_name . ' ' . $review->reviewer->last_name }}</div>
                            </div>
                            <div class="mb-3">
                                <div class="text-white-75 small mb-2">الحالة</div>
                                @switch($review->status)
                                    @case('approved')
                                        <span class="badge bg-success fs-14 px-3 py-2">موافق عليه</span>
                                        @break
                                    @case('completed')
                                        <span class="badge bg-primary fs-14 px-3 py-2">مكتمل</span>
                                        @break
                                    @case('draft')
                                        <span class="badge bg-secondary fs-14 px-3 py-2">مسودة</span>
                                        @break
                                    @default
                                        <span class="badge bg-danger fs-14 px-3 py-2">مرفوض</span>
                                @endswitch
                            </div>
                            <div class="mt-auto pt-3 border-top border-white border-opacity-25">
                                <div class="text-white-75 small mb-1">التقييم الإجمالي</div>
                                <div class="display-6 fw-bold lh-1">{{ number_format($review->overall_rating, 2) }}</div>
                                <div class="small text-white-75 mt-1">/ 5.00 — {{ $review->overall_rating_text }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-light py-3 border-bottom">
                            <h6 class="mb-0 fw-semibold">
                                <i class="fas fa-clipboard-list text-primary me-2"></i>معايير التقييم
                            </h6>
                            <small class="text-muted">تقييم الأداء حسب المعايير المحددة من 5</small>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <tbody>
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle" style="width:35%">
                                                <i class="fas fa-brain text-muted me-2"></i>المعرفة الوظيفية
                                            </th>
                                            <td class="pe-4 py-3 align-middle text-end">
                                                @if ($review->job_knowledge > 0)
                                                    @php $c = $review->job_knowledge >= 4 ? 'success' : ($review->job_knowledge >= 3 ? 'warning' : 'danger'); @endphp
                                                    <span class="badge bg-{{ $c }}-subtle text-{{ $c }} border fs-14">{{ $review->job_knowledge }} / 5</span>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-medal text-muted me-2"></i>جودة العمل
                                            </th>
                                            <td class="pe-4 py-3 align-middle text-end">
                                                @if ($review->work_quality > 0)
                                                    @php $c = $review->work_quality >= 4 ? 'success' : ($review->work_quality >= 3 ? 'warning' : 'danger'); @endphp
                                                    <span class="badge bg-{{ $c }}-subtle text-{{ $c }} border fs-14">{{ $review->work_quality }} / 5</span>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-chart-line text-muted me-2"></i>الإنتاجية
                                            </th>
                                            <td class="pe-4 py-3 align-middle text-end">
                                                @if ($review->productivity > 0)
                                                    @php $c = $review->productivity >= 4 ? 'success' : ($review->productivity >= 3 ? 'warning' : 'danger'); @endphp
                                                    <span class="badge bg-{{ $c }}-subtle text-{{ $c }} border fs-14">{{ $review->productivity }} / 5</span>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-comments text-muted me-2"></i>التواصل
                                            </th>
                                            <td class="pe-4 py-3 align-middle text-end">
                                                @if ($review->communication > 0)
                                                    @php $c = $review->communication >= 4 ? 'success' : ($review->communication >= 3 ? 'warning' : 'danger'); @endphp
                                                    <span class="badge bg-{{ $c }}-subtle text-{{ $c }} border fs-14">{{ $review->communication }} / 5</span>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-people-group text-muted me-2"></i>العمل الجماعي
                                            </th>
                                            <td class="pe-4 py-3 align-middle text-end">
                                                @if ($review->teamwork > 0)
                                                    @php $c = $review->teamwork >= 4 ? 'success' : ($review->teamwork >= 3 ? 'warning' : 'danger'); @endphp
                                                    <span class="badge bg-{{ $c }}-subtle text-{{ $c }} border fs-14">{{ $review->teamwork }} / 5</span>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-lightbulb text-muted me-2"></i>المبادرة
                                            </th>
                                            <td class="pe-4 py-3 align-middle text-end">
                                                @if ($review->initiative > 0)
                                                    @php $c = $review->initiative >= 4 ? 'success' : ($review->initiative >= 3 ? 'warning' : 'danger'); @endphp
                                                    <span class="badge bg-{{ $c }}-subtle text-{{ $c }} border fs-14">{{ $review->initiative }} / 5</span>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-puzzle-piece text-muted me-2"></i>حل المشاكل
                                            </th>
                                            <td class="pe-4 py-3 align-middle text-end">
                                                @if ($review->problem_solving > 0)
                                                    @php $c = $review->problem_solving >= 4 ? 'success' : ($review->problem_solving >= 3 ? 'warning' : 'danger'); @endphp
                                                    <span class="badge bg-{{ $c }}-subtle text-{{ $c }} border fs-14">{{ $review->problem_solving }} / 5</span>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-user-clock text-muted me-2"></i>الحضور والانضباط
                                            </th>
                                            <td class="pe-4 py-3 align-middle text-end">
                                                @if ($review->attendance_punctuality > 0)
                                                    @php $c = $review->attendance_punctuality >= 4 ? 'success' : ($review->attendance_punctuality >= 3 ? 'warning' : 'danger'); @endphp
                                                    <span class="badge bg-{{ $c }}-subtle text-{{ $c }} border fs-14">{{ $review->attendance_punctuality }} / 5</span>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if ($review->strengths || $review->weaknesses)
            <div class="row g-3 mt-1">
                @if ($review->strengths)
                <div class="col-md-6">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-light py-3 border-bottom">
                            <h6 class="mb-0 fw-semibold">
                                <i class="fas fa-circle-check text-success me-2"></i>نقاط القوة
                            </h6>
                        </div>
                        <div class="card-body">
                            <p class="mb-0">{{ $review->strengths }}</p>
                        </div>
                    </div>
                </div>
                @endif
                @if ($review->weaknesses)
                <div class="col-md-6">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-light py-3 border-bottom">
                            <h6 class="mb-0 fw-semibold">
                                <i class="fas fa-triangle-exclamation text-danger me-2"></i>نقاط الضعف
                            </h6>
                        </div>
                        <div class="card-body">
                            <p class="mb-0">{{ $review->weaknesses }}</p>
                        </div>
                    </div>
                </div>
                @endif
            </div>
            @endif

            @if ($review->goals_achieved || $review->future_goals)
            <div class="row g-3 mt-1">
                @if ($review->goals_achieved)
                <div class="col-md-6">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-light py-3 border-bottom">
                            <h6 class="mb-0 fw-semibold">
                                <i class="fas fa-bullseye text-primary me-2"></i>الأهداف المحققة
                            </h6>
                        </div>
                        <div class="card-body">
                            <p class="mb-0">{{ $review->goals_achieved }}</p>
                        </div>
                    </div>
                </div>
                @endif
                @if ($review->future_goals)
                <div class="col-md-6">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-light py-3 border-bottom">
                            <h6 class="mb-0 fw-semibold">
                                <i class="fas fa-flag text-info me-2"></i>الأهداف المستقبلية
                            </h6>
                        </div>
                        <div class="card-body">
                            <p class="mb-0">{{ $review->future_goals }}</p>
                        </div>
                    </div>
                </div>
                @endif
            </div>
            @endif

            @if ($review->comments || $review->employee_comments)
            <div class="row g-3 mt-1">
                @if ($review->comments)
                <div class="col-md-6">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-light py-3 border-bottom">
                            <h6 class="mb-0 fw-semibold">
                                <i class="fas fa-comment-dots text-muted me-2"></i>تعليقات المقيّم
                            </h6>
                        </div>
                        <div class="card-body">
                            <p class="mb-0">{{ $review->comments }}</p>
                        </div>
                    </div>
                </div>
                @endif
                @if ($review->employee_comments)
                <div class="col-md-6">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-light py-3 border-bottom">
                            <h6 class="mb-0 fw-semibold">
                                <i class="fas fa-reply text-muted me-2"></i>تعليقات الموظف
                            </h6>
                        </div>
                        <div class="card-body">
                            <p class="mb-0">{{ $review->employee_comments }}</p>
                        </div>
                    </div>
                </div>
                @endif
            </div>
            @endif

            <div class="row g-3 mt-1 mb-4">
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-light py-3">
                            <h6 class="mb-0 fw-semibold">
                                <i class="fas fa-circle-info text-primary me-2"></i>بيانات السجل
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row row-cols-1 row-cols-md-2 row-cols-xl-4 g-0">
                                <div class="col border-bottom border-end-md p-3">
                                    <div class="small text-muted mb-1"><i class="fas fa-calendar-alt me-1"></i>تاريخ التقييم</div>
                                    <div class="fw-semibold">{{ $review->review_date->format('Y-m-d') }}</div>
                                </div>
                                <div class="col border-bottom border-end-xl p-3">
                                    <div class="small text-muted mb-1"><i class="fas fa-calendar-range me-1"></i>الفترة</div>
                                    <div class="fw-semibold small">{{ $review->period_start_date->format('Y-m-d') }} — {{ $review->period_end_date->format('Y-m-d') }}</div>
                                </div>
                                @if ($review->approved_by)
                                <div class="col border-bottom border-end-md p-3">
                                    <div class="small text-muted mb-1"><i class="fas fa-user-check me-1"></i>وافق عليه</div>
                                    <div class="fw-semibold">{{ $review->approver->name ?? '—' }}</div>
                                    @if ($review->approved_at)
                                        <small class="text-muted font-monospace">{{ $review->approved_at->format('Y-m-d H:i') }}</small>
                                    @endif
                                </div>
                                @endif
                                <div class="col border-bottom p-3">
                                    <div class="small text-muted mb-1"><i class="far fa-clock me-1"></i>تاريخ الإنشاء</div>
                                    <div class="fw-semibold font-monospace small">{{ $review->created_at->format('Y-m-d H:i') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
