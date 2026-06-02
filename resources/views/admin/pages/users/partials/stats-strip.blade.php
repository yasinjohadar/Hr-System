<div class="row g-3 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="stat-pill">
            <p class="text-muted mb-1 fs-12">إجمالي المستخدمين</p>
            <h4 class="mb-0 fw-semibold text-primary">{{ $userStats['total'] ?? 0 }}</h4>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-pill">
            <p class="text-muted mb-1 fs-12">تفعيل الدخول نشط</p>
            <h4 class="mb-0 fw-semibold text-success">{{ $userStats['active_login'] ?? 0 }}</h4>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-pill">
            <p class="text-muted mb-1 fs-12">حسابات موقوفة</p>
            <h4 class="mb-0 fw-semibold text-warning">{{ $userStats['inactive_status'] ?? 0 }}</h4>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-pill">
            <p class="text-muted mb-1 fs-12">محظورون</p>
            <h4 class="mb-0 fw-semibold text-danger">{{ $userStats['banned'] ?? 0 }}</h4>
        </div>
    </div>
</div>
