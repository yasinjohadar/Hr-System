@extends('admin.layouts.master')

@section('page-title')
    تصنيفات المصروفات
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">تصنيفات المصروفات</h5>
                </div>
                <div>
                    @can('expense-category-create')
                    <a href="{{ route('admin.expense-categories.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>إضافة تصنيف جديد
                    </a>
                    @endcan
                </div>
            </div>

            <!-- فلترة -->
            <div class="card mb-3">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.expense-categories.index') }}" class="row g-3">
                        <div class="col-md-4">
                            <input type="text" name="search" class="form-control" placeholder="بحث..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3">
                            <select name="is_active" class="form-select">
                                <option value="">كل الحالات</option>
                                <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>نشط</option>
                                <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>غير نشط</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary w-100">تطبيق الفلترة</button>
                        </div>
                        <div class="col-md-2">
                            <a href="{{ route('admin.expense-categories.index') }}" class="btn btn-secondary w-100">مسح</a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- جدول التصنيفات -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">قائمة التصنيفات ({{ $categories->total() }})</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>الاسم</th>
                                    <th>الكود</th>
                                    <th>الحد الأقصى</th>
                                    <th>مستويات الموافقة</th>
                                    <th>يتطلب إيصال</th>
                                    <th>عدد الطلبات</th>
                                    <th>الحالة</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($categories as $category)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <strong>{{ $category->name_ar ?? $category->name }}</strong>
                                            @if ($category->name_ar && $category->name)
                                                <br><small class="text-muted">{{ $category->name }}</small>
                                            @endif
                                        </td>
                                        <td>{{ $category->code ?? '-' }}</td>
                                        <td>{{ $category->max_amount ? number_format($category->max_amount, 2) . ' ر.س' : 'غير محدد' }}</td>
                                        <td>{{ $category->approval_levels }}</td>
                                        <td>
                                            <span class="badge bg-{{ $category->requires_receipt ? 'success' : 'secondary' }}">
                                                {{ $category->requires_receipt ? 'نعم' : 'لا' }}
                                            </span>
                                        </td>
                                        <td>{{ $category->expense_requests_count }}</td>
                                        <td>
                                            <span class="badge bg-{{ $category->is_active ? 'success' : 'danger' }}">
                                                {{ $category->is_active ? 'نشط' : 'غير نشط' }}
                                            </span>
                                        </td>
                                        <td>
                                            @can('expense-category-show')
                                            <a href="{{ route('admin.expense-categories.show', $category->id) }}" class="btn btn-sm btn-primary" title="عرض">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @endcan
                                            @can('expense-category-edit')
                                            <a href="{{ route('admin.expense-categories.edit', $category->id) }}" class="btn btn-sm btn-info" title="تعديل">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @endcan
                                            @can('expense-category-delete')
                                            <button type="button" class="btn btn-sm btn-danger delete-btn" data-id="{{ $category->id }}" title="حذف">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            @endcan
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center">لا توجد تصنيفات</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $categories->withQueryString()->links() }}
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
                        <p>هل أنت متأكد من حذف هذا التصنيف؟</p>
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
            const deleteUrl = '{{ url("admin/expense-categories") }}/' + id;
            document.getElementById('deleteForm').action = deleteUrl;
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        });
    });
</script>
@stop

