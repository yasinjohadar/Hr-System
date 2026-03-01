@extends('admin.layouts.master')

@section('page-title')
    المهام
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">المهام</h5>
                </div>
                <div>
                    @can('task-create')
                    <a href="{{ route('admin.tasks.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>إضافة مهمة جديدة
                    </a>
                    @endcan
                </div>
            </div>

            <!-- فلترة -->
            <div class="card mb-3">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.tasks.index') }}" class="row g-3">
                        <div class="col-md-3">
                            <input type="text" name="search" class="form-control" placeholder="بحث..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-2">
                            <select name="status" class="form-select">
                                <option value="">كل الحالات</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                                <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>قيد التنفيذ</option>
                                <option value="in_review" {{ request('status') == 'in_review' ? 'selected' : '' }}>قيد المراجعة</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>مكتمل</option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>ملغي</option>
                                <option value="on_hold" {{ request('status') == 'on_hold' ? 'selected' : '' }}>معلق</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="priority" class="form-select">
                                <option value="">كل الأولويات</option>
                                <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>منخفض</option>
                                <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>متوسط</option>
                                <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>عالي</option>
                                <option value="urgent" {{ request('priority') == 'urgent' ? 'selected' : '' }}>عاجل</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="project_id" class="form-select">
                                <option value="">كل المشاريع</option>
                                @foreach ($projects as $proj)
                                    <option value="{{ $proj->id }}" {{ request('project_id') == $proj->id ? 'selected' : '' }}>
                                        {{ $proj->name_ar ?? $proj->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary w-100">تطبيق الفلترة</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- جدول المهام -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">قائمة المهام ({{ $tasks->total() }})</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>رقم المهمة</th>
                                    <th>العنوان</th>
                                    <th>المشروع</th>
                                    <th>تاريخ الاستحقاق</th>
                                    <th>نسبة الإنجاز</th>
                                    <th>الحالة</th>
                                    <th>الأولوية</th>
                                    <th>عدد التعيينات</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($tasks as $task)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td><strong>{{ $task->task_code }}</strong></td>
                                        <td>
                                            <strong>{{ $task->title_ar ?? $task->title }}</strong>
                                            @if ($task->title_ar && $task->title)
                                                <br><small class="text-muted">{{ $task->title }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($task->project)
                                                <a href="{{ route('admin.projects.show', $task->project_id) }}">
                                                    {{ $task->project->name_ar ?? $task->project->name }}
                                                </a>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>{{ $task->due_date ? $task->due_date->format('Y-m-d') : '-' }}</td>
                                        <td>
                                            <div class="progress" style="height: 20px;">
                                                <div class="progress-bar" role="progressbar" style="width: {{ $task->progress }}%">
                                                    {{ $task->progress }}%
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $task->status == 'completed' ? 'success' : ($task->status == 'in_progress' ? 'primary' : ($task->status == 'cancelled' ? 'danger' : 'warning')) }}">
                                                {{ $task->status_name_ar }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $task->priority == 'urgent' ? 'danger' : ($task->priority == 'high' ? 'warning' : 'info') }}">
                                                {{ $task->priority_name_ar }}
                                            </span>
                                        </td>
                                        <td>{{ $task->assignments_count }}</td>
                                        <td>
                                            @can('task-show')
                                            <a href="{{ route('admin.tasks.show', $task->id) }}" class="btn btn-sm btn-primary" title="عرض">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @endcan
                                            @can('task-edit')
                                            <a href="{{ route('admin.tasks.edit', $task->id) }}" class="btn btn-sm btn-info" title="تعديل">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @endcan
                                            @can('task-delete')
                                            <button type="button" class="btn btn-sm btn-danger delete-btn" data-id="{{ $task->id }}" title="حذف">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            @endcan
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center">لا توجد مهام</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $tasks->withQueryString()->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal حذف -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="" id="deleteForm">
                    @csrf
                    @method('DELETE')
                    <div class="modal-header">
                        <h5 class="modal-title">تأكيد الحذف</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>هل أنت متأكد من حذف هذه المهمة؟</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-danger">حذف</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('js')
<script>
    document.querySelectorAll('.delete-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const deleteUrl = '{{ url("admin/tasks") }}/' + id;
            document.getElementById('deleteForm').action = deleteUrl;
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        });
    });
</script>
@stop

