@extends('admin.layouts.master')

@section('page-title')
    تفاصيل المهمة
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">تفاصيل المهمة</h5>
                </div>
                <div>
                    <a href="{{ route('admin.tasks.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-right me-2"></i>العودة للقائمة
                    </a>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-8">
                    <!-- معلومات المهمة -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">{{ $task->title_ar ?? $task->title }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">رقم المهمة:</label>
                                    <p class="form-control-plaintext">{{ $task->task_code }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">الحالة:</label>
                                    <p class="form-control-plaintext">
                                        <span class="badge bg-{{ $task->status == 'completed' ? 'success' : ($task->status == 'in_progress' ? 'primary' : ($task->status == 'cancelled' ? 'danger' : 'warning')) }}">
                                            {{ $task->status_name_ar }}
                                        </span>
                                    </p>
                                </div>
                                @if ($task->project)
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">المشروع:</label>
                                    <p class="form-control-plaintext">
                                        <a href="{{ route('admin.projects.show', $task->project_id) }}">
                                            {{ $task->project->name_ar ?? $task->project->name }}
                                        </a>
                                    </p>
                                </div>
                                @endif
                                @if ($task->department)
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">القسم:</label>
                                    <p class="form-control-plaintext">{{ $task->department->name }}</p>
                                </div>
                                @endif
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">الأولوية:</label>
                                    <p class="form-control-plaintext">
                                        <span class="badge bg-{{ $task->priority == 'urgent' ? 'danger' : ($task->priority == 'high' ? 'warning' : 'info') }}">
                                            {{ $task->priority_name_ar }}
                                        </span>
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">نسبة الإنجاز:</label>
                                    <p class="form-control-plaintext">
                                        <div class="progress" style="height: 25px;">
                                            <div class="progress-bar" role="progressbar" style="width: {{ $task->progress }}%">
                                                {{ $task->progress }}%
                                            </div>
                                        </div>
                                    </p>
                                </div>
                                @if ($task->start_date)
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">تاريخ البدء:</label>
                                    <p class="form-control-plaintext">{{ $task->start_date->format('Y-m-d') }}</p>
                                </div>
                                @endif
                                @if ($task->due_date)
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">تاريخ الاستحقاق:</label>
                                    <p class="form-control-plaintext">{{ $task->due_date->format('Y-m-d') }}</p>
                                </div>
                                @endif
                                @if ($task->estimated_hours)
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">الساعات المتوقعة:</label>
                                    <p class="form-control-plaintext">{{ $task->estimated_hours }} ساعة</p>
                                </div>
                                @endif
                                @if ($task->actual_hours)
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">الساعات الفعلية:</label>
                                    <p class="form-control-plaintext">{{ $task->actual_hours }} ساعة</p>
                                </div>
                                @endif
                                @if ($task->description)
                                <div class="col-12 mb-3">
                                    <label class="form-label fw-bold">الوصف:</label>
                                    <p class="form-control-plaintext">{{ $task->description }}</p>
                                </div>
                                @endif
                            </div>
                            <div class="mt-3">
                                @can('task-edit')
                                <a href="{{ route('admin.tasks.edit', $task->id) }}" class="btn btn-info">
                                    <i class="fas fa-edit me-2"></i>تعديل
                                </a>
                                @endcan
                            </div>
                        </div>
                    </div>

                    <!-- تعيينات المهام -->
                    <div class="card mt-3">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">تعيينات المهام</h5>
                            @can('task-assign')
                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#assignModal">
                                <i class="fas fa-plus me-2"></i>تعيين موظف
                            </button>
                            @endcan
                        </div>
                        <div class="card-body">
                            @if ($task->assignments->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>الموظف</th>
                                                <th>تاريخ التعيين</th>
                                                <th>تاريخ الاستحقاق</th>
                                                <th>الحالة</th>
                                                <th>نسبة الإنجاز</th>
                                                <th>الإجراءات</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($task->assignments as $assignment)
                                                <tr>
                                                    <td>
                                                        <a href="{{ route('admin.employees.show', $assignment->employee_id) }}">
                                                            {{ $assignment->employee->full_name }}
                                                        </a>
                                                    </td>
                                                    <td>{{ $assignment->assigned_date->format('Y-m-d') }}</td>
                                                    <td>{{ $assignment->due_date ? $assignment->due_date->format('Y-m-d') : '-' }}</td>
                                                    <td>
                                                        <span class="badge bg-{{ $assignment->status == 'completed' ? 'success' : ($assignment->status == 'in_progress' ? 'primary' : 'warning') }}">
                                                            {{ $assignment->status_name_ar }}
                                                        </span>
                                                    </td>
                                                    <td>{{ $assignment->progress }}%</td>
                                                    <td>
                                                        <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" 
                                                                data-bs-target="#updateAssignmentModal{{ $assignment->id }}">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-muted text-center">لا توجد تعيينات</p>
                            @endif
                        </div>
                    </div>

                    <!-- التعليقات -->
                    <div class="card mt-3">
                        <div class="card-header">
                            <h5 class="card-title mb-0">التعليقات</h5>
                        </div>
                        <div class="card-body">
                            @can('task-comment')
                            <form method="POST" action="{{ route('admin.tasks.add-comment', $task->id) }}" class="mb-3">
                                @csrf
                                <div class="mb-2">
                                    <textarea name="comment" class="form-control" rows="3" placeholder="أضف تعليق..." required></textarea>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="is_internal" value="1" id="is_internal">
                                    <label class="form-check-label" for="is_internal">
                                        تعليق داخلي (غير مرئي للموظف)
                                    </label>
                                </div>
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="fas fa-comment me-2"></i>إضافة تعليق
                                </button>
                            </form>
                            @endcan
                            <div class="comments-list">
                                @forelse ($task->comments as $comment)
                                    <div class="card mb-2">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between">
                                                <div>
                                                    <strong>{{ $comment->user->name ?? $comment->employee->full_name ?? 'غير معروف' }}</strong>
                                                    @if ($comment->is_internal)
                                                        <span class="badge bg-secondary">داخلي</span>
                                                    @endif
                                                </div>
                                                <small class="text-muted">{{ $comment->created_at->format('Y-m-d H:i') }}</small>
                                            </div>
                                            <p class="mt-2 mb-0">{{ $comment->comment }}</p>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-muted text-center">لا توجد تعليقات</p>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <!-- المرفقات -->
                    <div class="card mt-3">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">المرفقات</h5>
                            @can('task-comment')
                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#uploadModal">
                                <i class="fas fa-upload me-2"></i>رفع ملف
                            </button>
                            @endcan
                        </div>
                        <div class="card-body">
                            @if ($task->attachments->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>اسم الملف</th>
                                                <th>النوع</th>
                                                <th>الحجم</th>
                                                <th>تاريخ الرفع</th>
                                                <th>الإجراءات</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($task->attachments as $attachment)
                                                <tr>
                                                    <td>{{ $attachment->file_name }}</td>
                                                    <td>{{ $attachment->file_type }}</td>
                                                    <td>{{ number_format($attachment->file_size / 1024, 2) }} KB</td>
                                                    <td>{{ $attachment->created_at->format('Y-m-d H:i') }}</td>
                                                    <td>
                                                        <a href="{{ Storage::url($attachment->file_path) }}" target="_blank" class="btn btn-sm btn-primary">
                                                            <i class="fas fa-download"></i>
                                                        </a>
                                                        @can('task-delete')
                                                        <form method="POST" action="{{ route('admin.tasks.delete-attachment', [$task->id, $attachment->id]) }}" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('هل أنت متأكد من حذف هذا المرفق؟')">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                        @endcan
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-muted text-center">لا توجد مرفقات</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal تعيين موظف -->
    @can('task-assign')
    <div class="modal fade" id="assignModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ route('admin.tasks.assign', $task->id) }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">تعيين مهمة لموظف</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">الموظف <span class="text-danger">*</span></label>
                            <select name="employee_id" class="form-select" required>
                                <option value="">اختر الموظف</option>
                                @foreach ($employees as $emp)
                                    <option value="{{ $emp->id }}">{{ $emp->full_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">تاريخ الاستحقاق</label>
                            <input type="date" name="due_date" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">ملاحظات</label>
                            <textarea name="notes" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-primary">تعيين</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endcan

    <!-- Modal رفع ملف -->
    @can('task-comment')
    <div class="modal fade" id="uploadModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ route('admin.tasks.upload-attachment', $task->id) }}" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">رفع ملف</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">الملف <span class="text-danger">*</span></label>
                            <input type="file" name="file" class="form-control" required>
                            <small class="text-muted">الحد الأقصى: 10MB</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">الوصف</label>
                            <textarea name="description" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-primary">رفع</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endcan
@stop

