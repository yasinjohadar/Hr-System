@extends('admin.layouts.master')

@section('page-title')
    إدارة الشهادات
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">إدارة الشهادات</h5>
                </div>
                <div>
                    @can('employee-certificate-create')
                    <a href="{{ route('admin.employee-certificates.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>إضافة شهادة جديدة
                    </a>
                    @endcan
                </div>
            </div>

            <!-- فلترة -->
            <div class="card mb-3">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.employee-certificates.index') }}" class="row g-3">
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
                            <select name="status" class="form-select">
                                <option value="">كل الحالات</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>نشط</option>
                                <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>منتهي</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary w-100">تطبيق الفلترة</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- جدول الشهادات -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">قائمة الشهادات ({{ $certificates->total() }})</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>الموظف</th>
                                    <th>اسم الشهادة</th>
                                    <th>الجهة المانحة</th>
                                    <th>تاريخ الإصدار</th>
                                    <th>تاريخ الانتهاء</th>
                                    <th>الحالة</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($certificates as $certificate)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td><strong>{{ $certificate->employee->full_name }}</strong></td>
                                        <td>{{ $certificate->certificate_name_ar ?? $certificate->certificate_name }}</td>
                                        <td>{{ $certificate->issuing_organization }}</td>
                                        <td>{{ $certificate->issue_date->format('Y-m-d') }}</td>
                                        <td>
                                            @if ($certificate->does_not_expire)
                                                <span class="text-muted">لا تنتهي</span>
                                            @elseif ($certificate->expiry_date)
                                                <span class="{{ $certificate->isExpired() ? 'text-danger' : '' }}">
                                                    {{ $certificate->expiry_date->format('Y-m-d') }}
                                                </span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $certificate->status == 'active' ? 'success' : 'danger' }}">
                                                {{ $certificate->status == 'active' ? 'نشط' : 'منتهي' }}
                                            </span>
                                        </td>
                                        <td>
                                            @can('employee-certificate-show')
                                            <a href="{{ route('admin.employee-certificates.show', $certificate->id) }}" class="btn btn-sm btn-primary" title="عرض">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @endcan
                                            @can('employee-certificate-edit')
                                            <a href="{{ route('admin.employee-certificates.edit', $certificate->id) }}" class="btn btn-sm btn-warning" title="تعديل">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @endcan
                                            @can('employee-certificate-delete')
                                            <button type="button" class="btn btn-sm btn-danger delete-btn" data-id="{{ $certificate->id }}" title="حذف">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            @endcan
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">لا توجد شهادات</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $certificates->withQueryString()->links() }}
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
                        <p>هل أنت متأكد من حذف هذه الشهادة؟</p>
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
            const deleteUrl = '{{ url("admin/employee-certificates") }}/' + id;
            document.getElementById('deleteForm').action = deleteUrl;
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        });
    });
</script>
@stop


