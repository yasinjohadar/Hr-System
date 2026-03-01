@extends('admin.layouts.master')

@section('page-title')
    تفاصيل الأصل
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">تفاصيل الأصل</h5>
                </div>
                <div>
                    <a href="{{ route('admin.assets.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-right me-2"></i>العودة للقائمة
                    </a>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">{{ $asset->name_ar ?? $asset->name }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">كود الأصل:</label>
                                    <p class="form-control-plaintext">{{ $asset->asset_code }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">الفئة:</label>
                                    <p class="form-control-plaintext">
                                        <span class="badge bg-info">{{ $asset->category_name_ar }}</span>
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">الشركة المصنعة:</label>
                                    <p class="form-control-plaintext">{{ $asset->manufacturer ?? '-' }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">الموديل:</label>
                                    <p class="form-control-plaintext">{{ $asset->model ?? '-' }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">الرقم التسلسلي:</label>
                                    <p class="form-control-plaintext">{{ $asset->serial_number ?? '-' }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">الحالة:</label>
                                    <p class="form-control-plaintext">
                                        <span class="badge bg-{{ $asset->status == 'available' ? 'success' : ($asset->status == 'assigned' ? 'primary' : ($asset->status == 'maintenance' ? 'warning' : 'danger')) }}">
                                            {{ $asset->status_name_ar }}
                                        </span>
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">الموظف الحالي:</label>
                                    <p class="form-control-plaintext">
                                        @if ($asset->currentEmployee())
                                            <a href="{{ route('admin.employees.show', $asset->currentEmployee()->id) }}">
                                                {{ $asset->currentEmployee()->full_name }}
                                            </a>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </p>
                                </div>
                                @if ($asset->description)
                                <div class="col-12 mb-3">
                                    <label class="form-label fw-bold">الوصف:</label>
                                    <p class="form-control-plaintext">{{ $asset->description }}</p>
                                </div>
                                @endif
                            </div>
                            <div class="mt-3">
                                @can('asset-edit')
                                <a href="{{ route('admin.assets.edit', $asset->id) }}" class="btn btn-warning">
                                    <i class="fas fa-edit me-2"></i>تعديل
                                </a>
                                @endcan
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop


