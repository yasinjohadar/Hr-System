<div class="row mb-4 g-3">
    <div class="col-xl-3 col-md-6">
        <div class="mini-stat-card card custom-card mb-0 h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-muted mb-1 fs-12">الأقسام</p>
                    <h3 class="mb-0 text-primary">{{ $stats['total_departments'] ?? 0 }}</h3>
                </div>
                <span class="avatar avatar-md bg-primary-transparent rounded">
                    <i class="ri-building-line fs-18 text-primary"></i>
                </span>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="mini-stat-card card custom-card mb-0 h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-muted mb-1 fs-12">المناصب</p>
                    <h3 class="mb-0 text-success">{{ $stats['total_positions'] ?? 0 }}</h3>
                </div>
                <span class="avatar avatar-md bg-success-transparent rounded">
                    <i class="ri-briefcase-line fs-18 text-success"></i>
                </span>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="mini-stat-card card custom-card mb-0 h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-muted mb-1 fs-12">الفروع</p>
                    <h3 class="mb-0 text-info">{{ $stats['total_branches'] ?? 0 }}</h3>
                </div>
                <span class="avatar avatar-md bg-info-transparent rounded">
                    <i class="ri-map-pin-line fs-18 text-info"></i>
                </span>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="mini-stat-card card custom-card mb-0 h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-muted mb-1 fs-12">معدل الحضور الشهري</p>
                    <h3 class="mb-0 text-warning">{{ $attendanceStats['monthly_attendance_rate'] ?? 0 }}%</h3>
                </div>
                <span class="avatar avatar-md bg-warning-transparent rounded">
                    <i class="ri-line-chart-line fs-18 text-warning"></i>
                </span>
            </div>
        </div>
    </div>
</div>
