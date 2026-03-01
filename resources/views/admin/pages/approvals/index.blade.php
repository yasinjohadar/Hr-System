@extends('admin.layouts.master')

@section('page-title')
    طلبات الموافقة المعلقة
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">طلبات الموافقة المعلقة</h5>
                </div>
            </div>

            <div class="row">
                <!-- طلبات الإجازات -->
                <div class="col-xl-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">طلبات الإجازات المعلقة ({{ $leaveRequests->count() }})</h5>
                        </div>
                        <div class="card-body">
                            @if($leaveRequests->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>الموظف</th>
                                                <th>نوع الإجازة</th>
                                                <th>من</th>
                                                <th>إلى</th>
                                                <th>عدد الأيام</th>
                                                <th>الإجراءات</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($leaveRequests as $leaveRequest)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $leaveRequest->employee->full_name ?? '-' }}</td>
                                                    <td>{{ $leaveRequest->leaveType->name_ar ?? $leaveRequest->leaveType->name ?? '-' }}</td>
                                                    <td>{{ $leaveRequest->start_date->format('Y-m-d') }}</td>
                                                    <td>{{ $leaveRequest->end_date->format('Y-m-d') }}</td>
                                                    <td>{{ $leaveRequest->days_count }}</td>
                                                    <td>
                                                        <a href="{{ route('admin.approvals.show', ['type' => 'leave', 'id' => $leaveRequest->id]) }}" 
                                                           class="btn btn-sm btn-primary">
                                                            <i class="fas fa-eye"></i> عرض
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-muted text-center">لا توجد طلبات إجازات معلقة</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- طلبات المصروفات -->
                <div class="col-xl-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">طلبات المصروفات المعلقة ({{ $expenseRequests->count() }})</h5>
                        </div>
                        <div class="card-body">
                            @if($expenseRequests->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>الموظف</th>
                                                <th>التصنيف</th>
                                                <th>المبلغ</th>
                                                <th>التاريخ</th>
                                                <th>الإجراءات</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($expenseRequests as $expenseRequest)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $expenseRequest->employee->full_name ?? '-' }}</td>
                                                    <td>{{ $expenseRequest->category->name_ar ?? $expenseRequest->category->name ?? '-' }}</td>
                                                    <td>{{ number_format($expenseRequest->amount, 2) }} {{ $expenseRequest->currency->code ?? 'SAR' }}</td>
                                                    <td>{{ $expenseRequest->expense_date->format('Y-m-d') }}</td>
                                                    <td>
                                                        <a href="{{ route('admin.approvals.show', ['type' => 'expense', 'id' => $expenseRequest->id]) }}" 
                                                           class="btn btn-sm btn-primary">
                                                            <i class="fas fa-eye"></i> عرض
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-muted text-center">لا توجد طلبات مصروفات معلقة</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
