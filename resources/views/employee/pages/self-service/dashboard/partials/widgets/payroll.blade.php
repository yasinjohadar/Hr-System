@forelse($recentPayrolls as $payroll)
    <div class="p-3 dashboard-list-item">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h6 class="mb-1 fs-14 fw-semibold">{{ $payroll->month_name }} {{ $payroll->payroll_year }}</h6>
                <p class="text-muted fs-13 mb-0">
                    الراتب الصافي:
                    <span class="fw-semibold">{{ number_format($payroll->net_salary, 2) }} {{ $payroll->currency->code ?? 'ر.س' }}</span>
                </p>
            </div>
            <span class="badge bg-{{ $payroll->status === 'paid' ? 'success' : ($payroll->status === 'approved' ? 'primary' : 'warning') }}-transparent">
                {{ $payroll->status_name_ar }}
            </span>
        </div>
    </div>
@empty
    <div class="text-center text-muted py-4">
        <i class="ri-money-dollar-circle-line fs-24 d-block mb-2"></i>
        لا توجد بيانات رواتب
    </div>
@endforelse
