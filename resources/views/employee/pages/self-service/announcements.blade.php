@extends('employee.layouts.master')

@section('page-title')
    الإعلانات
@stop

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/employee-announcements.css') }}">
@endpush

@section('content')
    <div class="main-content app-content employee-announcements-page">
        <div class="container-fluid pt-4">

            <div class="card page-hero mb-4">
                <div class="card-body py-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="page-hero-icon">
                            <i class="ri-megaphone-line"></i>
                        </div>
                        <div>
                            <h4 class="mb-1 page-hero-title fw-bold">لوحة الإعلانات</h4>
                            <p class="mb-0 page-hero-subtitle">آخر الأخبار والتعميمات الموجّهة إليك</p>
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
                                <div class="stat-label">إعلانات نشطة</div>
                            </div>
                            <div class="stat-icon stat-icon--primary"><i class="ri-notification-3-line"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-value">{{ $stats['this_month'] }}</div>
                                <div class="stat-label">هذا الشهر</div>
                            </div>
                            <div class="stat-icon stat-icon--success"><i class="ri-calendar-line"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-value">{{ $stats['expiring_soon'] }}</div>
                                <div class="stat-label">تنتهي خلال 7 أيام</div>
                            </div>
                            <div class="stat-icon stat-icon--warning"><i class="ri-alarm-warning-line"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-value">{{ $stats['company_wide'] }}</div>
                                <div class="stat-label">تعميم عام</div>
                            </div>
                            <div class="stat-icon stat-icon--primary"><i class="ri-global-line"></i></div>
                        </div>
                    </div>
                </div>
            </div>

            @if ($announcements->isNotEmpty())
                <div class="filter-pills" role="group">
                    <button type="button" class="filter-pill active" data-announcement-filter="all">الكل</button>
                    <button type="button" class="filter-pill" data-announcement-filter="recent">حديثة</button>
                    <button type="button" class="filter-pill" data-announcement-filter="expiring">تنتهي قريباً</button>
                    <button type="button" class="filter-pill" data-announcement-filter="all_targets">تعميم عام</button>
                </div>

                <div id="announcements-list">
                    @foreach ($announcements as $announcement)
                        @php
                            $isRecent = $announcement->publish_date
                                && $announcement->publish_date->gte(now()->subDays(7));
                            $isExpiring = $announcement->expiry_date
                                && $announcement->expiry_date->lte(now()->addDays(7))
                                && $announcement->expiry_date->gte(now());
                            $filterStates = [];
                            if ($isExpiring) {
                                $filterStates[] = 'expiring';
                            }
                            if ($isRecent) {
                                $filterStates[] = 'recent';
                            }
                            if ($announcement->target_type === 'all') {
                                $filterStates[] = 'all_targets';
                            }
                            if (empty($filterStates)) {
                                $filterStates[] = 'other';
                            }
                            $cardClass = trim(($isRecent ? 'is-new' : '') . ' ' . ($isExpiring ? 'is-expiring' : ''));
                            $contentLong = strlen($announcement->content ?? '') > 200;
                        @endphp
                        <article class="announcement-card announcement-card-item {{ $cardClass }}"
                            data-filter-state="{{ implode(' ', $filterStates) }}">
                            <div class="announcement-header">
                                <div>
                                    <div class="announcement-title">{{ $announcement->title }}</div>
                                    <div class="announcement-meta">
                                        <i class="ri-calendar-line me-1"></i>
                                        {{ $announcement->publish_date?->format('d/m/Y') ?? '—' }}
                                        @if ($announcement->expiry_date)
                                            <span class="mx-2">·</span>
                                            حتى {{ $announcement->expiry_date->format('d/m/Y') }}
                                        @endif
                                    </div>
                                </div>
                                <span class="target-pill">{{ $announcement->target_type_label }}</span>
                            </div>
                            <div class="announcement-content {{ $contentLong ? 'collapsed' : '' }}">
                                {!! nl2br(e($announcement->content)) !!}
                            </div>
                            @if ($contentLong)
                                <button type="button" class="btn-toggle-content">اقرأ المزيد</button>
                            @endif
                        </article>
                    @endforeach
                </div>

                @if ($announcements->hasPages())
                    <div class="mt-3">
                        {{ $announcements->links() }}
                    </div>
                @endif
            @else
                <div class="empty-state">
                    <div class="empty-icon"><i class="ri-megaphone-line"></i></div>
                    <h5 class="fw-semibold text-dark mb-2">لا توجد إعلانات حالياً</h5>
                    <p class="text-muted mb-0">ستظهر الإعلانات الجديدة هنا عند نشرها</p>
                </div>
            @endif
        </div>
    </div>
@stop

@push('scripts')
    <script src="{{ asset('assets/js/employee-announcements.js') }}"></script>
@endpush
