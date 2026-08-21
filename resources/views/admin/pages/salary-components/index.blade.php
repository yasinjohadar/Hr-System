@extends('admin.layouts.master')

@section('page-title')
    مكوّنات الراتب
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid admin-page-shell">
            @include('admin.pages.users.partials.alerts')

            <div class="admin-page-banner">
                <div class="admin-page-banner-main">
                    <span class="admin-page-banner-icon"><i class="ri-list-settings-line"></i></span>
                    <div class="admin-page-banner-text">
                        <h1>مكوّنات الراتب</h1>
                        <p>البدلات والخصومات والمكافآت وطرق احتسابها المستخدمة في كشوف الرواتب</p>
                    </div>
                </div>
                @can('salary-component-create')
                    <div class="admin-page-banner-actions">
                        <a href="{{ route('admin.salary-components.create') }}" class="admin-btn admin-btn-light">
                            <i class="ri-add-line"></i>
                            إضافة مكوّن جديد
                        </a>
                    </div>
                @endcan
            </div>

            <div class="admin-report-stats admin-report-stats-4 mb-4">
                <div class="admin-report-stat admin-report-stat-static admin-report-stat-blue">
                    <span class="admin-report-stat-icon"><i class="ri-list-settings-line"></i></span>
                    <span class="admin-report-stat-label">إجمالي المكوّنات</span>
                    <span class="admin-report-stat-value" style="color:#2563eb;">{{ $stats['total'] }}</span>
                </div>
                <div class="admin-report-stat admin-report-stat-static admin-report-stat-green">
                    <span class="admin-report-stat-icon"><i class="ri-add-circle-line"></i></span>
                    <span class="admin-report-stat-label">بدلات</span>
                    <span class="admin-report-stat-value" style="color:#059669;">{{ $stats['allowance'] }}</span>
                </div>
                <div class="admin-report-stat admin-report-stat-static admin-report-stat-red">
                    <span class="admin-report-stat-icon"><i class="ri-indeterminate-circle-line"></i></span>
                    <span class="admin-report-stat-label">خصومات</span>
                    <span class="admin-report-stat-value" style="color:#dc2626;">{{ $stats['deduction'] }}</span>
                </div>
                <div class="admin-report-stat admin-report-stat-static admin-report-stat-cyan">
                    <span class="admin-report-stat-icon"><i class="ri-gift-line"></i></span>
                    <span class="admin-report-stat-label">مكافآت</span>
                    <span class="admin-report-stat-value" style="color:#0891b2;">{{ $stats['bonus'] }}</span>
                </div>
            </div>

            <div class="admin-page-card">
                <div class="card-toolbar">
                    {{-- الفلترة عبر AJAX — assets/js/admin-filter-table.js يقرأ هذه السمات --}}
                    <form action="{{ route('admin.salary-components.index') }}" method="GET"
                          class="admin-filters w-100"
                          data-filter-table
                          data-filter-rows="#components-table-body"
                          data-filter-pagination="#components-pagination"
                          data-filter-meta="#components-meta"
                          data-filter-loading="#components-loading">
                        <div class="search-input-wrap">
                            <i class="ri-search-line"></i>
                            <input type="text" name="search" class="form-control"
                                   placeholder="بحث بالكود أو الاسم"
                                   value="{{ request('search') }}" autocomplete="off">
                        </div>

                        <select name="type" class="form-select admin-filter-select">
                            <option value="">كل الأنواع</option>
                            <option value="allowance" @selected(request('type') === 'allowance')>بدل</option>
                            <option value="deduction" @selected(request('type') === 'deduction')>خصم</option>
                            <option value="bonus" @selected(request('type') === 'bonus')>مكافأة</option>
                            <option value="overtime" @selected(request('type') === 'overtime')>ساعات إضافية</option>
                        </select>

                        <select name="is_active" class="form-select admin-filter-select">
                            <option value="">كل الحالات</option>
                            <option value="1" @selected(request('is_active') === '1')>نشط</option>
                            <option value="0" @selected(request('is_active') === '0')>غير نشط</option>
                        </select>

                        <select name="calculation_type" class="form-select admin-filter-select">
                            <option value="">كل طرق الحساب</option>
                            <option value="fixed" @selected(request('calculation_type') === 'fixed')>ثابت</option>
                            <option value="percentage" @selected(request('calculation_type') === 'percentage')>نسبة مئوية</option>
                            <option value="formula" @selected(request('calculation_type') === 'formula')>صيغة</option>
                            <option value="attendance_based" @selected(request('calculation_type') === 'attendance_based')>حسب الحضور</option>
                            <option value="leave_based" @selected(request('calculation_type') === 'leave_based')>حسب الإجازات</option>
                        </select>

                        <button type="submit" class="admin-btn admin-btn-primary">
                            <i class="ri-search-line"></i>
                            بحث
                        </button>
                        <a href="{{ route('admin.salary-components.index') }}" class="admin-btn admin-btn-danger">
                            <i class="ri-filter-off-line"></i>
                            إعادة تعيين
                        </a>
                    </form>
                    <span id="components-loading" class="spinner-border spinner-border-sm text-primary d-none ms-2"
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
                                    <th>القيمة</th>
                                    <th>الحالة</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody id="components-table-body">
                                @include('admin.pages.salary-components._index_rows', ['components' => $components])
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="admin-table-footer">
                    <div class="admin-table-meta" id="components-meta">
                        @include('admin.pages.salary-components._index_meta', ['components' => $components])
                    </div>
                    <div class="admin-pagination" id="components-pagination">
                        @include('admin.pages.salary-components._index_pagination', ['components' => $components])
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
