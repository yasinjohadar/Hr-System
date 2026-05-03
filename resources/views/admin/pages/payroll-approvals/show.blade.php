@extends('admin.layouts.master')

@section('page-title')
    تفاصيل الموافقة
@stop

@section('css')
    <style>
        .approval-show-hero {
            background: linear-gradient(145deg, var(--bs-primary) 0%, rgba(var(--bs-primary-rgb), 0.88) 55%, rgb(15, 76, 129) 100%);
            color: #fff;
            border: none;
        }
        .approval-show-hero .text-white-75 { color: rgba(255,255,255,.85) !important; }
    </style>
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">تفاصيل الموافقة</h5>
                    <p class="text-muted small mb-0">{{ $approval->payroll->payroll_code }} — {{ $approval->payroll->employee->full_name ?? '—' }}</p>
                </div>
                <div class="d-flex flex-wrap gap-2 mt-2 mt-md-0">
                    <a href="{{ route('admin.payroll-approvals.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-right me-1"></i>العودة للقائمة
                    </a>
                    @can('payroll-approval-edit')
                        @if($approval->status === 'pending')
                            <a href="{{ route('admin.payroll-approvals.edit', $approval->id) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit me-1"></i>تعديل
                            </a>
                        @endif
                    @endcan
                </div>
            </div>

            <div class="row g-3">
                <div class="col-lg-4">
                    <div class="card approval-show-hero shadow-sm h-100">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex align-items-start gap-3 mb-3">
                                <span class="avatar avatar-md bg-white bg-opacity-25 rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width:3rem;height:3rem;">
                                    <i class="fas fa-clipboard-check fs-4"></i>
                                </span>
                                <div class="min-w-0">
                                    <div class="text-white-75 small mb-1">الموافقة</div>
                                    <div class="fw-semibold fs-6 text-truncate">{{ $approval->payroll->payroll_code }}</div>
                                    <div class="small text-white-75">المستوى {{ $approval->approval_level }}</div>
                                </div>
                            </div>
                            <div class="mb-3 pb-3 border-bottom border-white border-opacity-25">
                                <div class="text-white-75 small mb-1"><i class="fas fa-user me-1"></i>الموظف</div>
                                <div class="fs-5 fw-semibold">{{ $approval->payroll->employee->full_name ?? '—' }}</div>
                                <div class="small text-white-75 font-monospace">{{ $approval->payroll->employee->employee_code ?? '—' }}</div>
                            </div>
                            <div class="mb-3">
                                <div class="text-white-75 small mb-2">الحالة</div>
                                @switch($approval->status)
                                    @case('approved')
                                        <span class="badge bg-success fs-14 px-3 py-2">موافق عليه</span>
                                        @break
                                    @case('rejected')
                                        <span class="badge bg-danger fs-14 px-3 py-2">مرفوض</span>
                                        @break
                                    @case('pending')
                                        <span class="badge bg-warning text-dark fs-14 px-3 py-2">قيد الانتظار</span>
                                        @break
                                    @default
                                        <span class="badge bg-secondary fs-14 px-3 py-2">{{ $approval->status_name_ar }}</span>
                                @endswitch
                            </div>
                            <div class="mt-auto pt-3 border-top border-white border-opacity-25">
                                <div class="text-white-75 small mb-1">الموافق</div>
                                <div class="fs-6 fw-semibold">{{ $approval->approver->name }}</div>
                                <div class="small text-white-75">{{ $approval->approver->email }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-light py-3 border-bottom">
                            <h6 class="mb-0 fw-semibold">
                                <i class="fas fa-circle-info text-primary me-2"></i>تفاصيل الموافقة
                            </h6>
                            <small class="text-muted">معلومات الموافقة والتواريخ والتعليقات</small>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <tbody>
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle" style="width:40%">
                                                <i class="fas fa-file-invoice text-muted me-2"></i>كشف الراتب
                                            </th>
                                            <td class="pe-4 py-3 align-middle">
                                                <span class="badge bg-primary-subtle text-primary border">{{ $approval->payroll->payroll_code }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-layer-group text-muted me-2"></i>مستوى الموافقة
                                            </th>
                                            <td class="pe-4 py-3 align-middle">
                                                <span class="badge bg-secondary-subtle text-dark border">المستوى {{ $approval->approval_level }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-sort-numeric-down text-muted me-2"></i>ترتيب الموافقة
                                            </th>
                                            <td class="pe-4 py-3 align-middle fw-semibold">{{ $approval->sort_order }}</td>
                                        </tr>
                                        @if($approval->approved_at)
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-calendar-check text-success me-2"></i>تاريخ الموافقة
                                            </th>
                                            <td class="pe-4 py-3 align-middle text-success fw-semibold">{{ $approval->approved_at->format('Y-m-d H:i') }}</td>
                                        </tr>
                                        @endif
                                        @if($approval->rejected_at)
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-calendar-xmark text-danger me-2"></i>تاريخ الرفض
                                            </th>
                                            <td class="pe-4 py-3 align-middle text-danger fw-semibold">{{ $approval->rejected_at->format('Y-m-d H:i') }}</td>
                                        </tr>
                                        @endif
                                        @if($approval->comments)
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-comment-dots text-muted me-2"></i>التعليقات
                                            </th>
                                            <td class="pe-4 py-3 align-middle">{{ $approval->comments }}</td>
                                        </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if($approval->status === 'pending' && auth()->id() == $approval->approver_id)
            <div class="row g-3 mt-1">
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-light py-3">
                            <h6 class="mb-0 fw-semibold">
                                <i class="fas fa-gavel text-primary me-2"></i>إجراءات الموافقة
                            </h6>
                            <small class="text-muted">يمكنك الموافقة أو رفض هذا الكشف</small>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-8">
                                    <form action="{{ route('admin.payroll-approvals.approve', $approval->id) }}" method="POST">
                                        @csrf
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold small">تعليقات الموافقة</label>
                                            <textarea class="form-control" name="comments" rows="2" placeholder="تعليقات اختيارية..."></textarea>
                                        </div>
                                        <div class="d-flex gap-2">
                                            <button type="submit" class="btn btn-success">
                                                <i class="fas fa-check me-1"></i>موافقة
                                            </button>
                                            <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
                                                <i class="fas fa-times me-1"></i>رفض
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="rejectModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">رفض الموافقة</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <form action="{{ route('admin.payroll-approvals.reject', $approval->id) }}" method="POST">
                            @csrf
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label">سبب الرفض <span class="text-danger">*</span></label>
                                    <textarea class="form-control" name="comments" rows="3" required placeholder="أدخل سبب الرفض..."></textarea>
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

            <div class="row g-3 mt-1 mb-4">
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-light py-3">
                            <h6 class="mb-0 fw-semibold">
                                <i class="fas fa-clock-rotate-left text-primary me-2"></i>بيانات السجل
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row row-cols-1 row-cols-md-2 g-0">
                                <div class="col border-bottom border-end-md p-3">
                                    <div class="small text-muted mb-1"><i class="far fa-clock me-1"></i>تاريخ الإنشاء</div>
                                    <div class="fw-semibold font-monospace small">{{ $approval->created_at->format('Y-m-d H:i') }}</div>
                                </div>
                                <div class="col border-bottom p-3">
                                    <div class="small text-muted mb-1"><i class="fas fa-pen-to-square me-1"></i>آخر تحديث</div>
                                    <div class="fw-semibold font-monospace small">{{ $approval->updated_at->format('Y-m-d H:i') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
