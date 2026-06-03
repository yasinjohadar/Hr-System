@extends('employee.layouts.master')

@section('page-title')
    المهام
@stop

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/employee-tasks.css') }}">
@endpush

@section('content')
    <div class="main-content app-content employee-tasks-page">
        <div class="container-fluid pt-4">

            <div class="card page-hero mb-4">
                <div class="card-body py-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="page-hero-icon">
                            <i class="ri-task-line"></i>
                        </div>
                        <div>
                            <h4 class="mb-1 page-hero-title fw-bold">مهامي</h4>
                            <p class="mb-0 page-hero-subtitle">المهام المعيّنة لك عبر المشاريع</p>
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
                                <div class="stat-label">إجمالي المهام</div>
                            </div>
                            <div class="stat-icon stat-icon--primary"><i class="ri-list-check-2"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-value">{{ $stats['in_progress'] }}</div>
                                <div class="stat-label">قيد التنفيذ</div>
                            </div>
                            <div class="stat-icon stat-icon--warning"><i class="ri-loader-4-line"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-value">{{ $stats['completed'] }}</div>
                                <div class="stat-label">مكتمل</div>
                            </div>
                            <div class="stat-icon stat-icon--success"><i class="ri-checkbox-circle-line"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-value">{{ $stats['overdue'] }}</div>
                                <div class="stat-label">متأخرة</div>
                            </div>
                            <div class="stat-icon stat-icon--danger"><i class="ri-alarm-warning-line"></i></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="content-panel">
                <div class="content-panel-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                    <div>
                        <h5 class="fw-bold mb-1 text-dark">قائمة المهام</h5>
                        <p class="text-muted fs-13 mb-0">{{ $tasks->total() }} مهمة</p>
                    </div>
                    @if ($tasks->isNotEmpty())
                        <div class="filter-pills" role="group">
                            <button type="button" class="filter-pill active" data-task-filter="all">الكل</button>
                            <button type="button" class="filter-pill" data-task-filter="active">نشطة</button>
                            <button type="button" class="filter-pill" data-task-filter="in_progress">قيد التنفيذ</button>
                            <button type="button" class="filter-pill" data-task-filter="completed">مكتمل</button>
                            <button type="button" class="filter-pill" data-task-filter="overdue">متأخرة</button>
                        </div>
                    @endif
                </div>

                <div class="p-0">
                    @forelse ($tasks as $task)
                        @php
                            $title = $task->title_ar ?? $task->title;
                            $projectName = $task->project->name_ar ?? $task->project->name ?? null;
                            $isOverdue = $task->due_date
                                && $task->due_date->isPast()
                                && ! in_array($task->status, ['completed', 'cancelled']);
                            $filterState = $isOverdue ? 'overdue' : $task->status;
                            $pct = min(100, max(0, (int) ($task->progress ?? 0)));
                        @endphp
                        <article class="task-card task-card-item {{ $isOverdue ? 'is-overdue' : '' }}"
                            data-filter-state="{{ $filterState }}">
                            <div class="task-icon">
                                <i class="ri-checkbox-blank-circle-line"></i>
                            </div>
                            <div class="flex-grow-1 min-w-0">
                                <div class="task-title">{{ $title }}</div>
                                <div class="task-project">
                                    <i class="ri-folder-3-line me-1"></i>
                                    @if ($task->project)
                                        <a href="{{ route('employee.projects.show', $task->project) }}">{{ $projectName }}</a>
                                    @else
                                        —
                                    @endif
                                </div>
                            </div>
                            <div class="task-progress d-none d-sm-block">
                                <div class="task-progress-bar-wrap">
                                    <div class="task-progress-bar" style="width: {{ $pct }}%"></div>
                                </div>
                                <div class="task-progress-pct">{{ $pct }}%</div>
                            </div>
                            <div class="task-due {{ $isOverdue ? 'is-late' : '' }}">
                                <i class="ri-calendar-line me-1"></i>
                                {{ $task->due_date ? $task->due_date->format('d/m/Y') : '—' }}
                            </div>
                            <span class="priority-pill priority-pill--{{ $task->priority }}">{{ $task->priority_name_ar }}</span>
                            <span class="status-pill status-pill--{{ $task->status }}">{{ $task->status_name_ar }}</span>
                        </article>
                    @empty
                        <div class="empty-state">
                            <div class="empty-icon"><i class="ri-task-line"></i></div>
                            <h5 class="fw-semibold text-dark mb-2">لا توجد مهام</h5>
                            <p class="text-muted mb-0">ستظهر المهام المعيّنة لك هنا</p>
                        </div>
                    @endforelse
                </div>

                @if ($tasks->hasPages())
                    <div class="p-3 border-top">
                        {{ $tasks->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@stop

@push('scripts')
    <script src="{{ asset('assets/js/employee-tasks.js') }}"></script>
@endpush
