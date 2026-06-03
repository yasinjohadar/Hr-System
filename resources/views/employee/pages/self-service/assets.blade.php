@extends('employee.layouts.master')

@section('page-title')
    الأصول المعينة
@stop

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/employee-assets.css') }}">
@endpush

@section('content')
    <div class="main-content app-content employee-assets-page">
        <div class="container-fluid pt-4">

            <div class="card page-hero mb-4">
                <div class="card-body py-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="page-hero-icon">
                            <i class="ri-computer-line"></i>
                        </div>
                        <div>
                            <h4 class="mb-1 page-hero-title fw-bold">الأصول المعينة</h4>
                            <p class="mb-0 page-hero-subtitle">الأجهزة والمعدات المسجّلة باسمك في الشركة</p>
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
                                <div class="stat-label">إجمالي الأصول</div>
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
                                <div class="stat-value">{{ $stats['returned'] }}</div>
                                <div class="stat-label">مسترجعة</div>
                            </div>
                            <div class="stat-icon stat-icon--muted"><i class="ri-arrow-go-back-line"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-value">{{ $stats['overdue'] }}</div>
                                <div class="stat-label">تجاوزت موعد الإرجاع</div>
                            </div>
                            <div class="stat-icon stat-icon--warning"><i class="ri-alarm-warning-line"></i></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="content-panel">
                <div class="content-panel-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                    <div>
                        <h5 class="fw-bold mb-1 text-dark">قائمة الأصول</h5>
                        <p class="text-muted fs-13 mb-0">{{ $assets->total() }} أصل</p>
                    </div>
                    @if ($assets->isNotEmpty())
                        <div class="filter-pills" role="group">
                            <button type="button" class="filter-pill active" data-asset-filter="all">الكل</button>
                            <button type="button" class="filter-pill" data-asset-filter="active">نشط</button>
                            <button type="button" class="filter-pill" data-asset-filter="returned">مسترجع</button>
                            <button type="button" class="filter-pill" data-asset-filter="overdue">متأخر</button>
                        </div>
                    @endif
                </div>

                <div class="p-0">
                    @forelse ($assets as $assignment)
                        @php
                            $asset = $assignment->asset;
                            $assetName = $asset->name_ar ?? $asset->name ?? '—';
                            $isOverdue = $assignment->assignment_status === 'active'
                                && $assignment->expected_return_date
                                && $assignment->expected_return_date->isPast();
                            $filterState = $isOverdue ? 'overdue' : $assignment->assignment_status;
                        @endphp
                        <article class="asset-card asset-card-item {{ $isOverdue ? 'is-overdue' : '' }}"
                            data-filter-state="{{ $filterState }}">
                            <div class="asset-icon">
                                <i class="ri-macbook-line"></i>
                            </div>
                            <div class="flex-grow-1 min-w-0">
                                <div class="asset-name text-truncate">{{ $assetName }}</div>
                                <div class="asset-code">{{ $asset->asset_code ?? '—' }}</div>
                            </div>
                            <span class="category-pill">{{ $asset->category_name_ar ?? $asset->category ?? '—' }}</span>
                            <div class="asset-dates">
                                <div>تعيين: {{ $assignment->assigned_date->format('d/m/Y') }}</div>
                            </div>
                            <div class="asset-dates {{ $isOverdue ? 'is-late' : '' }}">
                                إرجاع:
                                {{ $assignment->expected_return_date ? $assignment->expected_return_date->format('d/m/Y') : '—' }}
                            </div>
                            <span class="status-pill status-pill--{{ $assignment->assignment_status }}">
                                {{ $assignment->assignment_status_name_ar }}
                            </span>
                            <button type="button" class="btn-view-asset" data-bs-toggle="modal"
                                data-bs-target="#assetModal{{ $assignment->id }}" title="التفاصيل">
                                <i class="ri-eye-line"></i>
                            </button>
                        </article>

                        <div class="modal fade" id="assetModal{{ $assignment->id }}" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title fw-bold">{{ $assetName }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="detail-row">
                                            <strong>كود الأصل</strong>
                                            <span>{{ $asset->asset_code ?? '—' }}</span>
                                        </div>
                                        <div class="detail-row">
                                            <strong>الفئة</strong>
                                            <span>{{ $asset->category_name_ar ?? $asset->category ?? '—' }}</span>
                                        </div>
                                        <div class="detail-row">
                                            <strong>النوع</strong>
                                            <span>{{ $asset->type ?? '—' }}</span>
                                        </div>
                                        <div class="detail-row">
                                            <strong>الشركة المصنعة</strong>
                                            <span>{{ $asset->manufacturer ?? '—' }}</span>
                                        </div>
                                        <div class="detail-row">
                                            <strong>الموديل</strong>
                                            <span>{{ $asset->model ?? '—' }}</span>
                                        </div>
                                        <div class="detail-row">
                                            <strong>الرقم التسلسلي</strong>
                                            <span>{{ $asset->serial_number ?? '—' }}</span>
                                        </div>
                                        <div class="detail-row">
                                            <strong>تاريخ التعيين</strong>
                                            <span>{{ $assignment->assigned_date->format('d/m/Y') }}</span>
                                        </div>
                                        <div class="detail-row">
                                            <strong>تاريخ الإرجاع المتوقع</strong>
                                            <span>{{ $assignment->expected_return_date?->format('d/m/Y') ?? '—' }}</span>
                                        </div>
                                        <div class="detail-row">
                                            <strong>الحالة</strong>
                                            <span class="status-pill status-pill--{{ $assignment->assignment_status }}">
                                                {{ $assignment->assignment_status_name_ar }}
                                            </span>
                                        </div>
                                        @if ($assignment->condition_on_assignment)
                                            <div class="detail-row">
                                                <strong>الحالة عند التعيين</strong>
                                                <span>{{ $assignment->condition_on_assignment_name_ar }}</span>
                                            </div>
                                        @endif
                                        @if ($assignment->assignment_notes)
                                            <div class="detail-row flex-column align-items-start">
                                                <strong>ملاحظات</strong>
                                                <span class="mt-1">{{ $assignment->assignment_notes }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="empty-state">
                            <div class="empty-icon"><i class="ri-computer-line"></i></div>
                            <h5 class="fw-semibold text-dark mb-2">لا توجد أصول معينة</h5>
                            <p class="text-muted mb-0">ستظهر الأصول المسجّلة باسمك هنا عند تعيينها</p>
                        </div>
                    @endforelse
                </div>

                @if ($assets->hasPages())
                    <div class="p-3 border-top">
                        {{ $assets->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@stop

@push('scripts')
    <script src="{{ asset('assets/js/employee-assets.js') }}"></script>
@endpush
