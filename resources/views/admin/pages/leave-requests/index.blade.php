@extends('admin.layouts.master')

@section('page-title')
    قائمة طلبات الإجازات
@stop

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin-leave-requests.css') }}">
@endpush

@section('content')
    <div class="main-content app-content admin-leave-requests-page">
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
                                <i class="ri-sun-line"></i>
                            </div>
                            <div>
                                <h4 class="mb-1 page-hero-title fw-bold">طلبات الإجازات</h4>
                                <p class="mb-0 page-hero-subtitle">متابعة وموافقة طلبات إجازات الموظفين</p>
                            </div>
                        </div>
                        @can('leave-request-create')
                            <a href="{{ route('admin.leave-requests.create') }}" class="btn btn-hero-primary btn-sm">
                                <i class="ri-add-line me-1"></i>إضافة طلب إجازة
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
                                <div class="stat-label">إجمالي الطلبات</div>
                            </div>
                            <div class="stat-icon stat-icon--primary"><i class="ri-file-list-3-line"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-value" data-stat="pending">{{ $stats['pending'] }}</div>
                                <div class="stat-label">قيد الانتظار</div>
                            </div>
                            <div class="stat-icon stat-icon--warning"><i class="ri-time-line"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-value" data-stat="approved">{{ $stats['approved'] }}</div>
                                <div class="stat-label">موافق عليها</div>
                            </div>
                            <div class="stat-icon stat-icon--success"><i class="ri-checkbox-circle-line"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-value" data-stat="rejected">{{ $stats['rejected'] }}</div>
                                <div class="stat-label">مرفوضة</div>
                            </div>
                            <div class="stat-icon stat-icon--danger"><i class="ri-close-circle-line"></i></div>
                        </div>
                    </div>
                </div>
            </div>

            <form id="leave-requests-filter-form" method="GET" action="{{ route('admin.leave-requests.index') }}" class="filters-panel">
                <div class="row g-3 align-items-end">
                    <div class="col-md-6 col-lg-4">
                        <label class="form-label" for="lr-filter-employee">الموظف</label>
                        <select name="employee_id" id="lr-filter-employee" class="form-select">
                            <option value="">كل الموظفين</option>
                            @foreach ($employees as $employee)
                                <option value="{{ $employee->id }}"
                                    {{ request()->filled('employee_id') && (string) request('employee_id') === (string) $employee->id ? 'selected' : '' }}>
                                    {{ $employee->full_name ?? $employee->first_name . ' ' . $employee->last_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <label class="form-label" for="lr-filter-type">نوع الإجازة</label>
                        <select name="leave_type_id" id="lr-filter-type" class="form-select">
                            <option value="">كل الأنواع</option>
                            @foreach ($leaveTypes as $type)
                                <option value="{{ $type->id }}"
                                    {{ request()->filled('leave_type_id') && (string) request('leave_type_id') === (string) $type->id ? 'selected' : '' }}>
                                    {{ $type->name_ar ?? $type->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <label class="form-label" for="lr-filter-status">الحالة</label>
                        <select name="status" id="lr-filter-status" class="form-select">
                            <option value="">كل الحالات</option>
                            <option value="pending" {{ request()->filled('status') && request('status') === 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                            <option value="approved" {{ request()->filled('status') && request('status') === 'approved' ? 'selected' : '' }}>موافق عليه</option>
                            <option value="rejected" {{ request()->filled('status') && request('status') === 'rejected' ? 'selected' : '' }}>مرفوض</option>
                            <option value="cancelled" {{ request()->filled('status') && request('status') === 'cancelled' ? 'selected' : '' }}>ملغي</option>
                        </select>
                    </div>
                    <div class="col-md-6 col-lg-2">
                        <label class="form-label d-block" for="leave-requests-filter-clear">&nbsp;</label>
                        <button type="button" class="btn btn-filter-clear" id="leave-requests-filter-clear">إلغاء الفلترة</button>
                    </div>
                </div>
            </form>

            <div class="content-panel" id="leave-requests-table-card">
                <div class="content-panel-header">
                    <h5>قائمة الطلبات (<span id="leave-requests-total">{{ $leaveRequests->total() }}</span>)</h5>
                    <span id="leave-requests-loading" class="spinner-border spinner-border-sm text-primary d-none" role="status" aria-hidden="true"></span>
                </div>

                <div class="leave-table-scroll">
                    <div class="leave-table-header">
                        <span>#</span>
                        <span>الموظف</span>
                        <span>نوع الإجازة</span>
                        <span>من تاريخ</span>
                        <span>إلى تاريخ</span>
                        <span>عدد الأيام</span>
                        <span>الحالة</span>
                        <span class="text-end">العمليات</span>
                    </div>
                    <div id="leave-requests-list-body">
                        @include('admin.pages.leave-requests._index_rows', [
                            'leaveRequests' => $leaveRequests,
                            'canApproveNowById' => $canApproveNowById ?? [],
                            'workflowProgressById' => $workflowProgressById ?? [],
                        ])
                    </div>
                </div>

                <div class="pagination-wrap d-flex justify-content-center" id="leave-requests-pagination">
                    @include('admin.pages.leave-requests._index_pagination', ['leaveRequests' => $leaveRequests])
                </div>
            </div>

        </div>
    </div>
@stop

@push('scripts')
<script>
(function () {
    const form = document.getElementById('leave-requests-filter-form');
    const listBody = document.getElementById('leave-requests-list-body');
    const paginationEl = document.getElementById('leave-requests-pagination');
    const totalEl = document.getElementById('leave-requests-total');
    const loadingEl = document.getElementById('leave-requests-loading');

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

    function loadLeaveRequests(url) {
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
                try {
                    const u = new URL(absoluteUrl);
                    history.pushState({ leaveRequestsAjax: true }, '', u.pathname + u.search);
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

    const clearBtn = document.getElementById('leave-requests-filter-clear');
    if (clearBtn && form) {
        clearBtn.addEventListener('click', function () {
            form.querySelectorAll('select').forEach(function (sel) {
                sel.selectedIndex = 0;
            });
            loadLeaveRequests(form.getAttribute('action'));
        });
    }

    if (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            loadLeaveRequests(filterUrlPageOne());
        });

        form.querySelectorAll('select').forEach(function (sel) {
            sel.addEventListener('change', function () {
                loadLeaveRequests(filterUrlPageOne());
            });
        });
    }

    document.addEventListener('click', function (e) {
        const pagLink = e.target.closest('#leave-requests-pagination a[href]');
        if (!pagLink) return;
        const href = pagLink.getAttribute('href');
        if (!href || href === '#') return;
        e.preventDefault();
        loadLeaveRequests(href);
    });

    window.addEventListener('popstate', function () {
        window.location.reload();
    });
})();
</script>
@endpush
