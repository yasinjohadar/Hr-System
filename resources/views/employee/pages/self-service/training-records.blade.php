@extends('employee.layouts.master')

@section('page-title')
    سجل التدريب
@stop

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/employee-training.css') }}">
@endpush

@section('content')
    <div class="main-content app-content employee-training-page">
        <div class="container-fluid pt-4">

            <div class="card page-hero mb-4">
                <div class="card-body py-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="page-hero-icon">
                            <i class="ri-graduation-cap-line"></i>
                        </div>
                        <div>
                            <h4 class="mb-1 page-hero-title fw-bold">سجل التدريب</h4>
                            <p class="mb-0 page-hero-subtitle">الدورات والبرامج التدريبية المسجّلة في ملفك</p>
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
                            <div class="stat-icon stat-icon--primary"><i class="ri-book-open-line"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-value">{{ $stats['in_progress'] }}</div>
                                <div class="stat-label">قيد التنفيذ</div>
                            </div>
                            <div class="stat-icon stat-icon--warning"><i class="ri-time-line"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-value">{{ $stats['completed'] }}</div>
                                <div class="stat-label">مكتمل</div>
                            </div>
                            <div class="stat-icon stat-icon--success"><i class="ri-checkbox-circle-line"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-value">{{ $stats['certificates'] }}</div>
                                <div class="stat-label">شهادات صادرة</div>
                            </div>
                            <div class="stat-icon stat-icon--success"><i class="ri-award-line"></i></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="content-panel">
                <div class="content-panel-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                    <div>
                        <h5 class="fw-bold mb-1 text-dark">سجلات التدريب</h5>
                        <p class="text-muted fs-13 mb-0">{{ $records->total() }} سجل مسجّل</p>
                    </div>
                    @if ($records->isNotEmpty())
                        <div class="filter-pills" role="group">
                            <button type="button" class="filter-pill active" data-training-filter="all">الكل</button>
                            <button type="button" class="filter-pill" data-training-filter="in_progress">قيد التنفيذ</button>
                            <button type="button" class="filter-pill" data-training-filter="completed">مكتمل</button>
                            <button type="button" class="filter-pill" data-training-filter="failed">فاشل</button>
                        </div>
                    @endif
                </div>

                <div class="p-0">
                    @forelse ($records as $record)
                        @php
                            $title = $record->training->title_ar ?? $record->training->title ?? '—';
                            $statusClass = match ($record->status) {
                                'attending' => 'attending',
                                'completed' => 'completed',
                                'failed' => 'failed',
                                'cancelled' => 'cancelled',
                                default => 'registered',
                            };
                        @endphp
                        <article class="training-record-card training-record-item" data-status="{{ $record->status }}">
                            <div class="training-icon">
                                <i class="ri-presentation-line"></i>
                            </div>
                            <div class="flex-grow-1 min-w-0">
                                <div class="training-title">{{ $title }}</div>
                                <div class="training-dates">
                                    <i class="ri-calendar-line me-1"></i>
                                    تسجيل: {{ $record->registration_date?->format('d/m/Y') ?? '—' }}
                                    @if ($record->completion_date)
                                        <span class="mx-2">·</span>
                                        إكمال: {{ $record->completion_date->format('d/m/Y') }}
                                    @endif
                                </div>
                            </div>
                            @if ($record->score !== null)
                                <span class="score-chip" title="{{ $record->score_rating }}">
                                    {{ number_format($record->score, 1) }}
                                    <small class="fw-normal">({{ $record->score_rating }})</small>
                                </span>
                            @else
                                <span class="score-chip score-chip--muted">—</span>
                            @endif
                            <span class="status-pill status-pill--{{ $statusClass }}">{{ $record->status_ar }}</span>
                            <span class="cert-badge {{ $record->certificate_issued ? 'cert-badge--yes' : 'cert-badge--no' }}"
                                title="{{ $record->certificate_issued ? 'شهادة صادرة' : 'بدون شهادة' }}">
                                <i class="ri-award-line"></i>
                            </span>
                        </article>
                    @empty
                        <div class="empty-state">
                            <div class="empty-icon"><i class="ri-graduation-cap-line"></i></div>
                            <h5 class="fw-semibold text-dark mb-2">لا توجد سجلات تدريب</h5>
                            <p class="text-muted mb-0">ستظهر دوراتك التدريبية هنا عند تسجيلك فيها</p>
                        </div>
                    @endforelse
                </div>

                @if ($records->hasPages())
                    <div class="p-3 border-top">
                        {{ $records->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@stop

@push('scripts')
    <script src="{{ asset('assets/js/employee-training.js') }}"></script>
@endpush
