@forelse($upcomingTasks as $task)
    <div class="p-3 dashboard-list-item">
        <div class="d-flex align-items-start">
            <div class="avatar avatar-sm bg-{{ $task->status === 'in_progress' ? 'primary' : 'info' }}-transparent avatar-rounded me-3">
                <i class="ri-checkbox-circle-line text-{{ $task->status === 'in_progress' ? 'primary' : 'info' }}"></i>
            </div>
            <div>
                <h6 class="mb-1 fs-14 fw-semibold">{{ $task->title }}</h6>
                @if($task->project)
                    <p class="text-muted fs-12 mb-1"><i class="ri-folder-line me-1"></i>{{ $task->project->name }}</p>
                @endif
                <p class="text-muted fs-12 mb-1">
                    <i class="ri-calendar-line me-1"></i>
                    {{ $task->due_date ? \Carbon\Carbon::parse($task->due_date)->format('Y/m/d') : 'بدون تاريخ' }}
                </p>
                <span class="badge bg-{{ $task->status === 'in_progress' ? 'primary' : ($task->status === 'pending' ? 'warning' : 'info') }}-transparent">
                    {{ $task->status_name_ar ?? $task->status }}
                </span>
            </div>
        </div>
    </div>
@empty
    <div class="text-center text-muted py-4">
        <i class="ri-task-line fs-24 d-block mb-2"></i>
        لا توجد مهام نشطة
    </div>
@endforelse
