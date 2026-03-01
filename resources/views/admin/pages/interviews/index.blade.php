@extends('admin.layouts.master')

@section('page-title')
    قائمة المقابلات
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
                    <h5 class="page-title fs-21 mb-1">كافة المقابلات</h5>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header align-items-center d-flex gap-3">
                            @can('interview-create')
                            <a href="{{ route('admin.interviews.create') }}" class="btn btn-primary btn-sm">جدولة مقابلة جديدة</a>
                            @endcan

                            <div class="flex-shrink-0">
                                <form action="{{ route('admin.interviews.index') }}" method="GET" class="d-flex align-items-center gap-2">
                                    <input style="width: 300px" type="text" name="search" class="form-control"
                                        placeholder="بحث" value="{{ request('search') }}">
                                    <select name="status" class="form-select">
                                        <option value="">كل الحالات</option>
                                        <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>مجدولة</option>
                                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>مكتملة</option>
                                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>ملغاة</option>
                                    </select>
                                    <input type="date" name="interview_date" class="form-control" value="{{ request('interview_date') }}" placeholder="تاريخ المقابلة">
                                    <button type="submit" class="btn btn-secondary">بحث</button>
                                    <a href="{{ route('admin.interviews.index') }}" class="btn btn-danger">مسح</a>
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
                                            <th>نوع المقابلة</th>
                                            <th>التاريخ والوقت</th>
                                            <th>الحالة</th>
                                            <th>العمليات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($interviews as $interview)
                                            <tr>
                                                <th>{{ $loop->iteration }}</th>
                                                <td><strong>{{ $interview->candidate->full_name }}</strong></td>
                                                <td>{{ $interview->jobVacancy->title_ar ?? $interview->jobVacancy->title }}</td>
                                                <td><span class="badge bg-info">{{ $interview->type_name_ar }}</span></td>
                                                <td>
                                                    {{ $interview->interview_date->format('Y-m-d') }}
                                                    @if ($interview->interview_time)
                                                        <br><small class="text-muted">{{ $interview->interview_time->format('H:i') }}</small>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="badge bg-{{ $interview->status == 'completed' ? 'success' : ($interview->status == 'cancelled' ? 'danger' : 'warning') }}">
                                                        {{ $interview->status_name_ar }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @can('interview-show')
                                                    <a class="btn btn-info btn-sm me-1" href="{{ route('admin.interviews.show', $interview->id) }}" title="عرض">
                                                        <i class="fa-solid fa-eye"></i>
                                                    </a>
                                                    @endcan
                                                    @can('interview-edit')
                                                    <a class="btn btn-warning btn-sm me-1" href="{{ route('admin.interviews.edit', $interview->id) }}" title="تعديل">
                                                        <i class="fa-solid fa-pen-to-square"></i>
                                                    </a>
                                                    @endcan
                                                    @can('interview-delete')
                                                    <a class="btn btn-danger btn-sm me-1" data-bs-toggle="modal" data-bs-target="#delete{{ $interview->id }}" title="حذف">
                                                        <i class="fa-solid fa-trash-can"></i>
                                                    </a>
                                                    @endcan
                                                </td>
                                            </tr>
                                            @include('admin.pages.interviews.delete')
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center text-danger fw-bold">لا توجد بيانات متاحة</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>

                                <div class="mt-3">
                                    {{ $interviews->withQueryString()->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop


