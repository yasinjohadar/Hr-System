@extends('admin.layouts.master')

@section('page-title')
    قائمة أنواع الإجازات
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
                    <h5 class="page-title fs-21 mb-1">كافة أنواع الإجازات</h5>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header align-items-center d-flex gap-3">
                            @can('leave-type-create')
                            <a href="{{ route('admin.leave-types.create') }}" class="btn btn-primary btn-sm">إضافة نوع إجازة جديد</a>
                            @endcan

                            <div class="flex-shrink-0">
                                <form action="{{ route('admin.leave-types.index') }}" method="GET" class="d-flex align-items-center gap-2">
                                    <input style="width: 300px" type="text" name="query" class="form-control"
                                        placeholder="بحث بالاسم أو الكود" value="{{ request('query') }}">
                                    <select name="is_active" class="form-select">
                                        <option value="">كل الحالات</option>
                                        <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>نشط</option>
                                        <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>غير نشط</option>
                                    </select>
                                    <button type="submit" class="btn btn-secondary">بحث</button>
                                    <a href="{{ route('admin.leave-types.index') }}" class="btn btn-danger">مسح</a>
                                </form>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover align-middle table-nowrap mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>اسم نوع الإجازة</th>
                                            <th>الكود</th>
                                            <th>الحد الأقصى</th>
                                            <th>مدفوعة</th>
                                            <th>تحتاج موافقة</th>
                                            <th>ترحيل</th>
                                            <th>الحالة</th>
                                            <th>العمليات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($leaveTypes as $leaveType)
                                            <tr>
                                                <th>{{ $loop->iteration }}</th>
                                                <td>
                                                    <strong>{{ $leaveType->name_ar ?? $leaveType->name }}</strong>
                                                    @if ($leaveType->name_ar && $leaveType->name_ar != $leaveType->name)
                                                        <br><small class="text-muted">({{ $leaveType->name }})</small>
                                                    @endif
                                                </td>
                                                <td><span class="badge bg-info">{{ $leaveType->code }}</span></td>
                                                <td>{{ $leaveType->max_days ?? 'غير محدد' }} يوم</td>
                                                <td>
                                                    @if ($leaveType->is_paid)
                                                        <span class="badge bg-success">نعم</span>
                                                    @else
                                                        <span class="badge bg-danger">لا</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($leaveType->requires_approval)
                                                        <span class="badge bg-warning">نعم</span>
                                                    @else
                                                        <span class="badge bg-secondary">لا</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($leaveType->carry_forward)
                                                        <span class="badge bg-info">نعم</span>
                                                    @else
                                                        <span class="badge bg-secondary">لا</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($leaveType->is_active)
                                                        <span class="badge bg-success">نشط</span>
                                                    @else
                                                        <span class="badge bg-danger">غير نشط</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @can('leave-type-edit')
                                                    <a class="btn btn-warning btn-sm me-1" href="{{ route('admin.leave-types.edit', $leaveType->id) }}" title="تعديل">
                                                        <i class="fa-solid fa-pen-to-square"></i>
                                                    </a>
                                                    @endcan
                                                    @can('leave-type-delete')
                                                    <a class="btn btn-danger btn-sm me-1" data-bs-toggle="modal" data-bs-target="#delete{{ $leaveType->id }}" title="حذف">
                                                        <i class="fa-solid fa-trash-can"></i>
                                                    </a>
                                                    @endcan
                                                </td>
                                            </tr>
                                            @include('admin.pages.leave-types.delete')
                                        @empty
                                            <tr>
                                                <td colspan="9" class="text-center text-danger fw-bold">لا توجد بيانات متاحة</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>

                                <div class="mt-3">
                                    {{ $leaveTypes->withQueryString()->links() }}
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


