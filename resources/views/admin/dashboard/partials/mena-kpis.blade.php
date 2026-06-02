@if(!empty($menaKpis))
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card custom-card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted small">عطل رسمية (السنة)</div>
                <div class="fs-4 fw-bold">{{ $menaKpis['public_holidays_this_year'] ?? 0 }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card custom-card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted small">مستندات تنتهي خلال 30 يوماً</div>
                <div class="fs-4 fw-bold text-warning">{{ $menaKpis['documents_expiring_30d'] ?? 0 }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card custom-card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted small">شهادات تنتهي خلال 30 يوماً</div>
                <div class="fs-4 fw-bold text-warning">{{ $menaKpis['certificates_expiring_30d'] ?? 0 }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card custom-card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted small">إجازات معلقة</div>
                <div class="fs-4 fw-bold">{{ $menaKpis['pending_leave_requests'] ?? 0 }}</div>
            </div>
        </div>
    </div>
</div>
@endif
