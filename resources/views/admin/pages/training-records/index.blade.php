@extends('admin.layouts.master')

@section('page-title')
    قائمة سجلات التدريب
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
                    <h5 class="page-title fs-21 mb-1">كافة سجلات التدريب</h5>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header align-items-center d-flex gap-3">
                            @can('training-record-create')
                            <a href="{{ route('admin.training-records.create') }}" class="btn btn-primary btn-sm">إضافة سجل تدريب جديد</a>
                            @endcan

                            <div class="flex-shrink-0">
                                <form action="{{ route('admin.training-records.index') }}" method="GET" class="d-flex align-items-center gap-2">
                                    <select name="training_id" class="form-select" style="width: 200px;">
                                        <option value="">كل الدورات</option>
                                        @foreach ($trainings as $training)
                                            <option value="{{ $training->id }}" {{ request('training_id') == $training->id ? 'selected' : '' }}>
                                                {{ $training->title_ar ?? $training->title }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <select name="employee_id" class="form-select" style="width: 200px;">
                                        <option value="">كل الموظفين</option>
                                        @foreach ($employees as $employee)
                                            <option value="{{ $employee->id }}" {{ request('employee_id') == $employee->id ? 'selected' : '' }}>
                                                {{ $employee->full_name ?? $employee->first_name . ' ' . $employee->last_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <select name="status" class="form-select" style="width: 150px;">
                                        <option value="">كل الحالات</option>
                                        <option value="registered" {{ request('status') == 'registered' ? 'selected' : '' }}>مسجل</option>
                                        <option value="attending" {{ request('status') == 'attending' ? 'selected' : '' }}>يحضر</option>
                                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>مكتمل</option>
                                        <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>فاشل</option>
                                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>ملغي</option>
                                    </select>
                                    <button type="submit" class="btn btn-secondary">بحث</button>
                                    <a href="{{ route('admin.training-records.index') }}" class="btn btn-danger">مسح</a>
                                </form>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover align-middle table-nowrap mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>الدورة التدريبية</th>
                                            <th>الموظف</th>
                                            <th>تاريخ التسجيل</th>
                                            <th>الحالة</th>
                                            <th>النتيجة</th>
                                            <th>العمليات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($records as $record)
                                            <tr>
                                                <th>{{ $loop->iteration }}</th>
                                                <td>
                                                    <strong>{{ $record->training->title_ar ?? $record->training->title }}</strong>
                                                    <br><small class="text-muted">{{ $record->training->code }}</small>
                                                </td>
                                                <td>
                                                    {{ $record->employee->full_name ?? $record->employee->first_name . ' ' . $record->employee->last_name }}
                                                </td>
                                                <td>
                                                    {{ $record->registration_date ? $record->registration_date->format('Y-m-d') : '-' }}
                                                </td>
                                                <td>
                                                    @if ($record->status == 'completed')
                                                        <span class="badge bg-success">مكتمل</span>
                                                    @elseif ($record->status == 'attending')
                                                        <span class="badge bg-primary">يحضر</span>
                                                    @elseif ($record->status == 'registered')
                                                        <span class="badge bg-info">مسجل</span>
                                                    @elseif ($record->status == 'failed')
                                                        <span class="badge bg-danger">فاشل</span>
                                                    @else
                                                        <span class="badge bg-secondary">ملغي</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($record->score)
                                                        <span class="badge bg-{{ $record->score >= 80 ? 'success' : ($record->score >= 60 ? 'warning' : 'danger') }}">
                                                            {{ number_format($record->score, 2) }}%
                                                        </span>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @can('training-record-show')
                                                    <a class="btn btn-info btn-sm me-1" href="{{ route('admin.training-records.show', $record->id) }}" title="عرض">
                                                        <i class="fa-solid fa-eye"></i>
                                                    </a>
                                                    @endcan
                                                    @can('training-record-edit')
                                                    <a class="btn btn-warning btn-sm me-1" href="{{ route('admin.training-records.edit', $record->id) }}" title="تعديل">
                                                        <i class="fa-solid fa-pen-to-square"></i>
                                                    </a>
                                                    @endcan
                                                    @can('training-record-delete')
                                                    <a class="btn btn-danger btn-sm me-1" data-bs-toggle="modal" data-bs-target="#delete{{ $record->id }}" title="حذف">
                                                        <i class="fa-solid fa-trash-can"></i>
                                                    </a>
                                                    @endcan
                                                </td>
                                            </tr>
                                            @include('admin.pages.training-records.delete')
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center text-danger fw-bold">لا توجد بيانات متاحة</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>

                                <div class="mt-3">
                                    {{ $records->withQueryString()->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop


