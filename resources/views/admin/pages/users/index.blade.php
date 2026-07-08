@extends('admin.layouts.master')

@section('page-title')
    قائمة المستخدمين
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            @include('admin.pages.users.partials.alerts')

            <div class="admin-page-banner">
                <div class="admin-page-banner-main">
                    <span class="admin-page-banner-icon"><i class="ri-group-line"></i></span>
                    <div class="admin-page-banner-text">
                        <h1>كافة المستخدمين</h1>
                        <p>إدارة حسابات النظام والأدوار والصلاحيات</p>
                    </div>
                </div>
                @can('user-create')
                    <div class="admin-page-banner-actions">
                        <a href="{{ route('users.create') }}" class="admin-btn admin-btn-light">
                            <i class="ri-user-add-line"></i>
                            إنشاء مستخدم جديد
                        </a>
                    </div>
                @endcan
            </div>

            <div class="admin-report-stats admin-report-stats-4 admin-users-stats">
                <div class="admin-report-stat admin-report-stat-static admin-report-stat-blue">
                    <span class="admin-report-stat-icon"><i class="ri-team-line"></i></span>
                    <span class="admin-report-stat-label">إجمالي المستخدمين</span>
                    <span class="admin-report-stat-value" style="color:#2563eb;">{{ $userStats['total'] ?? 0 }}</span>
                </div>
                <div class="admin-report-stat admin-report-stat-static admin-report-stat-green">
                    <span class="admin-report-stat-icon"><i class="ri-user-follow-line"></i></span>
                    <span class="admin-report-stat-label">تفعيل الدخول نشط</span>
                    <span class="admin-report-stat-value" style="color:#059669;">{{ $userStats['active_login'] ?? 0 }}</span>
                </div>
                <div class="admin-report-stat admin-report-stat-static admin-report-stat-amber">
                    <span class="admin-report-stat-icon"><i class="ri-user-unfollow-line"></i></span>
                    <span class="admin-report-stat-label">حسابات موقوفة</span>
                    <span class="admin-report-stat-value" style="color:#d97706;">{{ $userStats['inactive_status'] ?? 0 }}</span>
                </div>
                <div class="admin-report-stat admin-report-stat-static admin-report-stat-red">
                    <span class="admin-report-stat-icon"><i class="ri-forbid-line"></i></span>
                    <span class="admin-report-stat-label">محظورون</span>
                    <span class="admin-report-stat-value" style="color:#dc2626;">{{ $userStats['banned'] ?? 0 }}</span>
                </div>
            </div>

            <div class="admin-page-card">
                <div class="card-toolbar">
                    <form id="users-filter-form" action="{{ route('users.index') }}" method="GET" class="admin-filters w-100">
                        <div class="search-input-wrap">
                            <i class="ri-search-line"></i>
                            <input type="text" name="query" class="form-control"
                                   placeholder="بحث بالاسم أو الإيميل أو الهاتف..."
                                   value="{{ request('query') }}" autocomplete="off">
                        </div>

                        <select name="is_active" class="form-select" style="width: auto; min-width: 160px;">
                            <option value="">كل تفعيل الدخول</option>
                            <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>دخول مفعّل</option>
                            <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>دخول معطّل</option>
                        </select>

                        <select name="status" class="form-select" style="width: auto; min-width: 150px;">
                            <option value="">كل حالات الحساب</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>مفعل</option>
                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>موقوف</option>
                            <option value="banned" {{ request('status') === 'banned' ? 'selected' : '' }}>محظور</option>
                        </select>

                        <button type="button" class="admin-btn admin-btn-danger" data-admin-filter-reset>
                            <i class="ri-filter-off-line"></i>
                            مسح
                        </button>
                    </form>
                </div>

                <div class="admin-table-wrap" id="users-table-wrap">
                    <div class="table-loader">
                        <div class="spinner-border text-primary" role="status"></div>
                    </div>
                    <div class="table-responsive">
                        <table class="admin-data-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>المستخدم</th>
                                    <th>البريد</th>
                                    <th>الهاتف</th>
                                    <th>آخر دخول</th>
                                    <th>الأدوار</th>
                                    <th>حالة الحساب</th>
                                    <th>تفعيل الدخول</th>
                                    <th>العمليات</th>
                                </tr>
                            </thead>
                            <tbody id="users-table-body">
                                @include('admin.partials.users-table-body')
                            </tbody>
                        </table>
                    </div>
                </div>

                <div id="users-ajax-extra">
                    @include('admin.partials.users-table-footer')
                </div>
            </div>
        </div>
    </div>

    @include('admin.pages.users.partials.modals')
@stop

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const loginCodeUrlTemplate = @json(route('admin.users.login-code', ['user' => '__ID__']));

        function setupLoginCodeButtons(root) {
            const modalEl = document.getElementById('loginCodeModal');
            if (!modalEl) return;

            const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
            const codeInput = document.getElementById('loginCodeValue');
            const urlInput = document.getElementById('loginCodeUrl');
            const userNameEl = document.getElementById('loginCodeUserName');
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

            (root || document).querySelectorAll('.login-code-btn').forEach(function (btn) {
                if (btn.dataset.loginBound) return;
                btn.dataset.loginBound = '1';

                btn.addEventListener('click', function (e) {
                    e.preventDefault();
                    const userId = this.dataset.userId;
                    const userName = this.dataset.userName;
                    if (userNameEl) userNameEl.textContent = userName;

                    const url = loginCodeUrlTemplate.replace('__ID__', userId);

                    fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            Accept: 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: JSON.stringify({}),
                    })
                        .then(function (r) { return r.json(); })
                        .then(function (data) {
                            if (data.error) {
                                AdminTables.showToast(data.error, 'error');
                                return;
                            }
                            if (codeInput) codeInput.value = data.code || '';
                            if (urlInput) urlInput.value = data.url || '';
                            modal.show();
                        })
                        .catch(function () {
                            AdminTables.showToast('حدث خطأ أثناء إنشاء الكود', 'error');
                        });
                });
            });
        }

        document.getElementById('copyCodeBtn')?.addEventListener('click', function () {
            const el = document.getElementById('loginCodeValue');
            if (el) navigator.clipboard.writeText(el.value);
        });
        document.getElementById('copyUrlBtn')?.addEventListener('click', function () {
            const el = document.getElementById('loginCodeUrl');
            if (el) navigator.clipboard.writeText(el.value);
        });

        AdminTables.initAjaxTable({
            formSelector: '#users-filter-form',
            bodySelector: '#users-table-body',
            extraSelector: '#users-ajax-extra',
            tableWrapSelector: '#users-table-wrap',
            metaSelector: '#users-table-meta',
            url: @json(route('users.index')),
            toggleOptions: {
                urlTemplate: '/users/:id/toggle-status',
            },
            onLoaded: function () {
                setupLoginCodeButtons(document.getElementById('users-ajax-extra'));
            },
        });

        setupLoginCodeButtons(document);
    });
</script>
@endpush
