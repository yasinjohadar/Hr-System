@extends('admin.layouts.master')

@section('page-title')
    قائمة الرواتب
@stop

@section('css')
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid admin-page-shell">
            @include('admin.pages.users.partials.alerts')

            <div class="admin-page-banner">
                <div class="admin-page-banner-main">
                    <span class="admin-page-banner-icon"><i class="ri-money-dollar-circle-line"></i></span>
                    <div class="admin-page-banner-text">
                        <h1>كافة الرواتب</h1>
                        <p>إدارة رواتب الموظفين وبنودها التفصيلية وحالات الدفع</p>
                    </div>
                </div>
                @can('salary-create')
                    <div class="admin-page-banner-actions">
                        <a href="{{ route('admin.salaries.create') }}" class="admin-btn admin-btn-light">
                            <i class="ri-add-line"></i>
                            إضافة راتب جديد
                        </a>
                    </div>
                @endcan
            </div>

            <div class="admin-page-card mb-3">
                <div class="card-toolbar">
                    {{-- الـ IDs هنا يقرأها سكربت الفلترة أسفل الصفحة — لا تُغيَّر --}}
                    <form id="salaries-filter-form" method="GET" action="{{ route('admin.salaries.index') }}"
                        class="row g-2 align-items-end w-100">
                        <div class="col-12 col-sm-6 col-md-4 col-xl-2">
                            <label class="admin-form-label mb-1" for="salaries-filter-employee">الموظف</label>
                            <select name="employee_id" id="salaries-filter-employee" class="form-select admin-filter-select">
                                <option value="">كل الموظفين</option>
                                @foreach ($employees as $employee)
                                    <option value="{{ $employee->id }}"
                                        {{ request()->filled('employee_id') && (string) request('employee_id') === (string) $employee->id ? 'selected' : '' }}>
                                        {{ $employee->full_name ?? $employee->first_name . ' ' . $employee->last_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-sm-6 col-md-4 col-xl-2">
                            <label class="admin-form-label mb-1" for="salaries-filter-month">الشهر</label>
                            <select name="salary_month" id="salaries-filter-month" class="form-select admin-filter-select">
                                <option value="">كل الأشهر</option>
                                @php
                                    $monthNames = ['', 'يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'];
                                @endphp
                                @for ($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i }}"
                                        {{ request()->filled('salary_month') && (string) request('salary_month') === (string) $i ? 'selected' : '' }}>
                                        {{ $monthNames[$i] }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-12 col-sm-6 col-md-4 col-xl-2">
                            <label class="admin-form-label mb-1" for="salaries-filter-year">السنة</label>
                            <select name="salary_year" id="salaries-filter-year" class="form-select admin-filter-select">
                                <option value="">كل السنوات</option>
                                @if ($years->isEmpty())
                                    <option value="{{ date('Y') }}"
                                        {{ request()->filled('salary_year') && (string) request('salary_year') === (string) date('Y') ? 'selected' : '' }}>
                                        {{ date('Y') }}
                                    </option>
                                @else
                                    @foreach ($years as $year)
                                        <option value="{{ $year }}"
                                            {{ request()->filled('salary_year') && (string) request('salary_year') === (string) $year ? 'selected' : '' }}>
                                            {{ $year }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="col-12 col-sm-6 col-md-4 col-xl-2">
                            <label class="admin-form-label mb-1" for="salaries-filter-payment">حالة الدفع</label>
                            <select name="payment_status" id="salaries-filter-payment" class="form-select admin-filter-select">
                                <option value="">كل الحالات</option>
                                <option value="pending"
                                    {{ request()->filled('payment_status') && request('payment_status') === 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                                <option value="paid"
                                    {{ request()->filled('payment_status') && request('payment_status') === 'paid' ? 'selected' : '' }}>مدفوع</option>
                                <option value="cancelled"
                                    {{ request()->filled('payment_status') && request('payment_status') === 'cancelled' ? 'selected' : '' }}>ملغي</option>
                            </select>
                        </div>
                        <div class="col-12 col-sm-6 col-md-4 col-xl-2">
                            <label class="admin-form-label mb-1 d-block" for="salaries-filter-clear">&nbsp;</label>
                            <button type="button" class="admin-btn admin-btn-danger w-100 text-nowrap"
                                id="salaries-filter-clear">
                                <i class="ri-filter-off-line"></i>
                                إلغاء الفلترة
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="admin-page-card position-relative" id="salaries-table-card">
                <div class="card-toolbar justify-content-between">
                    <h5 class="mb-0 fw-bold">قائمة الرواتب (<span id="salaries-total">{{ $salaries->total() }}</span>)</h5>
                    <span id="salaries-loading" class="spinner-border spinner-border-sm text-primary d-none" role="status" aria-hidden="true"></span>
                </div>

                <div class="admin-table-wrap">
                    <div class="table-responsive">
                        <table class="admin-data-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>الموظف</th>
                                    <th>الشهر</th>
                                    <th>الراتب الأساسي</th>
                                    <th>البدلات</th>
                                    <th>المكافآت</th>
                                    <th>الخصومات</th>
                                    <th>الإجمالي</th>
                                    <th>حالة الدفع</th>
                                    <th>تاريخ الدفع</th>
                                    <th>العمليات</th>
                                </tr>
                            </thead>
                            <tbody id="salaries-table-body">
                                @include('admin.pages.salaries._index_rows', ['salaries' => $salaries])
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="admin-table-footer">
                    <div class="admin-table-meta">
                        @if ($salaries->total() > 0)
                            عرض {{ $salaries->firstItem() }} إلى {{ $salaries->lastItem() }} من {{ $salaries->total() }} نتيجة
                        @else
                            لا توجد نتائج
                        @endif
                    </div>
                    <div class="admin-pagination" id="salaries-pagination">
                        @include('admin.pages.salaries._index_pagination', ['salaries' => $salaries])
                    </div>
                </div>
            </div>

        </div>
    </div>
@stop

@section('js')
<script>
(function () {
    const form = document.getElementById('salaries-filter-form');
    const tbody = document.getElementById('salaries-table-body');
    const paginationEl = document.getElementById('salaries-pagination');
    const totalEl = document.getElementById('salaries-total');
    const loadingEl = document.getElementById('salaries-loading');

    const jsonHeaders = {
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json',
    };

    function setLoading(on) {
        if (loadingEl) loadingEl.classList.toggle('d-none', !on);
    }

    function loadSalaries(url) {
        const absoluteUrl = url.startsWith('http') ? url : new URL(url, window.location.origin).href;
        setLoading(true);
        fetch(absoluteUrl, {
            method: 'GET',
            headers: jsonHeaders,
            credentials: 'same-origin',
        })
            .then(function (r) {
                if (!r.ok) throw new Error('Network error');
                return r.json();
            })
            .then(function (data) {
                tbody.innerHTML = data.html_rows;
                paginationEl.innerHTML = data.html_pagination;
                totalEl.textContent = data.total;
                try {
                    const u = new URL(absoluteUrl);
                    history.pushState({ salariesAjax: true }, '', u.pathname + u.search);
                } catch (e) { /* ignore */ }
            })
            .catch(function () {
                window.location.href = url;
            })
            .finally(function () {
                setLoading(false);
            });
    }

    function filterUrlPageOne() {
        const action = form.getAttribute('action');
        const params = new URLSearchParams(new FormData(form));
        params.set('page', '1');
        return action + (params.toString() ? '?' + params.toString() : '');
    }

    const clearBtn = document.getElementById('salaries-filter-clear');
    if (clearBtn && form) {
        clearBtn.addEventListener('click', function () {
            form.querySelectorAll('select').forEach(function (sel) {
                sel.selectedIndex = 0;
            });
            loadSalaries(form.getAttribute('action'));
        });
    }

    if (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            loadSalaries(filterUrlPageOne());
        });

        form.querySelectorAll('select').forEach(function (sel) {
            sel.addEventListener('change', function () {
                loadSalaries(filterUrlPageOne());
            });
        });
    }

    document.addEventListener('click', function (e) {
        const pagLink = e.target.closest('#salaries-pagination a[href]');
        if (!pagLink) return;
        const href = pagLink.getAttribute('href');
        if (!href || href === '#') return;
        e.preventDefault();
        loadSalaries(href);
    });

    window.addEventListener('popstate', function () {
        window.location.reload();
    });
})();
</script>
@stop
