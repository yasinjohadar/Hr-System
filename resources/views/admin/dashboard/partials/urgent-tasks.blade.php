<div class="card custom-card mb-4">
    <div class="card-header card-header-accent d-flex align-items-center gap-2">
        <i class="ri-alarm-warning-line text-warning fs-18"></i>
        <h6 class="card-title fw-semibold mb-0">المهام العاجلة</h6>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-xl-2 col-md-4 col-6">
                <a href="{{ route('admin.leave-requests.index', ['status' => 'pending']) }}" class="urgent-card urgent-card--warning">
                    <p class="text-muted mb-1 fs-12">طلبات إجازة</p>
                    <div class="urgent-count text-warning">{{ $urgentTasks['pending_leaves'] ?? 0 }}</div>
                </a>
            </div>
            <div class="col-xl-2 col-md-4 col-6">
                <a href="{{ route('admin.expense-requests.index', ['status' => 'pending']) }}" class="urgent-card urgent-card--info">
                    <p class="text-muted mb-1 fs-12">طلبات مصروفات</p>
                    <div class="urgent-count text-info">{{ $urgentTasks['pending_expenses'] ?? 0 }}</div>
                </a>
            </div>
            <div class="col-xl-2 col-md-4 col-6">
                <a href="{{ route('admin.tickets.index', ['status' => 'open']) }}" class="urgent-card urgent-card--danger">
                    <p class="text-muted mb-1 fs-12">تذاكر مفتوحة</p>
                    <div class="urgent-count text-danger">{{ $urgentTasks['open_tickets'] ?? 0 }}</div>
                </a>
            </div>
            <div class="col-xl-2 col-md-4 col-6">
                <a href="{{ route('admin.employee-violations.index', ['status' => 'pending']) }}" class="urgent-card urgent-card--secondary">
                    <p class="text-muted mb-1 fs-12">مخالفات</p>
                    <div class="urgent-count">{{ $urgentTasks['pending_violations'] ?? 0 }}</div>
                </a>
            </div>
            <div class="col-xl-2 col-md-4 col-6">
                <a href="{{ route('admin.meetings.index', ['status' => 'scheduled']) }}" class="urgent-card urgent-card--primary">
                    <p class="text-muted mb-1 fs-12">اجتماعات قادمة</p>
                    <div class="urgent-count text-primary">{{ $urgentTasks['upcoming_meetings'] ?? 0 }}</div>
                </a>
            </div>
            <div class="col-xl-2 col-md-4 col-6">
                <a href="{{ route('admin.tasks.index', ['status' => 'overdue']) }}" class="urgent-card urgent-card--dark">
                    <p class="text-muted mb-1 fs-12">مهام متأخرة</p>
                    <div class="urgent-count">{{ $urgentTasks['overdue_tasks'] ?? 0 }}</div>
                </a>
            </div>
        </div>
    </div>
</div>
