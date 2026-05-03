@extends('admin.layouts.master')

@section('page-title')
    تفاصيل إعداد الضريبة
@stop

@section('css')
    <style>
        .tax-show-hero {
            background: linear-gradient(145deg, var(--bs-primary) 0%, rgba(var(--bs-primary-rgb), 0.88) 55%, rgb(15, 76, 129) 100%);
            color: #fff;
            border: none;
        }
        .tax-show-hero .text-white-75 { color: rgba(255,255,255,.85) !important; }
        .tax-meta-item {
            padding: 0.65rem 0;
            border-bottom: 1px solid var(--bs-border-color-translucent);
        }
        .tax-meta-item:last-child { border-bottom: 0; }
    </style>
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">تفاصيل إعداد الضريبة</h5>
                    <p class="text-muted small mb-0">{{ $taxSetting->name_ar ?? $taxSetting->name }} — {{ $taxSetting->type_name_ar }}</p>
                </div>
                <div class="d-flex flex-wrap gap-2 mt-2 mt-md-0">
                    <a href="{{ route('admin.tax-settings.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-right me-1"></i>العودة للقائمة
                    </a>
                    @can('tax-setting-edit')
                        <a href="{{ route('admin.tax-settings.edit', $taxSetting->id) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit me-1"></i>تعديل
                        </a>
                    @endcan
                </div>
            </div>

            <div class="row g-3">
                <div class="col-lg-4">
                    <div class="card tax-show-hero shadow-sm h-100">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex align-items-start gap-3 mb-3">
                                <span class="avatar avatar-md bg-white bg-opacity-25 rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width:3rem;height:3rem;">
                                    <i class="fas fa-percent fs-4"></i>
                                </span>
                                <div class="min-w-0">
                                    <div class="text-white-75 small mb-1">إعداد الضريبة</div>
                                    <div class="fw-semibold fs-6 text-truncate">{{ $taxSetting->name_ar ?? $taxSetting->name }}</div>
                                    <div class="small text-white-75 font-monospace">{{ $taxSetting->code }}</div>
                                </div>
                            </div>
                            <div class="mb-3 pb-3 border-bottom border-white border-opacity-25">
                                <div class="text-white-75 small mb-1"><i class="fas fa-tag me-1"></i>النوع</div>
                                <div class="fs-5 fw-semibold">{{ $taxSetting->type_name_ar }}</div>
                            </div>
                            <div class="mb-3">
                                <div class="text-white-75 small mb-2">الحالة</div>
                                @if ($taxSetting->is_active)
                                    <span class="badge bg-success fs-14 px-3 py-2">نشط</span>
                                @else
                                    <span class="badge bg-secondary fs-14 px-3 py-2">غير نشط</span>
                                @endif
                            </div>
                            <div class="mt-auto pt-3 border-top border-white border-opacity-25">
                                @if($taxSetting->calculation_method == 'percentage')
                                    <div class="text-white-75 small mb-1">النسبة</div>
                                    <div class="fs-3 fw-bold lh-1">{{ $taxSetting->rate }}%</div>
                                @elseif($taxSetting->calculation_method == 'fixed')
                                    <div class="text-white-75 small mb-1">القيمة الثابتة</div>
                                    <div class="fs-3 fw-bold lh-1">{{ number_format($taxSetting->rate, 2) }}</div>
                                @else
                                    <div class="text-white-75 small mb-1">الشرائح</div>
                                    <div class="fs-6 fw-semibold">{{ count($taxSetting->slabs ?? []) }} شريحة</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-light py-3 border-bottom">
                            <h6 class="mb-0 fw-semibold">
                                <i class="fas fa-calculator text-primary me-2"></i>تفاصيل الحساب والمبالغ
                            </h6>
                            <small class="text-muted">طريقة الحساب، الحدود، والإعفاءات</small>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <tbody>
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle" style="width:40%">
                                                <i class="fas fa-cogs text-muted me-2"></i>طريقة الحساب
                                            </th>
                                            <td class="pe-4 py-3 align-middle fw-semibold">
                                                @if($taxSetting->calculation_method == 'percentage')
                                                    <span class="badge bg-primary-subtle text-primary border">نسبة مئوية</span>
                                                @elseif($taxSetting->calculation_method == 'slab')
                                                    <span class="badge bg-info-subtle text-dark border">شرائح</span>
                                                @else
                                                    <span class="badge bg-secondary-subtle text-dark border">مبلغ ثابت</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-percent text-muted me-2"></i>النسبة / القيمة
                                            </th>
                                            <td class="pe-4 py-3 align-middle fw-semibold">
                                                @if($taxSetting->calculation_method == 'percentage')
                                                    {{ $taxSetting->rate }}%
                                                @else
                                                    {{ number_format($taxSetting->rate, 2) }}
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-gift text-muted me-2"></i>مبلغ الإعفاء
                                            </th>
                                            <td class="pe-4 py-3 align-middle fw-semibold {{ $taxSetting->exemption_amount > 0 ? 'text-success' : '' }}">
                                                {{ number_format($taxSetting->exemption_amount, 2) }}
                                            </td>
                                        </tr>
                                        @if($taxSetting->min_amount)
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-arrow-down-short-wide text-muted me-2"></i>الحد الأدنى
                                            </th>
                                            <td class="pe-4 py-3 align-middle fw-semibold">{{ number_format($taxSetting->min_amount, 2) }}</td>
                                        </tr>
                                        @endif
                                        @if($taxSetting->max_amount)
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-arrow-up-wide-short text-muted me-2"></i>الحد الأقصى
                                            </th>
                                            <td class="pe-4 py-3 align-middle fw-semibold">{{ number_format($taxSetting->max_amount, 2) }}</td>
                                        </tr>
                                        @endif
                                        @if($taxSetting->effective_from || $taxSetting->effective_to)
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-calendar-check text-muted me-2"></i>فترة الصلاحية
                                            </th>
                                            <td class="pe-4 py-3 align-middle fw-semibold">
                                                {{ $taxSetting->effective_from ? $taxSetting->effective_from->format('Y-m-d') : '—' }}
                                                <span class="text-muted mx-1">—</span>
                                                {{ $taxSetting->effective_to ? $taxSetting->effective_to->format('Y-m-d') : '—' }}
                                            </td>
                                        </tr>
                                        @endif
                                        @if($taxSetting->description)
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-align-left text-muted me-2"></i>الوصف
                                            </th>
                                            <td class="pe-4 py-3 align-middle">{{ $taxSetting->description }}</td>
                                        </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if($taxSetting->slabs && count($taxSetting->slabs) > 0)
            <div class="row g-3 mt-1">
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-light py-3">
                            <h6 class="mb-0 fw-semibold">
                                <i class="fas fa-layer-group text-primary me-2"></i>شرائح الضريبة
                            </h6>
                            <small class="text-muted">تفصيل الشرائح ونسب الخصم لكل شريحة</small>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="ps-4">#</th>
                                            <th>من</th>
                                            <th>إلى</th>
                                            <th class="text-end">النسبة</th>
                                            <th class="pe-4 text-end">النطاق</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($taxSetting->slabs as $index => $slab)
                                            <tr>
                                                <td class="ps-4 text-muted small">{{ $index + 1 }}</td>
                                                <td class="fw-semibold">{{ number_format($slab['min'] ?? 0, 2) }}</td>
                                                <td class="fw-semibold">{{ number_format($slab['max'] ?? 0, 2) }}</td>
                                                <td class="text-end">
                                                    <span class="badge bg-primary-subtle text-primary">{{ $slab['rate'] ?? 0 }}%</span>
                                                </td>
                                                <td class="pe-4 text-end text-muted small">
                                                    {{ number_format($slab['min'] ?? 0, 0) }} — {{ number_format($slab['max'] ?? 0, 0) }}
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
                            <h6 class="mb-0 fw-semibold">
                                <i class="fas fa-circle-info text-primary me-2"></i>بيانات السجل
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-0">
                                @if($taxSetting->creator)
                                <div class="col border-bottom border-end-md p-3">
                                    <div class="small text-muted mb-1"><i class="fas fa-user-pen me-1"></i>أنشأ بواسطة</div>
                                    <div class="fw-semibold">{{ $taxSetting->creator->name }}</div>
                                </div>
                                @endif
                                <div class="col border-bottom border-end-md p-3">
                                    <div class="small text-muted mb-1"><i class="far fa-clock me-1"></i>تاريخ الإنشاء</div>
                                    <div class="fw-semibold font-monospace small">{{ $taxSetting->created_at->format('Y-m-d H:i') }}</div>
                                </div>
                                <div class="col border-bottom p-3">
                                    <div class="small text-muted mb-1"><i class="fas fa-pen-to-square me-1"></i>آخر تحديث</div>
                                    <div class="fw-semibold font-monospace small">{{ $taxSetting->updated_at->format('Y-m-d H:i') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
