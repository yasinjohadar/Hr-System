@extends('admin.layouts.master')

@section('page-title')
    عرض الإعلان
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid admin-page-shell">
            @include('admin.pages.users.partials.alerts')

            <div class="admin-page-banner">
                <div class="admin-page-banner-main">
                    <span class="admin-page-banner-icon"><i class="ri-megaphone-line"></i></span>
                    <div class="admin-page-banner-text">
                        <h1>عرض الإعلان</h1>
                        <p>{{ $announcement->title }}</p>
                    </div>
                </div>
                <div class="admin-page-banner-actions">
                    <a href="{{ route('admin.announcements.edit', $announcement) }}" class="admin-btn admin-btn-light">
                        <i class="ri-pencil-line"></i>
                        تعديل
                    </a>
                    <a href="{{ route('admin.announcements.index') }}" class="admin-btn admin-btn-secondary">
                        <i class="ri-arrow-right-line"></i>
                        العودة للقائمة
                    </a>
                </div>
            </div>

            <div class="admin-page-card">
                <div class="admin-form-body">
                    <h4 class="mb-2 fw-bold">{{ $announcement->title }}</h4>

                    <div class="d-flex flex-wrap gap-2 mb-3">
                        @if ($announcement->status === 'published')
                            <span class="admin-badge admin-badge-success">{{ $announcement->status_label }}</span>
                        @elseif ($announcement->status === 'draft')
                            <span class="admin-badge admin-badge-muted">{{ $announcement->status_label }}</span>
                        @else
                            <span class="admin-badge admin-badge-warning">{{ $announcement->status_label }}</span>
                        @endif

                        <span class="admin-badge admin-badge-role">{{ $announcement->target_type_label }}</span>

                        @if ($announcement->department)
                            <span class="admin-badge admin-badge-muted">{{ $announcement->department->name_ar ?? $announcement->department->name }}</span>
                        @endif
                        @if ($announcement->branch)
                            <span class="admin-badge admin-badge-muted">{{ $announcement->branch->name_ar ?? $announcement->branch->name }}</span>
                        @endif
                    </div>

                    @if ($announcement->publish_date || $announcement->expiry_date || $announcement->creator)
                        <div class="row g-3 mb-3">
                            @if ($announcement->publish_date)
                                <div class="col-sm-4">
                                    <label class="admin-form-label mb-0">تاريخ النشر</label>
                                    <div>{{ $announcement->publish_date->format('Y-m-d') }}</div>
                                </div>
                            @endif
                            @if ($announcement->expiry_date)
                                <div class="col-sm-4">
                                    <label class="admin-form-label mb-0">تاريخ الانتهاء</label>
                                    <div>{{ $announcement->expiry_date->format('Y-m-d') }}</div>
                                </div>
                            @endif
                            @if ($announcement->creator)
                                <div class="col-sm-4">
                                    <label class="admin-form-label mb-0">أنشأه</label>
                                    <div>{{ $announcement->creator->name }}</div>
                                </div>
                            @endif
                        </div>
                    @endif

                    <label class="admin-form-label">المحتوى</label>
                    {{-- e() ثم nl2br: النصّ يُفلتَر قبل إدخال <br> فلا يمرّ HTML من المستخدم --}}
                    <div class="announcement-content">
                        {!! nl2br(e($announcement->content ?: '—')) !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
