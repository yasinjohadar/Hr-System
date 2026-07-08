@extends('admin.layouts.master')

@section('page-title')
    رؤساء الأقسام
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid admin-page-shell">
            @include('admin.pages.users.partials.alerts')

            <div class="admin-page-banner">
                <div class="admin-page-banner-main">
                    <span class="admin-page-banner-icon"><i class="ri-user-star-line"></i></span>
                    <div class="admin-page-banner-text">
                        <h1>رؤساء الأقسام</h1>
                        <p>إدارة حسابات رؤساء الأقسام وتعيين الأقسام والصلاحيات</p>
                    </div>
                </div>
                @can('department-head-manage')
                    <div class="admin-page-banner-actions">
                        <a href="{{ route('admin.department-heads.create') }}" class="admin-btn admin-btn-light">
                            <i class="ri-user-add-line"></i>
                            تعيين رئيس قسم
                        </a>
                    </div>
                @endcan
            </div>

            <div class="admin-report-stats admin-report-stats-4 mb-4">
                <div class="admin-report-stat admin-report-stat-static admin-report-stat-blue">
                    <span class="admin-report-stat-icon"><i class="ri-user-star-line"></i></span>
                    <span class="admin-report-stat-label">إجمالي رؤساء الأقسام</span>
                    <span class="admin-report-stat-value" style="color:#2563eb;">{{ $stats['total'] }}</span>
                </div>
                <div class="admin-report-stat admin-report-stat-static admin-report-stat-green">
                    <span class="admin-report-stat-icon"><i class="ri-team-line"></i></span>
                    <span class="admin-report-stat-label">معيّنون على أقسام</span>
                    <span class="admin-report-stat-value" style="color:#059669;">{{ $stats['with_departments'] }}</span>
                </div>
                <div class="admin-report-stat admin-report-stat-static admin-report-stat-amber">
                    <span class="admin-report-stat-icon"><i class="ri-building-line"></i></span>
                    <span class="admin-report-stat-label">أقسام نشطة بلا مدير</span>
                    <span class="admin-report-stat-value" style="color:#d97706;">{{ $stats['unassigned_departments'] }}</span>
                </div>
            </div>

            <div class="admin-page-card">
                <div class="card-toolbar">
                    <form action="{{ route('admin.department-heads.index') }}" method="GET" class="admin-filters w-100">
                        <div class="search-input-wrap">
                            <i class="ri-search-line"></i>
                            <input type="text" name="search" class="form-control"
                                   placeholder="بحث بالاسم، البريد، أو رمز الموظف"
                                   value="{{ request('search') }}" autocomplete="off">
                        </div>
                        <select name="status" class="form-select admin-filter-select">
                            <option value="">كل الحالات</option>
                            <option value="active" @selected(request('status') === 'active')>نشط</option>
                            <option value="inactive" @selected(request('status') === 'inactive')>غير نشط</option>
                        </select>
                        <button type="submit" class="admin-btn admin-btn-primary">
                            <i class="ri-search-line"></i>
                            بحث
                        </button>
                        <a href="{{ route('admin.department-heads.index') }}" class="admin-btn admin-btn-danger">
                            <i class="ri-filter-off-line"></i>
                            مسح
                        </a>
                    </form>
                </div>

                <div class="admin-table-wrap">
                    <div class="table-responsive">
                        <table class="admin-data-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>المستخدم</th>
                                    <th>الموظف</th>
                                    <th>الأقسام المُدارة</th>
                                    <th>حجم الفريق</th>
                                    <th>الأدوار</th>
                                    <th>الحالة</th>
                                    <th>العمليات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @include('admin.partials.department-heads-table-body')
                            </tbody>
                        </table>
                    </div>
                </div>

                @include('admin.partials.department-heads-table-footer')
            </div>
        </div>
    </div>

    @include('admin.pages.users.partials.modals')
@stop

@push('scripts')
    <script>
        window.adminUsersConfig = {
            loginCodeUrlTemplate: @json(route('admin.users.login-code', ['user' => '__ID__'])),
        };
    </script>
    <script src="{{ asset('assets/js/admin-users.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (window.AdminTables?.initToggleStatus) {
                AdminTables.initToggleStatus(document, {
                    urlTemplate: '/users/:id/toggle-status',
                });
            }
            if (window.AdminTables?.initCopyButtons) {
                AdminTables.initCopyButtons(document);
            }
        });

        document.addEventListener('click', function (e) {
            const btn = e.target.closest('.toggle-password');
            if (!btn) return;
            const input = document.getElementById(btn.dataset.target);
            if (!input) return;
            const icon = btn.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                if (icon) icon.className = 'fas fa-eye-slash';
            } else {
                input.type = 'password';
                if (icon) icon.className = 'fas fa-eye';
            }
        });
    </script>
@endpush
