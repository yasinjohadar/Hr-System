@extends('admin.layouts.master')

@section('page-title')
    إدارة الأصول
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">إدارة الأصول</h5>
                </div>
                <div>
                    @can('asset-create')
                    <a href="{{ route('admin.assets.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>إضافة أصل جديد
                    </a>
                    @endcan
                </div>
            </div>

            <!-- فلترة -->
            <div class="card mb-3">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.assets.index') }}" class="row g-3">
                        <div class="col-md-3">
                            <input type="text" name="query" class="form-control" placeholder="بحث..." value="{{ request('query') }}">
                        </div>
                        <div class="col-md-2">
                            <select name="status" class="form-select">
                                <option value="">كل الحالات</option>
                                <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>متاح</option>
                                <option value="assigned" {{ request('status') == 'assigned' ? 'selected' : '' }}>موزع</option>
                                <option value="maintenance" {{ request('status') == 'maintenance' ? 'selected' : '' }}>قيد الصيانة</option>
                                <option value="damaged" {{ request('status') == 'damaged' ? 'selected' : '' }}>معطل</option>
                                <option value="lost" {{ request('status') == 'lost' ? 'selected' : '' }}>مفقود</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="category" class="form-select">
                                <option value="">كل الفئات</option>
                                <option value="technical" {{ request('category') == 'technical' ? 'selected' : '' }}>تقني</option>
                                <option value="office" {{ request('category') == 'office' ? 'selected' : '' }}>مكتبي</option>
                                <option value="mobile" {{ request('category') == 'mobile' ? 'selected' : '' }}>متنقل</option>
                                <option value="other" {{ request('category') == 'other' ? 'selected' : '' }}>أخرى</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="branch_id" class="form-select">
                                <option value="">كل الفروع</option>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                        {{ $branch->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary w-100">تطبيق الفلترة</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- جدول الأصول -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">قائمة الأصول ({{ $assets->total() }})</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>كود الأصل</th>
                                    <th>الاسم</th>
                                    <th>الفئة</th>
                                    <th>الشركة/الموديل</th>
                                    <th>الرقم التسلسلي</th>
                                    <th>الموقع</th>
                                    <th>الحالة</th>
                                    <th>الموظف</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($assets as $asset)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td><strong>{{ $asset->asset_code }}</strong></td>
                                        <td>{{ $asset->name_ar ?? $asset->name }}</td>
                                        <td><span class="badge bg-info">{{ $asset->category_name_ar }}</span></td>
                                        <td>
                                            @if ($asset->manufacturer || $asset->model)
                                                {{ $asset->manufacturer }} {{ $asset->model }}
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>{{ $asset->serial_number ?? '-' }}</td>
                                        <td>
                                            @if ($asset->branch)
                                                {{ $asset->branch->name }}
                                            @elseif ($asset->department)
                                                {{ $asset->department->name }}
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $asset->status == 'available' ? 'success' : ($asset->status == 'assigned' ? 'primary' : ($asset->status == 'maintenance' ? 'warning' : 'danger')) }}">
                                                {{ $asset->status_name_ar }}
                                            </span>
                                        </td>
                                        <td>
                                            @if ($asset->currentEmployee())
                                                <a href="{{ route('admin.employees.show', $asset->currentEmployee()->id) }}">
                                                    {{ $asset->currentEmployee()->full_name }}
                                                </a>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @can('asset-show')
                                            <a href="{{ route('admin.assets.show', $asset->id) }}" class="btn btn-sm btn-primary" title="عرض">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @endcan
                                            @can('asset-edit')
                                            <a href="{{ route('admin.assets.edit', $asset->id) }}" class="btn btn-sm btn-warning" title="تعديل">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @endcan
                                            @can('asset-delete')
                                            <button type="button" class="btn btn-sm btn-danger delete-btn" data-id="{{ $asset->id }}" title="حذف">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            @endcan
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center">لا توجد أصول</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $assets->withQueryString()->links() }}
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
                        <p>هل أنت متأكد من حذف هذا الأصل؟</p>
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
            // إنشاء URL بشكل صحيح
            const deleteUrl = '{{ url("admin/assets") }}/' + id;
            document.getElementById('deleteForm').action = deleteUrl;
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        });
    });
</script>
@stop


