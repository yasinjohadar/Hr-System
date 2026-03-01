@extends('admin.layouts.master')

@section('page-title')
    تفاصيل المرشح
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">تفاصيل المرشح: {{ $candidate->full_name }}</h5>
                </div>
                <div>
                    <a href="{{ route('admin.candidates.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-right me-2"></i>العودة للقائمة
                    </a>
                    @can('candidate-edit')
                    <a href="{{ route('admin.candidates.edit', $candidate->id) }}" class="btn btn-primary">
                        <i class="fas fa-edit me-2"></i>تعديل
                    </a>
                    @endcan
                </div>
            </div>

            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">معلومات المرشح</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">الاسم الكامل:</label>
                                    <p class="form-control-plaintext"><strong>{{ $candidate->full_name }}</strong></p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">كود المرشح:</label>
                                    <p class="form-control-plaintext"><span class="badge bg-info">{{ $candidate->candidate_code }}</span></p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">البريد الإلكتروني:</label>
                                    <p class="form-control-plaintext">{{ $candidate->email }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">الهاتف:</label>
                                    <p class="form-control-plaintext">{{ $candidate->phone }}</p>
                                </div>
                                @if ($candidate->current_position)
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">المنصب الحالي:</label>
                                    <p class="form-control-plaintext">{{ $candidate->current_position }}</p>
                                </div>
                                @endif
                                @if ($candidate->years_of_experience)
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">سنوات الخبرة:</label>
                                    <p class="form-control-plaintext"><span class="badge bg-info">{{ $candidate->years_of_experience }} سنة</span></p>
                                </div>
                                @endif
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">الحالة:</label>
                                    <p class="form-control-plaintext">
                                        <span class="badge bg-{{ $candidate->status == 'hired' ? 'success' : ($candidate->status == 'rejected' ? 'danger' : 'warning') }}">
                                            {{ $candidate->status_name_ar }}
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if ($candidate->applications->count() > 0)
                    <div class="card mt-3">
                        <div class="card-header">
                            <h5 class="card-title mb-0">طلبات التوظيف ({{ $candidate->applications->count() }})</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>الوظيفة</th>
                                            <th>تاريخ التقديم</th>
                                            <th>الحالة</th>
                                            <th>العمليات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($candidate->applications as $application)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $application->jobVacancy->title_ar ?? $application->jobVacancy->title }}</td>
                                                <td>{{ $application->application_date->format('Y-m-d') }}</td>
                                                <td>
                                                    <span class="badge bg-{{ $application->status == 'accepted' ? 'success' : ($application->status == 'rejected' ? 'danger' : 'warning') }}">
                                                        {{ $application->status_name_ar }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="{{ route('admin.job-applications.show', $application->id) }}" class="btn btn-info btn-sm">
                                                        <i class="fa-solid fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@stop


