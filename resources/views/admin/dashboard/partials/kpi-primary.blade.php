<div class="row mb-4 g-3">
    <div class="col-xl-3 col-md-6">
        <a href="{{ route('admin.employees.index') }}" class="kpi-card-link">
            <div class="card overflow-hidden bg-primary-gradient mb-0 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-2 text-white op-8">إجمالي الموظفين</h6>
                            <h2 class="mb-0 text-white">{{ $stats['total_employees'] ?? 0 }}</h2>
                            <small class="text-white-50">+{{ $stats['new_employees_this_month'] ?? 0 }} هذا الشهر</small>
                        </div>
                        <div class="fs-36 text-white op-7"><i class="ri-team-line"></i></div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-xl-3 col-md-6">
        <a href="{{ route('admin.attendances.index') }}" class="kpi-card-link">
            <div class="card overflow-hidden bg-success-gradient mb-0 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-2 text-white op-8">حضور اليوم</h6>
                            <h2 class="mb-0 text-white">{{ $attendanceStats['today_present'] ?? 0 }}</h2>
                            <small class="text-white-50">غائب: {{ $attendanceStats['today_absent'] ?? 0 }} · متأخر: {{ $attendanceStats['today_late'] ?? 0 }}</small>
                        </div>
                        <div class="fs-36 text-white op-7"><i class="ri-calendar-check-line"></i></div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-xl-3 col-md-6">
        <a href="{{ route('admin.leave-requests.index', ['status' => 'pending']) }}" class="kpi-card-link">
            <div class="card overflow-hidden bg-warning-gradient mb-0 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-2 text-white op-8">إجازات معلقة</h6>
                            <h2 class="mb-0 text-white">{{ $leaveStats['pending_requests'] ?? 0 }}</h2>
                            <small class="text-white-50">في إجازة اليوم: {{ $leaveStats['approved_today'] ?? 0 }}</small>
                        </div>
                        <div class="fs-36 text-white op-7"><i class="ri-sun-line"></i></div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-xl-3 col-md-6">
        <a href="{{ route('admin.salaries.index') }}" class="kpi-card-link">
            <div class="card overflow-hidden bg-info-gradient mb-0 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-2 text-white op-8">رواتب الشهر</h6>
                            <h2 class="mb-0 text-white fs-22">{{ number_format($salaryStats['total_this_month'] ?? 0, 0) }} <small class="fs-14">ر.س</small></h2>
                            <small class="text-white-50">مدفوعة: {{ $salaryStats['paid_count'] ?? 0 }}</small>
                        </div>
                        <div class="fs-36 text-white op-7"><i class="ri-money-dollar-circle-line"></i></div>
                    </div>
                </div>
            </div>
        </a>
    </div>
</div>
