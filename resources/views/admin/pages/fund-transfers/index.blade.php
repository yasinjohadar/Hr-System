@extends('admin.layouts.master')

@section('page-title')
    التحويلات المالية
@stop

@section('content')
    @php
        $statusLabels = [
            'pending' => 'بانتظار الموافقة',
            'completed' => 'منفّذ',
            'rejected' => 'مرفوض',
            'cancelled' => 'ملغي',
        ];
        $typeLabels = [
            'internal' => 'تحويل داخلي',
            'disbursement' => 'صرف لموظف',
            'adjustment' => 'تسوية / إيداع',
        ];
    @endphp

    <div class="main-content app-content">
        <div class="container-fluid admin-page-shell">
            @include('admin.pages.users.partials.alerts')

            <div class="admin-page-banner">
                <div class="admin-page-banner-main">
                    <span class="admin-page-banner-icon"><i class="ri-exchange-dollar-line"></i></span>
                    <div class="admin-page-banner-text">
                        <h1>التحويلات المالية</h1>
                        <p>سجل الحركات بين حسابات الشركة والصرف للموظفين — عتبة الموافقة: {{ number_format($threshold, 2) }}</p>
                    </div>
                </div>
                @can('fund-transfer-create')
                    <div class="admin-page-banner-actions">
                        <a href="{{ route('admin.fund-transfers.create') }}" class="admin-btn admin-btn-light">
                            <i class="ri-add-line"></i>
                            تحويل جديد
                        </a>
                    </div>
                @endcan
            </div>

            <div class="admin-report-stats admin-report-stats-4 mb-4">
                <div class="admin-report-stat admin-report-stat-static admin-report-stat-blue">
                    <span class="admin-report-stat-icon"><i class="ri-file-list-3-line"></i></span>
                    <span class="admin-report-stat-label">إجمالي الحركات</span>
                    <span class="admin-report-stat-value" style="color:#2563eb;">{{ $transferStats['total'] }}</span>
                </div>
                <div class="admin-report-stat admin-report-stat-static admin-report-stat-amber">
                    <span class="admin-report-stat-icon"><i class="ri-time-line"></i></span>
                    <span class="admin-report-stat-label">بانتظار الموافقة</span>
                    <span class="admin-report-stat-value" style="color:#d97706;">{{ $transferStats['pending'] }}</span>
                </div>
                <div class="admin-report-stat admin-report-stat-static admin-report-stat-green">
                    <span class="admin-report-stat-icon"><i class="ri-checkbox-circle-line"></i></span>
                    <span class="admin-report-stat-label">منفّذة</span>
                    <span class="admin-report-stat-value" style="color:#059669;">{{ $transferStats['completed'] }}</span>
                </div>
                <div class="admin-report-stat admin-report-stat-static admin-report-stat-cyan">
                    <span class="admin-report-stat-icon"><i class="ri-funds-line"></i></span>
                    <span class="admin-report-stat-label">منفّذ هذا الشهر</span>
                    <span class="admin-report-stat-value" style="color:#0891b2;">{{ number_format($transferStats['month_amount'], 2) }}</span>
                </div>
            </div>

            <div class="admin-page-card">
                <div class="card-toolbar">
                    <form method="GET" class="admin-filters w-100">
                        <select name="status" class="form-select" style="width:auto;min-width:160px;">
                            <option value="">كل الحالات</option>
                            @foreach ($statusLabels as $key => $label)
                                <option value="{{ $key }}" @selected(request('status') === $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                        <select name="type" class="form-select" style="width:auto;min-width:160px;">
                            <option value="">كل الأنواع</option>
                            @foreach ($typeLabels as $key => $label)
                                <option value="{{ $key }}" @selected(request('type') === $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                        <select name="project_id" class="form-select" style="width:auto;min-width:180px;">
                            <option value="">كل المشاريع</option>
                            @foreach ($projects as $p)
                                <option value="{{ $p->id }}" @selected(request('project_id') == $p->id)>{{ $p->name_ar ?? $p->name }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="admin-btn admin-btn-primary">
                            <i class="ri-search-line"></i> بحث
                        </button>
                        <a href="{{ route('admin.fund-transfers.index') }}" class="admin-btn admin-btn-danger">
                            <i class="ri-filter-off-line"></i> مسح
                        </a>
                    </form>
                </div>

                <div class="admin-table-wrap">
                    <div class="table-responsive">
                        <table class="admin-data-table">
                            <thead>
                                <tr>
                                    <th>الرمز</th>
                                    <th>النوع</th>
                                    <th>المبلغ</th>
                                    <th>المشروع</th>
                                    <th>الحالة</th>
                                    <th>بواسطة</th>
                                    <th>التاريخ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($transfers as $transfer)
                                    @php
                                        $stBadge = match ($transfer->status) {
                                            'completed' => 'admin-badge-success',
                                            'pending' => 'admin-badge-warning',
                                            'rejected' => 'admin-badge-danger',
                                            default => 'admin-badge-muted',
                                        };
                                    @endphp
                                    <tr>
                                        <td>
                                            <a href="{{ route('admin.fund-transfers.show', $transfer) }}" class="admin-user-link">
                                                {{ $transfer->transfer_code }}
                                            </a>
                                        </td>
                                        <td><span class="admin-badge admin-badge-muted">{{ $transfer->type_name_ar }}</span></td>
                                        <td class="fw-bold">{{ number_format((float) $transfer->amount, 2) }}</td>
                                        <td>{{ $transfer->project?->name_ar ?? $transfer->project?->name ?? '—' }}</td>
                                        <td><span class="admin-badge {{ $stBadge }}">{{ $transfer->status_name_ar }}</span></td>
                                        <td>{{ $transfer->requester?->name ?? '—' }}</td>
                                        <td>{{ $transfer->created_at?->format('Y-m-d H:i') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7">
                                            <div class="admin-empty-state">
                                                <i class="ri-exchange-dollar-line"></i>
                                                لا توجد تحويلات
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
                        @if ($transfers->total() > 0)
                            عرض {{ $transfers->firstItem() }} إلى {{ $transfers->lastItem() }} من {{ $transfers->total() }}
                        @else
                            لا توجد نتائج
                        @endif
                    </div>
                    <div class="admin-pagination">{{ $transfers->withQueryString()->links() }}</div>
                </div>
            </div>
        </div>
    </div>
@stop
