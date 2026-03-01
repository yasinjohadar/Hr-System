@extends('admin.layouts.master')

@section('page-title')
    إدارة إنهاء الخدمة
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">إدارة إنهاء الخدمة</h5>
                </div>
                <div>
                    @can('employee-exit-create')
                    <a href="{{ route('admin.employee-exits.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>طلب إنهاء خدمة جديد
                    </a>
                    @endcan
                </div>
            </div>

            <!-- فلترة -->
            <div class="card mb-3">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.employee-exits.index') }}" class="row g-3">
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
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                                <option value="in_process" {{ request('status') == 'in_process' ? 'selected' : '' }}>قيد المعالجة</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>مكتمل</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="exit_type" class="form-select">
                                <option value="">كل الأنواع</option>
                                <option value="resignation" {{ request('exit_type') == 'resignation' ? 'selected' : '' }}>استقالة</option>
                                <option value="termination" {{ request('exit_type') == 'termination' ? 'selected' : '' }}>إنهاء خدمة</option>
                                <option value="retirement" {{ request('exit_type') == 'retirement' ? 'selected' : '' }}>تقاعد</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary w-100">تطبيق الفلترة</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- جدول إنهاء الخدمة -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">قائمة طلبات إنهاء الخدمة ({{ $exits->total() }})</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>الموظف</th>
                                    <th>نوع إنهاء الخدمة</th>
                                    <th>تاريخ الاستقالة</th>
                                    <th>آخر يوم عمل</th>
                                    <th>الحالة</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($exits as $exit)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td><strong>{{ $exit->employee->full_name }}</strong></td>
                                        <td><span class="badge bg-info">{{ $exit->exit_type_name_ar }}</span></td>
                                        <td>{{ $exit->resignation_date->format('Y-m-d') }}</td>
                                        <td>{{ $exit->last_working_day->format('Y-m-d') }}</td>
                                        <td>
                                            <span class="badge bg-{{ $exit->status == 'completed' ? 'success' : ($exit->status == 'in_process' ? 'primary' : 'warning') }}">
                                                {{ $exit->status_name_ar }}
                                            </span>
                                        </td>
                                        <td>
                                            @can('employee-exit-show')
                                            <a href="{{ route('admin.employee-exits.show', $exit->id) }}" class="btn btn-sm btn-primary" title="عرض">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @endcan
                                            @can('employee-exit-edit')
                                            <a href="{{ route('admin.employee-exits.edit', $exit->id) }}" class="btn btn-sm btn-warning" title="تعديل">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @endcan
                                            @if ($exit->status == 'pending')
                                            <a href="{{ route('admin.employee-exits.approve', $exit->id) }}" class="btn btn-sm btn-success" title="موافقة">
                                                <i class="fas fa-check"></i>
                                            </a>
                                            @endif
                                            @can('employee-exit-delete')
                                            <button type="button" class="btn btn-sm btn-danger delete-btn" data-id="{{ $exit->id }}" title="حذف">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            @endcan
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">لا توجد طلبات إنهاء خدمة</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $exits->withQueryString()->links() }}
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
                        <p>هل أنت متأكد من حذف هذا الطلب؟</p>
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
            const deleteUrl = '{{ url("admin/employee-exits") }}/' + id;
            document.getElementById('deleteForm').action = deleteUrl;
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        });
    });
</script>
@stop


