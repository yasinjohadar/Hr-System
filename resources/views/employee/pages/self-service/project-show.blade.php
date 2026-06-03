@extends('employee.layouts.master')

@section('page-title')
    {{ $project->name_ar ?? $project->name }}
@stop

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/employee-projects.css') }}">
@endpush

@section('content')
    <div class="main-content app-content employee-projects-page">
        <div class="container-fluid pt-4">

            <div class="d-flex flex-wrap gap-2 mb-3">
                <a href="{{ route('employee.projects') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="ri-arrow-right-line me-1"></i>المشاريع
                </a>
                <a href="{{ route('employee.project-time.index') }}" class="btn btn-outline-primary btn-sm">
                    <i class="ri-time-line me-1"></i>سجلات وقتي
                </a>
            </div>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
                    <i class="ri-checkbox-circle-line me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
                    <i class="ri-error-warning-line me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @php
                $statusClass = match ($project->status) {
                    'active' => 'active',
                    'planning' => 'planning',
                    'on_hold' => 'on_hold',
                    'completed' => 'completed',
                    'cancelled' => 'cancelled',
                    default => 'planning',
                };
                $teamCount = ($project->manager ? 1 : 0) + $project->members->count();
            @endphp

            <div class="card detail-hero mb-4">
                <div class="card-body p-4">
                    <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3 mb-3">
                        <div class="d-flex align-items-start gap-3">
                            <div class="page-hero-icon">
                                <i class="ri-folder-chart-line"></i>
                            </div>
                            <div>
                                <h3 class="page-hero-title fw-bold mb-1">{{ $project->name_ar ?? $project->name }}</h3>
                                <p class="project-meta mb-2">
                                    <span class="font-monospace">{{ $project->project_code }}</span>
                                    @if ($project->department)
                                        <span class="mx-2">·</span>{{ $project->department->name }}
                                    @endif
                                </p>
                                <span class="status-pill status-pill--{{ $statusClass }}">{{ $project->status_name_ar }}</span>
                            </div>
                        </div>
                        <div class="text-lg-end">
                            <div class="hours-highlight mb-0">{{ number_format($totalMyHours, 1) }}</div>
                            <small class="text-muted">ساعاتك المسجّلة</small>
                        </div>
                    </div>
                    <div class="detail-hero-progress mb-2">
                        <div class="detail-hero-progress-bar" style="width: {{ min(100, (int) $project->progress) }}%"></div>
                    </div>
                    <div class="d-flex justify-content-between fs-13 text-muted">
                        <span>التقدم</span>
                        <strong class="text-dark">{{ (int) $project->progress }}%</strong>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="section-card">
                        <div class="section-card-header">
                            <i class="ri-information-line me-1 text-primary"></i>تفاصيل المشروع
                        </div>
                        <div class="section-card-body">
                            <div class="info-grid mb-3">
                                <div>
                                    <div class="info-item-label">مدير المشروع</div>
                                    <div class="info-item-value">{{ $project->manager->full_name ?? '—' }}</div>
                                </div>
                                <div>
                                    <div class="info-item-label">تاريخ البدء</div>
                                    <div class="info-item-value">{{ $project->start_date ? $project->start_date->format('d/m/Y') : '—' }}</div>
                                </div>
                                <div>
                                    <div class="info-item-label">تاريخ الانتهاء</div>
                                    <div class="info-item-value">{{ $project->end_date ? $project->end_date->format('d/m/Y') : '—' }}</div>
                                </div>
                                <div>
                                    <div class="info-item-label">مهامي</div>
                                    <div class="info-item-value">{{ $myTasks->count() }}</div>
                                </div>
                            </div>
                            @if ($project->description)
                                <p class="mb-0 text-muted fs-13 lh-lg">{{ $project->description }}</p>
                            @endif
                        </div>
                    </div>

                    <div class="section-card">
                        <div class="section-card-header">
                            <i class="ri-team-line me-1 text-primary"></i>فريق المشروع
                            <span class="badge bg-primary-transparent text-primary ms-1">{{ $teamCount }}</span>
                        </div>
                        <div class="section-card-body py-2">
                            @if ($project->manager)
                                <div class="team-member">
                                    <div class="team-avatar">{{ mb_substr($project->manager->first_name, 0, 1) }}</div>
                                    <div class="flex-grow-1">
                                        <div class="fw-semibold fs-13">{{ $project->manager->full_name }}</div>
                                        <span class="badge bg-primary-transparent text-primary">مدير المشروع</span>
                                    </div>
                                </div>
                            @endif
                            @foreach ($project->members as $m)
                                <div class="team-member">
                                    <div class="team-avatar">{{ mb_substr($m->employee->first_name ?? '?', 0, 1) }}</div>
                                    <div class="flex-grow-1">
                                        <div class="fw-semibold fs-13">{{ $m->employee->full_name ?? '—' }}</div>
                                        <span class="badge bg-secondary-transparent">{{ $m->role_name_ar }}</span>
                                    </div>
                                </div>
                            @endforeach
                            @if (! $project->manager && $project->members->isEmpty())
                                <p class="text-muted mb-0 py-2">لا يوجد أعضاء مسجّلون.</p>
                            @endif
                        </div>
                    </div>

                    <div class="section-card">
                        <div class="section-card-header">
                            <i class="ri-file-text-line me-1 text-primary"></i>مستندات المشروع
                        </div>
                        <div class="section-card-body py-2">
                            @forelse ($project->documents as $doc)
                                <div class="doc-row">
                                    <div>
                                        <div class="fw-semibold fs-13">{{ $doc->title }}</div>
                                        <small class="text-muted">{{ $doc->created_at?->format('d/m/Y') }}</small>
                                    </div>
                                    @if ($doc->disk_url)
                                        <a href="{{ $doc->disk_url }}" target="_blank" rel="noopener" class="btn btn-outline-primary btn-sm">
                                            <i class="ri-download-2-line me-1"></i>{{ $doc->original_name ?? 'تحميل' }}
                                        </a>
                                    @else
                                        <span class="text-muted fs-13">—</span>
                                    @endif
                                </div>
                            @empty
                                <p class="text-muted mb-0 py-2">لا توجد مستندات.</p>
                            @endforelse
                        </div>
                    </div>

                    <div class="section-card">
                        <div class="section-card-header">
                            <i class="ri-task-line me-1 text-primary"></i>مهامي في هذا المشروع
                        </div>
                        <div class="section-card-body py-2">
                            @forelse ($myTasks as $task)
                                @php
                                    $taskStatus = match ($task->status) {
                                        'completed', 'done' => 'completed',
                                        'in_progress' => 'active',
                                        default => 'planning',
                                    };
                                @endphp
                                <div class="task-row">
                                    <div>
                                        <div class="fw-semibold fs-13">{{ $task->title_ar ?? $task->title }}</div>
                                        <small class="text-muted">
                                            @if ($task->due_date)
                                                <i class="ri-calendar-line me-1"></i>{{ $task->due_date->format('d/m/Y') }}
                                            @else
                                                بدون موعد
                                            @endif
                                        </small>
                                    </div>
                                    <span class="status-pill status-pill--{{ $taskStatus }}">{{ $task->status_name_ar }}</span>
                                </div>
                            @empty
                                <p class="text-muted mb-0 py-2">لا توجد مهام معيّنة لك في هذا المشروع.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="time-log-card">
                        <div class="section-card-header">
                            <i class="ri-timer-line me-1 text-primary"></i>تسجيل وقت العمل
                        </div>
                        <div class="section-card-body">
                            <p class="text-muted fs-13 mb-3">
                                إجمالي ساعاتك: <span class="hours-highlight fs-18">{{ number_format($totalMyHours, 2) }}</span>
                            </p>

                            @if ($project->allowsTimeLogging())
                                <form action="{{ route('employee.projects.time.store', $project) }}" method="post">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="form-label fw-medium fs-13">تاريخ العمل</label>
                                        <input type="date" name="worked_date" class="form-control @error('worked_date') is-invalid @enderror"
                                            value="{{ old('worked_date', now()->format('Y-m-d')) }}" required>
                                        @error('worked_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-medium fs-13">الساعات</label>
                                        <input type="number" name="hours" class="form-control @error('hours') is-invalid @enderror"
                                            step="0.25" min="0.01" max="24" value="{{ old('hours') }}" required>
                                        @error('hours')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-medium fs-13">المهمة (اختياري)</label>
                                        <select name="task_id" class="form-select @error('task_id') is-invalid @enderror">
                                            <option value="">— بدون —</option>
                                            @foreach ($myTasks as $task)
                                                <option value="{{ $task->id }}" @selected(old('task_id') == $task->id)>
                                                    {{ $task->title_ar ?? $task->title }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('task_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-medium fs-13">وصف (اختياري)</label>
                                        <textarea name="description" class="form-control @error('description') is-invalid @enderror"
                                            rows="2" maxlength="2000">{{ old('description') }}</textarea>
                                        @error('description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="ri-save-line me-1"></i>حفظ
                                    </button>
                                </form>
                            @else
                                <div class="alert alert-warning mb-0 fs-13">
                                    <i class="ri-information-line me-1"></i>
                                    لا يمكن تسجيل وقت على مشروع في هذه الحالة.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
