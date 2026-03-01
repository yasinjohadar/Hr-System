@extends('admin.layouts.master')

@section('page-title')
    تفاصيل ميزة الموظف
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">تفاصيل ميزة الموظف</h5>
                </div>
                <div>
                    <a href="{{ route('admin.employee-benefits.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-right me-2"></i>العودة للقائمة
                    </a>
                    @can('employee-benefit-edit')
                    <a href="{{ route('admin.employee-benefits.edit', $employeeBenefit->id) }}" class="btn btn-primary">
                        <i class="fas fa-edit me-2"></i>تعديل
                    </a>
                    @endcan
                </div>
            </div>

            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">معلومات ميزة الموظف</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">الموظف:</label>
                                    <p class="form-control-plaintext"><strong>{{ $employeeBenefit->employee->full_name }}</strong></p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">نوع الميزة:</label>
                                    <p class="form-control-plaintext"><strong>{{ $employeeBenefit->benefitType->name_ar ?? $employeeBenefit->benefitType->name }}</strong></p>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">القيمة:</label>
                                    <p class="form-control-plaintext">
                                        @if ($employeeBenefit->value)
                                            {{ number_format($employeeBenefit->value, 2) }}
                                            @if ($employeeBenefit->currency)
                                                {{ $employeeBenefit->currency->symbol_ar ?? $employeeBenefit->currency->symbol }}
                                            @endif
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </p>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">تاريخ البدء:</label>
                                    <p class="form-control-plaintext">{{ $employeeBenefit->start_date->format('Y-m-d') }}</p>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">تاريخ الانتهاء:</label>
                                    <p class="form-control-plaintext">{{ $employeeBenefit->end_date ? $employeeBenefit->end_date->format('Y-m-d') : 'دائم' }}</p>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">الحالة:</label>
                                    <p class="form-control-plaintext">
                                        <span class="badge bg-{{ $employeeBenefit->status == 'active' ? 'success' : ($employeeBenefit->status == 'expired' ? 'danger' : 'warning') }}">
                                            {{ $employeeBenefit->status_name_ar }}
                                        </span>
                                    </p>
                                </div>
                                @if ($employeeBenefit->notes)
                                <div class="col-12 mb-3">
                                    <label class="form-label fw-bold">ملاحظات:</label>
                                    <p class="form-control-plaintext">{{ $employeeBenefit->notes }}</p>
                                </div>
                                @endif
                                @if ($employeeBenefit->document_path)
                                <div class="col-12 mb-3">
                                    <label class="form-label fw-bold">المستند المرفق:</label>
                                    <p class="form-control-plaintext">
                                        <a href="{{ Storage::url($employeeBenefit->document_path) }}" target="_blank" class="btn btn-info btn-sm">
                                            <i class="fas fa-download me-2"></i>تحميل المستند
                                        </a>
                                    </p>
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


