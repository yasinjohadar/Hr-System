@extends('admin.layouts.master')

@section('page-title')
    إدارة التفويض
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid admin-page-shell">
            @include('admin.pages.users.partials.alerts')

            <div class="admin-page-banner">
                <div class="admin-page-banner-main">
                    <span class="admin-page-banner-icon"><i class="ri-share-forward-line"></i></span>
                    <div class="admin-page-banner-text">
                        <h1>إدارة التفويض</h1>
                        <p>تفويض صلاحيات الموافقة لموظف آخر خلال فترة محدّدة</p>
                    </div>
                </div>
                <div class="admin-page-banner-actions">
                    <a href="{{ route('admin.team.delegations.create') }}" class="admin-btn admin-btn-light">
                        <i class="ri-add-line"></i>
                        تفويض جديد
                    </a>
                    <a href="{{ route('admin.team.dashboard') }}" class="admin-btn admin-btn-secondary">
                        <i class="ri-arrow-right-line"></i>
                        العودة
                    </a>
                </div>
            </div>

            <div class="admin-report-stats admin-report-stats-4 mb-4">
                <div class="admin-report-stat admin-report-stat-static admin-report-stat-blue">
                    <span class="admin-report-stat-icon"><i class="ri-share-line"></i></span>
                    <span class="admin-report-stat-label">تفويضات صادرة</span>
                    <span class="admin-report-stat-value" style="color:#2563eb;">{{ $stats['sent_total'] }}</span>
                </div>
                <div class="admin-report-stat admin-report-stat-static admin-report-stat-green">
                    <span class="admin-report-stat-icon"><i class="ri-checkbox-circle-line"></i></span>
                    <span class="admin-report-stat-label">صادرة ونشطة الآن</span>
                    <span class="admin-report-stat-value" style="color:#059669;">{{ $stats['sent_active'] }}</span>
                </div>
                <div class="admin-report-stat admin-report-stat-static admin-report-stat-cyan">
                    <span class="admin-report-stat-icon"><i class="ri-arrow-down-circle-line"></i></span>
                    <span class="admin-report-stat-label">تفويضات مستلمة</span>
                    <span class="admin-report-stat-value" style="color:#0891b2;">{{ $stats['received_total'] }}</span>
                </div>
                <div class="admin-report-stat admin-report-stat-static admin-report-stat-amber">
                    <span class="admin-report-stat-icon"><i class="ri-flashlight-line"></i></span>
                    <span class="admin-report-stat-label">مستلمة ونشطة الآن</span>
                    <span class="admin-report-stat-value" style="color:#d97706;">{{ $stats['received_active'] }}</span>
                </div>
            </div>

            {{-- التفويضات الصادرة --}}
            <div class="admin-page-card mb-4">
                <div class="card-toolbar">
                    <h6 class="mb-0 fw-bold">
                        <i class="ri-share-line text-primary me-2"></i>التفويضات الصادرة
                    </h6>
                    <small class="text-muted">صلاحيات فوّضتها أنت لموظفين آخرين</small>
                </div>

                <div class="admin-table-wrap">
                    <div class="table-responsive">
                        <table class="admin-data-table">
                            <thead>
                                <tr>
                                    <th>المفوَّض</th>
                                    <th>أنواع الطلبات</th>
                                    <th>من</th>
                                    <th>إلى</th>
                                    <th>الحالة</th>
                                    <th>ملاحظات</th>
                                    <th>إجراء</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($delegations as $delegation)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="admin-avatar-initial" style="width:1.75rem; height:1.75rem; font-size:0.8rem;">
                                                    {{ mb_substr($delegation->delegate->name, 0, 1) }}
                                                </span>
                                                <span class="fw-semibold">{{ $delegation->delegate->name }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            @if (empty($delegation->workflow_types))
                                                <span class="admin-badge admin-badge-role">جميع الأنواع</span>
                                            @else
                                                @foreach ($delegation->workflow_type_labels as $label)
                                                    <span class="admin-badge admin-badge-muted">{{ $label }}</span>
                                                @endforeach
                                            @endif
                                        </td>
                                        <td class="small text-muted">{{ $delegation->start_date->format('Y-m-d H:i') }}</td>
                                        <td class="small text-muted">{{ $delegation->end_date->format('Y-m-d H:i') }}</td>
                                        <td>
                                            <span class="admin-badge admin-badge-{{ match ($delegation->status) {
                                                'active' => 'success',
                                                'expired' => 'danger',
                                                default => 'muted',
                                            } }}">
                                                {{ $delegation->status_name_ar }}
                                            </span>
                                        </td>
                                        <td class="small">{{ $delegation->notes ? Str::limit($delegation->notes, 40) : '—' }}</td>
                                        <td>
                                            @if ($delegation->status === 'active')
                                                <button type="button" class="admin-btn admin-btn-danger admin-btn-sm"
                                                        data-post-url="{{ route('admin.team.delegations.cancel', $delegation->id) }}"
                                                        data-post-confirm="إلغاء تفويض <strong>{{ $delegation->delegate->name }}</strong>؟"
                                                        data-post-title="إلغاء التفويض"
                                                        data-post-type="danger"
                                                        data-post-btn="إلغاء">
                                                    <i class="ri-close-line"></i>
                                                    إلغاء
                                                </button>
                                            @else
                                                <span class="text-muted small">—</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7">
                                            <div class="admin-empty-state">
                                                <i class="ri-share-line"></i>
                                                لا توجد تفويضات صادرة
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- التفويضات المستلمة --}}
            <div class="admin-page-card">
                <div class="card-toolbar">
                    <h6 class="mb-0 fw-bold">
                        <i class="ri-arrow-down-circle-line text-primary me-2"></i>التفويضات المستلمة
                    </h6>
                    <small class="text-muted">صلاحيات فوَّضها لك آخرون</small>
                </div>

                <div class="admin-table-wrap">
                    <div class="table-responsive">
                        <table class="admin-data-table">
                            <thead>
                                <tr>
                                    <th>المفوِّض</th>
                                    <th>أنواع الطلبات</th>
                                    <th>من</th>
                                    <th>إلى</th>
                                    <th>الحالة</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($receivedDelegations as $delegation)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="admin-avatar-initial" style="width:1.75rem; height:1.75rem; font-size:0.8rem; background:linear-gradient(135deg,#10b981,#34d399);">
                                                    {{ mb_substr($delegation->delegator->name, 0, 1) }}
                                                </span>
                                                <span class="fw-semibold">{{ $delegation->delegator->name }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            @if (empty($delegation->workflow_types))
                                                <span class="admin-badge admin-badge-role">جميع الأنواع</span>
                                            @else
                                                @foreach ($delegation->workflow_type_labels as $label)
                                                    <span class="admin-badge admin-badge-muted">{{ $label }}</span>
                                                @endforeach
                                            @endif
                                        </td>
                                        <td class="small text-muted">{{ $delegation->start_date->format('Y-m-d H:i') }}</td>
                                        <td class="small text-muted">{{ $delegation->end_date->format('Y-m-d H:i') }}</td>
                                        <td>
                                            <span class="admin-badge admin-badge-{{ match ($delegation->status) {
                                                'active' => 'success',
                                                'expired' => 'danger',
                                                default => 'muted',
                                            } }}">
                                                {{ $delegation->status_name_ar }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5">
                                            <div class="admin-empty-state">
                                                <i class="ri-arrow-down-circle-line"></i>
                                                لا توجد تفويضات مستلمة
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
