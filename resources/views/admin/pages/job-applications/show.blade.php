@extends('admin.layouts.master')

@section('page-title')
    تفاصيل طلب التوظيف
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">تفاصيل طلب التوظيف</h5>
                </div>
                <div>
                    <a href="{{ route('admin.job-applications.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-right me-2"></i>العودة للقائمة
                    </a>
                    @can('job-application-edit')
                    <a href="{{ route('admin.job-applications.edit', $application->id) }}" class="btn btn-primary">
                        <i class="fas fa-edit me-2"></i>تعديل
                    </a>
                    @endcan
                </div>
            </div>

            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">معلومات طلب التوظيف</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">المرشح:</label>
                                    <p class="form-control-plaintext"><strong>{{ $application->candidate->full_name }}</strong></p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">الوظيفة:</label>
                                    <p class="form-control-plaintext"><strong>{{ $application->jobVacancy->title_ar ?? $application->jobVacancy->title }}</strong></p>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">تاريخ التقديم:</label>
                                    <p class="form-control-plaintext">{{ $application->application_date->format('Y-m-d') }}</p>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">المصدر:</label>
                                    <p class="form-control-plaintext"><span class="badge bg-info">{{ $application->source_name_ar }}</span></p>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">الحالة:</label>
                                    <p class="form-control-plaintext">
                                        <span class="badge bg-{{ $application->status == 'accepted' ? 'success' : ($application->status == 'rejected' ? 'danger' : 'warning') }}">
                                            {{ $application->status_name_ar }}
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop


