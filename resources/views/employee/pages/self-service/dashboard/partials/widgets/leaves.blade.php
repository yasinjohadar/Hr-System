@forelse($recentLeaves as $leave)
    <div class="p-3 dashboard-list-item">
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <h6 class="mb-1 fs-14 fw-semibold">{{ $leave->leaveType->name_ar ?? $leave->leaveType->name }}</h6>
                <p class="text-muted fs-13 mb-1">
                    <i class="ri-calendar-line me-1"></i>
                    {{ $leave->start_date->format('Y/m/d') }} — {{ $leave->end_date->format('Y/m/d') }}
                </p>
                <small class="text-muted">{{ $leave->days_count }} يوم</small>
            </div>
            <span class="badge bg-{{ $leave->status === 'approved' ? 'success' : ($leave->status === 'rejected' ? 'danger' : 'warning') }}-transparent">
                {{ $leave->status_name_ar }}
            </span>
        </div>
    </div>
@empty
    <div class="text-center text-muted py-4">
        <i class="ri-sun-line fs-24 d-block mb-2"></i>
        لا توجد طلبات إجازة
    </div>
@endforelse
