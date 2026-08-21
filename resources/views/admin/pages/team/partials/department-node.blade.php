{{--
    عقدة قسم واحدة في شجرة الهيكل التنظيمي — تُستدعي بشكل تكراري لعرض
    الأقسام الفرعية. $level يحدّد عمق التعشيش (0 = المستوى الأعلى) ويُستخدم
    للإزاحة البصرية فقط.
--}}
<div class="admin-page-card admin-org-node admin-org-node--level-{{ min($level, 2) }}"
     @if ($level > 0) style="margin-inline-start: {{ $level * 1.25 }}rem;" @endif>
    <div class="card-toolbar">
        <div class="d-flex align-items-center gap-2">
            <span class="admin-avatar-initial admin-org-node__icon">
                <i class="ri-building-line"></i>
            </span>
            <div>
                <h6 class="mb-0 fw-bold">{{ $node['department']->name }}</h6>
                @if ($node['department']->code)
                    <span class="admin-badge admin-badge-muted">{{ $node['department']->code }}</span>
                @endif
            </div>
        </div>
        @if ($node['department']->employees->isNotEmpty())
            <span class="admin-badge admin-badge-role">{{ $node['department']->employees->count() }} موظف</span>
        @endif
    </div>

    <div class="admin-form-body">
        @if ($node['department']->manager)
            <div class="admin-org-node__manager">
                <span class="admin-avatar-initial" style="width:1.75rem; height:1.75rem; font-size:0.8rem;">
                    {{ mb_substr($node['department']->manager->name, 0, 1) }}
                </span>
                <div>
                    <small class="text-muted d-block">رئيس القسم</small>
                    <span class="fw-semibold">{{ $node['department']->manager->name }}</span>
                </div>
            </div>
        @endif

        @if ($node['department']->employees->isNotEmpty())
            <div class="admin-org-node__employees">
                @foreach ($node['department']->employees as $emp)
                    <div class="admin-org-node__employee">
                        <span class="admin-avatar-initial" style="width:1.75rem; height:1.75rem; font-size:0.75rem; background:linear-gradient(135deg,#8b5cf6,#a78bfa);">
                            {{ mb_substr($emp->first_name, 0, 1) }}
                        </span>
                        <div class="min-w-0">
                            <div class="fs-13 fw-semibold text-truncate">{{ $emp->full_name }}</div>
                            @if ($emp->position)
                                <small class="text-muted text-truncate d-block">{{ $emp->position->title }}</small>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @elseif (! $node['department']->manager)
            <div class="admin-empty-state py-3">
                <i class="ri-user-line"></i>
                لا يوجد موظفون في هذا القسم مباشرة
            </div>
        @endif
    </div>
</div>

@foreach ($node['children'] as $child)
    <div class="mt-3">
        @include('admin.pages.team.partials.department-node', ['node' => $child, 'level' => $level + 1])
    </div>
@endforeach
