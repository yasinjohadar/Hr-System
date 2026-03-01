@extends('admin.layouts.master')

@section('page-title')
    تفاصيل الوظيفة الشاغرة
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">تفاصيل الوظيفة: {{ $vacancy->title_ar ?? $vacancy->title }}</h5>
                </div>
                <div>
                    <a href="{{ route('admin.job-vacancies.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-right me-2"></i>العودة للقائمة
                    </a>
                    @can('job-vacancy-edit')
                    <a href="{{ route('admin.job-vacancies.edit', $vacancy->id) }}" class="btn btn-primary">
                        <i class="fas fa-edit me-2"></i>تعديل
                    </a>
                    @endcan
                </div>
            </div>

            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">معلومات الوظيفة الشاغرة</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">عنوان الوظيفة:</label>
                                    <p class="form-control-plaintext">
                                        <strong>{{ $vacancy->title_ar ?? $vacancy->title }}</strong>
                                        @if ($vacancy->title_ar && $vacancy->title)
                                            <br><small class="text-muted">{{ $vacancy->title }}</small>
                                        @endif
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">كود الوظيفة:</label>
                                    <p class="form-control-plaintext">
                                        <span class="badge bg-info">{{ $vacancy->code }}</span>
                                    </p>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">القسم:</label>
                                    <p class="form-control-plaintext">
                                        @if ($vacancy->department)
                                            <span class="badge bg-primary">{{ $vacancy->department->name }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </p>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">المنصب:</label>
                                    <p class="form-control-plaintext">
                                        @if ($vacancy->position)
                                            <span class="badge bg-secondary">{{ $vacancy->position->title }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </p>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">الحالة:</label>
                                    <p class="form-control-plaintext">
                                        <span class="badge bg-{{ $vacancy->status == 'published' ? 'success' : ($vacancy->status == 'closed' ? 'danger' : 'secondary') }}">
                                            {{ $vacancy->status_name_ar }}
                                        </span>
                                    </p>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">نوع التوظيف:</label>
                                    <p class="form-control-plaintext">{{ $vacancy->employment_type_ar }}</p>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">عدد المناصب:</label>
                                    <p class="form-control-plaintext">
                                        <span class="badge bg-success">{{ $vacancy->number_of_positions }}</span>
                                    </p>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">المتقدمون:</label>
                                    <p class="form-control-plaintext">
                                        <span class="badge bg-warning">{{ $vacancy->applications_count ?? 0 }}</span>
                                    </p>
                                </div>
                                @if ($vacancy->min_salary || $vacancy->max_salary)
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">نطاق الراتب:</label>
                                    <p class="form-control-plaintext">
                                        @if ($vacancy->min_salary && $vacancy->max_salary)
                                            {{ number_format($vacancy->min_salary, 2) }} - {{ number_format($vacancy->max_salary, 2) }}
                                        @elseif ($vacancy->min_salary)
                                            من {{ number_format($vacancy->min_salary, 2) }}
                                        @elseif ($vacancy->max_salary)
                                            حتى {{ number_format($vacancy->max_salary, 2) }}
                                        @endif
                                        @if ($vacancy->currency)
                                            {{ $vacancy->currency->symbol_ar ?? $vacancy->currency->symbol }}
                                        @endif
                                    </p>
                                </div>
                                @endif
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">تاريخ النشر:</label>
                                    <p class="form-control-plaintext">{{ $vacancy->posted_date->format('Y-m-d') }}</p>
                                </div>
                                @if ($vacancy->closing_date)
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">تاريخ الإغلاق:</label>
                                    <p class="form-control-plaintext">{{ $vacancy->closing_date->format('Y-m-d') }}</p>
                                </div>
                                @endif
                                @if ($vacancy->description || $vacancy->description_ar)
                                <div class="col-12 mb-3">
                                    <label class="form-label fw-bold">الوصف:</label>
                                    <p class="form-control-plaintext">{{ $vacancy->description_ar ?? $vacancy->description }}</p>
                                </div>
                                @endif
                                @if ($vacancy->requirements)
                                <div class="col-12 mb-3">
                                    <label class="form-label fw-bold">المتطلبات:</label>
                                    <p class="form-control-plaintext">{{ $vacancy->requirements }}</p>
                                </div>
                                @endif
                                @if ($vacancy->responsibilities)
                                <div class="col-12 mb-3">
                                    <label class="form-label fw-bold">المسؤوليات:</label>
                                    <p class="form-control-plaintext">{{ $vacancy->responsibilities }}</p>
                                </div>
                                @endif
                                @if ($vacancy->benefits)
                                <div class="col-12 mb-3">
                                    <label class="form-label fw-bold">المزايا:</label>
                                    <p class="form-control-plaintext">{{ $vacancy->benefits }}</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if ($vacancy->applications->count() > 0)
                    <div class="card mt-3">
                        <div class="card-header">
                            <h5 class="card-title mb-0">طلبات التوظيف ({{ $vacancy->applications->count() }})</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>المرشح</th>
                                            <th>تاريخ التقديم</th>
                                            <th>الحالة</th>
                                            <th>العمليات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($vacancy->applications as $application)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $application->candidate->full_name }}</td>
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


