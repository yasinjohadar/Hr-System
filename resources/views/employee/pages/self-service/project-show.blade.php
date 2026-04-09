@extends('employee.layouts.master')

@section('page-title')
    {{ $project->name_ar ?? $project->name }}
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">{{ $project->name_ar ?? $project->name }}</h5>
                </div>
                <div>
                    <a href="{{ route('employee.projects') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-right me-1"></i>المشاريع
                    </a>
                    <a href="{{ route('employee.project-time.index') }}" class="btn btn-outline-primary btn-sm">سجلات وقتي</a>
                </div>
            </div>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="row">
                <div class="col-lg-8">
                    <div class="card mb-3">
                        <div class="card-header">
                            <h6 class="card-title mb-0">تفاصيل المشروع</h6>
                        </div>
                        <div class="card-body">
                            <p class="mb-1"><strong>الرمز:</strong> {{ $project->project_code }}</p>
                            @if ($project->department)
                                <p class="mb-1"><strong>القسم:</strong> {{ $project->department->name }}</p>
                            @endif
                            @if ($project->manager)
                                <p class="mb-1"><strong>مدير المشروع:</strong> {{ $project->manager->full_name }}</p>
                            @endif
                            <p class="mb-1"><strong>الحالة:</strong> {{ $project->status_name_ar }}</p>
                            <p class="mb-1"><strong>التقدم:</strong> {{ $project->progress }}%</p>
                            @if ($project->description)
                                <hr>
                                <p class="mb-0">{{ $project->description }}</p>
                            @endif
                        </div>
                    </div>

                    <div class="card mb-3">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="card-title mb-0">فريق المشروع</h6>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled mb-0">
                                @if ($project->manager)
                                    <li class="mb-1"><span class="badge bg-primary me-1">مدير</span> {{ $project->manager->full_name }}</li>
                                @endif
                                @foreach ($project->members as $m)
                                    <li class="mb-1"><span class="badge bg-secondary me-1">{{ $m->role_name_ar }}</span> {{ $m->employee->full_name ?? '—' }}</li>
                                @endforeach
                                @if (!$project->manager && $project->members->isEmpty())
                                    <li class="text-muted">لا يوجد أعضاء مسجّلون في جدول الفريق.</li>
                                @endif
                            </ul>
                        </div>
                    </div>

                    <div class="card mb-3">
                        <div class="card-header">
                            <h6 class="card-title mb-0">مستندات المشروع</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-striped mb-0">
                                    <thead>
                                        <tr>
                                            <th>العنوان</th>
                                            <th>الملف</th>
                                            <th>التاريخ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($project->documents as $doc)
                                            <tr>
                                                <td>{{ $doc->title }}</td>
                                                <td>
                                                    @if ($doc->disk_url)
                                                        <a href="{{ $doc->disk_url }}" target="_blank" rel="noopener">{{ $doc->original_name ?? 'تحميل' }}</a>
                                                    @else
                                                        —
                                                    @endif
                                                </td>
                                                <td>{{ $doc->created_at?->format('Y-m-d') }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="text-center text-muted py-3">لا توجد مستندات</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-3">
                        <div class="card-header">
                            <h6 class="card-title mb-0">مهامي في هذا المشروع</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>المهمة</th>
                                            <th>الاستحقاق</th>
                                            <th>الحالة</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($myTasks as $task)
                                            <tr>
                                                <td>{{ $task->title_ar ?? $task->title }}</td>
                                                <td>{{ $task->due_date ? $task->due_date->format('Y-m-d') : '—' }}</td>
                                                <td><span class="badge bg-secondary">{{ $task->status_name_ar }}</span></td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="text-center text-muted py-3">لا توجد مهام معيّنة لك ضمن هذا المشروع.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title mb-0">تسجيل وقت العمل</h6>
                        </div>
                        <div class="card-body">
                            <p class="text-muted small mb-3">ساعاتك المسجّلة على هذا المشروع: <strong>{{ number_format($totalMyHours, 2) }}</strong></p>

                            @if ($project->allowsTimeLogging())
                                <form action="{{ route('employee.projects.time.store', $project) }}" method="post">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="form-label">تاريخ العمل</label>
                                        <input type="date" name="worked_date" class="form-control @error('worked_date') is-invalid @enderror" value="{{ old('worked_date', now()->format('Y-m-d')) }}" required>
                                        @error('worked_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">الساعات</label>
                                        <input type="number" name="hours" class="form-control @error('hours') is-invalid @enderror" step="0.25" min="0.01" max="24" value="{{ old('hours') }}" required>
                                        @error('hours')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">المهمة (اختياري)</label>
                                        <select name="task_id" class="form-select @error('task_id') is-invalid @enderror">
                                            <option value="">— بدون —</option>
                                            @foreach ($myTasks as $task)
                                                <option value="{{ $task->id }}" @selected(old('task_id') == $task->id)>{{ $task->title_ar ?? $task->title }}</option>
                                            @endforeach
                                        </select>
                                        @error('task_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">وصف (اختياري)</label>
                                        <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="2" maxlength="2000">{{ old('description') }}</textarea>
                                        @error('description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100">حفظ</button>
                                </form>
                            @else
                                <div class="alert alert-warning mb-0">لا يمكن تسجيل وقت على مشروع في هذه الحالة.</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
