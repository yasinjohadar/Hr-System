@extends('employee.layouts.master')

@section('page-title')
    المشاريع
@stop

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/employee-projects.css') }}">
@endpush

@section('content')
    <div class="main-content app-content employee-projects-page">
        <div class="container-fluid pt-4">

            <div class="card page-hero mb-4">
                <div class="card-body py-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="page-hero-icon">
                            <i class="ri-briefcase-4-line"></i>
                        </div>
                        <div>
                            <h4 class="mb-1 page-hero-title fw-bold">مشاريعي</h4>
                            <p class="mb-0 page-hero-subtitle">المشاريع التي تشارك فيها أو تديرها</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-value">{{ $stats['total'] }}</div>
                                <div class="stat-label">إجمالي المشاريع</div>
                            </div>
                            <div class="stat-icon stat-icon--total"><i class="ri-stack-line"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-value">{{ $stats['active'] }}</div>
                                <div class="stat-label">نشطة</div>
                            </div>
                            <div class="stat-icon stat-icon--active"><i class="ri-play-circle-line"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-value">{{ $stats['planning'] }}</div>
                                <div class="stat-label">قيد التخطيط</div>
                            </div>
                            <div class="stat-icon stat-icon--planning"><i class="ri-draft-line"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-value">{{ $stats['completed'] }}</div>
                                <div class="stat-label">مكتملة</div>
                            </div>
                            <div class="stat-icon stat-icon--done"><i class="ri-checkbox-circle-line"></i></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="content-panel mb-4">
                <div class="content-panel-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                    <div>
                        <h5 class="fw-bold mb-1 text-dark">قائمة المشاريع</h5>
                        <p class="text-muted fs-13 mb-0">{{ $projects->total() }} مشروع</p>
                    </div>
                    <div class="filter-pills" role="group">
                        <button type="button" class="filter-pill active" data-project-filter="all">الكل</button>
                        <button type="button" class="filter-pill" data-project-filter="active">نشط</button>
                        <button type="button" class="filter-pill" data-project-filter="planning">تخطيط</button>
                        <button type="button" class="filter-pill" data-project-filter="on_hold">معلق</button>
                        <button type="button" class="filter-pill" data-project-filter="completed">مكتمل</button>
                    </div>
                </div>

                <div id="projects-list">
                    @forelse ($projects as $project)
                        @php
                            $statusClass = match ($project->status) {
                                'active' => 'active',
                                'planning' => 'planning',
                                'on_hold' => 'on_hold',
                                'completed' => 'completed',
                                'cancelled' => 'cancelled',
                                default => 'planning',
                            };
                        @endphp
                        <div class="project-card" data-status="{{ $project->status }}">
                            <div class="project-card-icon">
                                <i class="ri-folder-chart-line"></i>
                            </div>
                            <div class="flex-grow-1 min-w-0">
                                <h6 class="mb-1 fw-semibold text-dark">{{ $project->name_ar ?? $project->name }}</h6>
                                <div class="project-meta">
                                    <i class="ri-user-line me-1"></i>
                                    {{ $project->manager->full_name ?? '—' }}
                                    @if ($project->start_date)
                                        <span class="mx-2">·</span>
                                        <i class="ri-calendar-line me-1"></i>
                                        {{ $project->start_date->format('d/m/Y') }}
                                        @if ($project->end_date)
                                            — {{ $project->end_date->format('d/m/Y') }}
                                        @endif
                                    @endif
                                </div>
                            </div>
                            <div class="project-progress">
                                <div class="project-progress-track">
                                    <div class="project-progress-bar" style="width: {{ min(100, (int) $project->progress) }}%"></div>
                                </div>
                                <span class="project-progress-label">{{ (int) $project->progress }}% مكتمل</span>
                            </div>
                            <span class="status-pill status-pill--{{ $statusClass }}">{{ $project->status_name_ar }}</span>
                            <a href="{{ route('employee.projects.show', $project) }}" class="btn btn-outline-primary btn-sm btn-details">
                                <i class="ri-arrow-left-line me-1"></i>تفاصيل
                            </a>
                        </div>
                    @empty
                        <div class="empty-state">
                            <div class="empty-icon"><i class="ri-briefcase-line"></i></div>
                            <h5 class="fw-semibold text-dark mb-2">لا توجد مشاريع</h5>
                            <p class="text-muted mb-0">ستظهر هنا المشاريع المعيّنة لك</p>
                        </div>
                    @endforelse
                </div>

                @if ($projects->hasPages())
                    <div class="p-3 border-top">
                        {{ $projects->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@stop

@push('scripts')
    <script src="{{ asset('assets/js/employee-projects.js') }}"></script>
@endpush
