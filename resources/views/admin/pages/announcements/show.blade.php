@extends('admin.layouts.master')

@section('page-title')
    عرض الإعلان
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="page-header d-flex justify-content-between align-items-center my-4">
                <h5 class="page-title mb-0">عرض الإعلان</h5>
                <div>
                    <a href="{{ route('admin.announcements.edit', $announcement) }}" class="btn btn-warning btn-sm me-1">تعديل</a>
                    <a href="{{ route('admin.announcements.index') }}" class="btn btn-secondary btn-sm">العودة للقائمة</a>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">{{ $announcement->title }}</h4>
                    <div class="mt-2">
                        <span class="badge {{ $announcement->status === 'published' ? 'bg-success' : ($announcement->status === 'draft' ? 'bg-secondary' : 'bg-warning text-dark') }}">
                            {{ $announcement->status_label }}
                        </span>
                        <span class="badge bg-info">{{ $announcement->target_type_label }}</span>
                        @if ($announcement->department)
                            <span class="badge bg-light text-dark">{{ $announcement->department->name_ar ?? $announcement->department->name }}</span>
                        @endif
                        @if ($announcement->branch)
                            <span class="badge bg-light text-dark">{{ $announcement->branch->name_ar ?? $announcement->branch->name }}</span>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    @if ($announcement->publish_date || $announcement->expiry_date || $announcement->creator)
                        <div class="mb-3 text-muted small">
                            @if ($announcement->publish_date)
                                <span>تاريخ النشر: {{ $announcement->publish_date->format('Y-m-d') }}</span>
                            @endif
                            @if ($announcement->expiry_date)
                                <span class="ms-3">تاريخ الانتهاء: {{ $announcement->expiry_date->format('Y-m-d') }}</span>
                            @endif
                            @if ($announcement->creator)
                                <span class="ms-3">أنشأه: {{ $announcement->creator->name }}</span>
                            @endif
                        </div>
                    @endif
                    <div class="announcement-content">
                        {!! nl2br(e($announcement->content ?: '—')) !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
@stop
