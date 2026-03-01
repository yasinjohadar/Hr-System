@extends('admin.layouts.master')

@section('page-title')
    قائمة الوظائف الشاغرة
@stop

@section('content')
    @if (\Session::has('success'))
        <div class="alert alert-success">
            <ul>
                <li>{!! \Session::get('success') !!}</li>
            </ul>
        </div>
    @endif

    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">كافة الوظائف الشاغرة</h5>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header align-items-center d-flex gap-3">
                            @can('job-vacancy-create')
                            <a href="{{ route('admin.job-vacancies.create') }}" class="btn btn-primary btn-sm">إضافة وظيفة شاغرة</a>
                            @endcan

                            <div class="flex-shrink-0">
                                <form action="{{ route('admin.job-vacancies.index') }}" method="GET" class="d-flex align-items-center gap-2">
                                    <input style="width: 300px" type="text" name="search" class="form-control"
                                        placeholder="بحث" value="{{ request('search') }}">
                                    <select name="status" class="form-select">
                                        <option value="">كل الحالات</option>
                                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>مسودة</option>
                                        <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>منشور</option>
                                        <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>مغلق</option>
                                        <option value="filled" {{ request('status') == 'filled' ? 'selected' : '' }}>مكتمل</option>
                                    </select>
                                    <select name="department_id" class="form-select">
                                        <option value="">كل الأقسام</option>
                                        @foreach ($departments as $dept)
                                            <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                                                {{ $dept->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="btn btn-secondary">بحث</button>
                                    <a href="{{ route('admin.job-vacancies.index') }}" class="btn btn-danger">مسح</a>
                                </form>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover align-middle table-nowrap mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>العنوان</th>
                                            <th>الكود</th>
                                            <th>القسم</th>
                                            <th>عدد المناصب</th>
                                            <th>المتقدمون</th>
                                            <th>تاريخ النشر</th>
                                            <th>الحالة</th>
                                            <th>العمليات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($vacancies as $vacancy)
                                            <tr>
                                                <th>{{ $loop->iteration }}</th>
                                                <td>
                                                    <strong>{{ $vacancy->title_ar ?? $vacancy->title }}</strong>
                                                </td>
                                                <td><span class="badge bg-info">{{ $vacancy->code }}</span></td>
                                                <td>
                                                    @if ($vacancy->department)
                                                        <span class="badge bg-primary">{{ $vacancy->department->name }}</span>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td><span class="badge bg-success">{{ $vacancy->number_of_positions }}</span></td>
                                                <td><span class="badge bg-warning">{{ $vacancy->applications_count ?? 0 }}</span></td>
                                                <td>{{ $vacancy->posted_date->format('Y-m-d') }}</td>
                                                <td>
                                                    @if ($vacancy->status == 'published')
                                                        <span class="badge bg-success">{{ $vacancy->status_name_ar }}</span>
                                                    @elseif ($vacancy->status == 'closed')
                                                        <span class="badge bg-danger">{{ $vacancy->status_name_ar }}</span>
                                                    @else
                                                        <span class="badge bg-secondary">{{ $vacancy->status_name_ar }}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @can('job-vacancy-show')
                                                    <a class="btn btn-info btn-sm me-1" href="{{ route('admin.job-vacancies.show', $vacancy->id) }}" title="عرض">
                                                        <i class="fa-solid fa-eye"></i>
                                                    </a>
                                                    @endcan
                                                    @can('job-vacancy-edit')
                                                    <a class="btn btn-warning btn-sm me-1" href="{{ route('admin.job-vacancies.edit', $vacancy->id) }}" title="تعديل">
                                                        <i class="fa-solid fa-pen-to-square"></i>
                                                    </a>
                                                    @endcan
                                                    @can('job-vacancy-delete')
                                                    <a class="btn btn-danger btn-sm me-1" data-bs-toggle="modal" data-bs-target="#delete{{ $vacancy->id }}" title="حذف">
                                                        <i class="fa-solid fa-trash-can"></i>
                                                    </a>
                                                    @endcan
                                                </td>
                                            </tr>
                                            @include('admin.pages.job-vacancies.delete')
                                        @empty
                                            <tr>
                                                <td colspan="9" class="text-center text-danger fw-bold">لا توجد بيانات متاحة</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>

                                <div class="mt-3">
                                    {{ $vacancies->withQueryString()->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop


