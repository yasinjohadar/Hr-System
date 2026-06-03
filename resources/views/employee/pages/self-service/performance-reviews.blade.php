@extends('employee.layouts.master')

@section('page-title')
    التقييمات
@stop

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/employee-performance-reviews.css') }}">
@endpush

@section('content')
    <div class="main-content app-content employee-reviews-page">
        <div class="container-fluid pt-4">

            <div class="card page-hero mb-4">
                <div class="card-body py-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="page-hero-icon">
                            <i class="ri-bar-chart-box-line"></i>
                        </div>
                        <div>
                            <h4 class="mb-1 page-hero-title fw-bold">تقييمات الأداء</h4>
                            <p class="mb-0 page-hero-subtitle">سجل تقييماتك الدورية ونتائج الأداء</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-value">{{ $stats['total'] }}</div>
                                <div class="stat-label">إجمالي التقييمات</div>
                            </div>
                            <div class="stat-icon stat-icon--primary"><i class="ri-file-list-3-line"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-value">{{ $stats['approved'] }}</div>
                                <div class="stat-label">موافق عليها</div>
                            </div>
                            <div class="stat-icon stat-icon--success"><i class="ri-checkbox-circle-line"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-value">{{ $stats['pending'] }}</div>
                                <div class="stat-label">قيد المراجعة</div>
                            </div>
                            <div class="stat-icon stat-icon--warning"><i class="ri-time-line"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-value">{{ $stats['avg_rating'] > 0 ? number_format($stats['avg_rating'], 2) : '—' }}</div>
                                <div class="stat-label">متوسط التقييم / 5</div>
                            </div>
                            <div class="stat-icon stat-icon--primary"><i class="ri-star-line"></i></div>
                        </div>
                    </div>
                </div>
            </div>

            @if ($reviews->isNotEmpty())
                <div class="filter-pills" role="group">
                    <button type="button" class="filter-pill active" data-review-filter="all">الكل</button>
                    <button type="button" class="filter-pill" data-review-filter="approved">موافق عليه</button>
                    <button type="button" class="filter-pill" data-review-filter="pending">قيد المراجعة</button>
                    <button type="button" class="filter-pill" data-review-filter="rejected">مرفوض</button>
                </div>

                <div class="row g-3">
                    @foreach ($reviews as $review)
                        @php
                            $rating = (float) $review->overall_rating;
                            $ratingClass = $rating >= 4 ? 'high' : ($rating >= 3 ? 'mid' : 'low');
                            $reviewerName = $review->reviewer
                                ? ($review->reviewer->full_name ?? trim($review->reviewer->first_name . ' ' . $review->reviewer->last_name))
                                : '—';
                            $periodLabel = $review->review_period;
                            if ($review->period_start_date && $review->period_end_date) {
                                $periodLabel .= ' (' . $review->period_start_date->format('d/m/Y') . ' – ' . $review->period_end_date->format('d/m/Y') . ')';
                            }
                        @endphp
                        <div class="col-md-6 col-xl-4 review-card-item" data-status="{{ $review->status }}">
                            <div class="review-card">
                                <div class="review-card-top">
                                    <div>
                                        <span class="review-period-badge">{{ $review->review_period }}</span>
                                        <div class="rating-text mt-2">{{ $review->overall_rating_text }}</div>
                                    </div>
                                    <div class="rating-circle rating-circle--{{ $ratingClass }}">
                                        <span>{{ number_format($rating, 2) }}</span>
                                        <small>/5</small>
                                    </div>
                                </div>

                                <div class="review-meta">
                                    <div class="review-meta-row">
                                        <span>تاريخ التقييم</span>
                                        <span>{{ $review->review_date?->format('d/m/Y') ?? '—' }}</span>
                                    </div>
                                    <div class="review-meta-row">
                                        <span>المقيّم</span>
                                        <span>{{ $reviewerName ?: '—' }}</span>
                                    </div>
                                    @if ($review->period_start_date && $review->period_end_date)
                                        <div class="review-meta-row">
                                            <span>فترة التقييم</span>
                                            <span>
                                                {{ $review->period_start_date->format('d/m/Y') }}
                                                –
                                                {{ $review->period_end_date->format('d/m/Y') }}
                                            </span>
                                        </div>
                                    @endif
                                </div>

                                <div class="review-card-footer">
                                    <span class="status-pill status-pill--{{ $review->status }}">{{ $review->status_ar }}</span>
                                    <button type="button" class="btn-view-review" data-bs-toggle="modal"
                                        data-bs-target="#reviewModal{{ $review->id }}">
                                        <i class="ri-eye-line me-1"></i>التفاصيل
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="modal fade" id="reviewModal{{ $review->id }}" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title fw-bold">تفاصيل تقييم الأداء — {{ $review->review_period }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row g-3 mb-3">
                                            <div class="col-sm-6">
                                                <div class="detail-block">
                                                    <h6>التقييم الإجمالي</h6>
                                                    <p class="fw-semibold text-dark mb-0">
                                                        {{ number_format($rating, 2) }}/5 — {{ $review->overall_rating_text }}
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="detail-block">
                                                    <h6>الحالة</h6>
                                                    <p><span class="status-pill status-pill--{{ $review->status }}">{{ $review->status_ar }}</span></p>
                                                </div>
                                            </div>
                                        </div>
                                        @if ($review->strengths)
                                            <div class="detail-block">
                                                <h6>نقاط القوة</h6>
                                                <p>{{ $review->strengths }}</p>
                                            </div>
                                        @endif
                                        @if ($review->weaknesses)
                                            <div class="detail-block">
                                                <h6>نقاط التحسين</h6>
                                                <p>{{ $review->weaknesses }}</p>
                                            </div>
                                        @endif
                                        @if ($review->goals_achieved)
                                            <div class="detail-block">
                                                <h6>الأهداف المحققة</h6>
                                                <p>{{ $review->goals_achieved }}</p>
                                            </div>
                                        @endif
                                        @if ($review->future_goals)
                                            <div class="detail-block">
                                                <h6>الأهداف المستقبلية</h6>
                                                <p>{{ $review->future_goals }}</p>
                                            </div>
                                        @endif
                                        @if ($review->comments)
                                            <div class="detail-block">
                                                <h6>تعليقات المقيّم</h6>
                                                <p>{{ $review->comments }}</p>
                                            </div>
                                        @endif
                                        @if ($review->employee_comments)
                                            <div class="detail-block mb-0">
                                                <h6>تعليقاتك</h6>
                                                <p>{{ $review->employee_comments }}</p>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if ($reviews->hasPages())
                    <div class="mt-4">
                        {{ $reviews->links() }}
                    </div>
                @endif
            @else
                <div class="empty-state">
                    <div class="empty-icon"><i class="ri-bar-chart-box-line"></i></div>
                    <h5 class="fw-semibold text-dark mb-2">لا توجد تقييمات</h5>
                    <p class="text-muted mb-0">ستظهر تقييمات أدائك هنا بعد إجرائها من الإدارة</p>
                </div>
            @endif
        </div>
    </div>
@stop

@push('scripts')
    <script src="{{ asset('assets/js/employee-performance-reviews.js') }}"></script>
@endpush
