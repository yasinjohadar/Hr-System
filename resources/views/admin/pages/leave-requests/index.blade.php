@extends('admin.layouts.master')

@section('page-title')
    قائمة طلبات الإجازات
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
                    <h5 class="page-title fs-21 mb-1">كافة طلبات الإجازات</h5>
                </div>
                <div class="mt-2 mt-md-0">
                    @can('leave-request-create')
                        <a href="{{ route('admin.leave-requests.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus me-1"></i>إضافة طلب إجازة جديد
                        </a>
                    @endcan
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-body py-3">
                    <form id="leave-requests-filter-form" method="GET" action="{{ route('admin.leave-requests.index') }}"
                        class="row g-2 align-items-end">
                        <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                            <label class="form-label small text-muted mb-0" for="lr-filter-employee">الموظف</label>
                            <select name="employee_id" id="lr-filter-employee" class="form-select form-select-sm">
                                <option value="">كل الموظفين</option>
                                @foreach ($employees as $employee)
                                    <option value="{{ $employee->id }}"
                                        {{ request()->filled('employee_id') && (string) request('employee_id') === (string) $employee->id ? 'selected' : '' }}>
                                        {{ $employee->full_name ?? $employee->first_name . ' ' . $employee->last_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                            <label class="form-label small text-muted mb-0" for="lr-filter-type">نوع الإجازة</label>
                            <select name="leave_type_id" id="lr-filter-type" class="form-select form-select-sm">
                                <option value="">كل الأنواع</option>
                                @foreach ($leaveTypes as $type)
                                    <option value="{{ $type->id }}"
                                        {{ request()->filled('leave_type_id') && (string) request('leave_type_id') === (string) $type->id ? 'selected' : '' }}>
                                        {{ $type->name_ar ?? $type->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-sm-6 col-md-4 col-xl-2">
                            <label class="form-label small text-muted mb-0" for="lr-filter-status">الحالة</label>
                            <select name="status" id="lr-filter-status" class="form-select form-select-sm">
                                <option value="">كل الحالات</option>
                                <option value="pending" {{ request()->filled('status') && request('status') === 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                                <option value="approved" {{ request()->filled('status') && request('status') === 'approved' ? 'selected' : '' }}>موافق عليه</option>
                                <option value="rejected" {{ request()->filled('status') && request('status') === 'rejected' ? 'selected' : '' }}>مرفوض</option>
                                <option value="cancelled" {{ request()->filled('status') && request('status') === 'cancelled' ? 'selected' : '' }}>ملغي</option>
                            </select>
                        </div>
                        <div class="col-12 col-sm-6 col-md-4 col-xl-2">
                            <label class="form-label small text-muted mb-0 d-block" for="leave-requests-filter-clear">&nbsp;</label>
                            <button type="button" class="btn btn-sm btn-outline-secondary w-100 text-nowrap"
                                id="leave-requests-filter-clear">إلغاء الفلترة</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card position-relative" id="leave-requests-table-card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0">قائمة الطلبات (<span id="leave-requests-total">{{ $leaveRequests->total() }}</span>)</h5>
                    <span id="leave-requests-loading" class="spinner-border spinner-border-sm text-primary d-none" role="status" aria-hidden="true"></span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle table-nowrap mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>الموظف</th>
                                    <th>نوع الإجازة</th>
                                    <th>من تاريخ</th>
                                    <th>إلى تاريخ</th>
                                    <th>عدد الأيام</th>
                                    <th>الحالة</th>
                                    <th>العمليات</th>
                                </tr>
                            </thead>
                            <tbody id="leave-requests-table-body">
                                @include('admin.pages.leave-requests._index_rows', ['leaveRequests' => $leaveRequests])
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3" id="leave-requests-pagination">
                        @include('admin.pages.leave-requests._index_pagination', ['leaveRequests' => $leaveRequests])
                    </div>
                </div>
            </div>

        </div>
    </div>
@stop

@section('js')
<script>
(function () {
    const form = document.getElementById('leave-requests-filter-form');
    const tbody = document.getElementById('leave-requests-table-body');
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

    function loadLeaveRequests(url) {
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
@stop
