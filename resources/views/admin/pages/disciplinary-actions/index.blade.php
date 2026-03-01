@extends('admin.layouts.master')

@section('page-title')
    الإجراءات التأديبية
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">الإجراءات التأديبية</h5>
                </div>
                <div>
                    @can('disciplinary-action-create')
                    <a href="{{ route('admin.disciplinary-actions.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>إضافة إجراء جديد
                    </a>
                    @endcan
                </div>
            </div>

            <!-- فلترة -->
            <div class="card mb-3">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.disciplinary-actions.index') }}" class="row g-3">
                        <div class="col-md-4">
                            <input type="text" name="search" class="form-control" placeholder="بحث..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3">
                            <select name="action_type" class="form-select">
                                <option value="">كل الأنواع</option>
                                <option value="verbal_warning" {{ request('action_type') == 'verbal_warning' ? 'selected' : '' }}>تحذير شفهي</option>
                                <option value="written_warning" {{ request('action_type') == 'written_warning' ? 'selected' : '' }}>تحذير كتابي</option>
                                <option value="final_warning" {{ request('action_type') == 'final_warning' ? 'selected' : '' }}>إنذار نهائي</option>
                                <option value="deduction" {{ request('action_type') == 'deduction' ? 'selected' : '' }}>خصم</option>
                                <option value="suspension" {{ request('action_type') == 'suspension' ? 'selected' : '' }}>إيقاف</option>
                                <option value="termination" {{ request('action_type') == 'termination' ? 'selected' : '' }}>إنهاء خدمة</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="is_active" class="form-select">
                                <option value="">كل الحالات</option>
                                <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>نشط</option>
                                <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>غير نشط</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary w-100">تطبيق الفلترة</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- جدول الإجراءات -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">قائمة الإجراءات ({{ $disciplinaryActions->total() }})</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>الاسم</th>
                                    <th>نوع الإجراء</th>
                                    <th>مستوى الخطورة</th>
                                    <th>مبلغ الخصم</th>
                                    <th>أيام الإيقاف</th>
                                    <th>عدد الاستخدامات</th>
                                    <th>الحالة</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($disciplinaryActions as $action)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <strong>{{ $action->name_ar ?? $action->name }}</strong>
                                            @if ($action->name_ar && $action->name)
                                                <br><small class="text-muted">{{ $action->name }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $action->action_type_name_ar }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $action->severity_level >= 4 ? 'danger' : ($action->severity_level >= 3 ? 'warning' : 'info') }}">
                                                {{ $action->severity_level_name_ar }}
                                            </span>
                                        </td>
                                        <td>{{ $action->deduction_amount ? number_format($action->deduction_amount, 2) . ' ر.س' : '-' }}</td>
                                        <td>{{ $action->suspension_days ? $action->suspension_days . ' يوم' : '-' }}</td>
                                        <td>{{ $action->employee_violations_count }}</td>
                                        <td>
                                            <span class="badge bg-{{ $action->is_active ? 'success' : 'danger' }}">
                                                {{ $action->is_active ? 'نشط' : 'غير نشط' }}
                                            </span>
                                        </td>
                                        <td>
                                            @can('disciplinary-action-show')
                                            <a href="{{ route('admin.disciplinary-actions.show', $action->id) }}" class="btn btn-sm btn-primary" title="عرض">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @endcan
                                            @can('disciplinary-action-edit')
                                            <a href="{{ route('admin.disciplinary-actions.edit', $action->id) }}" class="btn btn-sm btn-info" title="تعديل">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @endcan
                                            @can('disciplinary-action-delete')
                                            <button type="button" class="btn btn-sm btn-danger delete-btn" data-id="{{ $action->id }}" title="حذف">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            @endcan
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center">لا توجد إجراءات</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $disciplinaryActions->withQueryString()->links() }}
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
                        <p>هل أنت متأكد من حذف هذا الإجراء؟</p>
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
            const deleteUrl = '{{ url("admin/disciplinary-actions") }}/' + id;
            document.getElementById('deleteForm').action = deleteUrl;
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        });
    });
</script>
@stop

