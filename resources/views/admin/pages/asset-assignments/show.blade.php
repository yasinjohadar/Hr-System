@extends('admin.layouts.master')

@section('page-title')
    تفاصيل التوزيع
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">تفاصيل التوزيع</h5>
                </div>
                <div>
                    <a href="{{ route('admin.asset-assignments.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-right me-2"></i>العودة للقائمة
                    </a>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">معلومات التوزيع</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">الأصل:</label>
                                    <p class="form-control-plaintext">
                                        <a href="{{ route('admin.assets.show', $assignment->asset_id) }}">
                                            <strong>{{ $assignment->asset->asset_code }}</strong> - {{ $assignment->asset->name_ar ?? $assignment->asset->name }}
                                        </a>
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">الموظف:</label>
                                    <p class="form-control-plaintext">
                                        <a href="{{ route('admin.employees.show', $assignment->employee_id) }}">
                                            {{ $assignment->employee->full_name }}
                                        </a>
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">تاريخ التوزيع:</label>
                                    <p class="form-control-plaintext">{{ $assignment->assigned_date->format('Y-m-d') }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">تاريخ الاسترجاع المتوقع:</label>
                                    <p class="form-control-plaintext">{{ $assignment->expected_return_date ? $assignment->expected_return_date->format('Y-m-d') : '-' }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">تاريخ الاسترجاع الفعلي:</label>
                                    <p class="form-control-plaintext">{{ $assignment->actual_return_date ? $assignment->actual_return_date->format('Y-m-d') : '-' }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">حالة التوزيع:</label>
                                    <p class="form-control-plaintext">
                                        <span class="badge bg-{{ $assignment->assignment_status == 'active' ? 'success' : ($assignment->assignment_status == 'returned' ? 'info' : 'danger') }}">
                                            {{ $assignment->assignment_status_name_ar }}
                                        </span>
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">حالة الأصل عند التوزيع:</label>
                                    <p class="form-control-plaintext">
                                        <span class="badge bg-info">{{ $assignment->condition_on_assignment_name_ar }}</span>
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">حالة الأصل عند الاسترجاع:</label>
                                    <p class="form-control-plaintext">
                                        @if ($assignment->condition_on_return)
                                            <span class="badge bg-{{ $assignment->condition_on_return == 'damaged' ? 'danger' : 'info' }}">
                                                {{ $assignment->condition_on_return_name_ar }}
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </p>
                                </div>
                                @if ($assignment->assigner)
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">من وزع:</label>
                                    <p class="form-control-plaintext">{{ $assignment->assigner->name }}</p>
                                </div>
                                @endif
                                @if ($assignment->returner)
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">من استرجع:</label>
                                    <p class="form-control-plaintext">{{ $assignment->returner->name }}</p>
                                </div>
                                @endif
                                @if ($assignment->assignment_notes)
                                <div class="col-12 mb-3">
                                    <label class="form-label fw-bold">ملاحظات التوزيع:</label>
                                    <p class="form-control-plaintext">{{ $assignment->assignment_notes }}</p>
                                </div>
                                @endif
                                @if ($assignment->return_notes)
                                <div class="col-12 mb-3">
                                    <label class="form-label fw-bold">ملاحظات الاسترجاع:</label>
                                    <p class="form-control-plaintext">{{ $assignment->return_notes }}</p>
                                </div>
                                @endif
                            </div>
                            <div class="mt-3">
                                @if ($assignment->assignment_status == 'active')
                                <a href="{{ route('admin.asset-assignments.return-form', $assignment->id) }}" class="btn btn-warning">
                                    <i class="fas fa-undo me-2"></i>استرجاع الأصل
                                </a>
                                @endif
                                @can('asset-assignment-edit')
                                <a href="{{ route('admin.asset-assignments.edit', $assignment->id) }}" class="btn btn-info">
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

