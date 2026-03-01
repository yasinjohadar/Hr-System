@extends('admin.layouts.master')

@section('page-title')
    قائمة طلبات التوظيف
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
                    <h5 class="page-title fs-21 mb-1">كافة طلبات التوظيف</h5>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header align-items-center d-flex gap-3">
                            @can('job-application-create')
                            <a href="{{ route('admin.job-applications.create') }}" class="btn btn-primary btn-sm">إضافة طلب توظيف جديد</a>
                            @endcan

                            <div class="flex-shrink-0">
                                <form action="{{ route('admin.job-applications.index') }}" method="GET" class="d-flex align-items-center gap-2">
                                    <input style="width: 300px" type="text" name="search" class="form-control"
                                        placeholder="بحث" value="{{ request('search') }}">
                                    <select name="status" class="form-select">
                                        <option value="">كل الحالات</option>
                                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                                        <option value="reviewing" {{ request('status') == 'reviewing' ? 'selected' : '' }}>قيد المراجعة</option>
                                        <option value="shortlisted" {{ request('status') == 'shortlisted' ? 'selected' : '' }}>قائمة مختصرة</option>
                                        <option value="accepted" {{ request('status') == 'accepted' ? 'selected' : '' }}>مقبول</option>
                                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>مرفوض</option>
                                    </select>
                                    <select name="job_vacancy_id" class="form-select">
                                        <option value="">كل الوظائف</option>
                                        @foreach ($vacancies as $vacancy)
                                            <option value="{{ $vacancy->id }}" {{ request('job_vacancy_id') == $vacancy->id ? 'selected' : '' }}>
                                                {{ $vacancy->title_ar ?? $vacancy->title }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="btn btn-secondary">بحث</button>
                                    <a href="{{ route('admin.job-applications.index') }}" class="btn btn-danger">مسح</a>
                                </form>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover align-middle table-nowrap mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>المرشح</th>
                                            <th>الوظيفة</th>
                                            <th>تاريخ التقديم</th>
                                            <th>المصدر</th>
                                            <th>الحالة</th>
                                            <th>العمليات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($applications as $application)
                                            <tr>
                                                <th>{{ $loop->iteration }}</th>
                                                <td>
                                                    <strong>{{ $application->candidate->full_name }}</strong>
                                                    <br><small class="text-muted">{{ $application->candidate->email }}</small>
                                                </td>
                                                <td>{{ $application->jobVacancy->title_ar ?? $application->jobVacancy->title }}</td>
                                                <td>{{ $application->application_date->format('Y-m-d') }}</td>
                                                <td><span class="badge bg-info">{{ $application->source_name_ar }}</span></td>
                                                <td>
                                                    <span class="badge bg-{{ $application->status == 'accepted' ? 'success' : ($application->status == 'rejected' ? 'danger' : 'warning') }}">
                                                        {{ $application->status_name_ar }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @can('job-application-show')
                                                    <a class="btn btn-info btn-sm me-1" href="{{ route('admin.job-applications.show', $application->id) }}" title="عرض">
                                                        <i class="fa-solid fa-eye"></i>
                                                    </a>
                                                    @endcan
                                                    @can('job-application-edit')
                                                    <a class="btn btn-warning btn-sm me-1" href="{{ route('admin.job-applications.edit', $application->id) }}" title="تعديل">
                                                        <i class="fa-solid fa-pen-to-square"></i>
                                                    </a>
                                                    @endcan
                                                    @can('job-application-delete')
                                                    <a class="btn btn-danger btn-sm me-1" data-bs-toggle="modal" data-bs-target="#delete{{ $application->id }}" title="حذف">
                                                        <i class="fa-solid fa-trash-can"></i>
                                                    </a>
                                                    @endcan
                                                </td>
                                            </tr>
                                            @include('admin.pages.job-applications.delete')
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center text-danger fw-bold">لا توجد بيانات متاحة</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>

                                <div class="mt-3">
                                    {{ $applications->withQueryString()->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop


