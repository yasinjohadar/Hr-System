@extends('admin.layouts.master')

@section('page-title')
    قائمة الحضور والانصراف
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
                    <h5 class="page-title fs-21 mb-1">كافة سجلات الحضور والانصراف</h5>
                </div>
                <div class="mt-2 mt-md-0">
                    @can('attendance-create')
                        <a href="{{ route('admin.attendances.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus me-1"></i>إضافة سجل حضور جديد
                        </a>
                    @endcan
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-body py-3">
                    <form id="attendances-filter-form" method="GET" action="{{ route('admin.attendances.index') }}"
                        class="row g-2 align-items-end"
                        data-default-start="{{ \Carbon\Carbon::now()->subDays(30)->format('Y-m-d') }}"
                        data-default-end="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
                        <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                            <label class="form-label small text-muted mb-0" for="att-filter-employee">الموظف</label>
                            <select name="employee_id" id="att-filter-employee" class="form-select form-select-sm">
                                <option value="">كل الموظفين</option>
                                @foreach ($employees as $employee)
                                    <option value="{{ $employee->id }}" {{ request()->filled('employee_id') && (string) request('employee_id') === (string) $employee->id ? 'selected' : '' }}>
                                        {{ $employee->full_name ?? $employee->first_name . ' ' . $employee->last_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-sm-6 col-md-4 col-xl-2">
                            <label class="form-label small text-muted mb-0" for="att-filter-start">من تاريخ</label>
                            <input type="date" name="start_date" id="att-filter-start" class="form-control form-control-sm"
                                value="{{ request('start_date', $currentStartDate) }}">
                        </div>
                        <div class="col-12 col-sm-6 col-md-4 col-xl-2">
                            <label class="form-label small text-muted mb-0" for="att-filter-end">إلى تاريخ</label>
                            <input type="date" name="end_date" id="att-filter-end" class="form-control form-control-sm"
                                value="{{ request('end_date', $currentEndDate) }}">
                        </div>
                        <div class="col-12 col-sm-6 col-md-4 col-xl-2">
                            <label class="form-label small text-muted mb-0" for="att-filter-status">الحالة</label>
                            <select name="status" id="att-filter-status" class="form-select form-select-sm">
                                <option value="">كل الحالات</option>
                                <option value="present" {{ request()->filled('status') && request('status') === 'present' ? 'selected' : '' }}>حاضر</option>
                                <option value="absent" {{ request()->filled('status') && request('status') === 'absent' ? 'selected' : '' }}>غائب</option>
                                <option value="late" {{ request()->filled('status') && request('status') === 'late' ? 'selected' : '' }}>متأخر</option>
                                <option value="half_day" {{ request()->filled('status') && request('status') === 'half_day' ? 'selected' : '' }}>نصف يوم</option>
                                <option value="on_leave" {{ request()->filled('status') && request('status') === 'on_leave' ? 'selected' : '' }}>في إجازة</option>
                                <option value="holiday" {{ request()->filled('status') && request('status') === 'holiday' ? 'selected' : '' }}>عطلة</option>
                            </select>
                        </div>
                        <div class="col-12 col-sm-6 col-md-4 col-xl-1">
                            <label class="form-label small text-muted mb-0 d-block" for="att-filter-submit">&nbsp;</label>
                            <button type="submit" id="att-filter-submit" class="btn btn-sm btn-primary w-100 text-nowrap">
                                <i class="fas fa-search me-1"></i>بحث
                            </button>
                        </div>
                        <div class="col-12 col-sm-6 col-md-4 col-xl-2">
                            <label class="form-label small text-muted mb-0 d-block" for="attendances-filter-clear">&nbsp;</label>
                            <button type="button" class="btn btn-sm btn-outline-secondary w-100 text-nowrap" id="attendances-filter-clear">إلغاء الفلترة</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card position-relative" id="attendances-table-card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0">سجلات الحضور (<span id="attendances-total">{{ $attendances->total() }}</span>)</h5>
                    <span id="attendances-loading" class="spinner-border spinner-border-sm text-primary d-none" role="status" aria-hidden="true"></span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle table-nowrap mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>الموظف</th>
                                    <th>التاريخ</th>
                                    <th>وقت الدخول</th>
                                    <th>وقت الخروج</th>
                                    <th>ساعات العمل</th>
                                    <th>التأخير</th>
                                    <th>ساعات إضافية</th>
                                    <th>الحالة</th>
                                    <th>العمليات</th>
                                </tr>
                            </thead>
                            <tbody id="attendances-table-body">
                                @include('admin.pages.attendances._index_rows', ['attendances' => $attendances])
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3" id="attendances-pagination">
                        @include('admin.pages.attendances._index_pagination', ['attendances' => $attendances])
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
<script>
(function () {
    const form = document.getElementById('attendances-filter-form');
    const tbody = document.getElementById('attendances-table-body');
    const paginationEl = document.getElementById('attendances-pagination');
    const totalEl = document.getElementById('attendances-total');
    const loadingEl = document.getElementById('attendances-loading');

    const jsonHeaders = {
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json',
    };

    function setLoading(on) {
        if (loadingEl) loadingEl.classList.toggle('d-none', !on);
    }

    function loadAttendances(url) {
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
                    history.pushState({ attendancesAjax: true }, '', u.pathname + u.search);
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

    const clearBtn = document.getElementById('attendances-filter-clear');
    if (clearBtn && form) {
        clearBtn.addEventListener('click', function () {
            const ds = form.getAttribute('data-default-start') || '';
            const de = form.getAttribute('data-default-end') || '';
            form.querySelectorAll('select').forEach(function (sel) {
                sel.selectedIndex = 0;
            });
            const startIn = form.querySelector('[name="start_date"]');
            const endIn = form.querySelector('[name="end_date"]');
            if (startIn) startIn.value = ds;
            if (endIn) endIn.value = de;
            loadAttendances(filterUrlPageOne());
        });
    }

    if (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            loadAttendances(filterUrlPageOne());
        });

        form.querySelectorAll('select').forEach(function (sel) {
            sel.addEventListener('change', function () {
                loadAttendances(filterUrlPageOne());
            });
        });

        ['att-filter-start', 'att-filter-end'].forEach(function (id) {
            const el = document.getElementById(id);
            if (el) {
                el.addEventListener('change', function () {
                    loadAttendances(filterUrlPageOne());
                });
            }
        });
    }

    document.addEventListener('click', function (e) {
        const pagLink = e.target.closest('#attendances-pagination a[href]');
        if (!pagLink) return;
        const href = pagLink.getAttribute('href');
        if (!href || href === '#') return;
        e.preventDefault();
        loadAttendances(href);
    });

    window.addEventListener('popstate', function () {
        window.location.reload();
    });
})();
</script>
@stop
