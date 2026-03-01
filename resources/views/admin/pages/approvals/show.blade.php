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
                    @if($instance && $workflowStatus)
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">سير العمل</h5>
                            </div>
                            <div class="card-body">
                                <div class="timeline">
                                    @foreach($workflowStatus['all_steps'] as $stepData)
                                        <div class="timeline-item {{ $stepData['status'] === 'completed' ? 'completed' : ($stepData['status'] === 'current' ? 'current' : 'pending') }}">
                                            <div class="timeline-marker"></div>
                                            <div class="timeline-content">
                                                <h6>{{ $stepData['step']->name_ar ?? $stepData['step']->name }}</h6>
                                                @if($stepData['approver'])
                                                    <p class="text-muted mb-1">
                                                        <small>الموافق: {{ $stepData['approver']->name }}</small>
                                                    </p>
                                                @endif
                                                <span class="badge bg-{{ $stepData['status'] === 'completed' ? 'success' : ($stepData['status'] === 'current' ? 'primary' : 'secondary') }}">
                                                    @if($stepData['status'] === 'completed')
                                                        مكتمل
                                                    @elseif($stepData['status'] === 'current')
                                                        قيد الانتظار
                                                    @else
                                                        معلق
                                                    @endif
                                                </span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                @if($workflowStatus['next_approver'])
                                    <div class="alert alert-info mt-3">
                                        <strong>الموافق التالي:</strong> {{ $workflowStatus['next_approver']->name }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    <div class="card mt-3">
                        <div class="card-header">
                            <h5 class="card-title mb-0">الإجراءات</h5>
                        </div>
                        <div class="card-body">
                            @if($type === 'leave')
                                @can('leave-request-approve')
                                <form action="{{ route('admin.leave-requests.approve', $entity->id) }}" method="POST" class="mb-2">
                                    @csrf
                                    <button type="submit" class="btn btn-success w-100" onclick="return confirm('هل أنت متأكد من الموافقة على هذا الطلب؟')">
                                        <i class="fas fa-check me-2"></i>موافقة
                                    </button>
                                </form>
                                @endcan

                                @can('leave-request-approve')
                                <form action="{{ route('admin.leave-requests.reject', $entity->id) }}" method="POST">
                                    @csrf
                                    <div class="mb-2">
                                        <textarea name="rejection_reason" class="form-control" rows="3" placeholder="سبب الرفض" required></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-danger w-100" onclick="return confirm('هل أنت متأكد من رفض هذا الطلب؟')">
                                        <i class="fas fa-times me-2"></i>رفض
                                    </button>
                                </form>
                                @endcan
                            @elseif($type === 'expense')
                                @can('expense-request-approve')
                                <a href="{{ route('admin.expense-requests.show-approve-form', $entity->id) }}" class="btn btn-success w-100 mb-2">
                                    <i class="fas fa-check me-2"></i>موافقة
                                </a>
                                @endcan

                                @can('expense-request-approve')
                                <form action="{{ route('admin.expense-requests.reject', $entity->id) }}" method="POST">
                                    @csrf
                                    <div class="mb-2">
                                        <textarea name="rejection_reason" class="form-control" rows="3" placeholder="سبب الرفض" required></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-danger w-100" onclick="return confirm('هل أنت متأكد من رفض هذا الطلب؟')">
                                        <i class="fas fa-times me-2"></i>رفض
                                    </button>
                                </form>
                                @endcan
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
