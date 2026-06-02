@foreach($announcements as $announcement)
    <div class="p-3 dashboard-list-item">
        <h6 class="mb-1 fw-semibold">{{ $announcement->title }}</h6>
        @if($announcement->content)
            <p class="text-muted fs-13 mb-2">{{ Str::limit(strip_tags($announcement->content), 120) }}</p>
        @endif
        <small class="text-muted">
            <i class="ri-calendar-line me-1"></i>
            {{ $announcement->publish_date?->format('Y/m/d') ?? $announcement->created_at->format('Y/m/d') }}
        </small>
    </div>
@endforeach
