@extends('employee.layouts.master')

@section('page-title')
    عقدي
@stop

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/employee-contract.css') }}">
@endpush

@section('content')
    <div class="main-content app-content employee-contract-page">
        <div class="container-fluid pt-4">

            <div class="card page-hero mb-4">
                <div class="card-body py-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="page-hero-icon">
                            <i class="ri-file-text-line"></i>
                        </div>
                        <div>
                            <h4 class="mb-1 page-hero-title fw-bold">عقدي</h4>
                            <p class="mb-0 page-hero-subtitle">العقد الحالي وسجل عقودك الوظيفية</p>
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
                                <div class="stat-label">إجمالي العقود</div>
                            </div>
                            <div class="stat-icon stat-icon--primary"><i class="ri-file-list-3-line"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-value">{{ $stats['active'] ? 'نشط' : '—' }}</div>
                                <div class="stat-label">العقد الحالي</div>
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
                                <div class="stat-label">ينتهي خلال 30 يوماً</div>
                            </div>
                            <div class="stat-icon stat-icon--warning"><i class="ri-alarm-warning-line"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-value">{{ $stats['ended'] }}</div>
                                <div class="stat-label">عقود منتهية / سابقة</div>
                            </div>
                            <div class="stat-icon stat-icon--muted"><i class="ri-history-line"></i></div>
                        </div>
                    </div>
                </div>
            </div>

            @if ($currentContract)
                @php
                    $daysLeft = $currentContract->days_remaining;
                    $expiringClass = $daysLeft !== null && $daysLeft <= 30 && $daysLeft >= 0 ? 'is-expiring-soon' : '';
                    $daysClass = '';
                    if ($daysLeft !== null) {
                        if ($daysLeft < 0) {
                            $daysClass = 'is-danger';
                        } elseif ($daysLeft <= 30) {
                            $daysClass = 'is-warning';
                        }
                    }
                    $totalDays = $currentContract->start_date && $currentContract->end_date
                        ? max(1, $currentContract->start_date->diffInDays($currentContract->end_date))
                        : null;
                    $elapsedPct = $totalDays && $daysLeft !== null
                        ? min(100, max(0, round((($totalDays - max(0, $daysLeft)) / $totalDays) * 100)))
                        : null;
                @endphp
                <div class="current-contract-panel {{ $expiringClass }}">
                    <div class="current-contract-header d-flex flex-wrap align-items-center justify-content-between gap-2">
                        <h5 class="fw-bold mb-0 text-dark">العقد الحالي</h5>
                        <span class="status-pill status-pill--{{ $currentContract->status }}">{{ $currentContract->status_label }}</span>
                    </div>
                    <div class="current-contract-body">
                        <span class="contract-type-badge">{{ $currentContract->contract_type_label }}</span>

                        <div class="info-grid">
                            <div class="info-item">
                                <label>تاريخ البداية</label>
                                <div class="value">{{ $currentContract->start_date?->format('d/m/Y') ?? '—' }}</div>
                            </div>
                            <div class="info-item">
                                <label>تاريخ النهاية</label>
                                <div class="value">{{ $currentContract->end_date?->format('d/m/Y') ?? 'غير محدد' }}</div>
                            </div>
                        </div>

                        @if ($currentContract->end_date && $daysLeft !== null)
                            <div class="days-remaining {{ $daysClass }}">
                                <div class="d-flex justify-content-between align-items-end">
                                    <div>
                                        <small class="text-muted d-block mb-1">الأيام المتبقية</small>
                                        <span class="days-num">{{ $daysLeft >= 0 ? $daysLeft : 0 }}</span>
                                        <small class="text-muted"> يوم</small>
                                    </div>
                                    @if ($daysLeft < 0)
                                        <span class="text-danger fw-semibold fs-13">منتهي</span>
                                    @endif
                                </div>
                                @if ($elapsedPct !== null)
                                    <div class="days-bar">
                                        <div class="days-bar-fill" style="width: {{ $elapsedPct }}%"></div>
                                    </div>
                                @endif
                            </div>
                        @endif

                        @if ($currentContract->notes)
                            <p class="text-muted fs-13 mb-3">{{ $currentContract->notes }}</p>
                        @endif

                        @if ($currentContract->document_path)
                            <a href="{{ asset('storage/' . $currentContract->document_path) }}" target="_blank"
                                class="btn btn-primary btn-download-contract">
                                <i class="ri-download-line me-1"></i>تحميل مستند العقد
                            </a>
                        @endif
                    </div>
                </div>
            @else
                <div class="empty-state mb-4">
                    <div class="empty-icon"><i class="ri-file-text-line"></i></div>
                    <h5 class="fw-semibold text-dark mb-2">لا يوجد عقد نشط حالياً</h5>
                    <p class="text-muted mb-0">تواصل مع الموارد البشرية إذا كان ذلك غير متوقع</p>
                </div>
            @endif

            @if ($contracts->isNotEmpty())
                <div class="content-panel">
                    <div class="content-panel-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                        <div>
                            <h5 class="fw-bold mb-1 text-dark">سجل العقود</h5>
                            <p class="text-muted fs-13 mb-0">{{ $contracts->count() }} عقد</p>
                        </div>
                        <div class="filter-pills" role="group">
                            <button type="button" class="filter-pill active" data-contract-filter="all">الكل</button>
                            <button type="button" class="filter-pill" data-contract-filter="active">نشط</button>
                            <button type="button" class="filter-pill" data-contract-filter="expired">منتهي</button>
                        </div>
                    </div>
                    <div class="p-0">
                        @foreach ($contracts as $c)
                            <article class="history-card history-card-item {{ $currentContract && $currentContract->id === $c->id ? 'is-current' : '' }}"
                                data-status="{{ $c->status }}">
                                <div class="history-icon">
                                    <i class="ri-file-paper-2-line"></i>
                                </div>
                                <div class="flex-grow-1 min-w-0">
                                    <div class="fw-semibold text-dark">{{ $c->contract_type_label }}</div>
                                    <div class="text-muted fs-12">
                                        {{ $c->start_date?->format('d/m/Y') ?? '—' }}
                                        @if ($c->end_date)
                                            – {{ $c->end_date->format('d/m/Y') }}
                                        @endif
                                    </div>
                                </div>
                                <span class="status-pill status-pill--{{ $c->status }}">{{ $c->status_label }}</span>
                            </article>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
@stop

@push('scripts')
    <script src="{{ asset('assets/js/employee-contract.js') }}"></script>
@endpush
