@extends('admin.layouts.master')

@section('page-title')
    تفاصيل الإشعار
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">تفاصيل الإشعار</h5>
                </div>
                <div>
                    <a href="{{ route('admin.notifications.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-right me-2"></i>العودة للقائمة
                    </a>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex align-items-center">
                                @if ($notification->icon)
                                    <i class="{{ $notification->icon }} fa-2x me-3 text-{{ $notification->color }}"></i>
                                @endif
                                <div>
                                    <h5 class="card-title mb-0">{{ $notification->title }}</h5>
                                    <small class="text-muted">
                                        <i class="fas fa-clock me-1"></i>{{ $notification->created_at->format('Y-m-d H:i') }}
                                    </small>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <label class="form-label fw-bold">الرسالة:</label>
                                    <p class="form-control-plaintext">{{ $notification->message_ar ?? $notification->message }}</p>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">النوع:</label>
                                    <p class="form-control-plaintext">
                                        <span class="badge bg-{{ $notification->color }}">{{ $notification->type_name_ar }}</span>
                                    </p>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">الحالة:</label>
                                    <p class="form-control-plaintext">
                                        <span class="badge bg-{{ $notification->is_read ? 'success' : 'warning' }}">
                                            {{ $notification->is_read ? 'مقروء' : 'غير مقروء' }}
                                        </span>
                                    </p>
                                </div>
                                @if ($notification->action_url)
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">الإجراء:</label>
                                    <p class="form-control-plaintext">
                                        <a href="{{ $notification->action_url }}" class="btn btn-{{ $notification->color }}">
                                            {{ $notification->action_text ?? 'عرض' }}
                                        </a>
                                    </p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop


