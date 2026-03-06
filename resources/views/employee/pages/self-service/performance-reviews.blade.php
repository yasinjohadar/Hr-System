@extends('employee.layouts.master')

@section('page-title')
    التقييمات
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">تقييمات الأداء</h5>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">قائمة التقييمات ({{ $reviews->total() }})</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @forelse ($reviews as $review)
                            <div class="col-md-6 mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="card-title">تقييم الأداء</h6>
                                        <p class="mb-2"><strong>الفترة:</strong> {{ $review->review_period_start?->format('Y-m-d') ?? '-' }} - {{ $review->review_period_end?->format('Y-m-d') ?? '-' }}</p>
                                        <p class="mb-2"><strong>تاريخ التقييم:</strong> {{ $review->review_date?->format('Y-m-d') ?? '-' }}</p>
                                        <p class="mb-2"><strong>المقيّم:</strong> {{ $review->reviewer->name ?? '-' }}</p>
                                        <p class="mb-2">
                                            <strong>التقييم الإجمالي:</strong>
                                            <span class="badge bg-{{ $review->overall_rating >= 4 ? 'success' : ($review->overall_rating >= 3 ? 'warning' : 'danger') }}">
                                                {{ number_format($review->overall_rating, 2) }}/5
                                            </span>
                                        </p>
                                        <p class="mb-0">
                                            <span class="badge bg-{{ $review->status == 'approved' ? 'success' : ($review->status == 'rejected' ? 'danger' : 'warning') }}">
                                                {{ $review->status_name_ar }}
                                            </span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <p class="text-center text-muted">لا توجد تقييمات</p>
                            </div>
                        @endforelse
                    </div>
                    <div class="mt-3">
                        {{ $reviews->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop


