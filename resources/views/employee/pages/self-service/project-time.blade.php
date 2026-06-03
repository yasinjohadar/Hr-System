@extends('employee.layouts.master')

@section('page-title')
    سجلات وقتي على المشاريع
@stop

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/employee-project-time.css') }}">
@endpush

@section('content')
    <div class="main-content app-content employee-project-time-page">
        <div class="container-fluid pt-4">

            <div class="card page-hero mb-4">
                <div class="card-body py-4">
                    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="page-hero-icon">
                                <i class="ri-time-line"></i>
                            </div>
                            <div>
                                <h4 class="mb-1 page-hero-title fw-bold">سجلات وقتي على المشاريع</h4>
                                <p class="mb-0 page-hero-subtitle">تتبّع الساعات المسجّلة على مشاريعك ومهامك</p>
                            </div>
                        </div>
                        <a href="{{ route('employee.projects') }}" class="btn-hero-link">
                            <i class="ri-folder-3-line me-1"></i>المشاريع
                        </a>
                    </div>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-value">{{ $stats['entries'] }}</div>
                                <div class="stat-label">السجلات (حسب التصفية)</div>
                            </div>
                            <div class="stat-icon stat-icon--primary"><i class="ri-file-list-3-line"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-value">{{ number_format($stats['total_hours'], 1) }}</div>
                                <div class="stat-label">إجمالي الساعات</div>
                            </div>
                            <div class="stat-icon stat-icon--success"><i class="ri-timer-line"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-value">{{ $stats['projects'] }}</div>
                                <div class="stat-label">مشاريع</div>
                            </div>
                            <div class="stat-icon stat-icon--primary"><i class="ri-briefcase-line"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-value">{{ number_format($stats['month_hours'], 1) }}</div>
                                <div class="stat-label">ساعات هذا الشهر</div>
                            </div>
                            <div class="stat-icon stat-icon--success"><i class="ri-calendar-line"></i></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="filter-panel">
                <form method="get" action="{{ route('employee.project-time.index') }}" class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label">المشروع</label>
                        <select name="project_id" class="form-select">
                            <option value="">— الكل —</option>
                            @foreach ($accessibleProjects as $p)
                                <option value="{{ $p->id }}" @selected(request('project_id') == $p->id)>
                                    {{ $p->name_ar ?? $p->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">من تاريخ</label>
                        <input type="date" name="from" class="form-control" value="{{ request('from') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">إلى تاريخ</label>
                        <input type="date" name="to" class="form-control" value="{{ request('to') }}">
                    </div>
                    <div class="col-md-2 d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-filter flex-grow-1">
                            <i class="ri-filter-3-line me-1"></i>تصفية
                        </button>
                        @if (request()->hasAny(['project_id', 'from', 'to']))
                            <a href="{{ route('employee.project-time.index') }}" class="btn-filter-reset px-3" title="مسح">
                                <i class="ri-close-line"></i>
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <div class="content-panel">
                <div class="content-panel-header">
                    <h5 class="fw-bold mb-1 text-dark">النتائج</h5>
                    <p class="text-muted fs-13 mb-0">{{ $entries->total() }} سجل</p>
                </div>

                <div class="p-0">
                    @forelse ($entries as $entry)
                        <article class="time-entry-card">
                            <div class="entry-icon">
                                <i class="ri-timer-2-line"></i>
                            </div>
                            <div class="entry-date">
                                {{ $entry->worked_date->format('d/m/Y') }}
                            </div>
                            <div class="flex-grow-1 min-w-0">
                                <div class="entry-project">
                                    @if ($entry->project)
                                        <a href="{{ route('employee.projects.show', $entry->project) }}">
                                            {{ $entry->project->name_ar ?? $entry->project->name }}
                                        </a>
                                    @else
                                        —
                                    @endif
                                </div>
                                @if ($entry->task)
                                    <div class="entry-task">
                                        <i class="ri-task-line me-1"></i>
                                        {{ $entry->task->title_ar ?? $entry->task->title }}
                                    </div>
                                @endif
                            </div>
                            <span class="hours-chip">{{ number_format((float) $entry->hours, 2) }} س</span>
                            @if ($entry->description)
                                <div class="entry-desc text-truncate" title="{{ $entry->description }}">
                                    {{ $entry->description }}
                                </div>
                            @endif
                        </article>
                    @empty
                        <div class="empty-state">
                            <div class="empty-icon"><i class="ri-time-line"></i></div>
                            <h5 class="fw-semibold text-dark mb-2">لا توجد سجلات</h5>
                            <p class="text-muted mb-0">سجّل وقتك من صفحة المشروع أو غيّر معايير التصفية</p>
                        </div>
                    @endforelse
                </div>

                @if ($entries->hasPages())
                    <div class="p-3 border-top">
                        {{ $entries->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@stop
