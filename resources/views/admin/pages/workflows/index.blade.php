@extends('admin.layouts.master')

@section('page-title')
    إدارة سير العمل
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">إدارة سير العمل</h5>
                </div>
                <div>
                    @can('workflow-create')
                    <a href="{{ route('admin.workflows.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>إضافة سير عمل جديد
                    </a>
                    @endcan
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.workflows.index') }}" class="row g-3">
                        <div class="col-md-4">
                            <input type="text" name="search" class="form-control" placeholder="بحث..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3">
                            <select name="type" class="form-select">
                                <option value="">كل الأنواع</option>
                                <option value="leave_request" {{ request('type') == 'leave_request' ? 'selected' : '' }}>طلب إجازة</option>
                                <option value="expense_request" {{ request('type') == 'expense_request' ? 'selected' : '' }}>طلب مصروف</option>
                                <option value="task_approval" {{ request('type') == 'task_approval' ? 'selected' : '' }}>موافقة مهمة</option>
                                <option value="performance_review" {{ request('type') == 'performance_review' ? 'selected' : '' }}>تقييم الأداء</option>
                                <option value="custom" {{ request('type') == 'custom' ? 'selected' : '' }}>مخصص</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="is_active" class="form-select">
                                <option value="">كل الحالات</option>
                                <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>نشط</option>
                                <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>غير نشط</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">تطبيق</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">قائمة سير العمل ({{ $workflows->total() }})</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>الاسم</th>
                                    <th>الكود</th>
                                    <th>النوع</th>
                                    <th>عدد الخطوات</th>
                                    <th>عدد الطلبات</th>
                                    <th>الحالة</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($workflows as $workflow)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td><strong>{{ $workflow->name_ar ?? $workflow->name }}</strong></td>
                                        <td>{{ $workflow->code ?? '-' }}</td>
                                        <td><span class="badge bg-info">{{ $workflow->type_name_ar }}</span></td>
                                        <td>{{ $workflow->steps_count }}</td>
                                        <td>{{ $workflow->instances_count }}</td>
                                        <td>
                                            <span class="badge bg-{{ $workflow->is_active ? 'success' : 'danger' }}">
                                                {{ $workflow->is_active ? 'نشط' : 'غير نشط' }}
                                            </span>
                                        </td>
                                        <td>
                                            @can('workflow-show')
                                            <a href="{{ route('admin.workflows.show', $workflow->id) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @endcan
                                            @can('workflow-edit')
                                            <a href="{{ route('admin.workflows.edit', $workflow->id) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @endcan
                                            @can('workflow-delete')
                                            <button type="button" class="btn btn-sm btn-danger delete-btn" data-id="{{ $workflow->id }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            @endcan
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">لا توجد سير عمل</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $workflows->withQueryString()->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
<script>
    document.querySelectorAll('.delete-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            if (confirm('هل أنت متأكد من حذف سير العمل؟')) {
                const form = document.createElement('form');
                form.method = 'POST';
                const id = this.getAttribute('data-id');
                form.action = '{{ url("admin/workflows") }}/' + id;
                form.innerHTML = '@csrf @method("DELETE")';
                document.body.appendChild(form);
                form.submit();
            }
        });
    });
</script>
@stop

