@extends('admin.layouts.master')

@section('page-title')
    قائمة التقييمات
@stop

@section('css')
@stop

@section('content')
    @if (\Session::has('success'))
        <div class="alert alert-success">
            <ul>
                <li>{!! \Session::get('success') !!}</li>
            </ul>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">كافة التقييمات</h5>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header align-items-center d-flex gap-3">
                            @can('performance-review-create')
                            <a href="{{ route('admin.performance-reviews.create') }}" class="btn btn-primary btn-sm">إضافة تقييم جديد</a>
                            @endcan

                            <div class="flex-shrink-0">
                                <form action="{{ route('admin.performance-reviews.index') }}" method="GET" class="d-flex align-items-center gap-2">
                                    <select name="employee_id" class="form-select" style="width: 200px;">
                                        <option value="">كل الموظفين</option>
                                        @foreach ($employees as $employee)
                                            <option value="{{ $employee->id }}" {{ request('employee_id') == $employee->id ? 'selected' : '' }}>
                                                {{ $employee->full_name ?? $employee->first_name . ' ' . $employee->last_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <select name="reviewer_id" class="form-select" style="width: 200px;">
                                        <option value="">كل المقيّمين</option>
                                        @foreach ($employees as $employee)
                                            <option value="{{ $employee->id }}" {{ request('reviewer_id') == $employee->id ? 'selected' : '' }}>
                                                {{ $employee->full_name ?? $employee->first_name . ' ' . $employee->last_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <input type="text" name="review_period" class="form-control" style="width: 150px;"
                                           placeholder="فترة التقييم" value="{{ request('review_period') }}">
                                    <select name="status" class="form-select" style="width: 150px;">
                                        <option value="">كل الحالات</option>
                                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>مسودة</option>
                                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>مكتمل</option>
                                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>موافق عليه</option>
                                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>مرفوض</option>
                                    </select>
                                    <button type="submit" class="btn btn-secondary">بحث</button>
                                    <a href="{{ route('admin.performance-reviews.index') }}" class="btn btn-danger">مسح</a>
                                </form>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover align-middle table-nowrap mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>الموظف</th>
                                            <th>المقيّم</th>
                                            <th>فترة التقييم</th>
                                            <th>تاريخ التقييم</th>
                                            <th>التقييم الإجمالي</th>
                                            <th>الحالة</th>
                                            <th>العمليات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($reviews as $review)
                                            <tr>
                                                <th>{{ $loop->iteration }}</th>
                                                <td>
                                                    <strong>{{ $review->employee->full_name ?? $review->employee->first_name . ' ' . $review->employee->last_name }}</strong>
                                                </td>
                                                <td>
                                                    {{ $review->reviewer->full_name ?? $review->reviewer->first_name . ' ' . $review->reviewer->last_name }}
                                                </td>
                                                <td>
                                                    <span class="badge bg-info">{{ $review->review_period }}</span>
                                                </td>
                                                <td>{{ $review->review_date->format('Y-m-d') }}</td>
                                                <td>
                                                    <span class="badge bg-{{ $review->overall_rating >= 4 ? 'success' : ($review->overall_rating >= 3 ? 'warning' : 'danger') }}">
                                                        {{ number_format($review->overall_rating, 2) }} / 5.00
                                                    </span>
                                                    <br><small class="text-muted">{{ $review->overall_rating_text }}</small>
                                                </td>
                                                <td>
                                                    @if ($review->status == 'approved')
                                                        <span class="badge bg-success">موافق عليه</span>
                                                    @elseif ($review->status == 'completed')
                                                        <span class="badge bg-primary">مكتمل</span>
                                                    @elseif ($review->status == 'draft')
                                                        <span class="badge bg-secondary">مسودة</span>
                                                    @else
                                                        <span class="badge bg-danger">مرفوض</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @can('performance-review-show')
                                                    <a class="btn btn-info btn-sm me-1" href="{{ route('admin.performance-reviews.show', $review->id) }}" title="عرض">
                                                        <i class="fa-solid fa-eye"></i>
                                                    </a>
                                                    @endcan
                                                    @if ($review->status != 'approved')
                                                        @can('performance-review-edit')
                                                        <a class="btn btn-warning btn-sm me-1" href="{{ route('admin.performance-reviews.edit', $review->id) }}" title="تعديل">
                                                            <i class="fa-solid fa-pen-to-square"></i>
                                                        </a>
                                                        @endcan
                                                    @endif
                                                    @can('performance-review-delete')
                                                    <a class="btn btn-danger btn-sm me-1" data-bs-toggle="modal" data-bs-target="#delete{{ $review->id }}" title="حذف">
                                                        <i class="fa-solid fa-trash-can"></i>
                                                    </a>
                                                    @endcan
                                                </td>
                                            </tr>
                                            @include('admin.pages.performance-reviews.delete')
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center text-danger fw-bold">لا توجد بيانات متاحة</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>

                                <div class="mt-3">
                                    {{ $reviews->withQueryString()->links() }}
                                </div>
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


