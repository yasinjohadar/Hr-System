@extends('admin.layouts.master')

@section('page-title')
    إدارة المستندات
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">إدارة المستندات</h5>
                </div>
                <div>
                    @can('employee-document-create')
                    <a href="{{ route('admin.employee-documents.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>إضافة مستند جديد
                    </a>
                    @endcan
                </div>
            </div>

            <!-- فلترة -->
            <div class="card mb-3">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.employee-documents.index') }}" class="row g-3">
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
                            <select name="document_type" class="form-select">
                                <option value="">كل الأنواع</option>
                                <option value="contract" {{ request('document_type') == 'contract' ? 'selected' : '' }}>عقد عمل</option>
                                <option value="certificate" {{ request('document_type') == 'certificate' ? 'selected' : '' }}>شهادة</option>
                                <option value="visa" {{ request('document_type') == 'visa' ? 'selected' : '' }}>تأشيرة</option>
                                <option value="id" {{ request('document_type') == 'id' ? 'selected' : '' }}>هوية</option>
                                <option value="passport" {{ request('document_type') == 'passport' ? 'selected' : '' }}>جواز سفر</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="status" class="form-select">
                                <option value="">كل الحالات</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>نشط</option>
                                <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>منتهي</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary w-100">تطبيق الفلترة</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- جدول المستندات -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">قائمة المستندات ({{ $documents->total() }})</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>الموظف</th>
                                    <th>نوع المستند</th>
                                    <th>العنوان</th>
                                    <th>تاريخ الإصدار</th>
                                    <th>تاريخ الانتهاء</th>
                                    <th>الحالة</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($documents as $document)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td><strong>{{ $document->employee->full_name }}</strong></td>
                                        <td><span class="badge bg-info">{{ $document->document_type_name_ar }}</span></td>
                                        <td>{{ $document->title }}</td>
                                        <td>{{ $document->issue_date ? $document->issue_date->format('Y-m-d') : '-' }}</td>
                                        <td>
                                            @if ($document->expiry_date)
                                                <span class="{{ $document->is_expired ? 'text-danger' : '' }}">
                                                    {{ $document->expiry_date->format('Y-m-d') }}
                                                </span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $document->status == 'active' ? 'success' : ($document->status == 'expired' ? 'danger' : 'warning') }}">
                                                {{ $document->status == 'active' ? 'نشط' : ($document->status == 'expired' ? 'منتهي' : 'قيد الانتظار') }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.employee-documents.download', $document->id) }}" class="btn btn-sm btn-info" title="تحميل">
                                                <i class="fas fa-download"></i>
                                            </a>
                                            @can('employee-document-show')
                                            <a href="{{ route('admin.employee-documents.show', $document->id) }}" class="btn btn-sm btn-primary" title="عرض">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @endcan
                                            @can('employee-document-edit')
                                            <a href="{{ route('admin.employee-documents.edit', $document->id) }}" class="btn btn-sm btn-warning" title="تعديل">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @endcan
                                            @can('employee-document-delete')
                                            <button type="button" class="btn btn-sm btn-danger delete-btn" data-id="{{ $document->id }}" title="حذف">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            @endcan
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">لا توجد مستندات</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $documents->withQueryString()->links() }}
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
                        <p>هل أنت متأكد من حذف هذا المستند؟</p>
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
            const deleteUrl = '{{ url("admin/employee-documents") }}/' + id;
            document.getElementById('deleteForm').action = deleteUrl;
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        });
    });
</script>
@stop


