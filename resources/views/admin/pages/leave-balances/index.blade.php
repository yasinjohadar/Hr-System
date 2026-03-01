@extends('admin.layouts.master')

@section('page-title')
    قائمة أرصدة الإجازات
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
                    <h5 class="page-title fs-21 mb-1">كافة أرصدة الإجازات</h5>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header align-items-center d-flex gap-3">
                            @can('leave-balance-create')
                            <a href="{{ route('admin.leave-balances.create') }}" class="btn btn-primary btn-sm">إضافة رصيد إجازة جديد</a>
                            @endcan

                            <div class="flex-shrink-0">
                                <form action="{{ route('admin.leave-balances.index') }}" method="GET" class="d-flex align-items-center gap-2">
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
                                    <select name="year" class="form-select" style="width: 120px;">
                                        @if ($years->isEmpty())
                                            <option value="{{ date('Y') }}" {{ request('year', $currentYear) == date('Y') ? 'selected' : '' }}>{{ date('Y') }}</option>
                                        @else
                                            @foreach ($years as $year)
                                                <option value="{{ $year }}" {{ request('year', $currentYear) == $year ? 'selected' : '' }}>{{ $year }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    <button type="submit" class="btn btn-secondary">بحث</button>
                                    <a href="{{ route('admin.leave-balances.index') }}" class="btn btn-danger">مسح</a>
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
                                            <th>السنة</th>
                                            <th>إجمالي الأيام</th>
                                            <th>المستخدم</th>
                                            <th>المتبقي</th>
                                            <th>المحمل</th>
                                            <th>العمليات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($leaveBalances as $balance)
                                            <tr>
                                                <th>{{ $loop->iteration }}</th>
                                                <td>
                                                    <strong>{{ $balance->employee->full_name ?? $balance->employee->first_name . ' ' . $balance->employee->last_name }}</strong>
                                                </td>
                                                <td>
                                                    <span class="badge bg-info">{{ $balance->leaveType->name_ar ?? $balance->leaveType->name }}</span>
                                                </td>
                                                <td>{{ $balance->year }}</td>
                                                <td><span class="badge bg-primary">{{ $balance->total_days }} يوم</span></td>
                                                <td><span class="badge bg-warning">{{ $balance->used_days }} يوم</span></td>
                                                <td>
                                                    @if ($balance->remaining_days > 0)
                                                        <span class="badge bg-success">{{ $balance->remaining_days }} يوم</span>
                                                    @else
                                                        <span class="badge bg-danger">{{ $balance->remaining_days }} يوم</span>
                                                    @endif
                                                </td>
                                                <td>{{ $balance->carried_forward }} يوم</td>
                                                <td>
                                                    @can('leave-balance-edit')
                                                    <a class="btn btn-warning btn-sm me-1" href="{{ route('admin.leave-balances.edit', $balance->id) }}" title="تعديل">
                                                        <i class="fa-solid fa-pen-to-square"></i>
                                                    </a>
                                                    @endcan
                                                    @can('leave-balance-delete')
                                                    <a class="btn btn-danger btn-sm me-1" data-bs-toggle="modal" data-bs-target="#delete{{ $balance->id }}" title="حذف">
                                                        <i class="fa-solid fa-trash-can"></i>
                                                    </a>
                                                    @endcan
                                                </td>
                                            </tr>
                                            @include('admin.pages.leave-balances.delete')
                                        @empty
                                            <tr>
                                                <td colspan="9" class="text-center text-danger fw-bold">لا توجد بيانات متاحة</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>

                                <div class="mt-3">
                                    {{ $leaveBalances->withQueryString()->links() }}
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


