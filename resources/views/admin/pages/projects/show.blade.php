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
                <div class="col-xl-10">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">{{ $project->name_ar ?? $project->name }}</h5>
                        </div>
                        <div class="card-body">
                            <ul class="nav nav-tabs mb-3" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="tab-overview-btn" data-bs-toggle="tab" data-bs-target="#tab-overview" type="button" role="tab">نظرة عامة</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="tab-team-btn" data-bs-toggle="tab" data-bs-target="#tab-team" type="button" role="tab">فريق المشروع</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="tab-docs-btn" data-bs-toggle="tab" data-bs-target="#tab-docs" type="button" role="tab">المستندات</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="tab-time-btn" data-bs-toggle="tab" data-bs-target="#tab-time" type="button" role="tab">سجل الوقت</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="tab-tasks-btn" data-bs-toggle="tab" data-bs-target="#tab-tasks" type="button" role="tab">المهام</button>
                                </li>
                            </ul>

                            <div class="tab-content">
                                <div class="tab-pane fade show active" id="tab-overview" role="tabpanel">
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
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">إجمالي الساعات المسجّلة:</label>
                                            <p class="form-control-plaintext">{{ number_format((float) ($project->total_logged_hours ?? 0), 2) }} ساعة</p>
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

                                <div class="tab-pane fade" id="tab-team" role="tabpanel">
                                    @can('project-edit')
                                        <form action="{{ route('admin.projects.members.store', $project) }}" method="post" class="row g-3 mb-4">
                                            @csrf
                                            <div class="col-md-5">
                                                <label class="form-label">الموظف</label>
                                                <select name="employee_id" class="form-select" required>
                                                    <option value="">— اختر —</option>
                                                    @foreach ($employees as $emp)
                                                        <option value="{{ $emp->id }}">{{ $emp->full_name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">الدور</label>
                                                <select name="role" class="form-select" required>
                                                    <option value="member">عضو فريق</option>
                                                    <option value="lead">قائد فريق</option>
                                                    <option value="sponsor">راعي / داعم</option>
                                                </select>
                                            </div>
                                            <div class="col-md-3 d-flex align-items-end">
                                                <button type="submit" class="btn btn-primary w-100">إضافة للفريق</button>
                                            </div>
                                        </form>
                                    @endcan

                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>الموظف</th>
                                                    <th>الدور</th>
                                                    <th>منذ</th>
                                                    @can('project-edit')
                                                        <th width="100">إجراءات</th>
                                                    @endcan
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($project->members as $member)
                                                    <tr>
                                                        <td>
                                                            <a href="{{ route('admin.employees.show', $member->employee_id) }}">{{ $member->employee->full_name ?? '—' }}</a>
                                                        </td>
                                                        <td>{{ $member->role_name_ar }}</td>
                                                        <td>{{ $member->created_at?->format('Y-m-d') }}</td>
                                                        @can('project-edit')
                                                            <td>
                                                                <form action="{{ route('admin.projects.members.destroy', [$project, $member]) }}" method="post" class="d-inline" onsubmit="return confirm('إزالة هذا العضو من المشروع؟');">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn btn-sm btn-outline-danger">إزالة</button>
                                                                </form>
                                                            </td>
                                                        @endcan
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="{{ auth()->user()->can('project-edit') ? 4 : 3 }}" class="text-center text-muted">لا يوجد أعضاء مسجّلون بجدول الفريق (عدا مدير المشروع).</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="tab-docs" role="tabpanel">
                                    @can('project-edit')
                                        <form action="{{ route('admin.projects.documents.store', $project) }}" method="post" enctype="multipart/form-data" class="row g-3 mb-4">
                                            @csrf
                                            <div class="col-md-4">
                                                <label class="form-label">عنوان المستند</label>
                                                <input type="text" name="title" class="form-control" required maxlength="255">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">الملف</label>
                                                <input type="file" name="file" class="form-control" required>
                                                <small class="text-muted">حتى 15 ميجابايت — pdf, office, صور, zip</small>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">وصف (اختياري)</label>
                                                <input type="text" name="description" class="form-control">
                                            </div>
                                            <div class="col-md-1 d-flex align-items-end">
                                                <button type="submit" class="btn btn-primary w-100">رفع</button>
                                            </div>
                                        </form>
                                    @endcan

                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>العنوان</th>
                                                    <th>الملف</th>
                                                    <th>الرافع</th>
                                                    <th>التاريخ</th>
                                                    @can('project-edit')
                                                        <th width="100">إجراءات</th>
                                                    @endcan
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
                                                        <td>{{ $doc->uploader->name ?? '—' }}</td>
                                                        <td>{{ $doc->created_at?->format('Y-m-d') }}</td>
                                                        @can('project-edit')
                                                            <td>
                                                                <form action="{{ route('admin.projects.documents.destroy', [$project, $doc]) }}" method="post" class="d-inline" onsubmit="return confirm('حذف المستند؟');">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn btn-sm btn-outline-danger">حذف</button>
                                                                </form>
                                                            </td>
                                                        @endcan
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="{{ auth()->user()->can('project-edit') ? 5 : 4 }}" class="text-center text-muted">لا توجد مستندات</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="tab-time" role="tabpanel">
                                    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
                                        <p class="mb-0"><strong>إجمالي الساعات:</strong> {{ number_format((float) ($project->total_logged_hours ?? 0), 2) }} ساعة</p>
                                        @can('project-show')
                                            <a href="{{ route('admin.projects.time-entries.export', $project) }}" class="btn btn-outline-secondary btn-sm">
                                                <i class="fas fa-download me-1"></i>تصدير CSV
                                            </a>
                                        @endcan
                                    </div>

                                    @can('project-edit')
                                        @if ($project->allowsTimeLogging())
                                            <form action="{{ route('admin.projects.time-entries.store', $project) }}" method="post" class="row g-3 mb-4">
                                                @csrf
                                                <div class="col-md-3">
                                                    <label class="form-label">الموظف</label>
                                                    <select name="employee_id" class="form-select" required>
                                                        @foreach ($employees as $emp)
                                                            <option value="{{ $emp->id }}">{{ $emp->full_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label">المهمة (اختياري)</label>
                                                    <select name="task_id" class="form-select">
                                                        <option value="">— بدون —</option>
                                                        @foreach ($project->tasks as $t)
                                                            <option value="{{ $t->id }}">{{ $t->task_code }} — {{ $t->title_ar ?? $t->title }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-2">
                                                    <label class="form-label">تاريخ العمل</label>
                                                    <input type="date" name="worked_date" class="form-control" value="{{ now()->format('Y-m-d') }}" required>
                                                </div>
                                                <div class="col-md-2">
                                                    <label class="form-label">الساعات</label>
                                                    <input type="number" name="hours" class="form-control" step="0.25" min="0.01" max="24" required>
                                                </div>
                                                <div class="col-md-12">
                                                    <label class="form-label">وصف (اختياري)</label>
                                                    <input type="text" name="description" class="form-control" maxlength="2000">
                                                </div>
                                                <div class="col-12">
                                                    <button type="submit" class="btn btn-primary">تسجيل الوقت</button>
                                                </div>
                                            </form>
                                        @else
                                            <div class="alert alert-warning">لا يمكن إضافة سجلات وقت جديدة لمشروع مكتمل أو ملغى.</div>
                                        @endif
                                    @endcan

                                    <div class="table-responsive">
                                        <table class="table table-striped table-sm">
                                            <thead>
                                                <tr>
                                                    <th>التاريخ</th>
                                                    <th>الموظف</th>
                                                    <th>الساعات</th>
                                                    <th>المهمة</th>
                                                    <th>الوصف</th>
                                                    <th>المسجّل</th>
                                                    @can('project-edit')
                                                        <th width="90">إجراءات</th>
                                                    @endcan
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($project->timeEntries as $entry)
                                                    <tr>
                                                        <td>{{ $entry->worked_date->format('Y-m-d') }}</td>
                                                        <td>{{ $entry->employee->full_name ?? '—' }}</td>
                                                        <td>{{ number_format((float) $entry->hours, 2) }}</td>
                                                        <td>
                                                            @if ($entry->task)
                                                                <a href="{{ route('admin.tasks.show', $entry->task_id) }}">{{ $entry->task->task_code }}</a>
                                                            @else
                                                                —
                                                            @endif
                                                        </td>
                                                        <td>{{ \Illuminate\Support\Str::limit($entry->description ?? '', 40) }}</td>
                                                        <td>{{ $entry->creator->name ?? '—' }}</td>
                                                        @can('project-edit')
                                                            <td>
                                                                <form action="{{ route('admin.projects.time-entries.destroy', [$project, $entry]) }}" method="post" class="d-inline" onsubmit="return confirm('حذف السجل؟');">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn btn-sm btn-outline-danger">حذف</button>
                                                                </form>
                                                            </td>
                                                        @endcan
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="{{ auth()->user()->can('project-edit') ? 7 : 6 }}" class="text-center text-muted">لا توجد سجلات وقت</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="tab-tasks" role="tabpanel">
                                    @if ($project->tasks->count() > 0)
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
                                    @else
                                        <p class="text-muted text-center mb-0">لا توجد مهام لهذا المشروع.</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
