@extends('admin.layouts.master')

@section('page-title')
    تفاصيل سجل التدريب
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">تفاصيل سجل التدريب</h5>
                </div>
                <div>
                    <a href="{{ route('admin.training-records.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-right me-2"></i>العودة للقائمة
                    </a>
                    @can('training-record-edit')
                    <a href="{{ route('admin.training-records.edit', $record->id) }}" class="btn btn-primary">
                        <i class="fas fa-edit me-2"></i>تعديل
                    </a>
                    @endcan
                </div>
            </div>

            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">معلومات سجل التدريب</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">الدورة التدريبية:</label>
                                    <p class="form-control-plaintext">
                                        <strong>{{ $record->training->title_ar ?? $record->training->title }}</strong>
                                        <br><small class="text-muted">{{ $record->training->code }}</small>
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">الموظف:</label>
                                    <p class="form-control-plaintext">
                                        <strong>{{ $record->employee->full_name ?? $record->employee->first_name . ' ' . $record->employee->last_name }}</strong>
                                        <br><small class="text-muted">{{ $record->employee->employee_code ?? '' }}</small>
                                    </p>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">الحالة:</label>
                                    <p class="form-control-plaintext">
                                        @if ($record->status == 'completed')
                                            <span class="badge bg-success">مكتمل</span>
                                        @elseif ($record->status == 'attending')
                                            <span class="badge bg-primary">يحضر</span>
                                        @elseif ($record->status == 'registered')
                                            <span class="badge bg-info">مسجل</span>
                                        @elseif ($record->status == 'failed')
                                            <span class="badge bg-danger">فاشل</span>
                                        @else
                                            <span class="badge bg-secondary">ملغي</span>
                                        @endif
                                    </p>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">تاريخ التسجيل:</label>
                                    <p class="form-control-plaintext">
                                        {{ $record->registration_date ? $record->registration_date->format('Y-m-d') : '-' }}
                                    </p>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">تاريخ الإتمام:</label>
                                    <p class="form-control-plaintext">
                                        {{ $record->completion_date ? $record->completion_date->format('Y-m-d') : '-' }}
                                    </p>
                                </div>
                                @if ($record->score)
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">النتيجة/الدرجة:</label>
                                    <p class="form-control-plaintext">
                                        <span class="badge bg-{{ $record->score >= 80 ? 'success' : ($record->score >= 60 ? 'warning' : 'danger') }} fs-6">
                                            {{ number_format($record->score, 2) }}%
                                        </span>
                                        <br><strong>{{ $record->score_rating }}</strong>
                                    </p>
                                </div>
                                @endif
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">الشهادة:</label>
                                    <p class="form-control-plaintext">
                                        @if ($record->certificate_issued)
                                            <span class="badge bg-success">تم الإصدار</span>
                                            @if ($record->certificate_date)
                                                <br><small class="text-muted">تاريخ الإصدار: {{ $record->certificate_date->format('Y-m-d') }}</small>
                                            @endif
                                        @else
                                            <span class="badge bg-secondary">لم يتم الإصدار</span>
                                        @endif
                                    </p>
                                </div>
                                @if ($record->feedback)
                                <div class="col-12 mb-3">
                                    <label class="form-label fw-bold">ملاحظات الموظف:</label>
                                    <p class="form-control-plaintext">{{ $record->feedback }}</p>
                                </div>
                                @endif
                                @if ($record->evaluation)
                                <div class="col-12 mb-3">
                                    <label class="form-label fw-bold">تقييم المدرب:</label>
                                    <p class="form-control-plaintext">{{ $record->evaluation }}</p>
                                </div>
                                @endif
                                @if ($record->notes)
                                <div class="col-12 mb-3">
                                    <label class="form-label fw-bold">ملاحظات إضافية:</label>
                                    <p class="form-control-plaintext">{{ $record->notes }}</p>
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


