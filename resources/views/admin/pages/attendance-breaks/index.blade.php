@extends('admin.layouts.master')

@section('page-title')
    استراحات الحضور
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
                    <h5 class="page-title fs-21 mb-1">استراحات الحضور</h5>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header align-items-center d-flex gap-3">
                            @can('attendance-break-create')
                            <a href="{{ route('admin.attendance-breaks.create') }}" class="btn btn-primary btn-sm">إضافة استراحة جديدة</a>
                            @endcan

                            <div class="flex-shrink-0">
                                <form action="{{ route('admin.attendance-breaks.index') }}" method="GET" class="d-flex align-items-center gap-2">
                                    <input type="text" name="search" class="form-control" placeholder="بحث..." value="{{ request('search') }}" style="width: 200px;">
                                    
                                    <select name="attendance_id" class="form-select" style="width: 200px;">
                                        <option value="">كل سجلات الحضور</option>
                                        @foreach ($attendances as $attendance)
                                            <option value="{{ $attendance->id }}" {{ request('attendance_id') == $attendance->id ? 'selected' : '' }}>
                                                {{ $attendance->employee->full_name }} - {{ $attendance->attendance_date }}
                                            </option>
                                        @endforeach
                                    </select>

                                    <select name="break_type" class="form-select" style="width: 150px;">
                                        <option value="">كل الأنواع</option>
                                        <option value="lunch" {{ request('break_type') == 'lunch' ? 'selected' : '' }}>غداء</option>
                                        <option value="coffee" {{ request('break_type') == 'coffee' ? 'selected' : '' }}>قهوة</option>
                                        <option value="prayer" {{ request('break_type') == 'prayer' ? 'selected' : '' }}>صلاة</option>
                                        <option value="other" {{ request('break_type') == 'other' ? 'selected' : '' }}>أخرى</option>
                                    </select>

                                    <button type="submit" class="btn btn-primary btn-sm">بحث</button>
                                    <a href="{{ route('admin.attendance-breaks.index') }}" class="btn btn-secondary btn-sm">إعادة تعيين</a>
                                </form>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered text-nowrap border-bottom">
                                    <thead>
                                        <tr>
                                            <th>الموظف</th>
                                            <th>تاريخ الحضور</th>
                                            <th>نوع الاستراحة</th>
                                            <th>وقت البدء</th>
                                            <th>وقت الانتهاء</th>
                                            <th>المدة (دقيقة)</th>
                                            <th>الإجراءات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($breaks as $break)
                                            <tr>
                                                <td>{{ $break->attendance->employee->full_name }}</td>
                                                <td>{{ $break->attendance->attendance_date }}</td>
                                                <td>
                                                    <span class="badge bg-info">{{ $break->break_type_name_ar }}</span>
                                                </td>
                                                <td>{{ $break->break_start }}</td>
                                                <td>{{ $break->break_end ?? '-' }}</td>
                                                <td>{{ $break->duration_minutes }} دقيقة</td>
                                                <td>
                                                    <div class="d-flex gap-2">
                                                        @can('attendance-break-show')
                                                        <a href="{{ route('admin.attendance-breaks.show', $break->id) }}" class="btn btn-sm btn-info">عرض</a>
                                                        @endcan
                                                        @can('attendance-break-edit')
                                                        <a href="{{ route('admin.attendance-breaks.edit', $break->id) }}" class="btn btn-sm btn-primary">تعديل</a>
                                                        @endcan
                                                        @can('attendance-break-delete')
                                                        <form action="{{ route('admin.attendance-breaks.destroy', $break->id) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من الحذف؟');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger">حذف</button>
                                                        </form>
                                                        @endcan
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center">لا توجد استراحات</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <div class="d-flex justify-content-center mt-3">
                                {{ $breaks->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

