@extends('employee.layouts.master')

@section('page-title')
    المستندات
@stop

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/employee-documents.css') }}">
@endpush

@section('content')
    <div class="main-content app-content employee-documents-page">
        <div class="container-fluid pt-4">

            <div class="card documents-hero mb-4">
                <div class="card-body py-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="documents-hero-icon">
                            <i class="ri-folder-3-line"></i>
                        </div>
                        <div>
                            <h4 class="mb-1 documents-hero-title fw-bold">مستنداتي</h4>
                            <p class="mb-0 documents-hero-subtitle">جميع مستنداتك الرسمية في مكان واحد</p>
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
                                <div class="stat-label">إجمالي المستندات</div>
                            </div>
                            <div class="stat-icon stat-icon--total"><i class="ri-file-list-3-line"></i></div>
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
                            <div class="stat-icon stat-icon--active"><i class="ri-checkbox-circle-line"></i></div>
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

            <div class="documents-panel mb-4">
                <div class="documents-panel-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                    <div>
                        <h5 class="fw-bold mb-1 text-dark">قائمة المستندات</h5>
                        <p class="text-muted fs-13 mb-0">{{ $documents->total() }} مستند</p>
                    </div>
                    <div class="filter-pills" role="group" aria-label="تصفية المستندات">
                        <button type="button" class="filter-pill active" data-filter="all">الكل</button>
                        <button type="button" class="filter-pill" data-filter="active">نشط</button>
                        <button type="button" class="filter-pill" data-filter="expiring">ينتهي قريباً</button>
                        <button type="button" class="filter-pill" data-filter="expired">منتهي</button>
                    </div>
                </div>

                <div id="documents-list">
                    @forelse ($documents as $document)
                        @php
                            $statusKey = $document->is_expired || $document->status === 'expired' ? 'expired' : $document->status;
                            $statusClass = match ($statusKey) {
                                'active' => 'active',
                                'expired' => 'expired',
                                'rejected' => 'rejected',
                                default => 'pending',
                            };
                            $expiringSoon = $document->expiry_date
                                && ! $document->is_expired
                                && $document->expiry_date->isFuture()
                                && $document->expiry_date->lte(now()->addDays(30));
                            $docIcon = match (true) {
                                str_contains($document->document_type, 'هوية') || $document->document_type === 'id' => 'ri-id-card-line',
                                str_contains($document->document_type, 'جواز') || $document->document_type === 'passport' => 'ri-passport-line',
                                str_contains($document->document_type, 'عقد') || $document->document_type === 'contract' => 'ri-file-text-line',
                                str_contains($document->document_type, 'شهادة') || $document->document_type === 'certificate' => 'ri-award-line',
                                str_contains($document->document_type, 'رخصة') || $document->document_type === 'license' => 'ri-steering-2-line',
                                str_contains($document->document_type, 'تأمين') => 'ri-shield-check-line',
                                default => 'ri-file-pdf-line',
                            };
                        @endphp
                        <article class="document-card"
                            data-filter-status="{{ $statusKey }}"
                            data-expiring="{{ $expiringSoon ? '1' : '0' }}">
                            <div class="doc-type-badge">
                                <i class="{{ $docIcon }}"></i>
                            </div>
                            <div class="flex-grow-1 min-w-0">
                                <h6 class="mb-1 fw-semibold text-dark">{{ $document->title }}</h6>
                                <span class="badge bg-primary-transparent text-primary mb-2">{{ $document->document_type_name_ar }}</span>
                                <div class="doc-meta d-flex flex-wrap gap-3">
                                    <span>
                                        <i class="ri-calendar-line me-1"></i>إصدار:
                                        <strong>{{ $document->issue_date ? $document->issue_date->format('d/m/Y') : '—' }}</strong>
                                    </span>
                                    <span>
                                        <i class="ri-time-line me-1"></i>انتهاء:
                                        @if ($document->expiry_date)
                                            <strong class="{{ $document->is_expired ? 'text-danger' : ($expiringSoon ? 'expiry-soon' : '') }}">
                                                {{ $document->expiry_date->format('d/m/Y') }}
                                            </strong>
                                        @else
                                            <strong>—</strong>
                                        @endif
                                    </span>
                                </div>
                            </div>
                            <span class="status-pill status-pill--{{ $statusClass }}">{{ $document->status_name_ar }}</span>
                            @if ($document->file_path)
                                <a href="{{ asset('storage/' . $document->file_path) }}" target="_blank" rel="noopener noreferrer"
                                    class="btn btn-outline-primary btn-sm btn-doc-download">
                                    <i class="ri-download-2-line me-1"></i>عرض
                                </a>
                            @endif
                        </article>
                    @empty
                        <div class="empty-documents">
                            <div class="empty-icon"><i class="ri-folder-open-line"></i></div>
                            <h5 class="fw-semibold text-dark mb-2">لا توجد مستندات</h5>
                            <p class="text-muted mb-0">ستظهر مستنداتك هنا عند إضافتها من الموارد البشرية</p>
                        </div>
                    @endforelse
                </div>

                @if ($documents->hasPages())
                    <div class="p-3 border-top">
                        {{ $documents->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@stop

@push('scripts')
    <script src="{{ asset('assets/js/employee-documents.js') }}"></script>
@endpush
