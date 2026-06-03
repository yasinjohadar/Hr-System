@extends('employee.layouts.master')

@section('page-title')
    الشهادات
@stop

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/employee-certificates.css') }}">
@endpush

@section('content')
    <div class="main-content app-content employee-certificates-page">
        <div class="container-fluid pt-4">

            <div class="card page-hero mb-4">
                <div class="card-body py-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="page-hero-icon">
                            <i class="ri-award-line"></i>
                        </div>
                        <div>
                            <h4 class="mb-1 page-hero-title fw-bold">شهاداتي</h4>
                            <p class="mb-0 page-hero-subtitle">الشهادات المهنية والأكاديمية المسجّلة في ملفك</p>
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
                                <div class="stat-label">إجمالي الشهادات</div>
                            </div>
                            <div class="stat-icon stat-icon--primary"><i class="ri-medal-line"></i></div>
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
                                <div class="stat-value">{{ $stats['expiring_soon'] }}</div>
                                <div class="stat-label">تنتهي خلال 30 يوماً</div>
                            </div>
                            <div class="stat-icon stat-icon--warning"><i class="ri-alarm-warning-line"></i></div>
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
                            <div class="stat-icon stat-icon--danger"><i class="ri-close-circle-line"></i></div>
                        </div>
                    </div>
                </div>
            </div>

            @if ($certificates->isNotEmpty())
                <div class="filter-pills" role="group">
                    <button type="button" class="filter-pill active" data-cert-filter="all">الكل</button>
                    <button type="button" class="filter-pill" data-cert-filter="active">نشط</button>
                    <button type="button" class="filter-pill" data-cert-filter="expiring_soon">تنتهي قريباً</button>
                    <button type="button" class="filter-pill" data-cert-filter="expired">منتهي</button>
                </div>

                <div class="row g-3">
                    @foreach ($certificates as $certificate)
                        @php
                            $displayName = $certificate->certificate_name_ar ?: $certificate->certificate_name;
                            $isExpired = $certificate->isExpired() || $certificate->status === 'expired';
                            $isExpiringSoon = ! $certificate->does_not_expire
                                && $certificate->expiry_date
                                && ! $certificate->expiry_date->isPast()
                                && $certificate->expiry_date->lte(now()->addDays(30));

                            if ($isExpired) {
                                $filterState = 'expired';
                                $cardClass = 'is-expired';
                                $pillClass = 'expired';
                                $pillLabel = 'منتهي';
                            } elseif ($isExpiringSoon) {
                                $filterState = 'expiring_soon';
                                $cardClass = 'is-expiring';
                                $pillClass = 'expiring';
                                $pillLabel = 'تنتهي قريباً';
                            } else {
                                $filterState = 'active';
                                $cardClass = '';
                                $pillClass = $certificate->status === 'pending' ? 'pending' : 'active';
                                $pillLabel = $certificate->status_name_ar;
                            }
                        @endphp
                        <div class="col-md-6 col-xl-4 certificate-card-item" data-filter-state="{{ $filterState }}">
                            <div class="certificate-card {{ $cardClass }}">
                                <div class="cert-card-header">
                                    <div class="cert-icon"><i class="ri-award-fill"></i></div>
                                    <div class="min-w-0 flex-grow-1">
                                        <div class="cert-name">{{ $displayName }}</div>
                                        @if ($certificate->issuing_organization)
                                            <div class="cert-org">{{ $certificate->issuing_organization }}</div>
                                        @endif
                                    </div>
                                </div>

                                <div class="cert-meta">
                                    @if ($certificate->certificate_number)
                                        <div class="cert-meta-row">
                                            <span>رقم الشهادة</span>
                                            <span class="text-truncate">{{ $certificate->certificate_number }}</span>
                                        </div>
                                    @endif
                                    <div class="cert-meta-row">
                                        <span>تاريخ الإصدار</span>
                                        <span>{{ $certificate->issue_date->format('d/m/Y') }}</span>
                                    </div>
                                    <div class="cert-meta-row">
                                        <span>تاريخ الانتهاء</span>
                                        <span>
                                            @if ($certificate->does_not_expire)
                                                <span class="text-muted">لا تنتهي</span>
                                            @elseif ($certificate->expiry_date)
                                                <span class="{{ $isExpired ? 'text-expired' : '' }}">
                                                    {{ $certificate->expiry_date->format('d/m/Y') }}
                                                </span>
                                            @else
                                                —
                                            @endif
                                        </span>
                                    </div>
                                </div>

                                <div class="cert-card-footer">
                                    <span class="status-pill status-pill--{{ $pillClass }}">{{ $pillLabel }}</span>
                                    @if ($certificate->file_path)
                                        <a href="{{ asset('storage/' . $certificate->file_path) }}" target="_blank"
                                            class="btn-cert-download">
                                            <i class="ri-download-line me-1"></i>تحميل
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state">
                    <div class="empty-icon"><i class="ri-award-line"></i></div>
                    <h5 class="fw-semibold text-dark mb-2">لا توجد شهادات مسجلة</h5>
                    <p class="text-muted mb-0">ستظهر شهاداتك هنا بعد إضافتها من الموارد البشرية</p>
                </div>
            @endif
        </div>
    </div>
@stop

@push('scripts')
    <script src="{{ asset('assets/js/employee-certificates.js') }}"></script>
@endpush
