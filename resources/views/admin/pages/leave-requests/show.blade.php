@extends('admin.layouts.master')

@section('page-title')
    تفاصيل طلب الإجازة
@stop

@section('css')
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">تفاصيل طلب الإجازة</h5>
                </div>
                <div>
                    <a href="{{ route('admin.leave-requests.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-right me-2"></i>العودة للقائمة
                    </a>
                    @if ($leaveRequest->status == 'pending')
                        @can('leave-request-approve')
                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#approve{{ $leaveRequest->id }}">
                            <i class="fas fa-check me-2"></i>موافقة
                        </button>
                        @endcan
                        @can('leave-request-edit')
                        <a href="{{ route('admin.leave-requests.edit', $leaveRequest->id) }}" class="btn btn-primary">
                            <i class="fas fa-edit me-2"></i>تعديل
                        </a>
                        @endcan
                    @endif
                </div>
            </div>

            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">معلومات طلب الإجازة</h5>
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
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <th scope="row">{{ $leaveRequest->id }}</th>
                                            <td>
                                                <strong>{{ $leaveRequest->employee->full_name ?? $leaveRequest->employee->first_name . ' ' . $leaveRequest->employee->last_name }}</strong>
                                                @if ($leaveRequest->employee->employee_code ?? null)
                                                    <br><small class="text-muted">{{ $leaveRequest->employee->employee_code }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-info">{{ $leaveRequest->leaveType->name_ar ?? $leaveRequest->leaveType->name }}</span>
                                            </td>
                                            <td>{{ $leaveRequest->start_date->format('Y-m-d') }}</td>
                                            <td>{{ $leaveRequest->end_date->format('Y-m-d') }}</td>
                                            <td><span class="badge bg-primary">{{ $leaveRequest->days_count }} يوم</span></td>
                                            <td>
                                                @if ($leaveRequest->status == 'approved')
                                                    <span class="badge bg-success">موافق عليه</span>
                                                @elseif ($leaveRequest->status == 'pending')
                                                    <span class="badge bg-warning">قيد الانتظار</span>
                                                @elseif ($leaveRequest->status == 'rejected')
                                                    <span class="badge bg-danger">مرفوض</span>
                                                @else
                                                    <span class="badge bg-secondary">ملغي</span>
                                                @endif
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="table-responsive mt-3">
                                <table class="table table-striped table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="w-25">البند</th>
                                            <th>التفاصيل</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if ($leaveRequest->reason)
                                            <tr>
                                                <th scope="row" class="table-light text-nowrap">سبب الإجازة</th>
                                                <td>{{ $leaveRequest->reason }}</td>
                                            </tr>
                                        @endif
                                        @if ($leaveRequest->notes)
                                            <tr>
                                                <th scope="row" class="table-light text-nowrap">ملاحظات</th>
                                                <td>{{ $leaveRequest->notes }}</td>
                                            </tr>
                                        @endif
                                        @if ($leaveRequest->approved_by)
                                            <tr>
                                                <th scope="row" class="table-light text-nowrap">وافق عليه</th>
                                                <td>
                                                    {{ $leaveRequest->approver->name ?? '-' }}
                                                    @if ($leaveRequest->approved_at)
                                                        <br><small class="text-muted">{{ $leaveRequest->approved_at->format('Y-m-d H:i') }}</small>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endif
                                        @if ($leaveRequest->rejection_reason)
                                            <tr>
                                                <th scope="row" class="table-light text-nowrap">سبب الرفض</th>
                                                <td class="text-danger">{{ $leaveRequest->rejection_reason }}</td>
                                            </tr>
                                        @endif
                                        <tr>
                                            <th scope="row" class="table-light text-nowrap">تاريخ الإنشاء</th>
                                            <td>{{ $leaveRequest->created_at->format('Y-m-d H:i') }}</td>
                                        </tr>
                                        <tr>
                                            <th scope="row" class="table-light text-nowrap">أنشأ بواسطة</th>
                                            <td>{{ $leaveRequest->creator->name ?? '-' }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if ($leaveRequest->status == 'pending')
                @can('leave-request-approve')
                    @include('admin.pages.leave-requests.approve', ['request' => $leaveRequest])
                @endcan
            @endif
        </div>
    </div>
@stop

@section('js')
@stop


