@extends('admin.layouts.master')

@section('page-title')
    تعيينات المناوبات
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
                    <h5 class="page-title fs-21 mb-1">تعيينات المناوبات</h5>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header align-items-center d-flex gap-3">
                            @can('shift-assignment-create')
                            <a href="{{ route('admin.shift-assignments.create') }}" class="btn btn-primary btn-sm">تعيين مناوبة جديدة</a>
                            @endcan

                            <div class="flex-shrink-0">
                                <form action="{{ route('admin.shift-assignments.index') }}" method="GET" class="d-flex align-items-center gap-2">
                                    <input type="text" name="search" class="form-control" placeholder="بحث..." value="{{ request('search') }}" style="width: 200px;">
                                    
                                    <select name="employee_id" class="form-select" style="width: 200px;">
                                        <option value="">كل الموظفين</option>
                                        @foreach ($employees as $employee)
                                            <option value="{{ $employee->id }}" {{ request('employee_id') == $employee->id ? 'selected' : '' }}>
                                                {{ $employee->full_name }}
                                            </option>
                                        @endforeach
                                    </select>

                                    <select name="shift_id" class="form-select" style="width: 200px;">
                                        <option value="">كل المناوبات</option>
                                        @foreach ($shifts as $shift)
                                            <option value="{{ $shift->id }}" {{ request('shift_id') == $shift->id ? 'selected' : '' }}>
                                                {{ $shift->name_ar ?? $shift->name }}
                                            </option>
                                        @endforeach
                                    </select>

                                    <select name="is_active" class="form-select" style="width: 120px;">
                                        <option value="">كل الحالات</option>
                                        <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>نشط</option>
                                        <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>غير نشط</option>
                                    </select>

                                    <button type="submit" class="btn btn-primary btn-sm">بحث</button>
                                    <a href="{{ route('admin.shift-assignments.index') }}" class="btn btn-secondary btn-sm">إعادة تعيين</a>
                                </form>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered text-nowrap border-bottom">
                                    <thead>
                                        <tr>
                                            <th>الموظف</th>
                                            <th>المناوبة</th>
                                            <th>تاريخ البدء</th>
                                            <th>تاريخ الانتهاء</th>
                                            <th>الحالة</th>
                                            <th>الإجراءات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($assignments as $assignment)
                                            <tr>
                                                <td>{{ $assignment->employee->full_name }}</td>
                                                <td>{{ $assignment->shift->name_ar ?? $assignment->shift->name }}</td>
                                                <td>{{ $assignment->start_date->format('Y-m-d') }}</td>
                                                <td>{{ $assignment->end_date ? $assignment->end_date->format('Y-m-d') : 'دائم' }}</td>
                                                <td>
                                                    <span class="badge bg-{{ $assignment->is_active ? 'success' : 'secondary' }}">
                                                        {{ $assignment->is_active ? 'نشط' : 'غير نشط' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="d-flex gap-2">
                                                        @can('shift-assignment-show')
                                                        <a href="{{ route('admin.shift-assignments.show', $assignment->id) }}" class="btn btn-sm btn-info">عرض</a>
                                                        @endcan
                                                        @can('shift-assignment-edit')
                                                        <a href="{{ route('admin.shift-assignments.edit', $assignment->id) }}" class="btn btn-sm btn-primary">تعديل</a>
                                                        @endcan
                                                        @can('shift-assignment-delete')
                                                        <form action="{{ route('admin.shift-assignments.destroy', $assignment->id) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من الحذف؟');">
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
                                                <td colspan="6" class="text-center">لا توجد تعيينات</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <div class="d-flex justify-content-center mt-3">
                                {{ $assignments->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

