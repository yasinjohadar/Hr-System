@extends('admin.layouts.master')

@section('page-title')
    الساعات الإضافية
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
                    <h5 class="page-title fs-21 mb-1">الساعات الإضافية</h5>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header align-items-center d-flex gap-3">
                            @can('overtime-create')
                            <a href="{{ route('admin.overtimes.create') }}" class="btn btn-primary btn-sm">إضافة ساعات إضافية</a>
                            @endcan

                            <div class="flex-shrink-0">
                                <form action="{{ route('admin.overtimes.index') }}" method="GET" class="d-flex align-items-center gap-2">
                                    <input type="text" name="search" class="form-control" placeholder="بحث..." value="{{ request('search') }}" style="width: 200px;">
                                    
                                    <select name="employee_id" class="form-select" style="width: 200px;">
                                        <option value="">كل الموظفين</option>
                                        @foreach ($employees as $employee)
                                            <option value="{{ $employee->id }}" {{ request('employee_id') == $employee->id ? 'selected' : '' }}>
                                                {{ $employee->full_name }}
                                            </option>
                                        @endforeach
                                    </select>

                                    <select name="status" class="form-select" style="width: 150px;">
                                        <option value="">كل الحالات</option>
                                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>موافق عليه</option>
                                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>مرفوض</option>
                                        <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>مدفوع</option>
                                    </select>

                                    <input type="date" name="date_from" class="form-control" placeholder="من تاريخ" value="{{ request('date_from') }}" style="width: 150px;">
                                    <input type="date" name="date_to" class="form-control" placeholder="إلى تاريخ" value="{{ request('date_to') }}" style="width: 150px;">

                                    <button type="submit" class="btn btn-primary btn-sm">بحث</button>
                                    <a href="{{ route('admin.overtimes.index') }}" class="btn btn-secondary btn-sm">إعادة تعيين</a>
                                </form>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered text-nowrap border-bottom">
                                    <thead>
                                        <tr>
                                            <th>الموظف</th>
                                            <th>التاريخ</th>
                                            <th>من</th>
                                            <th>إلى</th>
                                            <th>الساعات</th>
                                            <th>المبلغ</th>
                                            <th>النوع</th>
                                            <th>الحالة</th>
                                            <th>الإجراءات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($overtimes as $overtime)
                                            <tr>
                                                <td>{{ $overtime->employee->full_name }}</td>
                                                <td>{{ $overtime->overtime_date->format('Y-m-d') }}</td>
                                                <td>{{ \Carbon\Carbon::parse($overtime->start_time)->format('H:i') }}</td>
                                                <td>{{ \Carbon\Carbon::parse($overtime->end_time)->format('H:i') }}</td>
                                                <td>{{ number_format($overtime->overtime_hours, 2) }}</td>
                                                <td>{{ number_format($overtime->overtime_amount, 2) }}</td>
                                                <td><span class="badge bg-info">{{ $overtime->overtime_type_name_ar }}</span></td>
                                                <td>
                                                    <span class="badge bg-{{ match($overtime->status) {
                                                        'pending' => 'warning',
                                                        'approved' => 'success',
                                                        'rejected' => 'danger',
                                                        'paid' => 'primary',
                                                        default => 'secondary'
                                                    } }}">
                                                        {{ $overtime->status_name_ar }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="d-flex gap-2">
                                                        @can('overtime-show')
                                                        <a href="{{ route('admin.overtimes.show', $overtime->id) }}" class="btn btn-sm btn-info">عرض</a>
                                                        @endcan
                                                        @can('overtime-edit')
                                                        @if($overtime->status != 'paid')
                                                        <a href="{{ route('admin.overtimes.edit', $overtime->id) }}" class="btn btn-sm btn-primary">تعديل</a>
                                                        @endif
                                                        @endcan
                                                        @if($overtime->status == 'pending')
                                                        <form action="{{ route('admin.overtimes.approve', $overtime->id) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-success">موافقة</button>
                                                        </form>
                                                        <form action="{{ route('admin.overtimes.reject', $overtime->id) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-danger">رفض</button>
                                                        </form>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="9" class="text-center">لا توجد ساعات إضافية</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <div class="d-flex justify-content-center mt-3">
                                {{ $overtimes->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

