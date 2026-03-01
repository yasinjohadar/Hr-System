@extends('admin.layouts.master')

@section('page-title')
    قائمة طلبات الإجازات
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
                    <h5 class="page-title fs-21 mb-1">كافة طلبات الإجازات</h5>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header align-items-center d-flex gap-3">
                            @can('leave-request-create')
                            <a href="{{ route('admin.leave-requests.create') }}" class="btn btn-primary btn-sm">إضافة طلب إجازة جديد</a>
                            @endcan

                            <div class="flex-shrink-0">
                                <form action="{{ route('admin.leave-requests.index') }}" method="GET" class="d-flex align-items-center gap-2">
                                    <select name="employee_id" class="form-select" style="width: 200px;">
                                        <option value="">كل الموظفين</option>
                                        @foreach ($employees as $employee)
                                            <option value="{{ $employee->id }}" {{ request('employee_id') == $employee->id ? 'selected' : '' }}>
                                                {{ $employee->full_name ?? $employee->first_name . ' ' . $employee->last_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <select name="leave_type_id" class="form-select" style="width: 150px;">
                                        <option value="">كل الأنواع</option>
                                        @foreach ($leaveTypes as $type)
                                            <option value="{{ $type->id }}" {{ request('leave_type_id') == $type->id ? 'selected' : '' }}>
                                                {{ $type->name_ar ?? $type->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <select name="status" class="form-select" style="width: 150px;">
                                        <option value="">كل الحالات</option>
                                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>موافق عليه</option>
                                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>مرفوض</option>
                                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>ملغي</option>
                                    </select>
                                    <button type="submit" class="btn btn-secondary">بحث</button>
                                    <a href="{{ route('admin.leave-requests.index') }}" class="btn btn-danger">مسح</a>
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
                                            <th>نوع الإجازة</th>
                                            <th>من تاريخ</th>
                                            <th>إلى تاريخ</th>
                                            <th>عدد الأيام</th>
                                            <th>الحالة</th>
                                            <th>العمليات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($leaveRequests as $request)
                                            <tr>
                                                <th>{{ $loop->iteration }}</th>
                                                <td>
                                                    <strong>{{ $request->employee->full_name ?? $request->employee->first_name . ' ' . $request->employee->last_name }}</strong>
                                                </td>
                                                <td>
                                                    <span class="badge bg-info">{{ $request->leaveType->name_ar ?? $request->leaveType->name }}</span>
                                                </td>
                                                <td>{{ $request->start_date->format('Y-m-d') }}</td>
                                                <td>{{ $request->end_date->format('Y-m-d') }}</td>
                                                <td><span class="badge bg-primary">{{ $request->days_count }} يوم</span></td>
                                                <td>
                                                    @if ($request->status == 'approved')
                                                        <span class="badge bg-success">موافق عليه</span>
                                                    @elseif ($request->status == 'pending')
                                                        <span class="badge bg-warning">قيد الانتظار</span>
                                                    @elseif ($request->status == 'rejected')
                                                        <span class="badge bg-danger">مرفوض</span>
                                                    @else
                                                        <span class="badge bg-secondary">ملغي</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @can('leave-request-show')
                                                    <a class="btn btn-info btn-sm me-1" href="{{ route('admin.leave-requests.show', $request->id) }}" title="عرض">
                                                        <i class="fa-solid fa-eye"></i>
                                                    </a>
                                                    @endcan
                                                    @if ($request->status == 'pending')
                                                        @can('leave-request-approve')
                                                        <form action="{{ route('admin.leave-requests.approve', $request->id) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-success btn-sm me-1" 
                                                                    onclick="return confirm('هل أنت متأكد من الموافقة على هذا الطلب؟')" title="موافقة">
                                                                <i class="fa-solid fa-check"></i>
                                                            </button>
                                                        </form>
                                                        @endcan
                                                        @can('leave-request-approve')
                                                        <button class="btn btn-danger btn-sm me-1" data-bs-toggle="modal" data-bs-target="#reject{{ $request->id }}" title="رفض">
                                                            <i class="fa-solid fa-times"></i>
                                                        </button>
                                                        @endcan
                                                        @can('leave-request-edit')
                                                        <a class="btn btn-warning btn-sm me-1" href="{{ route('admin.leave-requests.edit', $request->id) }}" title="تعديل">
                                                            <i class="fa-solid fa-pen-to-square"></i>
                                                        </a>
                                                        @endcan
                                                    @endif
                                                    @can('leave-request-delete')
                                                    <a class="btn btn-danger btn-sm me-1" data-bs-toggle="modal" data-bs-target="#delete{{ $request->id }}" title="حذف">
                                                        <i class="fa-solid fa-trash-can"></i>
                                                    </a>
                                                    @endcan
                                                </td>
                                            </tr>
                                            @include('admin.pages.leave-requests.delete')
                                            @include('admin.pages.leave-requests.reject')
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center text-danger fw-bold">لا توجد بيانات متاحة</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>

                                <div class="mt-3">
                                    {{ $leaveRequests->withQueryString()->links() }}
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

