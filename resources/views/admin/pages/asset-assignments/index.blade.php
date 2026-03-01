@extends('admin.layouts.master')

@section('page-title')
    توزيع الأصول
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">توزيع الأصول</h5>
                </div>
                <div>
                    @can('asset-assignment-create')
                    <a href="{{ route('admin.asset-assignments.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>توزيع أصل جديد
                    </a>
                    @endcan
                </div>
            </div>

            <!-- فلترة -->
            <div class="card mb-3">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.asset-assignments.index') }}" class="row g-3">
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
                            <select name="asset_id" class="form-select">
                                <option value="">كل الأصول</option>
                                @foreach ($assets as $ast)
                                    <option value="{{ $ast->id }}" {{ request('asset_id') == $ast->id ? 'selected' : '' }}>
                                        {{ $ast->asset_code }} - {{ $ast->name_ar ?? $ast->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="assignment_status" class="form-select">
                                <option value="">كل الحالات</option>
                                <option value="active" {{ request('assignment_status') == 'active' ? 'selected' : '' }}>نشط</option>
                                <option value="returned" {{ request('assignment_status') == 'returned' ? 'selected' : '' }}>مسترجع</option>
                                <option value="lost" {{ request('assignment_status') == 'lost' ? 'selected' : '' }}>مفقود</option>
                                <option value="damaged" {{ request('assignment_status') == 'damaged' ? 'selected' : '' }}>معطل</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary w-100">تطبيق الفلترة</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- جدول التوزيعات -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">قائمة التوزيعات ({{ $assignments->total() }})</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>الأصل</th>
                                    <th>الموظف</th>
                                    <th>تاريخ التوزيع</th>
                                    <th>تاريخ الاسترجاع المتوقع</th>
                                    <th>تاريخ الاسترجاع الفعلي</th>
                                    <th>حالة التوزيع</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($assignments as $assignment)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <a href="{{ route('admin.assets.show', $assignment->asset_id) }}">
                                                <strong>{{ $assignment->asset->asset_code }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $assignment->asset->name_ar ?? $assignment->asset->name }}</small>
                                            </a>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.employees.show', $assignment->employee_id) }}">
                                                {{ $assignment->employee->full_name }}
                                            </a>
                                        </td>
                                        <td>{{ $assignment->assigned_date->format('Y-m-d') }}</td>
                                        <td>{{ $assignment->expected_return_date ? $assignment->expected_return_date->format('Y-m-d') : '-' }}</td>
                                        <td>{{ $assignment->actual_return_date ? $assignment->actual_return_date->format('Y-m-d') : '-' }}</td>
                                        <td>
                                            <span class="badge bg-{{ $assignment->assignment_status == 'active' ? 'success' : ($assignment->assignment_status == 'returned' ? 'info' : 'danger') }}">
                                                {{ $assignment->assignment_status_name_ar }}
                                            </span>
                                        </td>
                                        <td>
                                            @can('asset-assignment-show')
                                            <a href="{{ route('admin.asset-assignments.show', $assignment->id) }}" class="btn btn-sm btn-primary" title="عرض">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @endcan
                                            @if ($assignment->assignment_status == 'active')
                                            <a href="{{ route('admin.asset-assignments.return-form', $assignment->id) }}" class="btn btn-sm btn-warning" title="استرجاع">
                                                <i class="fas fa-undo"></i>
                                            </a>
                                            @endif
                                            @can('asset-assignment-edit')
                                            <a href="{{ route('admin.asset-assignments.edit', $assignment->id) }}" class="btn btn-sm btn-info" title="تعديل">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @endcan
                                            @can('asset-assignment-delete')
                                            <button type="button" class="btn btn-sm btn-danger delete-btn" data-id="{{ $assignment->id }}" title="حذف">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            @endcan
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">لا توجد توزيعات</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $assignments->withQueryString()->links() }}
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
                        <p>هل أنت متأكد من حذف هذا التوزيع؟</p>
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
            const deleteUrl = '{{ url("admin/asset-assignments") }}/' + id;
            document.getElementById('deleteForm').action = deleteUrl;
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        });
    });
</script>
@stop

