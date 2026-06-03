@extends('employee.layouts.master')

@section('page-title')
    المزايا والتعويضات
@stop

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/employee-benefits.css') }}">
@endpush

@section('content')
    <div class="main-content app-content employee-benefits-page">
        <div class="container-fluid pt-4">

            <div class="card page-hero mb-4">
                <div class="card-body py-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="page-hero-icon">
                            <i class="ri-gift-line"></i>
                        </div>
                        <div>
                            <h4 class="mb-1 page-hero-title fw-bold">المزايا والتعويضات</h4>
                            <p class="mb-0 page-hero-subtitle">المزايا المالية والتأمينية المخصّصة لك</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-value">{{ $stats['total'] }}</div>
                                <div class="stat-label">إجمالي المزايا</div>
                            </div>
                            <div class="stat-icon stat-icon--primary"><i class="ri-stack-line"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-value">{{ $stats['active'] }}</div>
                                <div class="stat-label">نشطة</div>
                            </div>
                            <div class="stat-icon stat-icon--success"><i class="ri-checkbox-circle-line"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-value">{{ $stats['expired'] }}</div>
                                <div class="stat-label">منتهية</div>
                            </div>
                            <div class="stat-icon stat-icon--muted"><i class="ri-time-line"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-value">{{ number_format($stats['active_value'], 0) }}</div>
                                <div class="stat-label">قيمة المزايا النشطة</div>
                            </div>
                            <div class="stat-icon stat-icon--success"><i class="ri-money-dollar-circle-line"></i></div>
                        </div>
                    </div>
                </div>
            </div>

            @if ($benefits->isNotEmpty())
                <div class="filter-pills" role="group">
                    <button type="button" class="filter-pill active" data-benefit-filter="all">الكل</button>
                    <button type="button" class="filter-pill" data-benefit-filter="active">نشط</button>
                    <button type="button" class="filter-pill" data-benefit-filter="expired">منتهي</button>
                    <button type="button" class="filter-pill" data-benefit-filter="suspended">معلق</button>
                </div>

                <div class="row g-3">
                    @foreach ($benefits as $benefit)
                        @php
                            $typeName = $benefit->benefitType->name_ar ?? $benefit->benefitType->name ?? '—';
                            $currencyLabel = $benefit->currency->symbol ?? $benefit->currency->code ?? '';
                        @endphp
                        <div class="col-md-6 col-xl-4 benefit-card-item" data-status="{{ $benefit->status }}">
                            <div class="benefit-card {{ $benefit->status === 'expired' ? 'is-expired' : '' }}">
                                <div class="benefit-card-header">
                                    <div class="benefit-icon"><i class="ri-gift-2-line"></i></div>
                                    <div class="benefit-name">{{ $typeName }}</div>
                                </div>
                                <div class="benefit-value">
                                    {{ number_format((float) $benefit->value, 2) }}
                                    @if ($currencyLabel)
                                        <small class="fs-14 fw-semibold">{{ $currencyLabel }}</small>
                                    @endif
                                </div>
                                <div class="benefit-meta">
                                    <div class="benefit-meta-row">
                                        <span>تاريخ البدء</span>
                                        <span>{{ $benefit->start_date?->format('d/m/Y') ?? '—' }}</span>
                                    </div>
                                    <div class="benefit-meta-row">
                                        <span>تاريخ الانتهاء</span>
                                        <span>{{ $benefit->end_date?->format('d/m/Y') ?? '—' }}</span>
                                    </div>
                                </div>
                                <span class="status-pill status-pill--{{ $benefit->status }}">{{ $benefit->status_name_ar }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state">
                    <div class="empty-icon"><i class="ri-gift-line"></i></div>
                    <h5 class="fw-semibold text-dark mb-2">لا توجد مزايا</h5>
                    <p class="text-muted mb-0">ستظهر مزاياك وتعويضاتك هنا بعد تفعيلها من الموارد البشرية</p>
                </div>
            @endif
        </div>
    </div>
@stop

@push('scripts')
    <script src="{{ asset('assets/js/employee-benefits.js') }}"></script>
@endpush
