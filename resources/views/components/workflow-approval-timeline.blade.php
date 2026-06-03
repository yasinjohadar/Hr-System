@props(['workflowProgress' => null, 'compact' => false])

@php
    $timeline = $workflowProgress['timeline'] ?? null;
    $steps = $timeline['steps'] ?? [];
@endphp

@if (!empty($steps))
    <div {{ $attributes->merge(['class' => 'workflow-approval-timeline' . ($compact ? ' workflow-approval-timeline--compact' : '')]) }}>
        @if (! $compact)
            <h6 class="fw-semibold mb-3">تسلسل الموافقات</h6>
        @endif
        <ul class="list-unstyled mb-0 workflow-timeline-list">
            @foreach ($steps as $stepData)
                @php
                    $step = $stepData['step'];
                    $status = $stepData['status'];
                    $action = $stepData['action'] ?? null;
                    $stepLabel = $step->name_ar ?? $step->name;
                @endphp
                <li class="workflow-timeline-item workflow-timeline-item--{{ $status }} mb-3">
                    <div class="d-flex align-items-start gap-2">
                        <span class="workflow-timeline-marker flex-shrink-0" aria-hidden="true">
                            @if ($status === 'completed')
                                <i class="ri-checkbox-circle-fill text-success"></i>
                            @elseif ($status === 'current')
                                <i class="ri-time-line text-warning"></i>
                            @else
                                <i class="ri-checkbox-blank-circle-line text-muted"></i>
                            @endif
                        </span>
                        <div class="flex-grow-1">
                            <div class="fw-medium">{{ $stepLabel }}</div>
                            @if ($status === 'completed' && $action)
                                <div class="text-muted fs-12 mt-1">
                                    {{ $action->action_name_ar }}
                                    @if ($stepData['action_user'])
                                        — {{ $stepData['action_user']->name }}
                                    @endif
                                    @if ($stepData['acted_at'])
                                        <span class="d-block">{{ $stepData['acted_at']->format('Y/m/d H:i') }}</span>
                                    @endif
                                </div>
                                @if (! empty($stepData['comments']))
                                    <div class="mt-1 fs-12 text-secondary">
                                        <i class="ri-chat-3-line me-1"></i>{{ $stepData['comments'] }}
                                    </div>
                                @endif
                            @elseif ($status === 'current')
                                <div class="text-warning fs-12 mt-1">
                                    بانتظار
                                    @if ($stepData['expected_approver'])
                                       : {{ $stepData['expected_approver']->name }}
                                    @endif
                                </div>
                            @else
                                <div class="text-muted fs-12 mt-1">لم تبدأ بعد</div>
                            @endif
                        </div>
                    </div>
                </li>
            @endforeach
        </ul>
    </div>
@endif
