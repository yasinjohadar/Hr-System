@extends('admin.layouts.master')

@section('page-title')
    قائمة أرصدة الإجازات
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
                    <h5 class="page-title fs-21 mb-1">كافة أرصدة الإجازات</h5>
                </div>
                <div class="mt-2 mt-md-0">
                    @can('leave-balance-create')
                        <a href="{{ route('admin.leave-balances.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus me-1"></i>إضافة رصيد إجازة جديد
                        </a>
                    @endcan
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-body py-3">
                    <form id="leave-balances-filter-form" method="GET" action="{{ route('admin.leave-balances.index') }}" class="row g-2 align-items-end">
                        <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                            <label class="form-label small text-muted mb-0" for="lb-filter-employee">الموظف</label>
                            <select name="employee_id" id="lb-filter-employee" class="form-select form-select-sm">
                                <option value="">كل الموظفين</option>
                                @foreach ($employees as $employee)
                                    <option value="{{ $employee->id }}" {{ request()->filled('employee_id') && (string) request('employee_id') === (string) $employee->id ? 'selected' : '' }}>
                                        {{ $employee->full_name ?? $employee->first_name . ' ' . $employee->last_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                            <label class="form-label small text-muted mb-0" for="lb-filter-type">نوع الإجازة</label>
                            <select name="leave_type_id" id="lb-filter-type" class="form-select form-select-sm">
                                <option value="">كل الأنواع</option>
                                @foreach ($leaveTypes as $type)
                                    <option value="{{ $type->id }}" {{ request()->filled('leave_type_id') && (string) request('leave_type_id') === (string) $type->id ? 'selected' : '' }}>
                                        {{ $type->name_ar ?? $type->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-sm-6 col-md-4 col-xl-2">
                            <label class="form-label small text-muted mb-0" for="lb-filter-year">السنة</label>
                            <select name="year" id="lb-filter-year" class="form-select form-select-sm">
                                <option value="" {{ ! request()->filled('year') ? 'selected' : '' }}>كل السنوات</option>
                                @if ($years->isEmpty())
                                    <option value="{{ date('Y') }}" {{ request()->filled('year') && (string) request('year') === (string) date('Y') ? 'selected' : '' }}>{{ date('Y') }}</option>
                                @else
                                    @foreach ($years as $year)
                                        <option value="{{ $year }}" {{ request()->filled('year') && (string) request('year') === (string) $year ? 'selected' : '' }}>{{ $year }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="col-12 col-sm-6 col-md-4 col-xl-2">
                            <label class="form-label small text-muted mb-0 d-block" for="lb-filter-submit">&nbsp;</label>
                            <button type="submit" id="lb-filter-submit" class="btn btn-sm btn-primary w-100 text-nowrap">
                                <i class="fas fa-search me-1"></i>بحث
                            </button>
                        </div>
                        <div class="col-12 col-sm-6 col-md-4 col-xl-2">
                            <label class="form-label small text-muted mb-0 d-block" for="leave-balances-filter-clear">&nbsp;</label>
                            <button type="button" class="btn btn-sm btn-outline-secondary w-100 text-nowrap" id="leave-balances-filter-clear">إلغاء الفلترة</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card position-relative" id="leave-balances-table-card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0">قائمة الأرصدة (<span id="leave-balances-total">{{ $leaveBalances->total() }}</span>)</h5>
                    <span id="leave-balances-loading" class="spinner-border spinner-border-sm text-primary d-none" role="status" aria-hidden="true"></span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle table-nowrap mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>الموظف</th>
                                    <th>نوع الإجازة</th>
                                    <th>السنة</th>
                                    <th class="text-center">إجمالي الأيام</th>
                                    <th class="text-center">المستخدم</th>
                                    <th class="text-center">المتبقي</th>
                                    <th class="text-center">المحمل</th>
                                    <th>العمليات</th>
                                </tr>
                            </thead>
                            <tbody id="leave-balances-table-body">
                                @include('admin.pages.leave-balances._index_rows', ['leaveBalances' => $leaveBalances])
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3" id="leave-balances-pagination">
                        @include('admin.pages.leave-balances._index_pagination', ['leaveBalances' => $leaveBalances])
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
<script>
(function () {
    const form = document.getElementById('leave-balances-filter-form');
    const tbody = document.getElementById('leave-balances-table-body');
    const paginationEl = document.getElementById('leave-balances-pagination');
    const totalEl = document.getElementById('leave-balances-total');
    const loadingEl = document.getElementById('leave-balances-loading');

    const jsonHeaders = {
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json',
    };

    function setLoading(on) {
        if (loadingEl) loadingEl.classList.toggle('d-none', !on);
    }

    function loadLeaveBalances(url) {
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
                    history.pushState({ leaveBalancesAjax: true }, '', u.pathname + u.search);
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

    const clearBtn = document.getElementById('leave-balances-filter-clear');
    if (clearBtn && form) {
        clearBtn.addEventListener('click', function () {
            form.querySelectorAll('select').forEach(function (sel) {
                sel.selectedIndex = 0;
            });
            loadLeaveBalances(form.getAttribute('action'));
        });
    }

    if (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            loadLeaveBalances(filterUrlPageOne());
        });

        form.querySelectorAll('select').forEach(function (sel) {
            sel.addEventListener('change', function () {
                loadLeaveBalances(filterUrlPageOne());
            });
        });
    }

    document.addEventListener('click', function (e) {
        const pagLink = e.target.closest('#leave-balances-pagination a[href]');
        if (!pagLink) return;
        const href = pagLink.getAttribute('href');
        if (!href || href === '#') return;
        e.preventDefault();
        loadLeaveBalances(href);
    });

    window.addEventListener('popstate', function () {
        window.location.reload();
    });
})();
</script>
@stop
