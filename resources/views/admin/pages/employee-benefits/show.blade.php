@extends('admin.layouts.master')

@section('page-title')
    تفاصيل ميزة الموظف
@stop

@section('css')
    <style>
        .employee-benefit-show-hero {
            background: linear-gradient(145deg, var(--bs-primary) 0%, rgba(var(--bs-primary-rgb), 0.88) 55%, rgb(15, 76, 129) 100%);
            color: #fff;
            border: none;
        }
        .employee-benefit-show-hero .text-white-75 { color: rgba(255,255,255,.85) !important; }
    </style>
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">تفاصيل ميزة الموظف</h5>
                    <p class="text-muted small mb-0">{{ $employeeBenefit->employee->full_name }} — {{ $employeeBenefit->benefitType->name_ar ?? $employeeBenefit->benefitType->name }}</p>
                </div>
                <div class="d-flex flex-wrap gap-2 mt-2 mt-md-0">
                    <a href="{{ route('admin.employee-benefits.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-right me-1"></i>العودة للقائمة
                    </a>
                    @can('employee-benefit-edit')
                        <a href="{{ route('admin.employee-benefits.edit', $employeeBenefit->id) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit me-1"></i>تعديل
                        </a>
                    @endcan
                </div>
            </div>

            <div class="row g-3">
                <div class="col-lg-4">
                    <div class="card employee-benefit-show-hero shadow-sm h-100">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex align-items-start gap-3 mb-3">
                                <span class="avatar avatar-md bg-white bg-opacity-25 rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width:3rem;height:3rem;">
                                    <i class="fas fa-gift fs-4"></i>
                                </span>
                                <div class="min-w-0">
                                    <div class="text-white-75 small mb-1">ميزة الموظف</div>
                                    <div class="fw-semibold fs-6 text-truncate">{{ $employeeBenefit->employee->full_name }}</div>
                                    <div class="small text-white-75">{{ $employeeBenefit->benefitType->name_ar ?? $employeeBenefit->benefitType->name }}</div>
                                </div>
                            </div>
                            <div class="mb-3 pb-3 border-bottom border-white border-opacity-25">
                                <div class="text-white-75 small mb-1"><i class="fas fa-calendar me-1"></i>تاريخ البدء</div>
                                <div class="fw-semibold">{{ $employeeBenefit->start_date->format('Y-m-d') }}</div>
                            </div>
                            <div class="mb-3">
                                <div class="text-white-75 small mb-2">الحالة</div>
                                <span class="badge bg-{{ $employeeBenefit->status == 'active' ? 'success' : ($employeeBenefit->status == 'expired' ? 'danger' : 'warning') }} fs-14 px-3 py-2">
                                    {{ $employeeBenefit->status_name_ar }}
                                </span>
                            </div>
                            <div class="mt-auto pt-3 border-top border-white border-opacity-25">
                                @if ($employeeBenefit->value)
                                <div class="text-white-75 small mb-1">القيمة</div>
                                <div class="display-6 fw-bold lh-1">{{ number_format($employeeBenefit->value, 2) }}</div>
                                @if ($employeeBenefit->currency) <div class="small text-white-75 mt-1">{{ $employeeBenefit->currency->symbol_ar ?? $employeeBenefit->currency->symbol }}</div> @endif
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-light py-3 border-bottom">
                            <h6 class="mb-0 fw-semibold">
                                <i class="fas fa-circle-info text-primary me-2"></i>تفاصيل الميزة
                            </h6>
                            <small class="text-muted">القيمة والتواريخ والمستندات</small>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <tbody>
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle" style="width:35%">
                                                <i class="fas fa-user text-muted me-2"></i>الموظف
                                            </th>
                                            <td class="pe-4 py-3 align-middle fw-semibold">{{ $employeeBenefit->employee->full_name }}</td>
                                        </tr>
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-hand-holding-heart text-muted me-2"></i>نوع الميزة
                                            </th>
                                            <td class="pe-4 py-3 align-middle fw-semibold">{{ $employeeBenefit->benefitType->name_ar ?? $employeeBenefit->benefitType->name }}</td>
                                        </tr>
                                        @if ($employeeBenefit->value)
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-coins text-muted me-2"></i>القيمة
                                            </th>
                                            <td class="pe-4 py-3 align-middle fw-semibold">
                                                {{ number_format($employeeBenefit->value, 2) }}
                                                @if ($employeeBenefit->currency) {{ $employeeBenefit->currency->symbol_ar ?? $employeeBenefit->currency->symbol }} @endif
                                            </td>
                                        </tr>
                                        @endif
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-calendar-range text-muted me-2"></i>الفترة
                                            </th>
                                            <td class="pe-4 py-3 align-middle fw-semibold">
                                                {{ $employeeBenefit->start_date->format('Y-m-d') }}
                                                <span class="text-muted mx-1">—</span>
                                                {{ $employeeBenefit->end_date ? $employeeBenefit->end_date->format('Y-m-d') : 'دائم' }}
                                            </td>
                                        </tr>
                                        @if ($employeeBenefit->notes)
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-sticky-note text-muted me-2"></i>ملاحظات
                                            </th>
                                            <td class="pe-4 py-3 align-middle">{{ $employeeBenefit->notes }}</td>
                                        </tr>
                                        @endif
                                        @if ($employeeBenefit->document_path)
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-paperclip text-muted me-2"></i>المستند المرفق
                                            </th>
                                            <td class="pe-4 py-3 align-middle">
                                                <a href="{{ Storage::url($employeeBenefit->document_path) }}" target="_blank" class="btn btn-outline-info btn-sm">
                                                    <i class="fas fa-download me-1"></i>تحميل المستند
                                                </a>
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
                                    <div class="fw-semibold font-monospace small">{{ $employeeBenefit->created_at->format('Y-m-d H:i') }}</div>
                                </div>
                                <div class="col border-bottom p-3">
                                    <div class="small text-muted mb-1"><i class="fas fa-pen-to-square me-1"></i>آخر تحديث</div>
                                    <div class="fw-semibold font-monospace small">{{ $employeeBenefit->updated_at->format('Y-m-d H:i') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
