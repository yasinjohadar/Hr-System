@extends('admin.layouts.master')

@section('page-title')
    إدارة الأصول
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">إدارة الأصول</h5>
                </div>
                <div>
                    @can('asset-create')
                    <a href="{{ route('admin.assets.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>إضافة أصل جديد
                    </a>
                    @endcan
                </div>
            </div>

            <!-- فلترة: صف واحد على الشاشات العريضة، تفاف تلقائي على الجوال -->
            <div class="card mb-3">
                <div class="card-body py-3">
                    <form id="assets-filter-form" method="GET" action="{{ route('admin.assets.index') }}" class="row g-2 align-items-end">
                        <div class="col-12 col-sm-6 col-md-4 col-xl-2">
                            <label class="form-label small text-muted mb-0" for="assets-filter-query">بحث</label>
                            <input type="text" name="query" id="assets-filter-query" class="form-control form-control-sm" placeholder="بحث..." value="{{ request('query') }}" autocomplete="off">
                        </div>
                        <div class="col-12 col-sm-6 col-md-4 col-xl-2">
                            <label class="form-label small text-muted mb-0" for="assets-filter-status">الحالة</label>
                            <select name="status" id="assets-filter-status" class="form-select form-select-sm">
                                <option value="">كل الحالات</option>
                                <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>متاح</option>
                                <option value="assigned" {{ request('status') == 'assigned' ? 'selected' : '' }}>موزع</option>
                                <option value="maintenance" {{ request('status') == 'maintenance' ? 'selected' : '' }}>قيد الصيانة</option>
                                <option value="damaged" {{ request('status') == 'damaged' ? 'selected' : '' }}>معطل</option>
                                <option value="lost" {{ request('status') == 'lost' ? 'selected' : '' }}>مفقود</option>
                                <option value="disposed" {{ request('status') == 'disposed' ? 'selected' : '' }}>مستبعد</option>
                            </select>
                        </div>
                        <div class="col-12 col-sm-6 col-md-4 col-xl-2">
                            <label class="form-label small text-muted mb-0" for="assets-filter-category">الفئة</label>
                            <select name="category" id="assets-filter-category" class="form-select form-select-sm">
                                <option value="">كل الفئات</option>
                                <option value="technical" {{ request('category') == 'technical' ? 'selected' : '' }}>تقني</option>
                                <option value="office" {{ request('category') == 'office' ? 'selected' : '' }}>مكتبي</option>
                                <option value="mobile" {{ request('category') == 'mobile' ? 'selected' : '' }}>متنقل</option>
                                <option value="other" {{ request('category') == 'other' ? 'selected' : '' }}>أخرى</option>
                            </select>
                        </div>
                        <div class="col-12 col-sm-6 col-md-4 col-xl-2">
                            <label class="form-label small text-muted mb-0" for="assets-filter-branch">الفرع</label>
                            <select name="branch_id" id="assets-filter-branch" class="form-select form-select-sm">
                                <option value="">كل الفروع</option>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}" {{ (string) request('branch_id') === (string) $branch->id ? 'selected' : '' }}>
                                        {{ $branch->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-sm-6 col-md-4 col-xl-2">
                            <label class="form-label small text-muted mb-0" for="assets-filter-department">القسم</label>
                            <select name="department_id" id="assets-filter-department" class="form-select form-select-sm">
                                <option value="">كل الأقسام</option>
                                @foreach ($departments as $department)
                                    <option value="{{ $department->id }}" {{ (string) request('department_id') === (string) $department->id ? 'selected' : '' }}>
                                        {{ $department->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-sm-6 col-md-4 col-xl-2">
                            <label class="form-label small text-muted mb-0 d-block" for="assets-filter-clear">&nbsp;</label>
                            <button type="button" class="btn btn-sm btn-outline-secondary w-100 text-nowrap" id="assets-filter-clear">إلغاء الفلترة</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- جدول الأصول -->
            <div class="card position-relative" id="assets-table-card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0">قائمة الأصول (<span id="assets-total">{{ $assets->total() }}</span>)</h5>
                    <span id="assets-loading" class="spinner-border spinner-border-sm text-primary d-none" role="status" aria-hidden="true"></span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>كود الأصل</th>
                                    <th>الاسم</th>
                                    <th>الفئة</th>
                                    <th>الشركة/الموديل</th>
                                    <th>الرقم التسلسلي</th>
                                    <th>الموقع</th>
                                    <th>الحالة</th>
                                    <th>الموظف</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody id="assets-table-body">
                                @include('admin.pages.assets._index_rows', ['assets' => $assets])
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3" id="assets-pagination">
                        @include('admin.pages.assets._index_pagination', ['assets' => $assets])
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal حذف -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="" id="deleteForm">
                    @csrf
                    @method('DELETE')
                    <div class="modal-header">
                        <h5 class="modal-title">تأكيد الحذف</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>هل أنت متأكد من حذف هذا الأصل؟</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-danger">حذف</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('js')
<script>
(function () {
    const form = document.getElementById('assets-filter-form');
    const tbody = document.getElementById('assets-table-body');
    const paginationEl = document.getElementById('assets-pagination');
    const totalEl = document.getElementById('assets-total');
    const loadingEl = document.getElementById('assets-loading');
    const queryInput = document.getElementById('assets-filter-query');

    const jsonHeaders = {
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json',
    };

    function setLoading(on) {
        if (loadingEl) loadingEl.classList.toggle('d-none', !on);
    }

    function loadAssets(url) {
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
                    history.pushState({ assetsAjax: true }, '', u.pathname + u.search);
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

    const clearBtn = document.getElementById('assets-filter-clear');
    if (clearBtn && form && queryInput) {
        clearBtn.addEventListener('click', function () {
            clearTimeout(queryDebounce);
            queryInput.value = '';
            form.querySelectorAll('select').forEach(function (sel) {
                sel.selectedIndex = 0;
            });
            loadAssets(form.getAttribute('action'));
        });
    }

    if (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            loadAssets(filterUrlPageOne());
        });

        form.querySelectorAll('select').forEach(function (sel) {
            sel.addEventListener('change', function () {
                loadAssets(filterUrlPageOne());
            });
        });
    }

    let queryDebounce;
    if (queryInput && form) {
        queryInput.addEventListener('input', function () {
            clearTimeout(queryDebounce);
            queryDebounce = setTimeout(function () {
                loadAssets(filterUrlPageOne());
            }, 380);
        });
    }

    document.addEventListener('click', function (e) {
        const pagLink = e.target.closest('#assets-pagination a[href]');
        if (!pagLink) return;
        const href = pagLink.getAttribute('href');
        if (!href || href === '#') return;
        e.preventDefault();
        loadAssets(href);
    });

    document.getElementById('assets-table-body').addEventListener('click', function (e) {
        const btn = e.target.closest('.delete-btn');
        if (!btn) return;
        const id = btn.getAttribute('data-id');
        const deleteUrl = "{{ url('admin/assets') }}/" + id;
        document.getElementById('deleteForm').action = deleteUrl;
        new bootstrap.Modal(document.getElementById('deleteModal')).show();
    });

    window.addEventListener('popstate', function () {
        window.location.reload();
    });
})();
</script>
@stop
