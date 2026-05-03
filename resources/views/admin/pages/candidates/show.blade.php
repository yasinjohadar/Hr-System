@extends('admin.layouts.master')

@section('page-title')
    تفاصيل المرشح
@stop

@section('css')
    <style>
        .candidate-show-hero {
            background: linear-gradient(145deg, var(--bs-primary) 0%, rgba(var(--bs-primary-rgb), 0.88) 55%, rgb(15, 76, 129) 100%);
            color: #fff;
            border: none;
        }
        .candidate-show-hero .text-white-75 { color: rgba(255,255,255,.85) !important; }
    </style>
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">تفاصيل المرشح</h5>
                    <p class="text-muted small mb-0">{{ $candidate->full_name }} — {{ $candidate->candidate_code }}</p>
                </div>
                <div class="d-flex flex-wrap gap-2 mt-2 mt-md-0">
                    <a href="{{ route('admin.candidates.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-right me-1"></i>العودة للقائمة
                    </a>
                    @can('candidate-edit')
                        <a href="{{ route('admin.candidates.edit', $candidate->id) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit me-1"></i>تعديل
                        </a>
                    @endcan
                </div>
            </div>

            <div class="row g-3">
                <div class="col-lg-4">
                    <div class="card candidate-show-hero shadow-sm h-100">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex align-items-start gap-3 mb-3">
                                <span class="avatar avatar-md bg-white bg-opacity-25 rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width:3rem;height:3rem;">
                                    <i class="fas fa-user-tie fs-4"></i>
                                </span>
                                <div class="min-w-0">
                                    <div class="text-white-75 small mb-1">المرشح</div>
                                    <div class="fw-semibold fs-6 text-truncate">{{ $candidate->full_name }}</div>
                                    <div class="small text-white-75 font-monospace">{{ $candidate->candidate_code }}</div>
                                </div>
                            </div>
                            @if ($candidate->current_position)
                            <div class="mb-3 pb-3 border-bottom border-white border-opacity-25">
                                <div class="text-white-75 small mb-1"><i class="fas fa-briefcase me-1"></i>المنصب الحالي</div>
                                <div class="fw-semibold">{{ $candidate->current_position }}</div>
                            </div>
                            @endif
                            <div class="mb-3">
                                <div class="text-white-75 small mb-2">الحالة</div>
                                <span class="badge bg-{{ $candidate->status == 'hired' ? 'success' : ($candidate->status == 'rejected' ? 'danger' : 'warning') }} fs-14 px-3 py-2">
                                    {{ $candidate->status_name_ar }}
                                </span>
                            </div>
                            <div class="mt-auto pt-3 border-top border-white border-opacity-25">
                                @if ($candidate->years_of_experience)
                                <div class="text-white-75 small mb-1">سنوات الخبرة</div>
                                <div class="fs-3 fw-bold lh-1">{{ $candidate->years_of_experience }}</div>
                                <div class="small text-white-75 mt-1">سنة</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-light py-3 border-bottom">
                            <h6 class="mb-0 fw-semibold">
                                <i class="fas fa-circle-info text-primary me-2"></i>بيانات المرشح
                            </h6>
                            <small class="text-muted">معلومات التواصل والخبرة</small>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <tbody>
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle" style="width:35%">
                                                <i class="fas fa-envelope text-muted me-2"></i>البريد الإلكتروني
                                            </th>
                                            <td class="pe-4 py-3 align-middle fw-semibold">{{ $candidate->email }}</td>
                                        </tr>
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-phone text-muted me-2"></i>الهاتف
                                            </th>
                                            <td class="pe-4 py-3 align-middle fw-semibold">{{ $candidate->phone }}</td>
                                        </tr>
                                        @if ($candidate->current_position)
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-briefcase text-muted me-2"></i>المنصب الحالي
                                            </th>
                                            <td class="pe-4 py-3 align-middle fw-semibold">{{ $candidate->current_position }}</td>
                                        </tr>
                                        @endif
                                        @if ($candidate->years_of_experience)
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-chart-simple text-muted me-2"></i>سنوات الخبرة
                                            </th>
                                            <td class="pe-4 py-3 align-middle">
                                                <span class="badge bg-info-subtle text-dark border">{{ $candidate->years_of_experience }} سنة</span>
                                            </td>
                                        </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if ($candidate->applications->count() > 0)
            <div class="row g-3 mt-1">
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-light py-3">
                            <h6 class="mb-0 fw-semibold">
                                <i class="fas fa-file-signature text-primary me-2"></i>طلبات التوظيف
                            </h6>
                            <small class="text-muted">{{ $candidate->applications->count() }} طلب</small>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="ps-4">#</th>
                                            <th>الوظيفة</th>
                                            <th>تاريخ التقديم</th>
                                            <th>الحالة</th>
                                            <th class="pe-4 text-end">إجراء</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($candidate->applications as $application)
                                            <tr>
                                                <td class="ps-4 text-muted small">{{ $loop->iteration }}</td>
                                                <td class="fw-semibold">{{ $application->jobVacancy->title_ar ?? $application->jobVacancy->title }}</td>
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
                                    <div class="fw-semibold font-monospace small">{{ $candidate->created_at->format('Y-m-d H:i') }}</div>
                                </div>
                                <div class="col border-bottom p-3">
                                    <div class="small text-muted mb-1"><i class="fas fa-pen-to-square me-1"></i>آخر تحديث</div>
                                    <div class="fw-semibold font-monospace small">{{ $candidate->updated_at->format('Y-m-d H:i') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
