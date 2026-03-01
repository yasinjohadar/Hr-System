@extends('admin.layouts.master')

@section('page-title')
    مواقع الحضور
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
                    <h5 class="page-title fs-21 mb-1">مواقع الحضور (GPS)</h5>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header align-items-center d-flex gap-3">
                            @can('attendance-location-create')
                            <a href="{{ route('admin.attendance-locations.create') }}" class="btn btn-primary btn-sm">إضافة موقع جديد</a>
                            @endcan

                            <div class="flex-shrink-0">
                                <form action="{{ route('admin.attendance-locations.index') }}" method="GET" class="d-flex align-items-center gap-2">
                                    <input type="text" name="search" class="form-control" placeholder="بحث..." value="{{ request('search') }}" style="width: 200px;">
                                    
                                    <select name="is_active" class="form-select" style="width: 120px;">
                                        <option value="">كل الحالات</option>
                                        <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>نشط</option>
                                        <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>غير نشط</option>
                                    </select>

                                    <button type="submit" class="btn btn-primary btn-sm">بحث</button>
                                    <a href="{{ route('admin.attendance-locations.index') }}" class="btn btn-secondary btn-sm">إعادة تعيين</a>
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
                                            <th>الإحداثيات</th>
                                            <th>نصف القطر (متر)</th>
                                            <th>يتطلب الموقع</th>
                                            <th>الحالة</th>
                                            <th>الإجراءات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($locations as $location)
                                            <tr>
                                                <td>{{ $location->code }}</td>
                                                <td>{{ $location->name_ar ?? $location->name }}</td>
                                                <td>
                                                    <small>{{ number_format($location->latitude, 6) }}, {{ number_format($location->longitude, 6) }}</small>
                                                </td>
                                                <td>{{ $location->radius_meters }} م</td>
                                                <td>
                                                    @if($location->require_location)
                                                        <span class="badge bg-success">نعم</span>
                                                    @else
                                                        <span class="badge bg-secondary">لا</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="badge bg-{{ $location->is_active ? 'success' : 'secondary' }}">
                                                        {{ $location->is_active ? 'نشط' : 'غير نشط' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="d-flex gap-2">
                                                        @can('attendance-location-show')
                                                        <a href="{{ route('admin.attendance-locations.show', $location->id) }}" class="btn btn-sm btn-info">عرض</a>
                                                        @endcan
                                                        @can('attendance-location-edit')
                                                        <a href="{{ route('admin.attendance-locations.edit', $location->id) }}" class="btn btn-sm btn-primary">تعديل</a>
                                                        @endcan
                                                        @can('attendance-location-delete')
                                                        <form action="{{ route('admin.attendance-locations.destroy', $location->id) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من الحذف؟');">
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
                                                <td colspan="7" class="text-center">لا توجد مواقع حضور</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <div class="d-flex justify-content-center mt-3">
                                {{ $locations->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

