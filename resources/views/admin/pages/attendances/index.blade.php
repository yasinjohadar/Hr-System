@extends('admin.layouts.master')

@section('page-title')
    قائمة الحضور والانصراف
@stop

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin-attendances.css') }}">
@endpush

@section('content')
    <div class="main-content app-content admin-attendances-page">
        <div class="container-fluid pt-4">

            @if (\Session::has('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {!! \Session::get('success') !!}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card page-hero mb-4">
                <div class="card-body py-4">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="page-hero-icon">
                                <i class="ri-calendar-check-line"></i>
                            </div>
                            <div>
                                <h4 class="mb-1 page-hero-title fw-bold">الحضور والانصراف</h4>
                                <p class="mb-0 page-hero-subtitle">سجلات حضور الموظفين والفلترة حسب الفترة</p>
                            </div>
                        </div>
                        @can('attendance-create')
                            <a href="{{ route('admin.attendances.create') }}" class="btn btn-hero-primary btn-sm">
                                <i class="ri-add-line me-1"></i>إضافة سجل حضور
                            </a>
                        @endcan
                    </div>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-value" data-stat="total">{{ $stats['total'] }}</div>
                                <div class="stat-label">إجمالي السجلات</div>
                            </div>
                            <div class="stat-icon stat-icon--primary"><i class="ri-file-list-3-line"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-value" data-stat="present">{{ $stats['present'] }}</div>
                                <div class="stat-label">حاضر</div>
                            </div>
                            <div class="stat-icon stat-icon--success"><i class="ri-user-check-line"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-value" data-stat="absent">{{ $stats['absent'] }}</div>
                                <div class="stat-label">غائب</div>
                            </div>
                            <div class="stat-icon stat-icon--danger"><i class="ri-user-unfollow-line"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-value" data-stat="late">{{ $stats['late'] }}</div>
                                <div class="stat-label">متأخر</div>
                            </div>
                            <div class="stat-icon stat-icon--warning"><i class="ri-time-line"></i></div>
                        </div>
                    </div>
                </div>
            </div>

            <form id="attendances-filter-form" method="GET" action="{{ route('admin.attendances.index') }}" class="filters-panel"
                data-default-start="{{ \Carbon\Carbon::now()->subDays(30)->format('Y-m-d') }}"
                data-default-end="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
                <div class="row g-3 align-items-end">
                    <div class="col-md-6 col-lg-3">
                        <label class="form-label" for="att-filter-employee">الموظف</label>
                        <select name="employee_id" id="att-filter-employee" class="form-select">
                            <option value="">كل الموظفين</option>
                            @foreach ($employees as $employee)
                                <option value="{{ $employee->id }}" {{ request()->filled('employee_id') && (string) request('employee_id') === (string) $employee->id ? 'selected' : '' }}>
                                    {{ $employee->full_name ?? $employee->first_name . ' ' . $employee->last_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6 col-lg-2">
                        <label class="form-label" for="att-filter-start">من تاريخ</label>
                        <input type="date" name="start_date" id="att-filter-start" class="form-control"
                            value="{{ request('start_date', $currentStartDate) }}">
                    </div>
                    <div class="col-6 col-lg-2">
                        <label class="form-label" for="att-filter-end">إلى تاريخ</label>
                        <input type="date" name="end_date" id="att-filter-end" class="form-control"
                            value="{{ request('end_date', $currentEndDate) }}">
                    </div>
                    <div class="col-md-6 col-lg-2">
                        <label class="form-label" for="att-filter-status">الحالة</label>
                        <select name="status" id="att-filter-status" class="form-select">
                            <option value="">كل الحالات</option>
                            <option value="present" {{ request()->filled('status') && request('status') === 'present' ? 'selected' : '' }}>حاضر</option>
                            <option value="absent" {{ request()->filled('status') && request('status') === 'absent' ? 'selected' : '' }}>غائب</option>
                            <option value="late" {{ request()->filled('status') && request('status') === 'late' ? 'selected' : '' }}>متأخر</option>
                            <option value="half_day" {{ request()->filled('status') && request('status') === 'half_day' ? 'selected' : '' }}>نصف يوم</option>
                            <option value="on_leave" {{ request()->filled('status') && request('status') === 'on_leave' ? 'selected' : '' }}>في إجازة</option>
                            <option value="holiday" {{ request()->filled('status') && request('status') === 'holiday' ? 'selected' : '' }}>عطلة</option>
                        </select>
                    </div>
                    <div class="col-6 col-lg-auto">
                        <label class="form-label d-block">&nbsp;</label>
                        <button type="submit" id="att-filter-submit" class="btn btn-filter-submit">
                            <i class="ri-search-line me-1"></i>بحث
                        </button>
                    </div>
                    <div class="col-6 col-lg-auto">
                        <label class="form-label d-block">&nbsp;</label>
                        <button type="button" class="btn btn-filter-clear" id="attendances-filter-clear">إلغاء الفلترة</button>
                    </div>
                </div>
            </form>

            <div class="content-panel" id="attendances-table-card">
                <div class="content-panel-header">
                    <span>سجلات الحضور (<span id="attendances-total">{{ $attendances->total() }}</span>)</span>
                    <span id="attendances-loading" class="spinner-border spinner-border-sm text-primary d-none" role="status" aria-hidden="true"></span>
                </div>

                <div class="att-table-scroll">
                    <div class="att-table-header">
                        <span>#</span>
                        <span>الموظف</span>
                        <span>التاريخ</span>
                        <span>دخول</span>
                        <span>خروج</span>
                        <span>ساعات</span>
                        <span>تأخير</span>
                        <span>إضافي</span>
                        <span>الحالة</span>
                        <span class="text-end">إجراء</span>
                    </div>
                    <div id="attendances-list-body">
                        @include('admin.pages.attendances._index_rows', ['attendances' => $attendances])
                    </div>
                </div>

                <div class="pagination-wrap d-flex justify-content-center" id="attendances-pagination">
                    @include('admin.pages.attendances._index_pagination', ['attendances' => $attendances])
                </div>
            </div>

        </div>
    </div>
@stop

@push('scripts')
<script>
(function () {
    const form = document.getElementById('attendances-filter-form');
    const listBody = document.getElementById('attendances-list-body');
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

    function updateStats(stats) {
        if (!stats) return;
        Object.keys(stats).forEach(function (key) {
            const el = document.querySelector('[data-stat="' + key + '"]');
            if (el) el.textContent = stats[key];
        });
    }

    function loadAttendances(url) {
        const fetchUrl = new URL(url, window.location.origin);
        fetchUrl.searchParams.set('ajax', '1');
        setLoading(true);
        fetch(fetchUrl.href, {
            method: 'GET',
            headers: jsonHeaders,
            credentials: 'same-origin',
        })
            .then(function (r) {
                if (!r.ok) throw new Error('Network error');
                return r.json();
            })
            .then(function (data) {
                listBody.innerHTML = data.html_rows;
                paginationEl.innerHTML = data.html_pagination;
                totalEl.textContent = data.total;
                updateStats(data.stats);
                const cleanUrl = new URL(fetchUrl);
                cleanUrl.searchParams.delete('ajax');
                history.pushState({ attendancesAjax: true }, '', cleanUrl.pathname + cleanUrl.search);
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
@endpush
