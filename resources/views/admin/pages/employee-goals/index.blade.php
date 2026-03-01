@extends('admin.layouts.master')

@section('page-title')
    إدارة الأهداف
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">إدارة الأهداف</h5>
                </div>
                <div>
                    @can('employee-goal-create')
                    <a href="{{ route('admin.employee-goals.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>إضافة هدف جديد
                    </a>
                    @endcan
                </div>
            </div>

            <!-- فلترة -->
            <div class="card mb-3">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.employee-goals.index') }}" class="row g-3">
                        <div class="col-md-3">
                            <select name="employee_id" class="form-select">
                                <option value="">كل الموظفين</option>
                                @foreach ($employees as $emp)
                                    <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>
                                        {{ $emp->full_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="status" class="form-select">
                                <option value="">كل الحالات</option>
                                <option value="not_started" {{ request('status') == 'not_started' ? 'selected' : '' }}>لم يبدأ</option>
                                <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>قيد التنفيذ</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>مكتمل</option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>ملغي</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="type" class="form-select">
                                <option value="">كل الأنواع</option>
                                <option value="personal" {{ request('type') == 'personal' ? 'selected' : '' }}>شخصي</option>
                                <option value="team" {{ request('type') == 'team' ? 'selected' : '' }}>فريق</option>
                                <option value="department" {{ request('type') == 'department' ? 'selected' : '' }}>قسم</option>
                                <option value="company" {{ request('type') == 'company' ? 'selected' : '' }}>شركة</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary w-100">تطبيق الفلترة</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- جدول الأهداف -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">قائمة الأهداف ({{ $goals->total() }})</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>الموظف</th>
                                    <th>عنوان الهدف</th>
                                    <th>النوع</th>
                                    <th>الأولوية</th>
                                    <th>تاريخ الهدف</th>
                                    <th>التقدم</th>
                                    <th>الحالة</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($goals as $goal)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td><strong>{{ $goal->employee->full_name }}</strong></td>
                                        <td>{{ $goal->title }}</td>
                                        <td><span class="badge bg-info">{{ $goal->type_name_ar }}</span></td>
                                        <td>
                                            <span class="badge bg-{{ $goal->priority == 'critical' ? 'danger' : ($goal->priority == 'high' ? 'warning' : ($goal->priority == 'medium' ? 'info' : 'secondary')) }}">
                                                {{ $goal->priority_name_ar }}
                                            </span>
                                        </td>
                                        <td>{{ $goal->target_date->format('Y-m-d') }}</td>
                                        <td>
                                            <div class="progress" style="height: 20px;">
                                                <div class="progress-bar" role="progressbar" 
                                                     style="width: {{ $goal->progress_percentage }}%"
                                                     aria-valuenow="{{ $goal->progress_percentage }}" 
                                                     aria-valuemin="0" aria-valuemax="100">
                                                    {{ $goal->progress_percentage }}%
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $goal->status == 'completed' ? 'success' : ($goal->status == 'in_progress' ? 'primary' : ($goal->status == 'cancelled' ? 'danger' : 'secondary')) }}">
                                                {{ $goal->status_name_ar }}
                                            </span>
                                        </td>
                                        <td>
                                            @can('employee-goal-show')
                                            <a href="{{ route('admin.employee-goals.show', $goal->id) }}" class="btn btn-sm btn-primary" title="عرض">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @endcan
                                            @can('employee-goal-edit')
                                            <a href="{{ route('admin.employee-goals.edit', $goal->id) }}" class="btn btn-sm btn-warning" title="تعديل">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @endcan
                                            @can('employee-goal-delete')
                                            <button type="button" class="btn btn-sm btn-danger delete-btn" data-id="{{ $goal->id }}" title="حذف">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            @endcan
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center">لا توجد أهداف</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $goals->withQueryString()->links() }}
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
                        <p>هل أنت متأكد من حذف هذا الهدف؟</p>
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
            document.getElementById('deleteId').value = id;
            const deleteUrl = '{{ url("admin/employee-goals") }}/' + id;
            document.getElementById('deleteForm').action = deleteUrl;
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        });
    });
</script>
@stop


