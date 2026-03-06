@extends('admin.layouts.master')

@section('page-title')
    عرض العقد
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="page-header d-flex justify-content-between align-items-center my-4">
                <h5 class="page-title mb-0">عرض العقد</h5>
                <div>
                    @if(in_array($contract->status, ['active', 'expired']))
                        <a href="{{ route('admin.contracts.renew', $contract) }}" class="btn btn-success btn-sm me-1">تجديد العقد</a>
                    @endif
                    <a href="{{ route('admin.contracts.edit', $contract) }}" class="btn btn-warning btn-sm me-1">تعديل</a>
                    <a href="{{ route('admin.contracts.index') }}" class="btn btn-secondary btn-sm">العودة للقائمة</a>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">عقد — {{ $contract->employee->full_name ?? $contract->employee->employee_code }}</h4>
                    <div class="mt-2">
                        <span class="badge {{ $contract->status === 'active' ? 'bg-success' : ($contract->status === 'expired' ? 'bg-secondary' : 'bg-info') }}">
                            {{ $contract->status_label }}
                        </span>
                        <span class="badge bg-primary">{{ $contract->contract_type_label }}</span>
                        @if($contract->days_remaining !== null && $contract->days_remaining >= 0 && $contract->status === 'active')
                            <span class="badge bg-warning text-dark">متبقي {{ $contract->days_remaining }} يوم</span>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">الموظف</label>
                            <p class="form-control-plaintext">
                                <a href="{{ route('admin.employees.show', $contract->employee_id) }}">{{ $contract->employee->full_name ?? $contract->employee->employee_code }}</a>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">نوع العقد</label>
                            <p class="form-control-plaintext">{{ $contract->contract_type_label }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">تاريخ البداية</label>
                            <p class="form-control-plaintext">{{ $contract->start_date->format('Y-m-d') }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">تاريخ النهاية</label>
                            <p class="form-control-plaintext">{{ $contract->end_date->format('Y-m-d') }}</p>
                        </div>
                        @if($contract->creator)
                            <div class="col-md-6">
                                <label class="form-label fw-bold">أنشأه</label>
                                <p class="form-control-plaintext">{{ $contract->creator->name }}</p>
                            </div>
                        @endif
                    </div>
                    @if($contract->notes)
                        <div class="mb-3">
                            <label class="form-label fw-bold">ملاحظات</label>
                            <p class="form-control-plaintext">{{ $contract->notes }}</p>
                        </div>
                    @endif
                    @if($contract->document_path)
                        <div>
                            <label class="form-label fw-bold">مرفق المستند</label>
                            <p class="form-control-plaintext">
                                <a href="{{ asset('storage/' . $contract->document_path) }}" target="_blank" class="btn btn-outline-primary btn-sm">
                                    <i class="fa-solid fa-file-pdf me-1"></i>عرض/تحميل المستند
                                </a>
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
@stop
