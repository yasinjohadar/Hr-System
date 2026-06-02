@foreach($assignedAssets as $assignment)
    <div class="p-3 dashboard-list-item">
        <div class="d-flex align-items-center">
            <div class="avatar avatar-sm bg-teal-transparent avatar-rounded me-3">
                <i class="ri-computer-line text-teal"></i>
            </div>
            <div>
                <h6 class="mb-1 fs-14 fw-semibold">{{ $assignment->asset->name ?? '-' }}</h6>
                <p class="text-muted fs-13 mb-0">
                    <i class="ri-calendar-line me-1"></i>
                    {{ $assignment->assigned_date->format('Y/m/d') }}
                </p>
            </div>
        </div>
    </div>
@endforeach
