@extends('admin.layouts.master')

@section('page-title')
    تفاصيل الموافقة
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="page-header d-flex justify-content-between align-items-center my-4">
                <h5 class="page-title mb-0">تفاصيل الموافقة</h5>
                <div>
                    @can('payroll-approval-edit')
                    @if($approval->status === 'pending')
                    <a href="{{ route('admin.payroll-approvals.edit', $approval->id) }}" class="btn btn-primary btn-sm">تعديل</a>
                    @endif
                    @endcan
                    <a href="{{ route('admin.payroll-approvals.index') }}" class="btn btn-secondary btn-sm">رجوع</a>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">معلومات الموافقة</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">كشف الراتب:</label>
                                    <p>{{ $approval->payroll->payroll_code }}</p>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold">الموظف:</label>
                                    <p>{{ $approval->payroll->employee->full_name }}</p>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold">مستوى الموافقة:</label>
                                    <p>
                                        <span class="badge bg-secondary">المستوى {{ $approval->approval_level }}</span>
                                    </p>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold">الموافق:</label>
                                    <p>{{ $approval->approver->name }} ({{ $approval->approver->email }})</p>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold">الحالة:</label>
                                    <p>
                                        <span class="badge bg-{{ match($approval->status) {
                                            'approved' => 'success',
                                            'rejected' => 'danger',
                                            'pending' => 'warning',
                                            default => 'secondary'
                                        } }}">
                                            {{ $approval->status_name_ar }}
                                        </span>
                                    </p>
                                </div>

                                @if($approval->approved_at)
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">تاريخ الموافقة:</label>
                                    <p>{{ $approval->approved_at->format('Y-m-d H:i') }}</p>
                                </div>
                                @endif

                                @if($approval->rejected_at)
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">تاريخ الرفض:</label>
                                    <p>{{ $approval->rejected_at->format('Y-m-d H:i') }}</p>
                                </div>
                                @endif

                                @if($approval->comments)
                                <div class="col-md-12">
                                    <label class="form-label fw-bold">التعليقات:</label>
                                    <p>{{ $approval->comments }}</p>
                                </div>
                                @endif

                                <div class="col-md-6">
                                    <label class="form-label fw-bold">ترتيب الموافقة:</label>
                                    <p>{{ $approval->sort_order }}</p>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold">تاريخ الإنشاء:</label>
                                    <p>{{ $approval->created_at->format('Y-m-d H:i') }}</p>
                                </div>
                            </div>

                            @if($approval->status === 'pending' && auth()->id() == $approval->approver_id)
                            <hr>
                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <h6>إجراءات الموافقة</h6>
                                    <form action="{{ route('admin.payroll-approvals.approve', $approval->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <div class="mb-2">
                                            <label class="form-label">تعليقات الموافقة</label>
                                            <textarea class="form-control" name="comments" rows="2" placeholder="تعليقات اختيارية..."></textarea>
                                        </div>
                                        <button type="submit" class="btn btn-success">موافقة</button>
                                    </form>

                                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">رفض</button>
                                </div>
                            </div>

                            <!-- Modal للرفض -->
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
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

