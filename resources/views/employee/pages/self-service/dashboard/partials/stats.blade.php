<div class="row mb-4" id="dashboard-kpi-row">
    <div class="col-xl-3 col-md-6 mb-3 mb-xl-0">
        <a href="{{ route('employee.attendance') }}" class="kpi-card-link">
            <div class="card overflow-hidden bg-primary-gradient mb-0 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-2 text-white op-8">حضور الشهر</h6>
                            <h2 class="mb-0 text-white">
                                <span data-stat="total_attendance">{{ $stats['total_attendance'] }}</span>
                                <small class="fs-14 text-white-50">يوم</small>
                            </h2>
                            <small class="text-white-50">
                                <span data-stat-display="absent">{{ $absentDays }}</span> غائب ·
                                <span data-stat-display="late">{{ $lateDays }}</span> متأخر
                            </small>
                        </div>
                        <div class="fs-36 text-white op-7"><i class="ri-calendar-check-line"></i></div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-xl-3 col-md-6 mb-3 mb-xl-0">
        <a href="{{ route('employee.leaves') }}" class="kpi-card-link">
            <div class="card overflow-hidden bg-success-gradient mb-0 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-2 text-white op-8">الإجازات</h6>
                            <h2 class="mb-0 text-white">
                                <span data-stat="pending_leaves">{{ $stats['pending_leaves'] }}</span>
                                <small class="fs-14 text-white-50">معلقة</small>
                            </h2>
                            <small class="text-white-50">
                                <span data-stat="approved_leaves">{{ $stats['approved_leaves'] }}</span> مقبولة
                            </small>
                        </div>
                        <div class="fs-36 text-white op-7"><i class="ri-sun-line"></i></div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-xl-3 col-md-6 mb-3 mb-xl-0">
        <a href="{{ route('employee.tasks') }}" class="kpi-card-link">
            <div class="card overflow-hidden bg-warning-gradient mb-0 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-2 text-white op-8">المهام</h6>
                            <h2 class="mb-0 text-white">
                                <span data-stat="pending_tasks">{{ $stats['pending_tasks'] }}</span>
                                <small class="fs-14 text-white-50">نشطة</small>
                            </h2>
                            <small class="text-white-50">
                                <span data-stat="upcoming_meetings">{{ $stats['upcoming_meetings'] }}</span> اجتماع قادم
                            </small>
                        </div>
                        <div class="fs-36 text-white op-7"><i class="ri-task-line"></i></div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-xl-3 col-md-6">
        <a href="{{ route('employee.salaries') }}" class="kpi-card-link">
            <div class="card overflow-hidden bg-info-gradient mb-0 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-2 text-white op-8">آخر راتب</h6>
                            <h2 class="mb-0 text-white fs-20" id="kpi-latest-payroll">
                                @if($latestPayroll)
                                    {{ number_format($latestPayroll->net_salary, 0) }}
                                    <small class="fs-14 text-white-50">{{ $latestPayroll->currency->code ?? 'ر.س' }}</small>
                                @else
                                    —
                                @endif
                            </h2>
                            <small class="text-white-50" id="kpi-payroll-meta">
                                @if($latestPayroll)
                                    {{ $latestPayroll->month_name }} {{ $latestPayroll->payroll_year }}
                                @else
                                    لا توجد بيانات
                                @endif
                            </small>
                        </div>
                        <div class="fs-36 text-white op-7"><i class="ri-money-dollar-circle-line"></i></div>
                    </div>
                </div>
            </div>
        </a>
    </div>
</div>
