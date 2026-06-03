@extends('admin.layouts.master')

@section('page-title')
    تفاصيل طلب الموافقة
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">تفاصيل طلب الموافقة</h5>
                </div>
                <div>
                    <a href="{{ route('admin.approvals.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-right me-2"></i>العودة للقائمة
                    </a>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-8">
                    @if($type === 'leave')
                        @include('admin.pages.leave-requests.show', ['leaveRequest' => $entity])
                    @elseif($type === 'expense')
                        @include('admin.pages.expense-requests.show', ['expenseRequest' => $entity])
                    @endif
                </div>

                <div class="col-xl-4">
                    @if($workflowProgress ?? null)
                        <div class="card">
                            <div class="card-body">
                                <x-workflow-approval-timeline :workflow-progress="$workflowProgress" />
                            </div>
                        </div>
                    @endif

                    <div class="card mt-3">
                        <div class="card-header">
                            <h5 class="card-title mb-0">الإجراءات</h5>
                        </div>
                        <div class="card-body">
                            @if(($canApproveNow ?? false) && $type === 'leave')
                                <form action="{{ route('admin.leave-requests.approve', $entity->id) }}" method="POST" class="mb-2">
                                    @csrf
                                    <div class="mb-2">
                                        <label class="form-label small">ملاحظة (اختياري)</label>
                                        <textarea name="comments" class="form-control" rows="2" maxlength="2000" placeholder="ملاحظة مع الموافقة…"></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-success w-100" onclick="return confirm('هل أنت متأكد من الموافقة على هذا الطلب؟')">
                                        <i class="fas fa-check me-2"></i>موافقة
                                    </button>
                                </form>
                                <form action="{{ route('admin.leave-requests.reject', $entity->id) }}" method="POST">
                                    @csrf
                                    <div class="mb-2">
                                        <textarea name="rejection_reason" class="form-control" rows="3" maxlength="2000" placeholder="سبب الرفض / ملاحظة (اختياري)"></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-danger w-100" onclick="return confirm('هل أنت متأكد من رفض هذا الطلب؟')">
                                        <i class="fas fa-times me-2"></i>رفض
                                    </button>
                                </form>
                            @elseif(($canApproveNow ?? false) && $type === 'expense')
                                <a href="{{ route('admin.expense-requests.approve-form', $entity->id) }}" class="btn btn-success w-100 mb-2">
                                    <i class="fas fa-check me-2"></i>موافقة
                                </a>
                                <form action="{{ route('admin.expense-requests.reject', $entity->id) }}" method="POST">
                                    @csrf
                                    <div class="mb-2">
                                        <textarea name="rejection_reason" class="form-control" rows="3" maxlength="2000" placeholder="سبب الرفض / ملاحظة (اختياري)"></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-danger w-100" onclick="return confirm('هل أنت متأكد من رفض هذا الطلب؟')">
                                        <i class="fas fa-times me-2"></i>رفض
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .timeline {
            position: relative;
            padding-left: 30px;
        }
        .timeline-item {
            position: relative;
            padding-bottom: 20px;
        }
        .timeline-item:not(:last-child)::before {
            content: '';
            position: absolute;
            left: -25px;
            top: 20px;
            width: 2px;
            height: calc(100% - 10px);
            background: #e9ecef;
        }
        .timeline-marker {
            position: absolute;
            left: -30px;
            top: 5px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #6c757d;
            border: 2px solid white;
        }
        .timeline-item.completed .timeline-marker {
            background: #28a745;
        }
        .timeline-item.current .timeline-marker {
            background: #007bff;
            box-shadow: 0 0 0 4px rgba(0, 123, 255, 0.25);
        }
        .timeline-item.pending .timeline-marker {
            background: #6c757d;
        }
    </style>
@stop
