@extends('admin.layouts.master')

@section('page-title')
    تفاصيل الصيانة
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">تفاصيل الصيانة</h5>
                </div>
                <div>
                    <a href="{{ route('admin.asset-maintenances.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-right me-2"></i>العودة للقائمة
                    </a>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">{{ $maintenance->title }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">الأصل:</label>
                                    <p class="form-control-plaintext">
                                        <a href="{{ route('admin.assets.show', $maintenance->asset_id) }}">
                                            <strong>{{ $maintenance->asset->asset_code }}</strong> - {{ $maintenance->asset->name_ar ?? $maintenance->asset->name }}
                                        </a>
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">نوع الصيانة:</label>
                                    <p class="form-control-plaintext">
                                        <span class="badge bg-info">{{ $maintenance->maintenance_type_name_ar }}</span>
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">الحالة:</label>
                                    <p class="form-control-plaintext">
                                        <span class="badge bg-{{ $maintenance->status == 'completed' ? 'success' : ($maintenance->status == 'in_progress' ? 'primary' : ($maintenance->status == 'cancelled' ? 'danger' : 'warning')) }}">
                                            {{ $maintenance->status_name_ar }}
                                        </span>
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">تاريخ الصيانة المجدول:</label>
                                    <p class="form-control-plaintext">{{ $maintenance->scheduled_date ? $maintenance->scheduled_date->format('Y-m-d') : '-' }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">تاريخ الصيانة الفعلي:</label>
                                    <p class="form-control-plaintext">{{ $maintenance->actual_date ? $maintenance->actual_date->format('Y-m-d') : '-' }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">التكلفة:</label>
                                    <p class="form-control-plaintext">{{ number_format($maintenance->cost, 2) }} ر.س</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">تاريخ الصيانة القادمة:</label>
                                    <p class="form-control-plaintext">{{ $maintenance->next_maintenance_date ? $maintenance->next_maintenance_date->format('Y-m-d') : '-' }}</p>
                                </div>
                                @if ($maintenance->service_provider)
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">مزود الخدمة:</label>
                                    <p class="form-control-plaintext">{{ $maintenance->service_provider }}</p>
                                </div>
                                @endif
                                @if ($maintenance->description)
                                <div class="col-12 mb-3">
                                    <label class="form-label fw-bold">الوصف:</label>
                                    <p class="form-control-plaintext">{{ $maintenance->description }}</p>
                                </div>
                                @endif
                                @if ($maintenance->notes)
                                <div class="col-12 mb-3">
                                    <label class="form-label fw-bold">ملاحظات:</label>
                                    <p class="form-control-plaintext">{{ $maintenance->notes }}</p>
                                </div>
                                @endif
                            </div>
                            <div class="mt-3">
                                @can('asset-maintenance-edit')
                                <a href="{{ route('admin.asset-maintenances.edit', $maintenance->id) }}" class="btn btn-warning">
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


