@extends('admin.layouts.master')

@section('page-title')
    إدارة المهارات
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">إدارة المهارات</h5>
                </div>
                <div>
                    @can('employee-skill-create')
                    <a href="{{ route('admin.employee-skills.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>إضافة مهارة جديدة
                    </a>
                    @endcan
                </div>
            </div>

            <!-- فلترة -->
            <div class="card mb-3">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.employee-skills.index') }}" class="row g-3">
                        <div class="col-md-4">
                            <select name="employee_id" class="form-select">
                                <option value="">كل الموظفين</option>
                                @foreach ($employees as $emp)
                                    <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>
                                        {{ $emp->full_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <select name="proficiency_level" class="form-select">
                                <option value="">كل المستويات</option>
                                <option value="beginner" {{ request('proficiency_level') == 'beginner' ? 'selected' : '' }}>مبتدئ</option>
                                <option value="intermediate" {{ request('proficiency_level') == 'intermediate' ? 'selected' : '' }}>متوسط</option>
                                <option value="advanced" {{ request('proficiency_level') == 'advanced' ? 'selected' : '' }}>متقدم</option>
                                <option value="expert" {{ request('proficiency_level') == 'expert' ? 'selected' : '' }}>خبير</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary w-100">تطبيق الفلترة</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- جدول المهارات -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">قائمة المهارات ({{ $skills->total() }})</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>الموظف</th>
                                    <th>اسم المهارة</th>
                                    <th>مستوى الكفاءة</th>
                                    <th>سنوات الخبرة</th>
                                    <th>تم التحقق</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($skills as $skill)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td><strong>{{ $skill->employee->full_name }}</strong></td>
                                        <td>{{ $skill->skill_name_ar ?? $skill->skill_name }}</td>
                                        <td>
                                            <span class="badge bg-{{ $skill->proficiency_level == 'expert' ? 'success' : ($skill->proficiency_level == 'advanced' ? 'info' : ($skill->proficiency_level == 'intermediate' ? 'warning' : 'secondary')) }}">
                                                {{ $skill->proficiency_level_name_ar }}
                                            </span>
                                        </td>
                                        <td>{{ $skill->years_of_experience ?? '-' }}</td>
                                        <td>
                                            @if ($skill->is_verified)
                                                <span class="badge bg-success">نعم</span>
                                            @else
                                                <span class="badge bg-warning">لا</span>
                                            @endif
                                        </td>
                                        <td>
                                            @can('employee-skill-show')
                                            <a href="{{ route('admin.employee-skills.show', $skill->id) }}" class="btn btn-sm btn-primary" title="عرض">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @endcan
                                            @can('employee-skill-edit')
                                            <a href="{{ route('admin.employee-skills.edit', $skill->id) }}" class="btn btn-sm btn-warning" title="تعديل">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @endcan
                                            @if (!$skill->is_verified)
                                            <a href="{{ route('admin.employee-skills.verify', $skill->id) }}" class="btn btn-sm btn-success" title="التحقق">
                                                <i class="fas fa-check"></i>
                                            </a>
                                            @endif
                                            @can('employee-skill-delete')
                                            <button type="button" class="btn btn-sm btn-danger delete-btn" data-id="{{ $skill->id }}" title="حذف">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            @endcan
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">لا توجد مهارات</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $skills->withQueryString()->links() }}
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
                        <p>هل أنت متأكد من حذف هذه المهارة؟</p>
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
            const deleteUrl = '{{ url("admin/employee-skills") }}/' + id;
            document.getElementById('deleteForm').action = deleteUrl;
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        });
    });
</script>
@stop


