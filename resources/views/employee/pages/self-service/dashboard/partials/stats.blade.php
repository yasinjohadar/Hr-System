<div class="row g-3 mb-4" id="dashboard-kpi-row">
    <div class="col-xl-3 col-md-6">
        <a href="{{ route('employee.attendance') }}" class="kpi-card-link">
            <div class="kpi-stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="kpi-stat-label">حضور الشهر</div>
                        <div class="kpi-stat-value">
                            <span data-stat="total_attendance">{{ $stats['total_attendance'] }}</span>
                            <small class="kpi-stat-unit">يوم</small>
                        </div>
                        <div class="kpi-stat-meta">
                            <span data-stat-display="absent">{{ $absentDays }}</span> غائب ·
                            <span data-stat-display="late">{{ $lateDays }}</span> متأخر
                        </div>
                    </div>
                    <div class="kpi-stat-icon kpi-stat-icon--primary"><i class="ri-calendar-check-line"></i></div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-xl-3 col-md-6">
        <a href="{{ route('employee.leaves') }}" class="kpi-card-link">
            <div class="kpi-stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="kpi-stat-label">الإجازات</div>
                        <div class="kpi-stat-value">
                            <span data-stat="pending_leaves">{{ $stats['pending_leaves'] }}</span>
                            <small class="kpi-stat-unit">معلقة</small>
                        </div>
                        <div class="kpi-stat-meta">
                            <span data-stat="approved_leaves">{{ $stats['approved_leaves'] }}</span> مقبولة
                        </div>
                    </div>
                    <div class="kpi-stat-icon kpi-stat-icon--success"><i class="ri-sun-line"></i></div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-xl-3 col-md-6">
        <a href="{{ route('employee.tasks') }}" class="kpi-card-link">
            <div class="kpi-stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="kpi-stat-label">المهام</div>
                        <div class="kpi-stat-value">
                            <span data-stat="pending_tasks">{{ $stats['pending_tasks'] }}</span>
                            <small class="kpi-stat-unit">نشطة</small>
                        </div>
                        <div class="kpi-stat-meta">
                            <span data-stat="upcoming_meetings">{{ $stats['upcoming_meetings'] }}</span> اجتماع قادم
                        </div>
                    </div>
                    <div class="kpi-stat-icon kpi-stat-icon--warning"><i class="ri-task-line"></i></div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-xl-3 col-md-6">
        <a href="{{ route('employee.salaries') }}" class="kpi-card-link">
            <div class="kpi-stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="kpi-stat-label">آخر راتب</div>
                        <div class="kpi-stat-value kpi-stat-value--payroll" id="kpi-latest-payroll">
                            @if ($latestPayroll)
                                {{ number_format($latestPayroll->net_salary, 0) }}
                                <small class="kpi-stat-unit">{{ $latestPayroll->currency->code ?? 'ر.س' }}</small>
                            @else
                                —
                            @endif
                        </div>
                        <div class="kpi-stat-meta" id="kpi-payroll-meta">
                            @if ($latestPayroll)
                                {{ $latestPayroll->month_name }} {{ $latestPayroll->payroll_year }}
                            @else
                                لا توجد بيانات
                            @endif
                        </div>
                    </div>
                    <div class="kpi-stat-icon kpi-stat-icon--info"><i class="ri-money-dollar-circle-line"></i></div>
                </div>
            </div>
        </a>
    </div>
</div>
