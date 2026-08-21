@extends('admin.layouts.master')

@section('page-title')
    إعدادات الضرائب
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid admin-page-shell">
            @include('admin.pages.users.partials.alerts')

            <div class="admin-page-banner">
                <div class="admin-page-banner-main">
                    <span class="admin-page-banner-icon"><i class="ri-percent-line"></i></span>
                    <div class="admin-page-banner-text">
                        <h1>إعدادات الضرائب</h1>
                        <p>ضرائب الدخل والتأمينات الاجتماعية والصحية وطرق احتسابها في كشوف الرواتب</p>
                    </div>
                </div>
                @can('tax-setting-create')
                    <div class="admin-page-banner-actions">
                        <a href="{{ route('admin.tax-settings.create') }}" class="admin-btn admin-btn-light">
                            <i class="ri-add-line"></i>
                            إضافة إعداد ضريبة جديد
                        </a>
                    </div>
                @endcan
            </div>

            <div class="admin-report-stats admin-report-stats-4 mb-4">
                <div class="admin-report-stat admin-report-stat-static admin-report-stat-blue">
                    <span class="admin-report-stat-icon"><i class="ri-percent-line"></i></span>
                    <span class="admin-report-stat-label">إجمالي الإعدادات</span>
                    <span class="admin-report-stat-value" style="color:#2563eb;">{{ $stats['total'] }}</span>
                </div>
                <div class="admin-report-stat admin-report-stat-static admin-report-stat-red">
                    <span class="admin-report-stat-icon"><i class="ri-hand-coin-line"></i></span>
                    <span class="admin-report-stat-label">ضريبة الدخل</span>
                    <span class="admin-report-stat-value" style="color:#dc2626;">{{ $stats['income_tax'] }}</span>
                </div>
                <div class="admin-report-stat admin-report-stat-static admin-report-stat-cyan">
                    <span class="admin-report-stat-icon"><i class="ri-team-line"></i></span>
                    <span class="admin-report-stat-label">تأمينات اجتماعية</span>
                    <span class="admin-report-stat-value" style="color:#0891b2;">{{ $stats['social_insurance'] }}</span>
                </div>
                <div class="admin-report-stat admin-report-stat-static admin-report-stat-green">
                    <span class="admin-report-stat-icon"><i class="ri-heart-pulse-line"></i></span>
                    <span class="admin-report-stat-label">نشطة</span>
                    <span class="admin-report-stat-value" style="color:#059669;">{{ $stats['active'] }}</span>
                </div>
            </div>

            <div class="admin-page-card">
                <div class="card-toolbar">
                    {{-- الفلترة عبر AJAX — assets/js/admin-filter-table.js يقرأ هذه السمات --}}
                    <form action="{{ route('admin.tax-settings.index') }}" method="GET"
                          class="admin-filters w-100"
                          data-filter-table
                          data-filter-rows="#tax-settings-table-body"
                          data-filter-pagination="#tax-settings-pagination"
                          data-filter-meta="#tax-settings-meta"
                          data-filter-loading="#tax-settings-loading">
                        <div class="search-input-wrap">
                            <i class="ri-search-line"></i>
                            <input type="text" name="search" class="form-control"
                                   placeholder="بحث بالكود أو الاسم"
                                   value="{{ request('search') }}" autocomplete="off">
                        </div>

                        <select name="type" class="form-select admin-filter-select">
                            <option value="">كل الأنواع</option>
                            <option value="income_tax" @selected(request('type') === 'income_tax')>ضريبة الدخل</option>
                            <option value="social_insurance" @selected(request('type') === 'social_insurance')>التأمينات الاجتماعية</option>
                            <option value="health_insurance" @selected(request('type') === 'health_insurance')>التأمين الصحي</option>
                            <option value="other" @selected(request('type') === 'other')>أخرى</option>
                        </select>

                        <select name="is_active" class="form-select admin-filter-select">
                            <option value="">كل الحالات</option>
                            <option value="1" @selected(request('is_active') === '1')>نشط</option>
                            <option value="0" @selected(request('is_active') === '0')>غير نشط</option>
                        </select>

                        <select name="calculation_method" class="form-select admin-filter-select">
                            <option value="">كل طرق الحساب</option>
                            <option value="percentage" @selected(request('calculation_method') === 'percentage')>نسبة مئوية</option>
                            <option value="slab" @selected(request('calculation_method') === 'slab')>شرائح</option>
                            <option value="fixed" @selected(request('calculation_method') === 'fixed')>ثابت</option>
                        </select>

                        <button type="submit" class="admin-btn admin-btn-primary">
                            <i class="ri-search-line"></i>
                            بحث
                        </button>
                        <a href="{{ route('admin.tax-settings.index') }}" class="admin-btn admin-btn-danger">
                            <i class="ri-filter-off-line"></i>
                            إعادة تعيين
                        </a>
                    </form>
                    <span id="tax-settings-loading" class="spinner-border spinner-border-sm text-primary d-none ms-2"
                          role="status" aria-hidden="true"></span>
                </div>

                <div class="admin-table-wrap">
                    <div class="table-responsive">
                        <table class="admin-data-table">
                            <thead>
                                <tr>
                                    <th>الكود</th>
                                    <th>الاسم</th>
                                    <th>النوع</th>
                                    <th>طريقة الحساب</th>
                                    <th>النسبة/القيمة</th>
                                    <th>الحالة</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody id="tax-settings-table-body">
                                @include('admin.pages.tax-settings._index_rows', ['taxSettings' => $taxSettings])
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="admin-table-footer">
                    <div class="admin-table-meta" id="tax-settings-meta">
                        @include('admin.pages.tax-settings._index_meta', ['taxSettings' => $taxSettings])
                    </div>
                    <div class="admin-pagination" id="tax-settings-pagination">
                        @include('admin.pages.tax-settings._index_pagination', ['taxSettings' => $taxSettings])
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
