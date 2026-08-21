@extends('admin.layouts.master')

@section('page-title')
    تفاصيل الدولة
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid admin-page-shell">
            @include('admin.pages.users.partials.alerts')

            <div class="admin-page-banner">
                <div class="admin-page-banner-main">
                    <span class="admin-page-banner-icon">
                        @if ($country->flag)
                            <span class="fs-4">{{ $country->flag }}</span>
                        @else
                            <i class="ri-earth-line"></i>
                        @endif
                    </span>
                    <div class="admin-page-banner-text">
                        <h1>{{ $country->name_ar ?? $country->name }}</h1>
                        <p>{{ $country->code }} @if ($country->code3) — {{ $country->code3 }} @endif</p>
                    </div>
                </div>
                <div class="admin-page-banner-actions">
                    @can('country-edit')
                        <a href="{{ route('admin.countries.edit', $country->id) }}" class="admin-btn admin-btn-light">
                            <i class="ri-pencil-line"></i>
                            تعديل
                        </a>
                    @endcan
                    <a href="{{ route('admin.countries.index') }}" class="admin-btn admin-btn-secondary">
                        <i class="ri-arrow-right-line"></i>
                        العودة للقائمة
                    </a>
                </div>
            </div>

            <div class="admin-report-stats admin-report-stats-4 mb-4">
                <div class="admin-report-stat admin-report-stat-static admin-report-stat-blue">
                    <span class="admin-report-stat-icon"><i class="ri-team-line"></i></span>
                    <span class="admin-report-stat-label">عدد الموظفين</span>
                    <span class="admin-report-stat-value" style="color:#2563eb;">{{ $country->employees_count ?? 0 }}</span>
                </div>
                <div class="admin-report-stat admin-report-stat-static admin-report-stat-cyan">
                    <span class="admin-report-stat-icon"><i class="ri-building-2-line"></i></span>
                    <span class="admin-report-stat-label">عدد الفروع</span>
                    <span class="admin-report-stat-value" style="color:#0891b2;">{{ $country->branches_count ?? 0 }}</span>
                </div>
                <div class="admin-report-stat admin-report-stat-static admin-report-stat-green">
                    <span class="admin-report-stat-icon"><i class="ri-hashtag"></i></span>
                    <span class="admin-report-stat-label">ترتيب العرض</span>
                    <span class="admin-report-stat-value" style="color:#059669;">{{ $country->sort_order }}</span>
                </div>
                <div class="admin-report-stat admin-report-stat-static admin-report-stat-amber">
                    <span class="admin-report-stat-icon"><i class="ri-shield-check-line"></i></span>
                    <span class="admin-report-stat-label">الحالة</span>
                    <span class="admin-report-stat-value" style="color:{{ $country->is_active ? '#059669' : '#dc2626' }};">
                        {{ $country->is_active ? 'نشط' : 'غير نشط' }}
                    </span>
                </div>
            </div>

            <div class="admin-page-card">
                <div class="card-toolbar">
                    <h5 class="mb-0 fw-bold">معلومات الدولة</h5>
                </div>
                <div class="admin-form-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="admin-form-label mb-0">اسم الدولة (إنجليزي)</label>
                            <div>{{ $country->name }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="admin-form-label mb-0">اسم الدولة (عربي)</label>
                            <div>{{ $country->name_ar ?? '—' }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="admin-form-label mb-0">كود الدولة (2 أحرف)</label>
                            <div><span class="admin-badge admin-badge-role">{{ $country->code }}</span></div>
                        </div>
                        <div class="col-md-4">
                            <label class="admin-form-label mb-0">كود الدولة (3 أحرف)</label>
                            <div>{{ $country->code3 ?? '—' }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="admin-form-label mb-0">رمز الهاتف</label>
                            <div>{{ $country->phone_code ? '+' . ltrim($country->phone_code, '+') : '—' }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="admin-form-label mb-0">رمز العملة</label>
                            <div>{{ $country->currency_code ?? '—' }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="admin-form-label mb-0">العلم</label>
                            <div>
                                @if ($country->flag)
                                    <span class="fs-4">{{ $country->flag }}</span>
                                @else
                                    <span class="admin-badge admin-badge-muted">{{ $country->code }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
