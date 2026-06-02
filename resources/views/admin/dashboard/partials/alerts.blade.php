<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="alert-tile d-flex align-items-center gap-3">
            <span class="alert-tile-icon bg-warning-transparent text-warning">
                <i class="ri-file-text-line"></i>
            </span>
            <div>
                <p class="text-muted mb-0 fs-12">مستندات تنتهي قريباً</p>
                <h4 class="mb-0 fw-semibold">{{ $importantNotifications['expiring_documents'] ?? 0 }}</h4>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="alert-tile d-flex align-items-center gap-3">
            <span class="alert-tile-icon bg-info-transparent text-info">
                <i class="ri-award-line"></i>
            </span>
            <div>
                <p class="text-muted mb-0 fs-12">شهادات تنتهي قريباً</p>
                <h4 class="mb-0 fw-semibold">{{ $importantNotifications['expiring_certificates'] ?? 0 }}</h4>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <a href="{{ route('admin.contracts.index', ['expiring' => 90]) }}" class="alert-tile d-flex align-items-center gap-3 text-decoration-none">
            <span class="alert-tile-icon bg-danger-transparent text-danger">
                <i class="ri-file-paper-2-line"></i>
            </span>
            <div>
                <p class="text-muted mb-0 fs-12">عقود تنتهي قريباً</p>
                <h4 class="mb-0 fw-semibold text-danger">{{ $importantNotifications['contracts_expiring'] ?? 0 }}</h4>
            </div>
        </a>
    </div>
</div>
