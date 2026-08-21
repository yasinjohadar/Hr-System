@extends('admin.layouts.master')

@section('page-title')
    كافة الدول
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid admin-page-shell">
            @include('admin.pages.users.partials.alerts')

            <div class="admin-page-banner">
                <div class="admin-page-banner-main">
                    <span class="admin-page-banner-icon"><i class="ri-earth-line"></i></span>
                    <div class="admin-page-banner-text">
                        <h1>كافة الدول</h1>
                        <p>قائمة الدول المرجعية المستخدمة في بيانات الموظفين والفروع والرواتب</p>
                    </div>
                </div>
                @can('country-create')
                    <div class="admin-page-banner-actions">
                        <a href="{{ route('admin.countries.create') }}" class="admin-btn admin-btn-light">
                            <i class="ri-add-line"></i>
                            إضافة دولة جديدة
                        </a>
                    </div>
                @endcan
            </div>

            <div class="admin-report-stats admin-report-stats-4 mb-4">
                <div class="admin-report-stat admin-report-stat-static admin-report-stat-blue">
                    <span class="admin-report-stat-icon"><i class="ri-earth-line"></i></span>
                    <span class="admin-report-stat-label">إجمالي الدول</span>
                    <span class="admin-report-stat-value" style="color:#2563eb;">{{ $stats['total'] }}</span>
                </div>
                <div class="admin-report-stat admin-report-stat-static admin-report-stat-green">
                    <span class="admin-report-stat-icon"><i class="ri-checkbox-circle-line"></i></span>
                    <span class="admin-report-stat-label">نشطة</span>
                    <span class="admin-report-stat-value" style="color:#059669;">{{ $stats['active'] }}</span>
                </div>
                <div class="admin-report-stat admin-report-stat-static admin-report-stat-red">
                    <span class="admin-report-stat-icon"><i class="ri-close-circle-line"></i></span>
                    <span class="admin-report-stat-label">غير نشطة</span>
                    <span class="admin-report-stat-value" style="color:#dc2626;">{{ $stats['inactive'] }}</span>
                </div>
                <div class="admin-report-stat admin-report-stat-static admin-report-stat-cyan">
                    <span class="admin-report-stat-icon"><i class="ri-building-2-line"></i></span>
                    <span class="admin-report-stat-label">لها فروع مسجّلة</span>
                    <span class="admin-report-stat-value" style="color:#0891b2;">{{ $stats['with_branches'] }}</span>
                </div>
            </div>

            <div class="admin-page-card">
                <div class="card-toolbar">
                    {{-- الفلترة عبر AJAX — assets/js/admin-filter-table.js يقرأ هذه السمات --}}
                    <form action="{{ route('admin.countries.index') }}" method="GET"
                          class="admin-filters w-100"
                          data-filter-table
                          data-filter-rows="#countries-table-body"
                          data-filter-pagination="#countries-pagination"
                          data-filter-meta="#countries-meta"
                          data-filter-loading="#countries-loading">
                        <div class="search-input-wrap">
                            <i class="ri-search-line"></i>
                            <input type="text" name="query" class="form-control"
                                   placeholder="بحث بالاسم أو الكود"
                                   value="{{ request('query') }}" autocomplete="off">
                        </div>

                        <select name="is_active" class="form-select admin-filter-select">
                            <option value="">كل الحالات</option>
                            <option value="1" @selected(request('is_active') === '1')>نشط</option>
                            <option value="0" @selected(request('is_active') === '0')>غير نشط</option>
                        </select>

                        <button type="submit" class="admin-btn admin-btn-primary">
                            <i class="ri-search-line"></i>
                            بحث
                        </button>
                        <a href="{{ route('admin.countries.index') }}" class="admin-btn admin-btn-danger">
                            <i class="ri-filter-off-line"></i>
                            مسح
                        </a>
                    </form>
                    <span id="countries-loading" class="spinner-border spinner-border-sm text-primary d-none ms-2"
                          role="status" aria-hidden="true"></span>
                </div>

                <div class="admin-table-wrap">
                    <div class="table-responsive">
                        <table class="admin-data-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>العلم</th>
                                    <th>اسم الدولة</th>
                                    <th>الكود</th>
                                    <th>رمز الهاتف</th>
                                    <th>العملة</th>
                                    <th>الحالة</th>
                                    <th>العمليات</th>
                                </tr>
                            </thead>
                            <tbody id="countries-table-body">
                                @include('admin.pages.countries._index_rows', ['countries' => $countries])
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="admin-table-footer">
                    <div class="admin-table-meta" id="countries-meta">
                        @include('admin.pages.countries._index_meta', ['countries' => $countries])
                    </div>
                    <div class="admin-pagination" id="countries-pagination">
                        @include('admin.pages.countries._index_pagination', ['countries' => $countries])
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
