@extends('admin.layouts.master')

@section('page-title')
    تفاصيل المشروع
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">تفاصيل المشروع</h5>
                </div>
                <div>
                    <a href="{{ route('admin.projects.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-right me-2"></i>العودة للقائمة
                    </a>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">{{ $project->name_ar ?? $project->name }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">رقم المشروع:</label>
                                    <p class="form-control-plaintext">{{ $project->project_code }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">الاسم:</label>
                                    <p class="form-control-plaintext">{{ $project->name }}</p>
                                </div>
                                @if ($project->name_ar)
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">الاسم (عربي):</label>
                                    <p class="form-control-plaintext">{{ $project->name_ar }}</p>
                                </div>
                                @endif
                                @if ($project->department)
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">القسم:</label>
                                    <p class="form-control-plaintext">{{ $project->department->name }}</p>
                                </div>
                                @endif
                                @if ($project->manager)
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">مدير المشروع:</label>
                                    <p class="form-control-plaintext">
                                        <a href="{{ route('admin.employees.show', $project->manager_id) }}">
                                            {{ $project->manager->full_name }}
                                        </a>
                                    </p>
                                </div>
                                @endif
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">تاريخ البدء:</label>
                                    <p class="form-control-plaintext">{{ $project->start_date->format('Y-m-d') }}</p>
                                </div>
                                @if ($project->end_date)
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">تاريخ الانتهاء:</label>
                                    <p class="form-control-plaintext">{{ $project->end_date->format('Y-m-d') }}</p>
                                </div>
                                @endif
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">الحالة:</label>
                                    <p class="form-control-plaintext">
                                        <span class="badge bg-{{ $project->status == 'completed' ? 'success' : ($project->status == 'active' ? 'primary' : ($project->status == 'cancelled' ? 'danger' : 'warning')) }}">
                                            {{ $project->status_name_ar }}
                                        </span>
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">الأولوية:</label>
                                    <p class="form-control-plaintext">
                                        <span class="badge bg-{{ $project->priority == 'urgent' ? 'danger' : ($project->priority == 'high' ? 'warning' : 'info') }}">
                                            {{ $project->priority_name_ar }}
                                        </span>
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">نسبة الإنجاز:</label>
                                    <p class="form-control-plaintext">
                                        <div class="progress" style="height: 25px;">
                                            <div class="progress-bar" role="progressbar" style="width: {{ $project->progress }}%">
                                                {{ $project->progress }}%
                                            </div>
                                        </div>
                                    </p>
                                </div>
                                @if ($project->budget)
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">الميزانية:</label>
                                    <p class="form-control-plaintext">
                                        {{ number_format($project->budget, 2) }}
                                        @if ($project->currency)
                                            {{ $project->currency->code }}
                                        @endif
                                    </p>
                                </div>
                                @endif
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">عدد المهام:</label>
                                    <p class="form-control-plaintext">{{ $project->tasks_count }}</p>
                                </div>
                                @if ($project->description)
                                <div class="col-12 mb-3">
                                    <label class="form-label fw-bold">الوصف:</label>
                                    <p class="form-control-plaintext">{{ $project->description }}</p>
                                </div>
                                @endif
                                @if ($project->notes)
                                <div class="col-12 mb-3">
                                    <label class="form-label fw-bold">ملاحظات:</label>
                                    <p class="form-control-plaintext">{{ $project->notes }}</p>
                                </div>
                                @endif
                            </div>
                            <div class="mt-3">
                                @can('project-edit')
                                <a href="{{ route('admin.projects.edit', $project->id) }}" class="btn btn-info">
                                    <i class="fas fa-edit me-2"></i>تعديل
                                </a>
                                @endcan
                                @can('task-create')
                                <a href="{{ route('admin.tasks.create', ['project_id' => $project->id]) }}" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i>إضافة مهمة
                                </a>
                                @endcan
                            </div>
                        </div>
                    </div>

                    <!-- قائمة المهام -->
                    @if ($project->tasks->count() > 0)
                    <div class="card mt-3">
                        <div class="card-header">
                            <h5 class="card-title mb-0">مهام المشروع</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>رقم المهمة</th>
                                            <th>العنوان</th>
                                            <th>الحالة</th>
                                            <th>نسبة الإنجاز</th>
                                            <th>الإجراءات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($project->tasks as $task)
                                            <tr>
                                                <td>{{ $task->task_code }}</td>
                                                <td>{{ $task->title_ar ?? $task->title }}</td>
                                                <td>
                                                    <span class="badge bg-{{ $task->status == 'completed' ? 'success' : ($task->status == 'in_progress' ? 'primary' : 'warning') }}">
                                                        {{ $task->status_name_ar }}
                                                    </span>
                                                </td>
                                                <td>{{ $task->progress }}%</td>
                                                <td>
                                                    <a href="{{ route('admin.tasks.show', $task->id) }}" class="btn btn-sm btn-primary">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@stop

