@extends('admin.layouts.master')

@section('page-title')
    تقرير التقييمات
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">تقرير التقييمات</h5>
                </div>
                <div>
                    <a href="{{ route('admin.reports.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-right me-2"></i>العودة للتقارير
                    </a>
                </div>
            </div>

            <!-- فلترة -->
            <div class="card mb-3">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.reports.performance') }}" class="row g-3">
                        <div class="col-md-4">
                            <select name="employee_id" class="form-select">
                                <option value="">كل الموظفين</option>
                                @foreach ($employees as $emp)
                                    <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>
                                        {{ $emp->full_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="date" name="period_from" class="form-control" value="{{ request('period_from') }}">
                        </div>
                        <div class="col-md-3">
                            <input type="date" name="period_to" class="form-control" value="{{ request('period_to') }}">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">تطبيق</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- الإحصائيات -->
            <div class="row mb-3">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h6 class="card-title">إجمالي التقييمات</h6>
                            <h2 class="mb-0">{{ $stats['total_reviews'] }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h6 class="card-title">متوسط التقييم</h6>
                            <h2 class="mb-0">{{ number_format($stats['average_rating'], 2) }}/5</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="card-title">التوزيع حسب الحالة</h6>
                            <div class="d-flex gap-2">
                                @foreach ($stats['by_status'] as $status => $count)
                                    <span class="badge bg-secondary">{{ $status }}: {{ $count }}</span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- جدول التقييمات -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">سجلات التقييمات ({{ $reviews->count() }})</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>الموظف</th>
                                    <th>الفترة</th>
                                    <th>تاريخ التقييم</th>
                                    <th>التقييم الإجمالي</th>
                                    <th>الحالة</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($reviews as $review)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td><strong>{{ $review->employee->full_name }}</strong></td>
                                        <td>
                                            {{ $review->review_period_start->format('Y-m-d') }} 
                                            إلى 
                                            {{ $review->review_period_end->format('Y-m-d') }}
                                        </td>
                                        <td>{{ $review->review_date->format('Y-m-d') }}</td>
                                        <td>
                                            <span class="badge bg-{{ $review->overall_rating >= 4 ? 'success' : ($review->overall_rating >= 3 ? 'warning' : 'danger') }}">
                                                {{ number_format($review->overall_rating, 2) }}/5
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $review->status == 'approved' ? 'success' : ($review->status == 'rejected' ? 'danger' : 'warning') }}">
                                                {{ $review->status_name_ar }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop


