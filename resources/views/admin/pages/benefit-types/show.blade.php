@extends('admin.layouts.master')

@section('page-title')
    تفاصيل نوع الميزة
@stop

@section('css')
<style>
    .benefit-show-hero {
        background: linear-gradient(
            125deg,
            rgba(var(--bs-primary-rgb), 0.14) 0%,
            rgba(var(--bs-primary-rgb), 0.04) 45%,
            transparent 100%
        );
    }
    [data-theme-mode="dark"] .benefit-show-hero,
    .dark-mode .benefit-show-hero {
        background: linear-gradient(
            125deg,
            rgba(var(--bs-primary-rgb), 0.22) 0%,
            rgba(var(--bs-primary-rgb), 0.06) 50%,
            transparent 100%
        );
    }
    .benefit-show-stat {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .benefit-show-stat:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.5rem 1.25rem rgba(0, 0, 0, 0.08);
    }
    [data-theme-mode="dark"] .benefit-show-stat:hover,
    .dark-mode .benefit-show-stat:hover {
        box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.35);
    }
    .letter-spacing-1 { letter-spacing: 0.06em; }
</style>
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">تفاصيل نوع الميزة</h5>
                    <p class="text-muted mb-0 small">{{ $benefitType->name_ar ?? $benefitType->name }}</p>
                </div>
                <div class="mt-2 mt-md-0 d-flex flex-wrap gap-2">
                    <a href="{{ route('admin.benefit-types.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-right me-2"></i>العودة للقائمة
                    </a>
                    @can('benefit-type-edit')
                        <a href="{{ route('admin.benefit-types.edit', $benefitType->id) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-edit me-2"></i>تعديل
                        </a>
                    @endcan
                </div>
            </div>

            <div class="row">
                <div class="col-xl-12">
                    <div class="card border-0 shadow-sm overflow-hidden">
                        <div class="benefit-show-hero px-4 py-4 px-lg-5 py-lg-5 border-bottom border-opacity-10">
                            <div class="row align-items-center g-4">
                                <div class="col-auto mx-auto mx-lg-0">
                                    <div class="rounded-4 d-flex align-items-center justify-content-center text-primary bg-primary bg-opacity-10 border border-primary border-opacity-25"
                                        style="width: 5.25rem; height: 5.25rem;">
                                        <i class="fas fa-hand-holding-heart fa-2x"></i>
                                    </div>
                                </div>
                                <div class="col text-center text-lg-start">
                                    <span class="d-inline-block small text-uppercase letter-spacing-1 text-muted fw-semibold mb-2">نوع الميزة</span>
                                    <h2 class="fw-bold mb-2 fs-2">{{ $benefitType->name_ar ?? $benefitType->name }}</h2>
                                    @if ($benefitType->name_ar && $benefitType->name)
                                        <p class="text-muted mb-3 mb-lg-4 fs-6">{{ $benefitType->name }}</p>
                                    @endif
                                    <div class="d-flex flex-wrap gap-2 justify-content-center justify-content-lg-start align-items-center">
                                        <span class="badge rounded-pill px-3 py-2 fs-6 fw-normal font-monospace bg-body-secondary border">
                                            {{ $benefitType->code }}
                                        </span>
                                        <span class="badge rounded-pill px-3 py-2 fs-6 fw-semibold {{ $benefitType->is_active ? 'bg-success-subtle text-success border border-success border-opacity-25' : 'bg-danger-subtle text-danger border border-danger border-opacity-25' }}">
                                            {{ $benefitType->is_active ? 'نشط' : 'غير نشط' }}
                                        </span>
                                        <span class="badge rounded-pill px-3 py-2 fs-6 fw-normal bg-info-subtle text-info border border-info border-opacity-25">
                                            {{ $benefitType->type_name_ar }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-body p-4 p-lg-5">
                            <div class="row g-3 g-lg-4 mb-4 mb-lg-5">
                                <div class="col-md-4">
                                    <div class="benefit-show-stat border rounded-4 p-4 h-100 text-center bg-body-tertiary bg-opacity-25">
                                        <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-primary bg-opacity-10 text-primary mb-3" style="width: 3rem; height: 3rem;">
                                            <i class="fas fa-coins"></i>
                                        </span>
                                        <div class="small text-muted text-uppercase fw-semibold mb-2 letter-spacing-1">القيمة الافتراضية</div>
                                        <div class="fs-3 fw-bold text-body">
                                            @if ($benefitType->default_value)
                                                {{ number_format($benefitType->default_value, 2) }}
                                                @if ($benefitType->currency)
                                                    <span class="fs-5 fw-semibold text-muted">{{ $benefitType->currency->symbol_ar ?? $benefitType->currency->symbol }}</span>
                                                @endif
                                            @else
                                                <span class="text-muted fs-5 fw-normal">—</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="benefit-show-stat border rounded-4 p-4 h-100 text-center bg-body-tertiary bg-opacity-25">
                                        <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-success bg-opacity-10 text-success mb-3" style="width: 3rem; height: 3rem;">
                                            <i class="fas fa-users"></i>
                                        </span>
                                        <div class="small text-muted text-uppercase fw-semibold mb-2 letter-spacing-1">عدد الموظفين</div>
                                        <div class="fs-3 fw-bold text-body">{{ $benefitType->employee_benefits_count ?? 0 }}</div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="benefit-show-stat border rounded-4 p-4 h-100 text-center bg-body-tertiary bg-opacity-25">
                                        <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-secondary bg-opacity-10 text-secondary mb-3" style="width: 3rem; height: 3rem;">
                                            <i class="fas fa-layer-group"></i>
                                        </span>
                                        <div class="small text-muted text-uppercase fw-semibold mb-2 letter-spacing-1">تصنيف النوع</div>
                                        <div class="fs-5 fw-semibold text-body">{{ $benefitType->type_name_ar }}</div>
                                    </div>
                                </div>
                            </div>

                            <h6 class="text-muted text-uppercase small fw-bold letter-spacing-1 mb-3">الإعدادات والسياسات</h6>
                            <div class="rounded-4 border overflow-hidden mb-4">
                                <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-2 px-4 py-3 border-bottom bg-body-tertiary bg-opacity-10">
                                    <span class="text-body d-flex align-items-center gap-2">
                                        <i class="fas fa-percent text-warning opacity-75"></i>
                                        <span>خاضع للضريبة</span>
                                    </span>
                                    <span class="badge rounded-pill px-3 py-2 {{ $benefitType->is_taxable ? 'bg-warning-subtle text-warning-emphasis border border-warning border-opacity-25' : 'bg-success-subtle text-success-emphasis border border-success border-opacity-25' }}">
                                        {{ $benefitType->is_taxable ? 'نعم' : 'لا' }}
                                    </span>
                                </div>
                                <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-2 px-4 py-3 border-bottom bg-body-tertiary bg-opacity-10">
                                    <span class="text-body d-flex align-items-center gap-2">
                                        <i class="fas fa-asterisk text-info opacity-75"></i>
                                        <span>إلزامي للموظف</span>
                                    </span>
                                    <span class="badge rounded-pill px-3 py-2 {{ $benefitType->is_mandatory ? 'bg-info-subtle text-info-emphasis border border-info border-opacity-25' : 'bg-body-secondary border' }}">
                                        {{ $benefitType->is_mandatory ? 'نعم' : 'لا' }}
                                    </span>
                                </div>
                                <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-2 px-4 py-3 bg-body-tertiary bg-opacity-10">
                                    <span class="text-body d-flex align-items-center gap-2">
                                        <i class="fas fa-user-check text-primary opacity-75"></i>
                                        <span>يتطلب موافقة</span>
                                    </span>
                                    <span class="badge rounded-pill px-3 py-2 {{ $benefitType->requires_approval ? 'bg-primary-subtle text-primary-emphasis border border-primary border-opacity-25' : 'bg-success-subtle text-success-emphasis border border-success border-opacity-25' }}">
                                        {{ $benefitType->requires_approval ? 'نعم' : 'لا' }}
                                    </span>
                                </div>
                            </div>

                            @if ($benefitType->description || $benefitType->description_ar)
                                <div class="rounded-4 p-4 border border-start border-primary border-4 bg-primary bg-opacity-5">
                                    <h6 class="small text-uppercase fw-bold text-muted letter-spacing-1 mb-3">الوصف</h6>
                                    <p class="mb-0 fs-6 lh-lg text-body-secondary">{{ $benefitType->description_ar ?? $benefitType->description }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
