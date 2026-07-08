@extends('admin.layouts.master')

@section('page-title')
    الأدوار
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid admin-page-shell">
            @include('admin.pages.users.partials.alerts')

            <div class="admin-page-banner">
                <div class="admin-page-banner-main">
                    <span class="admin-page-banner-icon"><i class="ri-shield-keyhole-line"></i></span>
                    <div class="admin-page-banner-text">
                        <h1>جدول الأدوار</h1>
                        <p>إدارة الأدوار والصلاحيات في النظام</p>
                    </div>
                </div>
                @can('role-create')
                    <div class="admin-page-banner-actions">
                        <a href="{{ route('roles.create') }}" class="admin-btn admin-btn-light">
                            <i class="ri-add-line"></i>
                            إضافة دور جديد
                        </a>
                    </div>
                @endcan
            </div>

            <div class="admin-report-stats admin-report-stats-4 mb-4">
                <div class="admin-report-stat admin-report-stat-static admin-report-stat-blue">
                    <span class="admin-report-stat-icon"><i class="ri-shield-user-line"></i></span>
                    <span class="admin-report-stat-label">إجمالي الأدوار</span>
                    <span class="admin-report-stat-value" style="color:#2563eb;">{{ $roleStats['total'] ?? 0 }}</span>
                </div>
                <div class="admin-report-stat admin-report-stat-static admin-report-stat-cyan">
                    <span class="admin-report-stat-icon"><i class="ri-key-2-line"></i></span>
                    <span class="admin-report-stat-label">إجمالي الصلاحيات</span>
                    <span class="admin-report-stat-value" style="color:#0891b2;">{{ $roleStats['permissions'] ?? 0 }}</span>
                </div>
                <div class="admin-report-stat admin-report-stat-static admin-report-stat-green">
                    <span class="admin-report-stat-icon"><i class="ri-list-check-2"></i></span>
                    <span class="admin-report-stat-label">النتائج المعروضة</span>
                    <span class="admin-report-stat-value" style="color:#059669;">{{ $roles->total() }}</span>
                </div>
                <div class="admin-report-stat admin-report-stat-static admin-report-stat-amber">
                    <span class="admin-report-stat-icon"><i class="ri-pages-line"></i></span>
                    <span class="admin-report-stat-label">الصفحة الحالية</span>
                    <span class="admin-report-stat-value" style="color:#d97706;">{{ $roles->currentPage() }}</span>
                </div>
            </div>

            <div class="admin-page-card">
                <div class="card-toolbar">
                    <form id="roles-filter-form" action="{{ route('roles.index') }}" method="GET" class="admin-filters w-100">
                        <div class="search-input-wrap">
                            <i class="ri-search-line"></i>
                            <input type="text" name="query" class="form-control"
                                   placeholder="بحث باسم الدور..."
                                   value="{{ request('query') }}" autocomplete="off">
                        </div>

                        <button type="button" class="admin-btn admin-btn-danger" data-admin-filter-reset>
                            <i class="ri-filter-off-line"></i>
                            مسح
                        </button>
                    </form>
                </div>

                <div class="admin-table-wrap" id="roles-table-wrap">
                    <div class="table-loader">
                        <div class="spinner-border text-primary" role="status"></div>
                    </div>
                    <div class="table-responsive">
                        <table class="admin-data-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>اسم الدور</th>
                                    <th>الصلاحيات</th>
                                    <th>العمليات</th>
                                </tr>
                            </thead>
                            <tbody id="roles-table-body">
                                @include('admin.partials.roles-table-body')
                            </tbody>
                        </table>
                    </div>
                </div>

                <div id="roles-ajax-extra">
                    @include('admin.partials.roles-table-footer')
                </div>
            </div>

        </div>
    </div>
@stop

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (window.AdminTables?.initAjaxTable) {
            AdminTables.initAjaxTable({
                formSelector: '#roles-filter-form',
                bodySelector: '#roles-table-body',
                extraSelector: '#roles-ajax-extra',
                tableWrapSelector: '#roles-table-wrap',
                metaSelector: '#roles-table-meta',
                url: '{{ route('roles.index') }}',
            });
        }
    });
</script>
@endpush
