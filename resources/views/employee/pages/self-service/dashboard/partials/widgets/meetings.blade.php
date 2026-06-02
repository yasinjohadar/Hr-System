@forelse($upcomingMeetings as $meeting)
    <div class="p-3 dashboard-list-item">
        <div class="d-flex align-items-start">
            <div class="avatar avatar-sm bg-primary-transparent avatar-rounded me-3">
                <i class="ri-video-on-line text-primary"></i>
            </div>
            <div>
                <h6 class="mb-1 fs-14 fw-semibold">{{ $meeting->title }}</h6>
                <p class="text-muted fs-12 mb-1">
                    <i class="ri-time-line me-1"></i>{{ \Carbon\Carbon::parse($meeting->start_time)->format('Y/m/d H:i') }}
                </p>
                @if($meeting->location)
                    <p class="text-muted fs-12 mb-0"><i class="ri-map-pin-line me-1"></i>{{ $meeting->location }}</p>
                @endif
            </div>
        </div>
    </div>
@empty
    <div class="text-center text-muted py-4">
        <i class="ri-calendar-check-line fs-24 d-block mb-2"></i>
        لا توجد اجتماعات قادمة
    </div>
@endforelse
