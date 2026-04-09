@extends('admin.layouts.master')

@section('page-title')
    قائمة الرواتب
@stop

@section('css')
@stop

@section('content')
    @if (\Session::has('success'))
        <div class="alert alert-success">
            <ul>
                <li>{!! \Session::get('success') !!}</li>
            </ul>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="main-content app-content">
        <div class="container-fluid">

            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">كافة الرواتب</h5>
                </div>
                <div class="mt-2 mt-md-0">
                    @can('salary-create')
                        <a href="{{ route('admin.salaries.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus me-1"></i>إضافة راتب جديد
                        </a>
                    @endcan
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-body py-3">
                    <form id="salaries-filter-form" method="GET" action="{{ route('admin.salaries.index') }}"
                        class="row g-2 align-items-end">
                        <div class="col-12 col-sm-6 col-md-4 col-xl-2">
                            <label class="form-label small text-muted mb-0" for="salaries-filter-employee">الموظف</label>
                            <select name="employee_id" id="salaries-filter-employee" class="form-select form-select-sm">
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
                            <label class="form-label small text-muted mb-0" for="salaries-filter-month">الشهر</label>
                            <select name="salary_month" id="salaries-filter-month" class="form-select form-select-sm">
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
                            <label class="form-label small text-muted mb-0" for="salaries-filter-year">السنة</label>
                            <select name="salary_year" id="salaries-filter-year" class="form-select form-select-sm">
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
                            <label class="form-label small text-muted mb-0" for="salaries-filter-payment">حالة الدفع</label>
                            <select name="payment_status" id="salaries-filter-payment" class="form-select form-select-sm">
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
                            <label class="form-label small text-muted mb-0 d-block" for="salaries-filter-clear">&nbsp;</label>
                            <button type="button" class="btn btn-sm btn-outline-secondary w-100 text-nowrap"
                                id="salaries-filter-clear">إلغاء الفلترة</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card position-relative" id="salaries-table-card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0">قائمة الرواتب (<span id="salaries-total">{{ $salaries->total() }}</span>)</h5>
                    <span id="salaries-loading" class="spinner-border spinner-border-sm text-primary d-none" role="status" aria-hidden="true"></span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle table-nowrap mb-0">
                            <thead class="table-light">
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
                    <div class="mt-3" id="salaries-pagination">
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
