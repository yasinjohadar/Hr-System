@extends('admin.layouts.master')

@section('page-title')
    تفاصيل الاجتماع
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">تفاصيل الاجتماع</h5>
                </div>
                <div>
                    <a href="{{ route('admin.meetings.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-right me-2"></i>العودة للقائمة
                    </a>
                    @can('meeting-edit')
                    <a href="{{ route('admin.meetings.edit', $meeting->id) }}" class="btn btn-primary">
                        <i class="fas fa-edit me-2"></i>تعديل
                    </a>
                    @endcan
                </div>
            </div>

            <div class="row">
                <div class="col-xl-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">معلومات الاجتماع</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">كود الاجتماع:</label>
                                    <p class="form-control-plaintext"><strong>{{ $meeting->meeting_code }}</strong></p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">العنوان:</label>
                                    <p class="form-control-plaintext"><strong>{{ $meeting->title_ar ?? $meeting->title }}</strong></p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">تاريخ ووقت البدء:</label>
                                    <p class="form-control-plaintext">{{ $meeting->start_time->format('Y-m-d H:i') }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">تاريخ ووقت الانتهاء:</label>
                                    <p class="form-control-plaintext">{{ $meeting->end_time->format('Y-m-d H:i') }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">نوع الاجتماع:</label>
                                    <p class="form-control-plaintext">
                                        <span class="badge bg-info">{{ $meeting->type_name_ar }}</span>
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">الحالة:</label>
                                    <p class="form-control-plaintext">
                                        <span class="badge bg-{{ $meeting->status == 'completed' ? 'success' : ($meeting->status == 'cancelled' ? 'danger' : ($meeting->status == 'in_progress' ? 'primary' : 'warning')) }}">
                                            {{ $meeting->status_name_ar }}
                                        </span>
                                    </p>
                                </div>
                                @if ($meeting->organizer)
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">المنظم:</label>
                                    <p class="form-control-plaintext">
                                        <a href="{{ route('admin.employees.show', $meeting->organizer_id) }}">
                                            {{ $meeting->organizer->full_name }}
                                        </a>
                                    </p>
                                </div>
                                @endif
                                @if ($meeting->location)
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">الموقع:</label>
                                    <p class="form-control-plaintext">{{ $meeting->location }}</p>
                                </div>
                                @endif
                                @if ($meeting->meeting_link)
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">رابط الاجتماع:</label>
                                    <p class="form-control-plaintext">
                                        <a href="{{ $meeting->meeting_link }}" target="_blank" class="btn btn-sm btn-info">
                                            <i class="fas fa-external-link-alt me-2"></i>فتح الرابط
                                        </a>
                                    </p>
                                </div>
                                @endif
                                @if ($meeting->description)
                                <div class="col-12 mb-3">
                                    <label class="form-label fw-bold">الوصف:</label>
                                    <p class="form-control-plaintext">{{ $meeting->description }}</p>
                                </div>
                                @endif
                                @if ($meeting->agenda)
                                <div class="col-12 mb-3">
                                    <label class="form-label fw-bold">جدول الأعمال:</label>
                                    <div class="form-control-plaintext border rounded p-3 bg-light">
                                        {!! nl2br(e($meeting->agenda)) !!}
                                    </div>
                                </div>
                                @endif
                                @if ($meeting->minutes)
                                <div class="col-12 mb-3">
                                    <label class="form-label fw-bold">محضر الاجتماع:</label>
                                    <div class="form-control-plaintext border rounded p-3 bg-light">
                                        {!! nl2br(e($meeting->minutes)) !!}
                                    </div>
                                </div>
                                @endif
                                @if ($meeting->action_items)
                                <div class="col-12 mb-3">
                                    <label class="form-label fw-bold">بنود العمل:</label>
                                    <div class="form-control-plaintext border rounded p-3 bg-light">
                                        {!! nl2br(e($meeting->action_items)) !!}
                                    </div>
                                </div>
                                @endif
                                @if ($meeting->creator)
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">تم الإنشاء بواسطة:</label>
                                    <p class="form-control-plaintext">{{ $meeting->creator->name }}</p>
                                </div>
                                @endif
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">تاريخ الإنشاء:</label>
                                    <p class="form-control-plaintext">{{ $meeting->created_at->format('Y-m-d H:i') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">الحضور ({{ $meeting->attendees->count() }})</h5>
                        </div>
                        <div class="card-body">
                            @if ($meeting->attendees->count() > 0)
                                <div class="list-group">
                                    @foreach ($meeting->attendees as $attendee)
                                        <div class="list-group-item d-flex justify-content-between align-items-center">
                                            <div>
                                                <a href="{{ route('admin.employees.show', $attendee->employee_id) }}" class="text-decoration-none">
                                                    <strong>{{ $attendee->employee->full_name }}</strong>
                                                </a>
                                                @if ($attendee->is_required)
                                                    <span class="badge bg-danger ms-2">مطلوب</span>
                                                @endif
                                            </div>
                                            <div>
                                                <span class="badge bg-{{ $attendee->status == 'attended' ? 'success' : ($attendee->status == 'declined' ? 'danger' : 'warning') }}">
                                                    @if ($attendee->status == 'invited')
                                                        مدعو
                                                    @elseif ($attendee->status == 'accepted')
                                                        موافق
                                                    @elseif ($attendee->status == 'declined')
                                                        رفض
                                                    @elseif ($attendee->status == 'attended')
                                                        حضر
                                                    @else
                                                        {{ $attendee->status }}
                                                    @endif
                                                </span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-muted text-center">لا يوجد حضور</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

