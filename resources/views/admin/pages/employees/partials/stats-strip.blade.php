<div class="row g-3 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="stat-pill">
            <p class="text-muted mb-1 fs-12">إجمالي الموظفين</p>
            <h4 class="mb-0 fw-semibold text-primary">{{ $employeeStats['total'] ?? 0 }}</h4>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-pill">
            <p class="text-muted mb-1 fs-12">موظفون مفعّلون</p>
            <h4 class="mb-0 fw-semibold text-success">{{ $employeeStats['active'] ?? 0 }}</h4>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-pill">
            <p class="text-muted mb-1 fs-12">غير مفعّلين</p>
            <h4 class="mb-0 fw-semibold text-secondary">{{ $employeeStats['inactive'] ?? 0 }}</h4>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-pill">
            <p class="text-muted mb-1 fs-12">في إجازة</p>
            <h4 class="mb-0 fw-semibold text-warning">{{ $employeeStats['on_leave'] ?? 0 }}</h4>
        </div>
    </div>
</div>
