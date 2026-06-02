<div class="card custom-card" style="{{ $level > 0 ? 'margin-left: ' . ($level * 20) . 'px;' : '' }}">
    <div class="card-header bg-{{ $level === 0 ? 'primary text-white' : 'light' }}">
        <h6 class="card-title fw-semibold mb-0">
            <i class="ri-building-line me-1"></i>{{ $node['department']->name }}
            @if($node['department']->code)
                <small class="text-muted">({{ $node['department']->code }})</small>
            @endif
        </h6>
    </div>
    <div class="card-body">
        @if($node['department']->manager)
            <div class="mb-3">
                <small class="text-muted d-block">رئيس القسم:</small>
                <span class="fw-medium">{{ $node['department']->manager->name }}</span>
            </div>
        @endif

        @if($node['department']->employees->isNotEmpty())
            <div class="mb-2">
                <small class="text-muted d-block mb-2">الموظفون ({{ $node['department']->employees->count() }}):</small>
                @foreach($node['department']->employees as $emp)
                    <div class="d-flex align-items-center mb-2 p-2 bg-light rounded">
                        <div class="avatar avatar-xs bg-primary-transparent avatar-rounded me-2">
                            {{ substr($emp->first_name, 0, 1) }}
                        </div>
                        <div>
                            <span class="fs-13 fw-medium">{{ $emp->full_name }}</span>
                            @if($emp->position)
                                <br><small class="text-muted">{{ $emp->position->title }}</small>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

@foreach($node['children'] as $child)
    <div class="mt-3">
        @include('employee.pages.department-head.partials.department-node', ['node' => $child, 'level' => $level + 1])
    </div>
@endforeach
