<div class="card custom-card employee-detail-card h-100">
    <div class="card-header border-bottom py-3">
        <h6 class="mb-0 fw-semibold">
            <i class="{{ $icon }} text-primary me-2"></i>{{ $title }}
        </h6>
        @if (!empty($subtitle))
            <small class="text-muted">{{ $subtitle }}</small>
        @endif
    </div>
    <div class="card-body p-0">
        <dl class="employee-detail-list mb-0">
            @foreach ($rows as $row)
                <div class="detail-row">
                    <dt>
                        @if (!empty($row['icon']))
                            <i class="{{ $row['icon'] }} text-muted me-2"></i>
                        @endif
                        {{ $row['label'] }}
                    </dt>
                    <dd>{!! $row['value'] !!}</dd>
                </div>
            @endforeach
        </dl>
    </div>
</div>
