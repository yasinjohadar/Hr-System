@extends('admin.layouts.master')

@section('page-title')
    قائمة الموظفين
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

    @if (\Session::has('error'))
        <div class="alert alert-danger">
            <ul>
                <li>{!! \Session::get('error') !!}</li>
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
                    <h5 class="page-title fs-21 mb-1">كافة الموظفين</h5>
                </div>
                <div class="d-flex flex-wrap gap-2 mt-2 mt-md-0">
                    @can('employee-create')
                        <a href="{{ route('admin.employees.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus me-1"></i>إضافة موظف جديد
                        </a>
                    @endcan
                    @can('export-data')
                        <a href="{{ route('admin.export.employees') }}" class="btn btn-success btn-sm">
                            <i class="fe fe-download"></i> تصدير Excel
                        </a>
                    @endcan
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-body py-3">
                    <form id="employees-filter-form" method="GET" action="{{ route('admin.employees.index') }}"
                        class="row g-2 align-items-end">
                        <div class="col-12 col-sm-6 col-md-4 col-xl-2">
                            <label class="form-label small text-muted mb-0" for="employees-filter-query">بحث</label>
                            <input type="text" name="query" id="employees-filter-query"
                                class="form-control form-control-sm" placeholder="بحث بالاسم أو الرقم أو البريد"
                                value="{{ request('query') }}" autocomplete="off">
                        </div>
                        <div class="col-12 col-sm-6 col-md-4 col-xl-2">
                            <label class="form-label small text-muted mb-0" for="employees-filter-department">القسم</label>
                            <select name="department_id" id="employees-filter-department" class="form-select form-select-sm">
                                <option value="">كل الأقسام</option>
                                @foreach ($departments as $dept)
                                    <option value="{{ $dept->id }}"
                                        {{ (string) request('department_id') === (string) $dept->id ? 'selected' : '' }}>
                                        {{ $dept->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-sm-6 col-md-4 col-xl-2">
                            <label class="form-label small text-muted mb-0" for="employees-filter-position">المنصب</label>
                            <select name="position_id" id="employees-filter-position" class="form-select form-select-sm">
                                <option value="">كل المناصب</option>
                                @foreach ($positions as $pos)
                                    <option value="{{ $pos->id }}"
                                        {{ (string) request('position_id') === (string) $pos->id ? 'selected' : '' }}>
                                        {{ $pos->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-sm-6 col-md-4 col-xl-2">
                            <label class="form-label small text-muted mb-0" for="employees-filter-employment">الحالة الوظيفية</label>
                            <select name="employment_status" id="employees-filter-employment" class="form-select form-select-sm">
                                <option value="">كل الحالات</option>
                                <option value="active" {{ request('employment_status') == 'active' ? 'selected' : '' }}>نشط</option>
                                <option value="on_leave" {{ request('employment_status') == 'on_leave' ? 'selected' : '' }}>في إجازة</option>
                                <option value="terminated" {{ request('employment_status') == 'terminated' ? 'selected' : '' }}>منتهي</option>
                                <option value="resigned" {{ request('employment_status') == 'resigned' ? 'selected' : '' }}>استقال</option>
                            </select>
                        </div>
                        <div class="col-12 col-sm-6 col-md-4 col-xl-2">
                            <label class="form-label small text-muted mb-0" for="employees-filter-active">الحالة النشطة</label>
                            <select name="is_active" id="employees-filter-active" class="form-select form-select-sm">
                                <option value="">كل الحالات النشطة</option>
                                <option value="1" {{ request('is_active') === '1' || request('is_active') === 1 ? 'selected' : '' }}>نشط</option>
                                <option value="0" {{ request('is_active') === '0' || request('is_active') === 0 ? 'selected' : '' }}>غير نشط</option>
                            </select>
                        </div>
                        <div class="col-12 col-sm-6 col-md-4 col-xl-2">
                            <label class="form-label small text-muted mb-0 d-block" for="employees-filter-clear">&nbsp;</label>
                            <button type="button" class="btn btn-sm btn-outline-secondary w-100 text-nowrap"
                                id="employees-filter-clear">إلغاء الفلترة</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card position-relative" id="employees-table-card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0">قائمة الموظفين (<span id="employees-total">{{ $employees->total() }}</span>)</h5>
                    <span id="employees-loading" class="spinner-border spinner-border-sm text-primary d-none" role="status" aria-hidden="true"></span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle table-nowrap mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col" style="width: 40px;">#</th>
                                    <th scope="col" style="min-width: 120px;">رقم الموظف</th>
                                    <th scope="col" style="min-width: 200px;">الاسم</th>
                                    <th scope="col" style="min-width: 150px;">القسم</th>
                                    <th scope="col" style="min-width: 150px;">المنصب</th>
                                    <th scope="col" style="min-width: 120px;">البريد</th>
                                    <th scope="col" style="min-width: 120px;">الهاتف</th>
                                    <th scope="col" style="min-width: 120px;">تاريخ التوظيف</th>
                                    <th scope="col" style="min-width: 110px;">الحالة</th>
                                    <th scope="col" style="min-width: 120px;">الحالة النشطة</th>
                                    <th scope="col" style="min-width: 200px;">العمليات</th>
                                </tr>
                            </thead>
                            <tbody id="employees-table-body">
                                @include('admin.pages.employees._index_rows', ['employees' => $employees])
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3" id="employees-pagination">
                        @include('admin.pages.employees._index_pagination', ['employees' => $employees])
                    </div>
                </div>
            </div>

        </div>
    </div>
@stop

@section('js')
<script>
(function () {
    const form = document.getElementById('employees-filter-form');
    const tbody = document.getElementById('employees-table-body');
    const paginationEl = document.getElementById('employees-pagination');
    const totalEl = document.getElementById('employees-total');
    const loadingEl = document.getElementById('employees-loading');
    const queryInput = document.getElementById('employees-filter-query');

    const jsonHeaders = {
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json',
    };

    function setLoading(on) {
        if (loadingEl) loadingEl.classList.toggle('d-none', !on);
    }

    function loadEmployees(url) {
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
                    history.pushState({ employeesAjax: true }, '', u.pathname + u.search);
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

    const clearBtn = document.getElementById('employees-filter-clear');
    if (clearBtn && form && queryInput) {
        clearBtn.addEventListener('click', function () {
            clearTimeout(queryDebounce);
            queryInput.value = '';
            form.querySelectorAll('select').forEach(function (sel) {
                sel.selectedIndex = 0;
            });
            loadEmployees(form.getAttribute('action'));
        });
    }

    if (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            loadEmployees(filterUrlPageOne());
        });

        form.querySelectorAll('select').forEach(function (sel) {
            sel.addEventListener('change', function () {
                loadEmployees(filterUrlPageOne());
            });
        });
    }

    let queryDebounce;
    if (queryInput && form) {
        queryInput.addEventListener('input', function () {
            clearTimeout(queryDebounce);
            queryDebounce = setTimeout(function () {
                loadEmployees(filterUrlPageOne());
            }, 380);
        });
    }

    document.addEventListener('click', function (e) {
        const pagLink = e.target.closest('#employees-pagination a[href]');
        if (!pagLink) return;
        const href = pagLink.getAttribute('href');
        if (!href || href === '#') return;
        e.preventDefault();
        loadEmployees(href);
    });

    window.addEventListener('popstate', function () {
        window.location.reload();
    });
})();
</script>
@stop
