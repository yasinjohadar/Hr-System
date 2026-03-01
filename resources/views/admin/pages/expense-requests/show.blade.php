@extends('admin.layouts.master')

@section('page-title')
    تفاصيل طلب المصروف
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">تفاصيل طلب المصروف</h5>
                </div>
                <div>
                    <a href="{{ route('admin.expense-requests.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-right me-2"></i>العودة للقائمة
                    </a>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">طلب رقم: {{ $expenseRequest->request_code }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">الموظف:</label>
                                    <p class="form-control-plaintext">
                                        <a href="{{ route('admin.employees.show', $expenseRequest->employee_id) }}">
                                            {{ $expenseRequest->employee->full_name }}
                                        </a>
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">التصنيف:</label>
                                    <p class="form-control-plaintext">{{ $expenseRequest->category->name_ar ?? $expenseRequest->category->name }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">المبلغ:</label>
                                    <p class="form-control-plaintext">
                                        <strong>{{ number_format($expenseRequest->amount, 2) }}</strong>
                                        @if ($expenseRequest->currency)
                                            <small class="text-muted">{{ $expenseRequest->currency->code }}</small>
                                        @endif
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">تاريخ المصروف:</label>
                                    <p class="form-control-plaintext">{{ $expenseRequest->expense_date->format('Y-m-d') }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">الحالة:</label>
                                    <p class="form-control-plaintext">
                                        <span class="badge bg-{{ $expenseRequest->status == 'approved' ? 'success' : ($expenseRequest->status == 'rejected' ? 'danger' : ($expenseRequest->status == 'paid' ? 'info' : 'warning')) }}">
                                            {{ $expenseRequest->status_name_ar }}
                                        </span>
                                    </p>
                                </div>
                                @if ($expenseRequest->payment_method)
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">طريقة الدفع:</label>
                                    <p class="form-control-plaintext">{{ $expenseRequest->payment_method_name_ar }}</p>
                                </div>
                                @endif
                                @if ($expenseRequest->vendor_name)
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">اسم المورد:</label>
                                    <p class="form-control-plaintext">{{ $expenseRequest->vendor_name }}</p>
                                </div>
                                @endif
                                @if ($expenseRequest->project_code)
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">كود المشروع:</label>
                                    <p class="form-control-plaintext">{{ $expenseRequest->project_code }}</p>
                                </div>
                                @endif
                                @if ($expenseRequest->paid_date)
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">تاريخ الدفع:</label>
                                    <p class="form-control-plaintext">{{ $expenseRequest->paid_date->format('Y-m-d') }}</p>
                                </div>
                                @endif
                                @if ($expenseRequest->payer)
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">من قام بالدفع:</label>
                                    <p class="form-control-plaintext">{{ $expenseRequest->payer->name }}</p>
                                </div>
                                @endif
                                @if ($expenseRequest->rejection_reason)
                                <div class="col-12 mb-3">
                                    <label class="form-label fw-bold">سبب الرفض:</label>
                                    <p class="form-control-plaintext text-danger">{{ $expenseRequest->rejection_reason }}</p>
                                </div>
                                @endif
                                <div class="col-12 mb-3">
                                    <label class="form-label fw-bold">الوصف:</label>
                                    <p class="form-control-plaintext">{{ $expenseRequest->description }}</p>
                                </div>
                                @if ($expenseRequest->notes)
                                <div class="col-12 mb-3">
                                    <label class="form-label fw-bold">ملاحظات:</label>
                                    <p class="form-control-plaintext">{{ $expenseRequest->notes }}</p>
                                </div>
                                @endif
                                @if ($expenseRequest->receipt_path)
                                <div class="col-12 mb-3">
                                    <label class="form-label fw-bold">الإيصال:</label>
                                    <p class="form-control-plaintext">
                                        <a href="{{ asset('storage/' . $expenseRequest->receipt_path) }}" target="_blank" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye me-1"></i>عرض الإيصال
                                        </a>
                                    </p>
                                </div>
                                @endif
                            </div>

                            <!-- الموافقات -->
                            @if ($expenseRequest->approvals->count() > 0)
                            <div class="mt-4">
                                <h6 class="fw-bold">سجل الموافقات:</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>الموافق</th>
                                                <th>المستوى</th>
                                                <th>الحالة</th>
                                                <th>التعليقات</th>
                                                <th>التاريخ</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($expenseRequest->approvals as $approval)
                                                <tr>
                                                    <td>{{ $approval->approver->name }}</td>
                                                    <td>{{ $approval->approval_level }}</td>
                                                    <td>
                                                        <span class="badge bg-{{ $approval->status == 'approved' ? 'success' : 'danger' }}">
                                                            {{ $approval->status_name_ar }}
                                                        </span>
                                                    </td>
                                                    <td>{{ $approval->comments ?? '-' }}</td>
                                                    <td>{{ $approval->approved_at ? $approval->approved_at->format('Y-m-d H:i') : ($approval->rejected_at ? $approval->rejected_at->format('Y-m-d H:i') : '-') }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            @endif

                            <div class="mt-3">
                                @if ($expenseRequest->status == 'pending')
                                @can('expense-request-approve')
                                <a href="{{ route('admin.expense-requests.approve-form', $expenseRequest->id) }}" class="btn btn-success">
                                    <i class="fas fa-check me-2"></i>موافقة
                                </a>
                                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
                                    <i class="fas fa-times me-2"></i>رفض
                                </button>
                                @endcan
                                @endif
                                @if ($expenseRequest->status == 'approved')
                                @can('expense-request-pay')
                                <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#payModal">
                                    <i class="fas fa-money-bill me-2"></i>تحديد كمدفوع
                                </button>
                                @endcan
                                @endif
                                @if (in_array($expenseRequest->status, ['pending', 'rejected']))
                                @can('expense-request-edit')
                                <a href="{{ route('admin.expense-requests.edit', $expenseRequest->id) }}" class="btn btn-warning">
                                    <i class="fas fa-edit me-2"></i>تعديل
                                </a>
                                @endcan
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal رفض -->
    @if ($expenseRequest->status == 'pending')
    <div class="modal fade" id="rejectModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ route('admin.expense-requests.reject', $expenseRequest->id) }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">رفض طلب المصروف</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">سبب الرفض <span class="text-danger">*</span></label>
                            <textarea name="rejection_reason" class="form-control" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-danger">رفض</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    <!-- Modal تحديد كمدفوع -->
    @if ($expenseRequest->status == 'approved')
    <div class="modal fade" id="payModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ route('admin.expense-requests.pay', $expenseRequest->id) }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">تحديد الطلب كمدفوع</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">تاريخ الدفع <span class="text-danger">*</span></label>
                            <input type="date" name="paid_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-primary">تأكيد الدفع</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
@stop

