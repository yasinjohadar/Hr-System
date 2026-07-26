@extends('admin.layouts.master')

@section('page-title')
    حسابات الشركة البنكية
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid admin-page-shell">
            @include('admin.pages.users.partials.alerts')

            <div class="admin-page-banner">
                <div class="admin-page-banner-main">
                    <span class="admin-page-banner-icon"><i class="ri-bank-line"></i></span>
                    <div class="admin-page-banner-text">
                        <h1>حسابات الشركة البنكية</h1>
                        <p>إدارة الحسابات التشغيلية ومتابعة الأرصدة</p>
                    </div>
                </div>
                @can('company-bank-account-create')
                    <div class="admin-page-banner-actions">
                        <a href="{{ route('admin.company-bank-accounts.create') }}" class="admin-btn admin-btn-light">
                            <i class="ri-add-line"></i>
                            حساب جديد
                        </a>
                    </div>
                @endcan
            </div>

            <div class="admin-report-stats admin-report-stats-4 mb-4">
                <div class="admin-report-stat admin-report-stat-static admin-report-stat-blue">
                    <span class="admin-report-stat-icon"><i class="ri-bank-card-line"></i></span>
                    <span class="admin-report-stat-label">إجمالي الحسابات</span>
                    <span class="admin-report-stat-value" style="color:#2563eb;">{{ $accountStats['total'] }}</span>
                </div>
                <div class="admin-report-stat admin-report-stat-static admin-report-stat-green">
                    <span class="admin-report-stat-icon"><i class="ri-checkbox-circle-line"></i></span>
                    <span class="admin-report-stat-label">حسابات نشطة</span>
                    <span class="admin-report-stat-value" style="color:#059669;">{{ $accountStats['active'] }}</span>
                </div>
                <div class="admin-report-stat admin-report-stat-static admin-report-stat-cyan">
                    <span class="admin-report-stat-icon"><i class="ri-wallet-3-line"></i></span>
                    <span class="admin-report-stat-label">إجمالي الأرصدة</span>
                    <span class="admin-report-stat-value" style="color:#0891b2;">{{ number_format($accountStats['balance'], 2) }}</span>
                </div>
                <div class="admin-report-stat admin-report-stat-static admin-report-stat-amber">
                    <span class="admin-report-stat-icon"><i class="ri-pause-circle-line"></i></span>
                    <span class="admin-report-stat-label">موقوفة</span>
                    <span class="admin-report-stat-value" style="color:#d97706;">{{ $accountStats['inactive'] }}</span>
                </div>
            </div>

            <div class="admin-page-card">
                <div class="admin-table-wrap">
                    <div class="table-responsive">
                        <table class="admin-data-table">
                            <thead>
                                <tr>
                                    <th>الحساب</th>
                                    <th>البنك</th>
                                    <th>رقم الحساب</th>
                                    <th>العملة</th>
                                    <th>الرصيد</th>
                                    <th>الحالة</th>
                                    <th>العمليات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($accounts as $account)
                                    <tr>
                                        <td>
                                            <a href="{{ route('admin.company-bank-accounts.show', $account) }}" class="admin-user-link">{{ $account->name }}</a>
                                            @if ($account->iban)
                                                <small class="text-muted d-block">{{ $account->iban }}</small>
                                            @endif
                                        </td>
                                        <td>{{ $account->bank_name }}</td>
                                        <td><code class="small">{{ $account->account_number }}</code></td>
                                        <td>{{ $account->currency->code ?? '—' }}</td>
                                        <td class="fw-bold">{{ number_format((float) $account->balance, 2) }}</td>
                                        <td>
                                            @if ($account->is_active)
                                                <span class="admin-badge admin-badge-success">نشط</span>
                                            @else
                                                <span class="admin-badge admin-badge-muted">موقوف</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="admin-row-actions dropdown">
                                                <button class="admin-kebab-btn" type="button" data-bs-toggle="dropdown">
                                                    <i class="ri-more-2-fill"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    @can('company-bank-account-show')
                                                        <li>
                                                            <a class="dropdown-item" href="{{ route('admin.company-bank-accounts.show', $account) }}">
                                                                <i class="ri-eye-line text-info me-2"></i>عرض
                                                            </a>
                                                        </li>
                                                    @endcan
                                                    @can('company-bank-account-edit')
                                                        <li>
                                                            <a class="dropdown-item" href="{{ route('admin.company-bank-accounts.edit', $account) }}">
                                                                <i class="ri-edit-line text-primary me-2"></i>تعديل
                                                            </a>
                                                        </li>
                                                    @endcan
                                                    @can('fund-transfer-create')
                                                        <li>
                                                            <a class="dropdown-item" href="{{ route('admin.fund-transfers.create') }}">
                                                                <i class="ri-exchange-dollar-line text-success me-2"></i>تحويل جديد
                                                            </a>
                                                        </li>
                                                    @endcan
                                                    @can('company-bank-account-delete')
                                                        <li><hr class="dropdown-divider"></li>
                                                        <li>
                                                            <button type="button" class="dropdown-item text-danger border-0 bg-transparent w-100 text-start"
                                                                    data-delete-url="{{ route('admin.company-bank-accounts.destroy', $account) }}"
                                                                    data-delete-message="حذف الحساب <strong>{{ $account->name }}</strong>؟">
                                                                <i class="ri-delete-bin-line me-2"></i>حذف
                                                            </button>
                                                        </li>
                                                    @endcan
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7">
                                            <div class="admin-empty-state">
                                                <i class="ri-bank-line"></i>
                                                لا توجد حسابات بعد
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="admin-table-footer">
                    <div class="admin-table-meta">
                        @if ($accounts->total() > 0)
                            عرض {{ $accounts->firstItem() }} إلى {{ $accounts->lastItem() }} من {{ $accounts->total() }}
                        @else
                            لا توجد نتائج
                        @endif
                    </div>
                    <div class="admin-pagination">{{ $accounts->links() }}</div>
                </div>
            </div>
        </div>
    </div>
@stop
