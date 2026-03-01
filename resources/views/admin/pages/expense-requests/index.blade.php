@extends('admin.layouts.master')

@section('page-title')
    طلبات المصروفات
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">طلبات المصروفات</h5>
                </div>
                <div>
                    @can('expense-request-create')
                    <a href="{{ route('admin.expense-requests.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>إضافة طلب مصروف جديد
                    </a>
                    @endcan
                </div>
            </div>

            <!-- فلترة -->
            <div class="card mb-3">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.expense-requests.index') }}" class="row g-3">
                        <div class="col-md-3">
                            <select name="employee_id" class="form-select">
                                <option value="">كل الموظفين</option>
                                @foreach ($employees as $emp)
                                    <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>
                                        {{ $emp->full_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="expense_category_id" class="form-select">
                                <option value="">كل التصنيفات</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ request('expense_category_id') == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name_ar ?? $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="status" class="form-select">
                                <option value="">كل الحالات</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>موافق عليه</option>
                                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>مرفوض</option>
                                <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>مدفوع</option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>ملغي</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}" placeholder="من تاريخ">
                        </div>
                        <div class="col-md-2">
                            <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}" placeholder="إلى تاريخ">
                        </div>
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary">تطبيق الفلترة</button>
                            <a href="{{ route('admin.expense-requests.index') }}" class="btn btn-secondary">مسح</a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- جدول الطلبات -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">قائمة الطلبات ({{ $expenseRequests->total() }})</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>رقم الطلب</th>
                                    <th>الموظف</th>
                                    <th>التصنيف</th>
                                    <th>المبلغ</th>
                                    <th>تاريخ المصروف</th>
                                    <th>الحالة</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($expenseRequests as $request)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td><strong>{{ $request->request_code }}</strong></td>
                                        <td>
                                            <a href="{{ route('admin.employees.show', $request->employee_id) }}">
                                                {{ $request->employee->full_name }}
                                            </a>
                                        </td>
                                        <td>{{ $request->category->name_ar ?? $request->category->name }}</td>
                                        <td>
                                            <strong>{{ number_format($request->amount, 2) }}</strong>
                                            @if ($request->currency)
                                                <small class="text-muted">{{ $request->currency->code }}</small>
                                            @endif
                                        </td>
                                        <td>{{ $request->expense_date->format('Y-m-d') }}</td>
                                        <td>
                                            <span class="badge bg-{{ $request->status == 'approved' ? 'success' : ($request->status == 'rejected' ? 'danger' : ($request->status == 'paid' ? 'info' : 'warning')) }}">
                                                {{ $request->status_name_ar }}
                                            </span>
                                        </td>
                                        <td>
                                            @can('expense-request-show')
                                            <a href="{{ route('admin.expense-requests.show', $request->id) }}" class="btn btn-sm btn-primary" title="عرض">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @endcan
                                            @if (in_array($request->status, ['pending', 'rejected']))
                                            @can('expense-request-edit')
                                            <a href="{{ route('admin.expense-requests.edit', $request->id) }}" class="btn btn-sm btn-info" title="تعديل">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @endcan
                                            @endif
                                            @if ($request->status == 'pending')
                                            @can('expense-request-approve')
                                            <a href="{{ route('admin.expense-requests.approve-form', $request->id) }}" class="btn btn-sm btn-success" title="موافقة">
                                                <i class="fas fa-check"></i>
                                            </a>
                                            @endcan
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">لا توجد طلبات</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $expenseRequests->withQueryString()->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

