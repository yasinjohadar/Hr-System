@extends('admin.layouts.master')

@section('page-title')
    كشوف الرواتب
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid admin-page-shell">
            @include('admin.pages.users.partials.alerts')

            <div class="admin-page-banner">
                <div class="admin-page-banner-main">
                    <span class="admin-page-banner-icon"><i class="ri-file-paper-2-line"></i></span>
                    <div class="admin-page-banner-text">
                        <h1>كشوف الرواتب</h1>
                        <p>إنشاء كشوف الرواتب واحتسابها والموافقة عليها وتصديرها للبنك</p>
                    </div>
                </div>
                <div class="admin-page-banner-actions">
                    @can('payroll-create')
                        <a href="{{ route('admin.payrolls.create') }}" class="admin-btn admin-btn-light">
                            <i class="ri-add-line"></i>
                            إنشاء كشف راتب جديد
                        </a>
                    @endcan
                    @can('payroll-list')
                        <a href="{{ route('admin.payrolls.export-bank-file') }}" class="admin-btn admin-btn-secondary">
                            <i class="ri-bank-line"></i>
                            تصدير للبنك
                        </a>
                    @endcan
                </div>
            </div>

            <div class="admin-report-stats admin-report-stats-4 mb-4">
                <div class="admin-report-stat admin-report-stat-static admin-report-stat-blue">
                    <span class="admin-report-stat-icon"><i class="ri-file-paper-2-line"></i></span>
                    <span class="admin-report-stat-label">إجمالي الكشوف</span>
                    <span class="admin-report-stat-value" style="color:#2563eb;">{{ $stats['total'] }}</span>
                </div>
                <div class="admin-report-stat admin-report-stat-static admin-report-stat-cyan">
                    <span class="admin-report-stat-icon"><i class="ri-calculator-line"></i></span>
                    <span class="admin-report-stat-label">محسوبة</span>
                    <span class="admin-report-stat-value" style="color:#0891b2;">{{ $stats['calculated'] }}</span>
                </div>
                <div class="admin-report-stat admin-report-stat-static admin-report-stat-amber">
                    <span class="admin-report-stat-icon"><i class="ri-shield-check-line"></i></span>
                    <span class="admin-report-stat-label">موافق عليها</span>
                    <span class="admin-report-stat-value" style="color:#d97706;">{{ $stats['approved'] }}</span>
                </div>
                <div class="admin-report-stat admin-report-stat-static admin-report-stat-green">
                    <span class="admin-report-stat-icon"><i class="ri-checkbox-circle-line"></i></span>
                    <span class="admin-report-stat-label">مدفوعة</span>
                    <span class="admin-report-stat-value" style="color:#059669;">{{ $stats['paid'] }}</span>
                </div>
            </div>

            <div class="admin-page-card">
                <div class="card-toolbar">
                    {{-- الفلترة عبر AJAX — assets/js/admin-filter-table.js يقرأ هذه السمات --}}
                    <form action="{{ route('admin.payrolls.index') }}" method="GET"
                          class="admin-filters w-100"
                          data-filter-table
                          data-filter-rows="#payrolls-table-body"
                          data-filter-pagination="#payrolls-pagination"
                          data-filter-total="#payrolls-total"
                          data-filter-meta="#payrolls-meta"
                          data-filter-loading="#payrolls-loading">
                        <div class="search-input-wrap">
                            <i class="ri-search-line"></i>
                            <input type="text" name="search" class="form-control"
                                   placeholder="بحث بكود الكشف أو اسم الموظف"
                                   value="{{ request('search') }}" autocomplete="off">
                        </div>

                        <select name="employee_id" class="form-select admin-filter-select">
                            <option value="">كل الموظفين</option>
                            @foreach ($employees as $employee)
                                <option value="{{ $employee->id }}" @selected(request('employee_id') == $employee->id)>
                                    {{ $employee->full_name }}
                                </option>
                            @endforeach
                        </select>

                        <select name="status" class="form-select admin-filter-select">
                            <option value="">كل الحالات</option>
                            <option value="draft" @selected(request('status') === 'draft')>مسودة</option>
                            <option value="calculated" @selected(request('status') === 'calculated')>محسوب</option>
                            <option value="approved" @selected(request('status') === 'approved')>موافق عليه</option>
                            <option value="paid" @selected(request('status') === 'paid')>مدفوع</option>
                        </select>

                        <select name="month" class="form-select admin-filter-select">
                            <option value="">كل الأشهر</option>
                            @foreach (['', 'يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'] as $i => $monthName)
                                @continue($i === 0)
                                <option value="{{ $i }}" @selected(request('month') == $i)>{{ $monthName }}</option>
                            @endforeach
                        </select>

                        <input type="number" name="year" class="form-control admin-filter-select"
                               placeholder="السنة" value="{{ request('year') }}" min="2000" max="2100">

                        <button type="submit" class="admin-btn admin-btn-primary">
                            <i class="ri-search-line"></i>
                            بحث
                        </button>
                        <a href="{{ route('admin.payrolls.index') }}" class="admin-btn admin-btn-danger">
                            <i class="ri-filter-off-line"></i>
                            إعادة تعيين
                        </a>
                    </form>
                    <span id="payrolls-loading" class="spinner-border spinner-border-sm text-primary d-none ms-2"
                          role="status" aria-hidden="true"></span>
                </div>

                <div class="admin-table-wrap">
                    <div class="table-responsive">
                        <table class="admin-data-table">
                            <thead>
                                <tr>
                                    <th>كود الكشف</th>
                                    <th>الموظف</th>
                                    <th>الشهر/السنة</th>
                                    <th>الراتب الأساسي</th>
                                    <th>البدلات</th>
                                    <th>الخصومات</th>
                                    <th>الراتب الصافي</th>
                                    <th>الحالة</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody id="payrolls-table-body">
                                @include('admin.pages.payrolls._index_rows', ['payrolls' => $payrolls])
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="admin-table-footer">
                    <div class="admin-table-meta" id="payrolls-meta">
                        @include('admin.pages.payrolls._index_meta', ['payrolls' => $payrolls])
                    </div>
                    <div class="admin-pagination" id="payrolls-pagination">
                        @include('admin.pages.payrolls._index_pagination', ['payrolls' => $payrolls])
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
