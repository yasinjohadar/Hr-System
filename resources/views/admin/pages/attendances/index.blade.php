@extends('admin.layouts.master')

@section('page-title')
    قائمة الحضور والانصراف
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
                    <h5 class="page-title fs-21 mb-1">كافة سجلات الحضور والانصراف</h5>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header align-items-center d-flex gap-3">
                            @can('attendance-create')
                            <a href="{{ route('admin.attendances.create') }}" class="btn btn-primary btn-sm">إضافة سجل حضور جديد</a>
                            @endcan

                            <div class="flex-shrink-0">
                                <form action="{{ route('admin.attendances.index') }}" method="GET" class="d-flex align-items-center gap-2">
                                    <select name="employee_id" class="form-select" style="width: 200px;">
                                        <option value="">كل الموظفين</option>
                                        @foreach ($employees as $employee)
                                            <option value="{{ $employee->id }}" {{ request('employee_id') == $employee->id ? 'selected' : '' }}>
                                                {{ $employee->full_name ?? $employee->first_name . ' ' . $employee->last_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <input type="date" name="start_date" class="form-control" style="width: 150px;"
                                           value="{{ request('start_date', $currentStartDate) }}">
                                    <input type="date" name="end_date" class="form-control" style="width: 150px;"
                                           value="{{ request('end_date', $currentEndDate) }}">
                                    <select name="status" class="form-select" style="width: 150px;">
                                        <option value="">كل الحالات</option>
                                        <option value="present" {{ request('status') == 'present' ? 'selected' : '' }}>حاضر</option>
                                        <option value="absent" {{ request('status') == 'absent' ? 'selected' : '' }}>غائب</option>
                                        <option value="late" {{ request('status') == 'late' ? 'selected' : '' }}>متأخر</option>
                                        <option value="half_day" {{ request('status') == 'half_day' ? 'selected' : '' }}>نصف يوم</option>
                                        <option value="on_leave" {{ request('status') == 'on_leave' ? 'selected' : '' }}>في إجازة</option>
                                        <option value="holiday" {{ request('status') == 'holiday' ? 'selected' : '' }}>عطلة</option>
                                    </select>
                                    <button type="submit" class="btn btn-secondary">بحث</button>
                                    <a href="{{ route('admin.attendances.index') }}" class="btn btn-danger">مسح</a>
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
                                            <th>التاريخ</th>
                                            <th>وقت الدخول</th>
                                            <th>وقت الخروج</th>
                                            <th>ساعات العمل</th>
                                            <th>التأخير</th>
                                            <th>ساعات إضافية</th>
                                            <th>الحالة</th>
                                            <th>العمليات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($attendances as $attendance)
                                            <tr>
                                                <th>{{ $loop->iteration }}</th>
                                                <td>
                                                    <strong>{{ $attendance->employee->full_name ?? $attendance->employee->first_name . ' ' . $attendance->employee->last_name }}</strong>
                                                    <br><small class="text-muted">{{ $attendance->employee->employee_code ?? '' }}</small>
                                                </td>
                                                <td>{{ $attendance->attendance_date->format('Y-m-d') }}</td>
                                                <td>
                                                    @if ($attendance->check_in)
                                                        <span class="badge bg-success">{{ is_string($attendance->check_in) ? $attendance->check_in : $attendance->check_in->format('H:i') }}</span>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($attendance->check_out)
                                                        <span class="badge bg-info">{{ is_string($attendance->check_out) ? $attendance->check_out : $attendance->check_out->format('H:i') }}</span>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($attendance->hours_worked > 0)
                                                        <span class="badge bg-primary">{{ $attendance->hours_worked_formatted }}</span>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($attendance->late_minutes > 0)
                                                        <span class="badge bg-warning">{{ $attendance->late_minutes }} دقيقة</span>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($attendance->overtime_minutes > 0)
                                                        <span class="badge bg-success">{{ floor($attendance->overtime_minutes / 60) }}:{{ str_pad($attendance->overtime_minutes % 60, 2, '0', STR_PAD_LEFT) }}</span>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($attendance->status == 'present')
                                                        <span class="badge bg-success">حاضر</span>
                                                    @elseif ($attendance->status == 'absent')
                                                        <span class="badge bg-danger">غائب</span>
                                                    @elseif ($attendance->status == 'late')
                                                        <span class="badge bg-warning">متأخر</span>
                                                    @elseif ($attendance->status == 'half_day')
                                                        <span class="badge bg-info">نصف يوم</span>
                                                    @elseif ($attendance->status == 'on_leave')
                                                        <span class="badge bg-secondary">في إجازة</span>
                                                    @else
                                                        <span class="badge bg-primary">عطلة</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @can('attendance-show')
                                                    <a class="btn btn-info btn-sm me-1" href="{{ route('admin.attendances.show', $attendance->id) }}" title="عرض">
                                                        <i class="fa-solid fa-eye"></i>
                                                    </a>
                                                    @endcan
                                                    @can('attendance-edit')
                                                    <a class="btn btn-warning btn-sm me-1" href="{{ route('admin.attendances.edit', $attendance->id) }}" title="تعديل">
                                                        <i class="fa-solid fa-pen-to-square"></i>
                                                    </a>
                                                    @endcan
                                                    @can('attendance-delete')
                                                    <a class="btn btn-danger btn-sm me-1" data-bs-toggle="modal" data-bs-target="#delete{{ $attendance->id }}" title="حذف">
                                                        <i class="fa-solid fa-trash-can"></i>
                                                    </a>
                                                    @endcan
                                                </td>
                                            </tr>
                                            @include('admin.pages.attendances.delete')
                                        @empty
                                            <tr>
                                                <td colspan="10" class="text-center text-danger fw-bold">لا توجد بيانات متاحة</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>

                                <div class="mt-3">
                                    {{ $attendances->withQueryString()->links() }}
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

