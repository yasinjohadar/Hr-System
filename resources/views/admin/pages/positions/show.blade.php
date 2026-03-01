@extends('admin.layouts.master')

@section('page-title')
    تفاصيل المنصب
@stop

@section('css')
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">تفاصيل المنصب: {{ $position->title }}</h5>
                </div>
                <div>
                    <a href="{{ route('admin.positions.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-right me-2"></i>العودة للقائمة
                    </a>
                    @can('position-edit')
                    <a href="{{ route('admin.positions.edit', $position->id) }}" class="btn btn-primary">
                        <i class="fas fa-edit me-2"></i>تعديل
                    </a>
                    @endcan
                </div>
            </div>

            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">معلومات المنصب</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">اسم المنصب:</label>
                                    <p class="form-control-plaintext">{{ $position->title }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">كود المنصب:</label>
                                    <p class="form-control-plaintext">
                                        @if ($position->code)
                                            <span class="badge bg-info">{{ $position->code }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">القسم:</label>
                                    <p class="form-control-plaintext">
                                        @if ($position->department)
                                            <span class="badge bg-primary">{{ $position->department->name }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">عدد الموظفين:</label>
                                    <p class="form-control-plaintext">
                                        <span class="badge bg-success">{{ $position->employees_count ?? 0 }}</span>
                                    </p>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">الراتب الأدنى:</label>
                                    <p class="form-control-plaintext">
                                        @if ($position->min_salary)
                                            {{ number_format($position->min_salary, 2) }} ر.س
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </p>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">الراتب الأقصى:</label>
                                    <p class="form-control-plaintext">
                                        @if ($position->max_salary)
                                            {{ number_format($position->max_salary, 2) }} ر.س
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </p>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">الحالة:</label>
                                    <p class="form-control-plaintext">
                                        @if ($position->is_active)
                                            <span class="badge bg-success">نشط</span>
                                        @else
                                            <span class="badge bg-danger">غير نشط</span>
                                        @endif
                                    </p>
                                </div>
                                @if ($position->description)
                                <div class="col-12 mb-3">
                                    <label class="form-label fw-bold">الوصف:</label>
                                    <p class="form-control-plaintext">{{ $position->description }}</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
@stop

