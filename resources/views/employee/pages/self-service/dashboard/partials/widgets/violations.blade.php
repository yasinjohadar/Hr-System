@foreach($recentViolations as $violation)
    <div class="p-3 dashboard-list-item">
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <h6 class="mb-1 fs-14 fw-semibold">{{ $violation->violationType->name_ar ?? $violation->violationType->name }}</h6>
                <p class="text-muted fs-13 mb-1">
                    <i class="ri-calendar-line me-1"></i>{{ $violation->violation_date->format('Y/m/d') }}
                </p>
                @if($violation->description)
                    <p class="text-muted fs-13 mb-0">{{ Str::limit($violation->description, 100) }}</p>
                @endif
            </div>
            <span class="badge bg-danger-transparent">{{ $violation->status_name_ar ?? $violation->status }}</span>
        </div>
    </div>
@endforeach
