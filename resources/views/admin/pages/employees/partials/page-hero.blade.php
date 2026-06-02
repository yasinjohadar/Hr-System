<div class="card custom-card employees-hero bg-primary-gradient mb-4">
    <div class="card-body py-4">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="hero-icon-wrap">
                    <i class="ri-team-line"></i>
                </div>
                <div class="text-white">
                    <h4 class="mb-1 text-white">{{ $heroTitle }}</h4>
                    @if (!empty($heroSubtitle))
                        <p class="mb-0 op-8 fs-13">{{ $heroSubtitle }}</p>
                    @endif
                </div>
            </div>
            @if (!empty($heroActions))
                <div class="d-flex flex-wrap gap-2">
                    {!! $heroActions !!}
                </div>
            @endif
        </div>
    </div>
</div>
