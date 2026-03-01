@extends('admin.layouts.master')

@section('page-title')
    المناوبات
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
                    <h5 class="page-title fs-21 mb-1">المناوبات</h5>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header align-items-center d-flex gap-3">
                            @can('shift-create')
                            <a href="{{ route('admin.shifts.create') }}" class="btn btn-primary btn-sm">إضافة مناوبة جديدة</a>
                            @endcan

                            <div class="flex-shrink-0">
                                <form action="{{ route('admin.shifts.index') }}" method="GET" class="d-flex align-items-center gap-2">
                                    <input type="text" name="search" class="form-control" placeholder="بحث..." value="{{ request('search') }}" style="width: 200px;">
                                    
                                    <select name="is_active" class="form-select" style="width: 120px;">
                                        <option value="">كل الحالات</option>
                                        <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>نشط</option>
                                        <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>غير نشط</option>
                                    </select>

                                    <button type="submit" class="btn btn-primary btn-sm">بحث</button>
                                    <a href="{{ route('admin.shifts.index') }}" class="btn btn-secondary btn-sm">إعادة تعيين</a>
                                </form>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered text-nowrap border-bottom">
                                    <thead>
                                        <tr>
                                            <th>الكود</th>
                                            <th>الاسم</th>
                                            <th>وقت البدء</th>
                                            <th>وقت الانتهاء</th>
                                            <th>المدة (ساعات)</th>
                                            <th>عدد الموظفين</th>
                                            <th>الحالة</th>
                                            <th>الإجراءات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($shifts as $shift)
                                            <tr>
                                                <td>{{ $shift->shift_code }}</td>
                                                <td>{{ $shift->name_ar ?? $shift->name }}</td>
                                                <td>{{ \Carbon\Carbon::parse($shift->start_time)->format('H:i') }}</td>
                                                <td>{{ \Carbon\Carbon::parse($shift->end_time)->format('H:i') }}</td>
                                                <td>{{ $shift->duration_hours }}</td>
                                                <td>{{ $shift->active_assignments_count ?? 0 }}</td>
                                                <td>
                                                    <span class="badge bg-{{ $shift->is_active ? 'success' : 'secondary' }}">
                                                        {{ $shift->is_active ? 'نشط' : 'غير نشط' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="d-flex gap-2">
                                                        @can('shift-show')
                                                        <a href="{{ route('admin.shifts.show', $shift->id) }}" class="btn btn-sm btn-info">عرض</a>
                                                        @endcan
                                                        @can('shift-edit')
                                                        <a href="{{ route('admin.shifts.edit', $shift->id) }}" class="btn btn-sm btn-primary">تعديل</a>
                                                        @endcan
                                                        @can('shift-delete')
                                                        <form action="{{ route('admin.shifts.destroy', $shift->id) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من الحذف؟');">
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
                                                <td colspan="8" class="text-center">لا توجد مناوبات</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <div class="d-flex justify-content-center mt-3">
                                {{ $shifts->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

