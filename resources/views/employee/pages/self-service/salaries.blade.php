@extends('employee.layouts.master')

@section('page-title')
    الرواتب
@stop

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/employee-salaries.css') }}">
@endpush

@section('content')
    <div class="main-content app-content employee-salaries-page">
        <div class="container-fluid pt-4">

            <div class="card page-hero mb-4">
                <div class="card-body py-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="page-hero-icon">
                            <i class="ri-wallet-3-line"></i>
                        </div>
                        <div>
                            <h4 class="mb-1 page-hero-title fw-bold">كشف الرواتب</h4>
                            <p class="mb-0 page-hero-subtitle">سجلات الرواتب وكشوف القسائم الشهرية</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-value">
                                    @if ($stats['latest_net'])
                                        {{ number_format($stats['latest_net'], 0) }}
                                        <small class="fs-14 fw-normal text-muted">{{ $stats['latest_currency'] }}</small>
                                    @else
                                        —
                                    @endif
                                </div>
                                <div class="stat-label">آخر صافي @if($stats['latest_period'])<span class="d-block">{{ $stats['latest_period'] }}</span>@endif</div>
                            </div>
                            <div class="stat-icon stat-icon--primary"><i class="ri-money-dollar-circle-line"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-value">{{ $stats['records'] }}</div>
                                <div class="stat-label">سجلات راتب</div>
                            </div>
                            <div class="stat-icon stat-icon--primary"><i class="ri-file-list-3-line"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-value">{{ $stats['paid'] }}</div>
                                <div class="stat-label">مدفوعة</div>
                            </div>
                            <div class="stat-icon stat-icon--success"><i class="ri-checkbox-circle-line"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-value">{{ $stats['pending'] }}</div>
                                <div class="stat-label">قيد الانتظار</div>
                            </div>
                            <div class="stat-icon stat-icon--warning"><i class="ri-time-line"></i></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="content-panel">
                <div class="content-panel-header">
                    <h5 class="fw-bold mb-1 text-dark">سجلات الرواتب</h5>
                    <p class="text-muted fs-13 mb-0">{{ $salaries->total() }} سجل</p>
                </div>
                @forelse ($salaries as $salary)
                    @php
                        $cur = $salary->currency->symbol_ar ?? $salary->currency->symbol ?? 'ر.س';
                        $paid = $salary->payment_status === 'paid';
                    @endphp
                    <div class="salary-record-card">
                        <div class="d-flex flex-wrap align-items-start justify-content-between gap-3">
                            <div>
                                <div class="salary-period">{{ $salary->month_name }} {{ $salary->salary_year }}</div>
                                <div class="salary-breakdown">
                                    <span>أساسي: {{ number_format($salary->base_salary, 2) }} {{ $cur }}</span>
                                    <span>بدلات: {{ number_format($salary->allowances, 2) }}</span>
                                    <span>مكافآت: {{ number_format($salary->bonuses, 2) }}</span>
                                    <span>خصومات: {{ number_format($salary->deductions, 2) }}</span>
                                </div>
                            </div>
                            <div class="text-end">
                                <div class="salary-total mb-1">{{ number_format($salary->total_salary, 2) }} {{ $cur }}</div>
                                <span class="status-pill status-pill--{{ $paid ? 'paid' : 'pending' }}">{{ $salary->payment_status_ar }}</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="empty-state">
                        <div class="empty-icon"><i class="ri-wallet-line"></i></div>
                        <p class="mb-0">لا توجد سجلات رواتب</p>
                    </div>
                @endforelse
                @if ($salaries->hasPages())
                    <div class="p-3 border-top">{{ $salaries->links() }}</div>
                @endif
            </div>

            @if ($payrolls->isNotEmpty())
                <div class="content-panel">
                    <div class="content-panel-header">
                        <h5 class="fw-bold mb-1 text-dark">كشوف الرواتب الشهرية</h5>
                        <p class="text-muted fs-13 mb-0">{{ $stats['payslips'] }} قسيمة — تحميل PDF</p>
                    </div>
                    @foreach ($payrolls as $p)
                        @php
                            $cur = $p->currency->symbol ?? $p->currency->code ?? 'ر.س';
                            $statusClass = match ($p->status) {
                                'paid', 'approved' => 'paid',
                                'calculated' => 'calculated',
                                'draft' => 'draft',
                                'cancelled' => 'cancelled',
                                default => 'pending',
                            };
                        @endphp
                        <div class="payslip-card">
                            <div class="flex-grow-1 min-w-0">
                                <div class="salary-period">{{ $p->month_name }} {{ $p->payroll_year }}</div>
                                <div class="payslip-code mt-1">{{ $p->payroll_code }}</div>
                            </div>
                            <div class="text-end">
                                <div class="salary-total fs-16 mb-1">
                                    {{ $p->net_salary ? number_format($p->net_salary, 2).' '.$cur : '—' }}
                                </div>
                                <span class="status-pill status-pill--{{ $statusClass }}">{{ $p->status_name_ar }}</span>
                            </div>
                            <a href="{{ route('employee.payrolls.payslip.pdf', $p->id) }}" class="btn btn-outline-primary btn-sm" target="_blank" rel="noopener">
                                <i class="ri-file-pdf-line me-1"></i>PDF
                            </a>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@stop
