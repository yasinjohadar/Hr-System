@extends('admin.layouts.master')

@section('page-title')
    تفاصيل التذكرة
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">تفاصيل التذكرة: {{ $ticket->ticket_code }}</h5>
                </div>
                <div>
                    <a href="{{ route('admin.tickets.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-right me-2"></i>العودة للقائمة
                    </a>
                    @can('ticket-edit')
                    <a href="{{ route('admin.tickets.edit', $ticket->id) }}" class="btn btn-primary">
                        <i class="fas fa-edit me-2"></i>تعديل
                    </a>
                    @endcan
                </div>
            </div>

            <div class="row">
                <div class="col-xl-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">معلومات التذكرة</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">كود التذكرة:</label>
                                    <p class="form-control-plaintext"><strong>{{ $ticket->ticket_code }}</strong></p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">العنوان:</label>
                                    <p class="form-control-plaintext"><strong>{{ $ticket->title }}</strong></p>
                                </div>
                                @if ($ticket->employee)
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">الموظف:</label>
                                    <p class="form-control-plaintext">
                                        <a href="{{ route('admin.employees.show', $ticket->employee_id) }}">
                                            {{ $ticket->employee->full_name }}
                                        </a>
                                    </p>
                                </div>
                                @endif
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">الفئة:</label>
                                    <p class="form-control-plaintext">
                                        <span class="badge bg-info">{{ $ticket->category_name_ar }}</span>
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">الأولوية:</label>
                                    <p class="form-control-plaintext">
                                        <span class="badge bg-{{ $ticket->priority == 'urgent' ? 'danger' : ($ticket->priority == 'high' ? 'warning' : ($ticket->priority == 'medium' ? 'info' : 'secondary')) }}">
                                            {{ $ticket->priority_name_ar }}
                                        </span>
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">الحالة:</label>
                                    <p class="form-control-plaintext">
                                        <span class="badge bg-{{ $ticket->status == 'resolved' ? 'success' : ($ticket->status == 'closed' ? 'secondary' : ($ticket->status == 'in_progress' ? 'primary' : ($ticket->status == 'cancelled' ? 'danger' : 'warning'))) }}">
                                            {{ $ticket->status_name_ar }}
                                        </span>
                                    </p>
                                </div>
                                @if ($ticket->assignedTo)
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">معين إلى:</label>
                                    <p class="form-control-plaintext">
                                        <a href="{{ route('admin.employees.show', $ticket->assigned_to) }}">
                                            {{ $ticket->assignedTo->full_name }}
                                        </a>
                                    </p>
                                </div>
                                @endif
                                @if ($ticket->resolved_at)
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">تاريخ الحل:</label>
                                    <p class="form-control-plaintext">{{ $ticket->resolved_at->format('Y-m-d H:i') }}</p>
                                </div>
                                @endif
                                <div class="col-12 mb-3">
                                    <label class="form-label fw-bold">الوصف:</label>
                                    <div class="form-control-plaintext border rounded p-3 bg-light">
                                        {!! nl2br(e($ticket->description)) !!}
                                    </div>
                                </div>
                                @if ($ticket->resolution_notes)
                                <div class="col-12 mb-3">
                                    <label class="form-label fw-bold">ملاحظات الحل:</label>
                                    <div class="form-control-plaintext border rounded p-3 bg-light">
                                        {!! nl2br(e($ticket->resolution_notes)) !!}
                                    </div>
                                </div>
                                @endif
                                @if ($ticket->creator)
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">تم الإنشاء بواسطة:</label>
                                    <p class="form-control-plaintext">{{ $ticket->creator->name }}</p>
                                </div>
                                @endif
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">تاريخ الإنشاء:</label>
                                    <p class="form-control-plaintext">{{ $ticket->created_at->format('Y-m-d H:i') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mt-3">
                        <div class="card-header">
                            <h5 class="card-title mb-0">التعليقات ({{ $ticket->comments->count() }})</h5>
                        </div>
                        <div class="card-body">
                            @if ($ticket->comments->count() > 0)
                                <div class="list-group">
                                    @foreach ($ticket->comments as $comment)
                                        <div class="list-group-item">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <div>
                                                    <strong>
                                                        @if ($comment->employee)
                                                            {{ $comment->employee->full_name }}
                                                        @elseif ($comment->user)
                                                            {{ $comment->user->name }}
                                                        @else
                                                            غير معروف
                                                        @endif
                                                    </strong>
                                                </div>
                                                <small class="text-muted">{{ $comment->created_at->format('Y-m-d H:i') }}</small>
                                            </div>
                                            <p class="mb-0">{{ $comment->comment }}</p>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-muted text-center">لا توجد تعليقات</p>
                            @endif

                            <form method="POST" action="{{ route('admin.ticket-comments.store') }}" class="mt-3">
                                @csrf
                                <input type="hidden" name="ticket_id" value="{{ $ticket->id }}">
                                <div class="mb-2">
                                    <textarea name="comment" class="form-control" rows="3" placeholder="أضف تعليقاً..." required></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="fas fa-comment me-2"></i>إضافة تعليق
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-xl-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">الإجراءات</h5>
                        </div>
                        <div class="card-body">
                            @can('ticket-assign')
                            @if (!$ticket->assigned_to || $ticket->status == 'open')
                            <form method="POST" action="{{ route('admin.tickets.assign', $ticket->id) }}" class="mb-3">
                                @csrf
                                <label class="form-label">تعيين إلى:</label>
                                <select name="assigned_to" class="form-select mb-2" required>
                                    <option value="">اختر الموظف</option>
                                    @foreach ($employees as $employee)
                                        <option value="{{ $employee->id }}">{{ $employee->full_name }}</option>
                                    @endforeach
                                </select>
                                <button type="submit" class="btn btn-primary btn-sm w-100">
                                    <i class="fas fa-user-check me-2"></i>تعيين
                                </button>
                            </form>
                            @endif
                            @endcan

                            @can('ticket-assign')
                            @if ($ticket->status != 'resolved' && $ticket->status != 'closed')
                            <form method="POST" action="{{ route('admin.tickets.resolve', $ticket->id) }}">
                                @csrf
                                <label class="form-label">حل التذكرة:</label>
                                <textarea name="resolution_notes" class="form-control mb-2" rows="3" placeholder="ملاحظات الحل..." required></textarea>
                                <button type="submit" class="btn btn-success btn-sm w-100">
                                    <i class="fas fa-check-circle me-2"></i>حل التذكرة
                                </button>
                            </form>
                            @endif
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

