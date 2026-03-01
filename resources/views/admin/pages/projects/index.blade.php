@extends('admin.layouts.master')

@section('page-title')
    المشاريع
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">المشاريع</h5>
                </div>
                <div>
                    @can('project-create')
                    <a href="{{ route('admin.projects.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>إضافة مشروع جديد
                    </a>
                    @endcan
                </div>
            </div>

            <!-- فلترة -->
            <div class="card mb-3">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.projects.index') }}" class="row g-3">
                        <div class="col-md-3">
                            <input type="text" name="search" class="form-control" placeholder="بحث..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-2">
                            <select name="status" class="form-select">
                                <option value="">كل الحالات</option>
                                <option value="planning" {{ request('status') == 'planning' ? 'selected' : '' }}>قيد التخطيط</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>نشط</option>
                                <option value="on_hold" {{ request('status') == 'on_hold' ? 'selected' : '' }}>معلق</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>مكتمل</option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>ملغي</option>
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
                            <select name="department_id" class="form-select">
                                <option value="">كل الأقسام</option>
                                @foreach ($departments as $dept)
                                    <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                                        {{ $dept->name }}
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

            <!-- جدول المشاريع -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">قائمة المشاريع ({{ $projects->total() }})</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>رقم المشروع</th>
                                    <th>الاسم</th>
                                    <th>القسم</th>
                                    <th>المدير</th>
                                    <th>تاريخ البدء</th>
                                    <th>نسبة الإنجاز</th>
                                    <th>الحالة</th>
                                    <th>الأولوية</th>
                                    <th>عدد المهام</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($projects as $project)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td><strong>{{ $project->project_code }}</strong></td>
                                        <td>
                                            <strong>{{ $project->name_ar ?? $project->name }}</strong>
                                            @if ($project->name_ar && $project->name)
                                                <br><small class="text-muted">{{ $project->name }}</small>
                                            @endif
                                        </td>
                                        <td>{{ $project->department->name ?? '-' }}</td>
                                        <td>{{ $project->manager->full_name ?? '-' }}</td>
                                        <td>{{ $project->start_date->format('Y-m-d') }}</td>
                                        <td>
                                            <div class="progress" style="height: 20px;">
                                                <div class="progress-bar" role="progressbar" style="width: {{ $project->progress }}%">
                                                    {{ $project->progress }}%
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $project->status == 'completed' ? 'success' : ($project->status == 'active' ? 'primary' : ($project->status == 'cancelled' ? 'danger' : 'warning')) }}">
                                                {{ $project->status_name_ar }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $project->priority == 'urgent' ? 'danger' : ($project->priority == 'high' ? 'warning' : 'info') }}">
                                                {{ $project->priority_name_ar }}
                                            </span>
                                        </td>
                                        <td>{{ $project->tasks_count }}</td>
                                        <td>
                                            @can('project-show')
                                            <a href="{{ route('admin.projects.show', $project->id) }}" class="btn btn-sm btn-primary" title="عرض">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @endcan
                                            @can('project-edit')
                                            <a href="{{ route('admin.projects.edit', $project->id) }}" class="btn btn-sm btn-info" title="تعديل">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @endcan
                                            @can('project-delete')
                                            <button type="button" class="btn btn-sm btn-danger delete-btn" data-id="{{ $project->id }}" title="حذف">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            @endcan
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="11" class="text-center">لا توجد مشاريع</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $projects->withQueryString()->links() }}
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
                        <p>هل أنت متأكد من حذف هذا المشروع؟</p>
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
            // إنشاء URL بشكل صحيح
            const deleteUrl = '{{ url("admin/projects") }}/' + id;
            document.getElementById('deleteForm').action = deleteUrl;
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        });
    });
</script>
@stop

