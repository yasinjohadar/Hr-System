@extends('admin.layouts.master')

@section('page-title')
    صيانة الأصول
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">صيانة الأصول</h5>
                </div>
                <div>
                    @can('asset-maintenance-create')
                    <a href="{{ route('admin.asset-maintenances.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>جدولة صيانة جديدة
                    </a>
                    @endcan
                </div>
            </div>

            <!-- فلترة -->
            <div class="card mb-3">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.asset-maintenances.index') }}" class="row g-3">
                        <div class="col-md-3">
                            <select name="asset_id" class="form-select">
                                <option value="">كل الأصول</option>
                                @foreach ($assets as $asset)
                                    <option value="{{ $asset->id }}" {{ request('asset_id') == $asset->id ? 'selected' : '' }}>
                                        {{ $asset->asset_code }} - {{ $asset->name_ar ?? $asset->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="status" class="form-select">
                                <option value="">كل الحالات</option>
                                <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>مجدولة</option>
                                <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>قيد التنفيذ</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>مكتملة</option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>ملغاة</option>
                                <option value="postponed" {{ request('status') == 'postponed' ? 'selected' : '' }}>مؤجلة</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="maintenance_type" class="form-select">
                                <option value="">كل الأنواع</option>
                                <option value="preventive" {{ request('maintenance_type') == 'preventive' ? 'selected' : '' }}>وقائية</option>
                                <option value="corrective" {{ request('maintenance_type') == 'corrective' ? 'selected' : '' }}>تصحيحية</option>
                                <option value="upgrade" {{ request('maintenance_type') == 'upgrade' ? 'selected' : '' }}>ترقية</option>
                                <option value="cleaning" {{ request('maintenance_type') == 'cleaning' ? 'selected' : '' }}>تنظيف</option>
                                <option value="inspection" {{ request('maintenance_type') == 'inspection' ? 'selected' : '' }}>فحص</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary w-100">تطبيق الفلترة</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- جدول الصيانة -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">قائمة الصيانة ({{ $maintenances->total() }})</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>الأصل</th>
                                    <th>عنوان الصيانة</th>
                                    <th>نوع الصيانة</th>
                                    <th>تاريخ الصيانة</th>
                                    <th>التكلفة</th>
                                    <th>الحالة</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($maintenances as $maintenance)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <a href="{{ route('admin.assets.show', $maintenance->asset_id) }}">
                                                <strong>{{ $maintenance->asset->asset_code }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $maintenance->asset->name_ar ?? $maintenance->asset->name }}</small>
                                            </a>
                                        </td>
                                        <td>{{ $maintenance->title }}</td>
                                        <td><span class="badge bg-info">{{ $maintenance->maintenance_type_name_ar }}</span></td>
                                        <td>
                                            @if ($maintenance->actual_date)
                                                {{ $maintenance->actual_date->format('Y-m-d') }}
                                            @elseif ($maintenance->scheduled_date)
                                                <span class="text-muted">{{ $maintenance->scheduled_date->format('Y-m-d') }} (مجدول)</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>{{ number_format($maintenance->cost, 2) }} ر.س</td>
                                        <td>
                                            <span class="badge bg-{{ $maintenance->status == 'completed' ? 'success' : ($maintenance->status == 'in_progress' ? 'primary' : ($maintenance->status == 'cancelled' ? 'danger' : 'warning')) }}">
                                                {{ $maintenance->status_name_ar }}
                                            </span>
                                        </td>
                                        <td>
                                            @can('asset-maintenance-show')
                                            <a href="{{ route('admin.asset-maintenances.show', $maintenance->id) }}" class="btn btn-sm btn-primary" title="عرض">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @endcan
                                            @can('asset-maintenance-edit')
                                            <a href="{{ route('admin.asset-maintenances.edit', $maintenance->id) }}" class="btn btn-sm btn-warning" title="تعديل">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @endcan
                                            @can('asset-maintenance-delete')
                                            <button type="button" class="btn btn-sm btn-danger delete-btn" data-id="{{ $maintenance->id }}" title="حذف">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            @endcan
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">لا توجد سجلات صيانة</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $maintenances->withQueryString()->links() }}
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
                        <p>هل أنت متأكد من حذف سجل الصيانة هذا؟</p>
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
            const deleteUrl = '{{ url("admin/asset-maintenances") }}/' + id;
            document.getElementById('deleteForm').action = deleteUrl;
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        });
    });
</script>
@stop


