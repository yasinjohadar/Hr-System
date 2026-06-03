@extends('admin.layouts.master')

@section('page-title')
    قائمة التقييمات
@stop

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin-performance-reviews.css') }}">
@endpush

@section('content')
    <div class="main-content app-content admin-performance-reviews-page">
        <div class="container-fluid pt-4">

            @if (\Session::has('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {!! \Session::get('success') !!}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card page-hero mb-4">
                <div class="card-body py-4">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="page-hero-icon">
                                <i class="ri-star-line"></i>
                            </div>
                            <div>
                                <h4 class="mb-1 page-hero-title fw-bold">تقييمات الأداء</h4>
                                <p class="mb-0 page-hero-subtitle">متابعة تقييمات الموظفين والموافقات</p>
                            </div>
                        </div>
                        @can('performance-review-create')
                            <a href="{{ route('admin.performance-reviews.create') }}" class="btn btn-hero-primary btn-sm">
                                <i class="ri-add-line me-1"></i>إضافة تقييم جديد
                            </a>
                        @endcan
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
                                <div class="stat-value">{{ $stats['draft'] }}</div>
                                <div class="stat-label">مسودة</div>
                            </div>
                            <div class="stat-icon stat-icon--muted"><i class="ri-draft-line"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-value">{{ $stats['completed'] }}</div>
                                <div class="stat-label">مكتمل</div>
                            </div>
                            <div class="stat-icon stat-icon--info"><i class="ri-checkbox-blank-circle-line"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-value">{{ $stats['approved'] }}</div>
                                <div class="stat-label">موافق عليه</div>
                            </div>
                            <div class="stat-icon stat-icon--success"><i class="ri-checkbox-circle-line"></i></div>
                        </div>
                    </div>
                </div>
            </div>

            <form action="{{ route('admin.performance-reviews.index') }}" method="GET" class="filters-panel">
                <div class="row g-3 align-items-end">
                    <div class="col-md-6 col-lg-3">
                        <label class="form-label">الموظف</label>
                        <select name="employee_id" class="form-select">
                            <option value="">كل الموظفين</option>
                            @foreach ($employees as $employee)
                                <option value="{{ $employee->id }}" {{ request('employee_id') == $employee->id ? 'selected' : '' }}>
                                    {{ $employee->full_name ?? $employee->first_name . ' ' . $employee->last_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <label class="form-label">المقيّم</label>
                        <select name="reviewer_id" class="form-select">
                            <option value="">كل المقيّمين</option>
                            @foreach ($employees as $employee)
                                <option value="{{ $employee->id }}" {{ request('reviewer_id') == $employee->id ? 'selected' : '' }}>
                                    {{ $employee->full_name ?? $employee->first_name . ' ' . $employee->last_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 col-lg-2">
                        <label class="form-label">فترة التقييم</label>
                        <input type="text" name="review_period" class="form-control" placeholder="مثال: Q4 2025" value="{{ request('review_period') }}">
                    </div>
                    <div class="col-md-6 col-lg-2">
                        <label class="form-label">الحالة</label>
                        <select name="status" class="form-select">
                            <option value="">كل الحالات</option>
                            <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>مسودة</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>مكتمل</option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>موافق عليه</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>مرفوض</option>
                        </select>
                    </div>
                    <div class="col-12 col-lg-auto d-flex flex-wrap gap-2">
                        <button type="submit" class="btn btn-filter-submit">
                            <i class="ri-search-line me-1"></i>بحث
                        </button>
                        <a href="{{ route('admin.performance-reviews.index') }}" class="btn btn-filter-clear">مسح</a>
                    </div>
                </div>
            </form>

            <div class="content-panel">
                <div class="content-panel-header">
                    قائمة التقييمات ({{ $reviews->total() }})
                </div>

                @if ($reviews->isNotEmpty())
                    <div class="reviews-table-scroll">
                        <div class="reviews-table-header">
                            <span>#</span>
                            <span>الموظف</span>
                            <span>المقيّم</span>
                            <span>فترة التقييم</span>
                            <span>تاريخ التقييم</span>
                            <span>التقييم الإجمالي</span>
                            <span>الحالة</span>
                            <span class="text-end">العمليات</span>
                        </div>

                        @foreach ($reviews as $review)
                            @php
                                $employeeName = $review->employee->full_name ?? trim($review->employee->first_name . ' ' . $review->employee->last_name);
                                $reviewerName = $review->reviewer->full_name ?? trim($review->reviewer->first_name . ' ' . $review->reviewer->last_name);
                                $ratingClass = $review->overall_rating >= 4 ? 'high' : ($review->overall_rating >= 3 ? 'mid' : 'low');
                            @endphp
                            <div class="reviews-table-row">
                                <span class="row-index">{{ ($reviews->firstItem() ?? 0) + $loop->index }}</span>

                                <div class="reviews-mobile-field">
                                    <span class="reviews-mobile-label">الموظف</span>
                                    <span class="cell-name" title="{{ $employeeName }}">{{ $employeeName }}</span>
                                </div>

                                <div class="reviews-mobile-field">
                                    <span class="reviews-mobile-label">المقيّم</span>
                                    <span title="{{ $reviewerName }}">{{ $reviewerName }}</span>
                                </div>

                                <div class="reviews-mobile-field">
                                    <span class="reviews-mobile-label">فترة التقييم</span>
                                    <span class="period-pill">{{ $review->review_period }}</span>
                                </div>

                                <div class="reviews-mobile-field">
                                    <span class="reviews-mobile-label">تاريخ التقييم</span>
                                    <span>{{ $review->review_date->format('Y/m/d') }}</span>
                                </div>

                                <div class="reviews-mobile-field">
                                    <span class="reviews-mobile-label">التقييم الإجمالي</span>
                                    <span>
                                        <span class="rating-pill rating-pill--{{ $ratingClass }}">{{ number_format($review->overall_rating, 2) }} / 5.00</span>
                                        <span class="rating-text">{{ $review->overall_rating_text }}</span>
                                    </span>
                                </div>

                                <div class="reviews-mobile-field">
                                    <span class="reviews-mobile-label">الحالة</span>
                                    <span class="status-pill status-pill--{{ $review->status }}">{{ $review->status_ar }}</span>
                                </div>

                                <div class="row-actions">
                                    @can('performance-review-show')
                                        <a class="btn-action btn-action--view" href="{{ route('admin.performance-reviews.show', $review->id) }}" title="عرض">
                                            <i class="ri-eye-line"></i>
                                        </a>
                                    @endcan
                                    @if ($review->status != 'approved')
                                        @can('performance-review-edit')
                                            <a class="btn-action btn-action--edit" href="{{ route('admin.performance-reviews.edit', $review->id) }}" title="تعديل">
                                                <i class="ri-pencil-line"></i>
                                            </a>
                                        @endcan
                                    @endif
                                    @can('performance-review-delete')
                                        <button type="button" class="btn-action btn-action--delete" data-bs-toggle="modal" data-bs-target="#delete{{ $review->id }}" title="حذف">
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                    @endcan
                                </div>
                            </div>

                            @include('admin.pages.performance-reviews.delete')
                        @endforeach
                    </div>

                    <div class="pagination-wrap d-flex justify-content-center">
                        {{ $reviews->withQueryString()->links() }}
                    </div>
                @else
                    <div class="empty-state">لا توجد تقييمات</div>
                @endif
            </div>

        </div>
    </div>
@stop
