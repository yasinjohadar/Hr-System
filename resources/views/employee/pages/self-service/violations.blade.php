@extends('employee.layouts.master')

@section('page-title')
    المخالفات
@stop

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/employee-violations.css') }}">
@endpush

@section('content')
    <div class="main-content app-content employee-violations-page">
        <div class="container-fluid pt-4">

            <div class="card page-hero mb-4">
                <div class="card-body py-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="page-hero-icon">
                            <i class="ri-error-warning-line"></i>
                        </div>
                        <div>
                            <h4 class="mb-1 page-hero-title fw-bold">المخالفات</h4>
                            <p class="mb-0 page-hero-subtitle">سجل المخالفات والإجراءات التأديبية المرتبطة بك</p>
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
                                <div class="stat-label">إجمالي السجلات</div>
                            </div>
                            <div class="stat-icon stat-icon--primary"><i class="ri-file-list-3-line"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-value">{{ $stats['pending'] }}</div>
                                <div class="stat-label">قيد المراجعة</div>
                            </div>
                            <div class="stat-icon stat-icon--warning"><i class="ri-time-line"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-value">{{ $stats['confirmed'] }}</div>
                                <div class="stat-label">مؤكدة</div>
                            </div>
                            <div class="stat-icon stat-icon--danger"><i class="ri-alert-line"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-value">{{ $stats['resolved'] }}</div>
                                <div class="stat-label">محلولة / مرفوضة</div>
                            </div>
                            <div class="stat-icon stat-icon--success"><i class="ri-checkbox-circle-line"></i></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="content-panel">
                <div class="content-panel-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                    <div>
                        <h5 class="fw-bold mb-1 text-dark">قائمة المخالفات</h5>
                        <p class="text-muted fs-13 mb-0">{{ $violations->total() }} مخالفة</p>
                    </div>
                    @if ($violations->isNotEmpty())
                        <div class="filter-pills" role="group">
                            <button type="button" class="filter-pill active" data-violation-filter="all">الكل</button>
                            <button type="button" class="filter-pill" data-violation-filter="pending">قيد المراجعة</button>
                            <button type="button" class="filter-pill" data-violation-filter="confirmed">مؤكدة</button>
                            <button type="button" class="filter-pill" data-violation-filter="resolved">محلولة</button>
                            <button type="button" class="filter-pill" data-violation-filter="critical">حرجة</button>
                        </div>
                    @endif
                </div>

                <div class="p-0">
                    @forelse ($violations as $violation)
                        @php
                            $typeName = $violation->violationType->name_ar ?? $violation->violationType->name ?? '—';
                            $actionName = $violation->disciplinaryAction
                                ? ($violation->disciplinaryAction->name_ar ?? $violation->disciplinaryAction->name)
                                : null;
                        @endphp
                        <article class="violation-card violation-card-item severity-{{ $violation->severity }}"
                            data-status="{{ $violation->status }}"
                            data-severity="{{ $violation->severity }}">
                            <div class="violation-icon">
                                <i class="ri-shield-cross-line"></i>
                            </div>
                            <div class="flex-grow-1 min-w-0">
                                <div class="violation-type text-truncate">{{ $typeName }}</div>
                                <div class="violation-code">{{ $violation->violation_code }}</div>
                            </div>
                            <div class="violation-date">
                                <i class="ri-calendar-line me-1"></i>{{ $violation->violation_date->format('d/m/Y') }}
                            </div>
                            <div class="action-text text-truncate" title="{{ $actionName }}">
                                {{ $actionName ?: '—' }}
                            </div>
                            <span class="severity-pill severity-pill--{{ $violation->severity }}">{{ $violation->severity_name_ar }}</span>
                            <span class="status-pill status-pill--{{ $violation->status }}">{{ $violation->status_name_ar }}</span>
                            <button type="button" class="btn-view-violation" data-bs-toggle="modal"
                                data-bs-target="#violationModal{{ $violation->id }}" title="التفاصيل">
                                <i class="ri-eye-line"></i>
                            </button>
                        </article>

                        <div class="modal fade" id="violationModal{{ $violation->id }}" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title fw-bold">تفاصيل المخالفة — {{ $violation->violation_code }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="detail-row">
                                            <strong>نوع المخالفة</strong>
                                            <span>{{ $typeName }}</span>
                                        </div>
                                        <div class="detail-row">
                                            <strong>تاريخ المخالفة</strong>
                                            <span>{{ $violation->violation_date->format('d/m/Y') }}</span>
                                        </div>
                                        @if ($actionName)
                                            <div class="detail-row">
                                                <strong>الإجراء التأديبي</strong>
                                                <span>{{ $actionName }}</span>
                                            </div>
                                        @endif
                                        <div class="detail-row">
                                            <strong>الخطورة</strong>
                                            <span class="severity-pill severity-pill--{{ $violation->severity }}">{{ $violation->severity_name_ar }}</span>
                                        </div>
                                        <div class="detail-row">
                                            <strong>الحالة</strong>
                                            <span class="status-pill status-pill--{{ $violation->status }}">{{ $violation->status_name_ar }}</span>
                                        </div>
                                        @if ($violation->description_ar || $violation->description)
                                            <div class="detail-row flex-column align-items-start">
                                                <strong>الوصف</strong>
                                                <span class="mt-1">{{ $violation->description_ar ?? $violation->description }}</span>
                                            </div>
                                        @endif
                                        @if ($violation->employee_response)
                                            <div class="detail-row flex-column align-items-start">
                                                <strong>ردك</strong>
                                                <span class="mt-1">{{ $violation->employee_response }}</span>
                                            </div>
                                        @endif
                                        @if ($violation->investigation_notes)
                                            <div class="detail-row flex-column align-items-start">
                                                <strong>ملاحظات التحقيق</strong>
                                                <span class="mt-1">{{ $violation->investigation_notes }}</span>
                                            </div>
                                        @endif
                                        @if ($violation->resolution_notes)
                                            <div class="detail-row flex-column align-items-start">
                                                <strong>ملاحظات الحل</strong>
                                                <span class="mt-1">{{ $violation->resolution_notes }}</span>
                                            </div>
                                        @endif
                                        @if ($violation->notes)
                                            <div class="detail-row flex-column align-items-start mb-0">
                                                <strong>ملاحظات</strong>
                                                <span class="mt-1">{{ $violation->notes }}</span>
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
                            <div class="empty-icon"><i class="ri-shield-check-line"></i></div>
                            <h5 class="fw-semibold text-dark mb-2">لا توجد مخالفات</h5>
                            <p class="text-muted mb-0">لا يوجد سجل مخالفات مسجّل باسمك</p>
                        </div>
                    @endforelse
                </div>

                @if ($violations->hasPages())
                    <div class="p-3 border-top">
                        {{ $violations->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@stop

@push('scripts')
    <script src="{{ asset('assets/js/employee-violations.js') }}"></script>
@endpush
