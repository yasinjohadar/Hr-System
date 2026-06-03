@extends('admin.layouts.master')

@section('page-title')
    تفاصيل طلب المصروف
@stop

@section('css')
    <style>
        .expense-show-hero {
            background: linear-gradient(145deg, var(--bs-primary) 0%, rgba(var(--bs-primary-rgb), 0.88) 55%, rgb(15, 76, 129) 100%);
            color: #fff;
            border: none;
        }
        .expense-show-hero .text-white-75 { color: rgba(255,255,255,.85) !important; }
    </style>
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">تفاصيل طلب المصروف</h5>
                    <p class="text-muted small mb-0">{{ $expenseRequest->request_code }} — {{ $expenseRequest->employee->full_name }}</p>
                </div>
                <div class="d-flex flex-wrap gap-2 mt-2 mt-md-0">
                    <a href="{{ route('admin.expense-requests.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-right me-1"></i>العودة للقائمة
                    </a>
                    @php $canApproveNow = $canApproveNow ?? false; @endphp
                    @if ($expenseRequest->status == 'pending' && $canApproveNow)
                        <a href="{{ route('admin.expense-requests.approve-form', $expenseRequest->id) }}" class="btn btn-success btn-sm">
                            <i class="fas fa-check me-1"></i>موافقة
                        </a>
                        <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#rejectModal">
                            <i class="fas fa-times me-1"></i>رفض
                        </button>
                    @endif
                    @if ($expenseRequest->status == 'approved')
                        @can('expense-request-pay')
                            <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#payModal">
                                <i class="fas fa-money-bill me-1"></i>تحديد كمدفوع
                            </button>
                        @endcan
                    @endif
                    @if (in_array($expenseRequest->status, ['pending', 'rejected']))
                        @can('expense-request-edit')
                            <a href="{{ route('admin.expense-requests.edit', $expenseRequest->id) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit me-1"></i>تعديل
                            </a>
                        @endcan
                    @endif
                </div>
            </div>

            <div class="row g-3">
                <div class="col-lg-4">
                    <div class="card expense-show-hero shadow-sm h-100">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex align-items-start gap-3 mb-3">
                                <span class="avatar avatar-md bg-white bg-opacity-25 rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width:3rem;height:3rem;">
                                    <i class="fas fa-receipt fs-4"></i>
                                </span>
                                <div class="min-w-0">
                                    <div class="text-white-75 small mb-1">طلب المصروف</div>
                                    <div class="fw-semibold fs-6 text-truncate">{{ $expenseRequest->employee->full_name }}</div>
                                    <div class="small text-white-75 font-monospace">{{ $expenseRequest->request_code }}</div>
                                </div>
                            </div>
                            <div class="mb-3 pb-3 border-bottom border-white border-opacity-25">
                                <div class="text-white-75 small mb-1"><i class="fas fa-folder me-1"></i>التصنيف</div>
                                <div class="fw-semibold">{{ $expenseRequest->category->name_ar ?? $expenseRequest->category->name }}</div>
                            </div>
                            @php
                                $displayBadge = $workflowProgress['badge_ar'] ?? $expenseRequest->status_name_ar;
                                $displayVariant = $workflowProgress['badge_variant'] ?? 'warning';
                                $badgeBg = match ($displayVariant) {
                                    'success' => 'success',
                                    'danger' => 'danger',
                                    default => $expenseRequest->status == 'paid' ? 'info' : 'warning',
                                };
                            @endphp
                            <div class="mb-3">
                                <div class="text-white-75 small mb-2">الحالة</div>
                                <span class="badge bg-{{ $badgeBg }} fs-14 px-3 py-2">
                                    {{ $displayBadge }}
                                </span>
                            </div>
                            <div class="mt-auto pt-3 border-top border-white border-opacity-25">
                                <div class="text-white-75 small mb-1">المبلغ</div>
                                <div class="display-6 fw-bold lh-1">{{ number_format($expenseRequest->amount, 2) }}</div>
                                @if ($expenseRequest->currency) <div class="small text-white-75 mt-1">{{ $expenseRequest->currency->code }}</div> @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    @if ($workflowProgress ?? null)
                        <div class="card shadow-sm border-0 mb-3">
                            <div class="card-body">
                                <x-workflow-approval-timeline :workflow-progress="$workflowProgress" />
                            </div>
                        </div>
                    @endif
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-light py-3 border-bottom">
                            <h6 class="mb-0 fw-semibold"><i class="fas fa-circle-info text-primary me-2"></i>تفاصيل الطلب</h6>
                            <small class="text-muted">المبلغ والتواريخ والمورد</small>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <tbody>
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle" style="width:35%">
                                                <i class="fas fa-calendar-plus text-muted me-2"></i>تاريخ المصروف
                                            </th>
                                            <td class="pe-4 py-3 align-middle fw-semibold">{{ $expenseRequest->expense_date->format('Y-m-d') }}</td>
                                        </tr>
                                        @if ($expenseRequest->payment_method)
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-credit-card text-muted me-2"></i>طريقة الدفع
                                            </th>
                                            <td class="pe-4 py-3 align-middle fw-semibold">{{ $expenseRequest->payment_method_name_ar }}</td>
                                        </tr>
                                        @endif
                                        @if ($expenseRequest->vendor_name)
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-store text-muted me-2"></i>اسم المورد
                                            </th>
                                            <td class="pe-4 py-3 align-middle fw-semibold">{{ $expenseRequest->vendor_name }}</td>
                                        </tr>
                                        @endif
                                        @if ($expenseRequest->project_code)
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-code text-muted me-2"></i>كود المشروع
                                            </th>
                                            <td class="pe-4 py-3 align-middle font-monospace fw-semibold">{{ $expenseRequest->project_code }}</td>
                                        </tr>
                                        @endif
                                        @if ($expenseRequest->paid_date)
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-calendar-check text-success me-2"></i>تاريخ الدفع
                                            </th>
                                            <td class="pe-4 py-3 align-middle fw-semibold">{{ $expenseRequest->paid_date->format('Y-m-d') }}</td>
                                        </tr>
                                        @endif
                                        @if ($expenseRequest->payer)
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-user-check text-muted me-2"></i>من قام بالدفع
                                            </th>
                                            <td class="pe-4 py-3 align-middle fw-semibold">{{ $expenseRequest->payer->name }}</td>
                                        </tr>
                                        @endif
                                        @if ($expenseRequest->rejection_reason)
                                        <tr class="table-danger bg-danger bg-opacity-10">
                                            <th scope="row" class="ps-4 py-3 align-middle border-danger border-opacity-25">
                                                <i class="fas fa-triangle-exclamation text-danger me-2"></i>سبب الرفض
                                            </th>
                                            <td class="pe-4 py-3 align-middle text-danger fw-semibold">{{ $expenseRequest->rejection_reason }}</td>
                                        </tr>
                                        @endif
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-align-left text-muted me-2"></i>الوصف
                                            </th>
                                            <td class="pe-4 py-3 align-middle">{{ $expenseRequest->description }}</td>
                                        </tr>
                                        @if ($expenseRequest->notes)
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-sticky-note text-muted me-2"></i>ملاحظات
                                            </th>
                                            <td class="pe-4 py-3 align-middle">{{ $expenseRequest->notes }}</td>
                                        </tr>
                                        @endif
                                        @if ($expenseRequest->receipt_path)
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-paperclip text-muted me-2"></i>الإيصال
                                            </th>
                                            <td class="pe-4 py-3 align-middle">
                                                <a href="{{ asset('storage/' . $expenseRequest->receipt_path) }}" target="_blank" class="btn btn-outline-info btn-sm">
                                                    <i class="fas fa-eye me-1"></i>عرض الإيصال
                                                </a>
                                            </td>
                                        </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if ($expenseRequest->approvals->count() > 0)
            <div class="row g-3 mt-1">
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-light py-3">
                            <h6 class="mb-0 fw-semibold"><i class="fas fa-list-check text-primary me-2"></i>سجل الموافقات</h6>
                            <small class="text-muted">{{ $expenseRequest->approvals->count() }} موافقة</small>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="ps-4">الموافق</th>
                                            <th>المستوى</th>
                                            <th>الحالة</th>
                                            <th>التعليقات</th>
                                            <th class="pe-4">التاريخ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($expenseRequest->approvals as $approval)
                                            <tr>
                                                <td class="ps-4 fw-semibold">{{ $approval->approver->name }}</td>
                                                <td>{{ $approval->approval_level }}</td>
                                                <td>
                                                    <span class="badge bg-{{ $approval->status == 'approved' ? 'success-subtle text-success' : 'danger-subtle text-danger' }} border">
                                                        {{ $approval->status_name_ar }}
                                                    </span>
                                                </td>
                                                <td>{{ $approval->comments ?? '—' }}</td>
                                                <td class="pe-4 text-muted small">
                                                    {{ $approval->approved_at ? $approval->approved_at->format('Y-m-d H:i') : ($approval->rejected_at ? $approval->rejected_at->format('Y-m-d H:i') : '—') }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <div class="row g-3 mt-1 mb-4">
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-light py-3"><h6 class="mb-0 fw-semibold"><i class="fas fa-clock-rotate-left text-primary me-2"></i>بيانات السجل</h6></div>
                        <div class="card-body">
                            <div class="row row-cols-1 row-cols-md-2 g-0">
                                <div class="col border-bottom border-end-md p-3">
                                    <div class="small text-muted mb-1"><i class="far fa-clock me-1"></i>تاريخ الإنشاء</div>
                                    <div class="fw-semibold font-monospace small">{{ $expenseRequest->created_at->format('Y-m-d H:i') }}</div>
                                </div>
                                <div class="col border-bottom p-3">
                                    <div class="small text-muted mb-1"><i class="fas fa-pen-to-square me-1"></i>آخر تحديث</div>
                                    <div class="fw-semibold font-monospace small">{{ $expenseRequest->updated_at->format('Y-m-d H:i') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if ($expenseRequest->status == 'pending' && ($canApproveNow ?? false))
    <div class="modal fade" id="rejectModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ route('admin.expense-requests.reject', $expenseRequest->id) }}">
                    @csrf
                    <div class="modal-header"><h5 class="modal-title">رفض طلب المصروف</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                    <div class="modal-body"><div class="mb-3"><label class="form-label">سبب الرفض <span class="text-danger">*</span></label><textarea name="rejection_reason" class="form-control" rows="3" required></textarea></div></div>
                    <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button><button type="submit" class="btn btn-danger">رفض</button></div>
                </form>
            </div>
        </div>
    </div>
    @endif

    @if ($expenseRequest->status == 'approved')
    <div class="modal fade" id="payModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ route('admin.expense-requests.pay', $expenseRequest->id) }}">
                    @csrf
                    <div class="modal-header"><h5 class="modal-title">تحديد الطلب كمدفوع</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                    <div class="modal-body"><div class="mb-3"><label class="form-label">تاريخ الدفع <span class="text-danger">*</span></label><input type="date" name="paid_date" class="form-control" value="{{ date('Y-m-d') }}" required></div></div>
                    <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button><button type="submit" class="btn btn-primary">تأكيد الدفع</button></div>
                </form>
            </div>
        </div>
    </div>
    @endif
@stop
