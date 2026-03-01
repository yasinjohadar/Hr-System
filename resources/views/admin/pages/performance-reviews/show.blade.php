@extends('admin.layouts.master')

@section('page-title')
    تفاصيل التقييم
@stop

@section('css')
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">تفاصيل التقييم</h5>
                </div>
                <div>
                    <a href="{{ route('admin.performance-reviews.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-right me-2"></i>العودة للقائمة
                    </a>
                    @if ($review->status != 'approved')
                        @can('performance-review-edit')
                        <a href="{{ route('admin.performance-reviews.edit', $review->id) }}" class="btn btn-primary">
                            <i class="fas fa-edit me-2"></i>تعديل
                        </a>
                        @endcan
                    @endif
                    @if ($review->status == 'completed')
                        @can('performance-review-approve')
                        <form action="{{ route('admin.performance-reviews.approve', $review->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-success" onclick="return confirm('هل أنت متأكد من الموافقة على هذا التقييم؟')">
                                <i class="fas fa-check me-2"></i>موافقة
                            </button>
                        </form>
                        @endcan
                    @endif
                </div>
            </div>

            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">معلومات التقييم</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">الموظف المقيّم:</label>
                                    <p class="form-control-plaintext">
                                        <strong>{{ $review->employee->full_name ?? $review->employee->first_name . ' ' . $review->employee->last_name }}</strong>
                                        <br><small class="text-muted">{{ $review->employee->employee_code ?? '' }}</small>
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">المقيّم (المدير):</label>
                                    <p class="form-control-plaintext">
                                        {{ $review->reviewer->full_name ?? $review->reviewer->first_name . ' ' . $review->reviewer->last_name }}
                                    </p>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">فترة التقييم:</label>
                                    <p class="form-control-plaintext">
                                        <span class="badge bg-info">{{ $review->review_period }}</span>
                                    </p>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">تاريخ التقييم:</label>
                                    <p class="form-control-plaintext">{{ $review->review_date->format('Y-m-d') }}</p>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">الفترة:</label>
                                    <p class="form-control-plaintext">
                                        {{ $review->period_start_date->format('Y-m-d') }} إلى {{ $review->period_end_date->format('Y-m-d') }}
                                    </p>
                                </div>
                                <div class="col-12">
                                    <hr>
                                    <h6 class="mb-3">التقييمات التفصيلية</h6>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">المعرفة الوظيفية:</label>
                                    <p class="form-control-plaintext">
                                        @if ($review->job_knowledge > 0)
                                            <span class="badge bg-{{ $review->job_knowledge >= 4 ? 'success' : ($review->job_knowledge >= 3 ? 'warning' : 'danger') }}">
                                                {{ $review->job_knowledge }} / 5
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">جودة العمل:</label>
                                    <p class="form-control-plaintext">
                                        @if ($review->work_quality > 0)
                                            <span class="badge bg-{{ $review->work_quality >= 4 ? 'success' : ($review->work_quality >= 3 ? 'warning' : 'danger') }}">
                                                {{ $review->work_quality }} / 5
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">الإنتاجية:</label>
                                    <p class="form-control-plaintext">
                                        @if ($review->productivity > 0)
                                            <span class="badge bg-{{ $review->productivity >= 4 ? 'success' : ($review->productivity >= 3 ? 'warning' : 'danger') }}">
                                                {{ $review->productivity }} / 5
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">التواصل:</label>
                                    <p class="form-control-plaintext">
                                        @if ($review->communication > 0)
                                            <span class="badge bg-{{ $review->communication >= 4 ? 'success' : ($review->communication >= 3 ? 'warning' : 'danger') }}">
                                                {{ $review->communication }} / 5
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">العمل الجماعي:</label>
                                    <p class="form-control-plaintext">
                                        @if ($review->teamwork > 0)
                                            <span class="badge bg-{{ $review->teamwork >= 4 ? 'success' : ($review->teamwork >= 3 ? 'warning' : 'danger') }}">
                                                {{ $review->teamwork }} / 5
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">المبادرة:</label>
                                    <p class="form-control-plaintext">
                                        @if ($review->initiative > 0)
                                            <span class="badge bg-{{ $review->initiative >= 4 ? 'success' : ($review->initiative >= 3 ? 'warning' : 'danger') }}">
                                                {{ $review->initiative }} / 5
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">حل المشاكل:</label>
                                    <p class="form-control-plaintext">
                                        @if ($review->problem_solving > 0)
                                            <span class="badge bg-{{ $review->problem_solving >= 4 ? 'success' : ($review->problem_solving >= 3 ? 'warning' : 'danger') }}">
                                                {{ $review->problem_solving }} / 5
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">الحضور والانضباط:</label>
                                    <p class="form-control-plaintext">
                                        @if ($review->attendance_punctuality > 0)
                                            <span class="badge bg-{{ $review->attendance_punctuality >= 4 ? 'success' : ($review->attendance_punctuality >= 3 ? 'warning' : 'danger') }}">
                                                {{ $review->attendance_punctuality }} / 5
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">التقييم الإجمالي:</label>
                                    <p class="form-control-plaintext">
                                        <span class="badge bg-{{ $review->overall_rating >= 4 ? 'success' : ($review->overall_rating >= 3 ? 'warning' : 'danger') }} fs-6">
                                            {{ number_format($review->overall_rating, 2) }} / 5.00
                                        </span>
                                        <br><strong>{{ $review->overall_rating_text }}</strong>
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">الحالة:</label>
                                    <p class="form-control-plaintext">
                                        @if ($review->status == 'approved')
                                            <span class="badge bg-success">موافق عليه</span>
                                        @elseif ($review->status == 'completed')
                                            <span class="badge bg-primary">مكتمل</span>
                                        @elseif ($review->status == 'draft')
                                            <span class="badge bg-secondary">مسودة</span>
                                        @else
                                            <span class="badge bg-danger">مرفوض</span>
                                        @endif
                                    </p>
                                </div>
                                @if ($review->strengths)
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">نقاط القوة:</label>
                                    <p class="form-control-plaintext">{{ $review->strengths }}</p>
                                </div>
                                @endif
                                @if ($review->weaknesses)
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">نقاط الضعف:</label>
                                    <p class="form-control-plaintext">{{ $review->weaknesses }}</p>
                                </div>
                                @endif
                                @if ($review->goals_achieved)
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">الأهداف المحققة:</label>
                                    <p class="form-control-plaintext">{{ $review->goals_achieved }}</p>
                                </div>
                                @endif
                                @if ($review->future_goals)
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">الأهداف المستقبلية:</label>
                                    <p class="form-control-plaintext">{{ $review->future_goals }}</p>
                                </div>
                                @endif
                                @if ($review->comments)
                                <div class="col-12 mb-3">
                                    <label class="form-label fw-bold">تعليقات المقيّم:</label>
                                    <p class="form-control-plaintext">{{ $review->comments }}</p>
                                </div>
                                @endif
                                @if ($review->employee_comments)
                                <div class="col-12 mb-3">
                                    <label class="form-label fw-bold">تعليقات الموظف:</label>
                                    <p class="form-control-plaintext">{{ $review->employee_comments }}</p>
                                </div>
                                @endif
                                @if ($review->approved_by)
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">وافق عليه:</label>
                                    <p class="form-control-plaintext">
                                        {{ $review->approver->name ?? '-' }}
                                        @if ($review->approved_at)
                                            <br><small class="text-muted">{{ $review->approved_at->format('Y-m-d H:i') }}</small>
                                        @endif
                                    </p>
                                </div>
                                @endif
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


