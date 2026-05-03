@extends('admin.layouts.master')

@section('page-title')
    تفاصيل الوظيفة الشاغرة
@stop

@section('css')
    <style>
        .vacancy-show-hero {
            background: linear-gradient(145deg, var(--bs-primary) 0%, rgba(var(--bs-primary-rgb), 0.88) 55%, rgb(15, 76, 129) 100%);
            color: #fff;
            border: none;
        }
        .vacancy-show-hero .text-white-75 { color: rgba(255,255,255,.85) !important; }
    </style>
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">تفاصيل الوظيفة</h5>
                    <p class="text-muted small mb-0">{{ $vacancy->title_ar ?? $vacancy->title }} — {{ $vacancy->code }}</p>
                </div>
                <div class="d-flex flex-wrap gap-2 mt-2 mt-md-0">
                    <a href="{{ route('admin.job-vacancies.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-right me-1"></i>العودة للقائمة
                    </a>
                    @can('job-vacancy-edit')
                        <a href="{{ route('admin.job-vacancies.edit', $vacancy->id) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit me-1"></i>تعديل
                        </a>
                    @endcan
                </div>
            </div>

            <div class="row g-3">
                <div class="col-lg-4">
                    <div class="card vacancy-show-hero shadow-sm h-100">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex align-items-start gap-3 mb-3">
                                <span class="avatar avatar-md bg-white bg-opacity-25 rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width:3rem;height:3rem;">
                                    <i class="fas fa-briefcase fs-4"></i>
                                </span>
                                <div class="min-w-0">
                                    <div class="text-white-75 small mb-1">الوظيفة الشاغرة</div>
                                    <div class="fw-semibold fs-6 text-truncate">{{ $vacancy->title_ar ?? $vacancy->title }}</div>
                                    <div class="small text-white-75 font-monospace">{{ $vacancy->code }}</div>
                                </div>
                            </div>
                            <div class="mb-3 pb-3 border-bottom border-white border-opacity-25">
                                <div class="text-white-75 small mb-1"><i class="fas fa-building me-1"></i>القسم / المنصب</div>
                                <div class="fw-semibold">
                                    {{ $vacancy->department->name ?? '—' }}
                                    @if($vacancy->position) / {{ $vacancy->position->title }} @endif
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="text-white-75 small mb-2">الحالة</div>
                                <span class="badge bg-{{ $vacancy->status == 'published' ? 'success' : ($vacancy->status == 'closed' ? 'danger' : 'secondary') }} fs-14 px-3 py-2">
                                    {{ $vacancy->status_name_ar }}
                                </span>
                            </div>
                            <div class="mt-auto pt-3 border-top border-white border-opacity-25">
                                <div class="text-white-75 small mb-1">المتقدمون</div>
                                <div class="display-6 fw-bold lh-1">{{ $vacancy->applications_count ?? $vacancy->applications->count() ?? 0 }}</div>
                                <div class="small text-white-75 mt-1">متقدم</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-light py-3 border-bottom">
                            <h6 class="mb-0 fw-semibold">
                                <i class="fas fa-circle-info text-primary me-2"></i>تفاصيل الوظيفة
                            </h6>
                            <small class="text-muted">نوع التوظيف والراتب والتواريخ</small>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <tbody>
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle" style="width:35%">
                                                <i class="fas fa-clock text-muted me-2"></i>نوع التوظيف
                                            </th>
                                            <td class="pe-4 py-3 align-middle">
                                                <span class="badge bg-info-subtle text-dark border">{{ $vacancy->employment_type_ar }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-users text-muted me-2"></i>عدد المناصب
                                            </th>
                                            <td class="pe-4 py-3 align-middle fw-semibold">{{ $vacancy->number_of_positions }}</td>
                                        </tr>
                                        @if ($vacancy->min_salary || $vacancy->max_salary)
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-money-bill-wave text-muted me-2"></i>نطاق الراتب
                                            </th>
                                            <td class="pe-4 py-3 align-middle fw-semibold">
                                                @if ($vacancy->min_salary && $vacancy->max_salary)
                                                    {{ number_format($vacancy->min_salary, 2) }} — {{ number_format($vacancy->max_salary, 2) }}
                                                @elseif ($vacancy->min_salary)
                                                    من {{ number_format($vacancy->min_salary, 2) }}
                                                @else
                                                    حتى {{ number_format($vacancy->max_salary, 2) }}
                                                @endif
                                                @if ($vacancy->currency) {{ $vacancy->currency->symbol_ar ?? $vacancy->currency->symbol }} @endif
                                            </td>
                                        </tr>
                                        @endif
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-calendar-plus text-muted me-2"></i>تاريخ النشر
                                            </th>
                                            <td class="pe-4 py-3 align-middle fw-semibold">{{ $vacancy->posted_date->format('Y-m-d') }}</td>
                                        </tr>
                                        @if ($vacancy->closing_date)
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-calendar-xmark text-muted me-2"></i>تاريخ الإغلاق
                                            </th>
                                            <td class="pe-4 py-3 align-middle fw-semibold">{{ $vacancy->closing_date->format('Y-m-d') }}</td>
                                        </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if ($vacancy->description || $vacancy->description_ar || $vacancy->requirements)
            <div class="row g-3 mt-1">
                @if ($vacancy->description || $vacancy->description_ar)
                <div class="col-md-6">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-light py-3 border-bottom">
                            <h6 class="mb-0 fw-semibold"><i class="fas fa-align-left text-muted me-2"></i>الوصف</h6>
                        </div>
                        <div class="card-body"><p class="mb-0">{{ $vacancy->description_ar ?? $vacancy->description }}</p></div>
                    </div>
                </div>
                @endif
                @if ($vacancy->requirements)
                <div class="col-md-6">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-light py-3 border-bottom">
                            <h6 class="mb-0 fw-semibold"><i class="fas fa-list-check text-muted me-2"></i>المتطلبات</h6>
                        </div>
                        <div class="card-body"><p class="mb-0">{{ $vacancy->requirements }}</p></div>
                    </div>
                </div>
                @endif
            </div>
            @endif

            @if ($vacancy->responsibilities || $vacancy->benefits)
            <div class="row g-3 mt-1">
                @if ($vacancy->responsibilities)
                <div class="col-md-6">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-light py-3 border-bottom">
                            <h6 class="mb-0 fw-semibold"><i class="fas fa-tasks text-muted me-2"></i>المسؤوليات</h6>
                        </div>
                        <div class="card-body"><p class="mb-0">{{ $vacancy->responsibilities }}</p></div>
                    </div>
                </div>
                @endif
                @if ($vacancy->benefits)
                <div class="col-md-6">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-light py-3 border-bottom">
                            <h6 class="mb-0 fw-semibold"><i class="fas fa-gift text-muted me-2"></i>المزايا</h6>
                        </div>
                        <div class="card-body"><p class="mb-0">{{ $vacancy->benefits }}</p></div>
                    </div>
                </div>
                @endif
            </div>
            @endif

            @if ($vacancy->applications->count() > 0)
            <div class="row g-3 mt-1">
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-light py-3">
                            <h6 class="mb-0 fw-semibold">
                                <i class="fas fa-file-signature text-primary me-2"></i>طلبات التوظيف
                            </h6>
                            <small class="text-muted">{{ $vacancy->applications->count() }} طلب</small>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="ps-4">#</th>
                                            <th>المرشح</th>
                                            <th>تاريخ التقديم</th>
                                            <th>الحالة</th>
                                            <th class="pe-4 text-end">إجراء</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($vacancy->applications as $application)
                                            <tr>
                                                <td class="ps-4 text-muted small">{{ $loop->iteration }}</td>
                                                <td class="fw-semibold">{{ $application->candidate->full_name }}</td>
                                                <td>{{ $application->application_date->format('Y-m-d') }}</td>
                                                <td>
                                                    <span class="badge bg-{{ $application->status == 'accepted' ? 'success-subtle text-success' : ($application->status == 'rejected' ? 'danger-subtle text-danger' : 'warning-subtle text-dark') }} border">
                                                        {{ $application->status_name_ar }}
                                                    </span>
                                                </td>
                                                <td class="pe-4 text-end">
                                                    <a href="{{ route('admin.job-applications.show', $application->id) }}" class="btn btn-sm btn-outline-primary">
                                                        عرض <i class="fas fa-chevron-left ms-1 small"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <div class="row g-3 mt-1 mb-4">
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-light py-3">
                            <h6 class="mb-0 fw-semibold"><i class="fas fa-clock-rotate-left text-primary me-2"></i>بيانات السجل</h6>
                        </div>
                        <div class="card-body">
                            <div class="row row-cols-1 row-cols-md-2 g-0">
                                <div class="col border-bottom border-end-md p-3">
                                    <div class="small text-muted mb-1"><i class="far fa-clock me-1"></i>تاريخ الإنشاء</div>
                                    <div class="fw-semibold font-monospace small">{{ $vacancy->created_at->format('Y-m-d H:i') }}</div>
                                </div>
                                <div class="col border-bottom p-3">
                                    <div class="small text-muted mb-1"><i class="fas fa-pen-to-square me-1"></i>آخر تحديث</div>
                                    <div class="fw-semibold font-monospace small">{{ $vacancy->updated_at->format('Y-m-d H:i') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
