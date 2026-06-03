@forelse ($leaveRequests as $request)
    <div class="leave-table-row">
        <span class="row-index">{{ ($leaveRequests->firstItem() ?? 0) + $loop->index }}</span>

        <div class="leave-mobile-field">
            <span class="leave-mobile-label">الموظف</span>
            <span class="employee-name" title="{{ $request->employee->full_name ?? '' }}">
                {{ $request->employee->full_name ?? $request->employee->first_name . ' ' . $request->employee->last_name }}
            </span>
        </div>

        <div class="leave-mobile-field">
            <span class="leave-mobile-label">نوع الإجازة</span>
            <span class="type-pill" title="{{ $request->leaveType->name_ar ?? $request->leaveType->name }}">
                {{ $request->leaveType->name_ar ?? $request->leaveType->name }}
            </span>
        </div>

        <div class="leave-mobile-field">
            <span class="leave-mobile-label">من تاريخ</span>
            <span>{{ $request->start_date->format('Y/m/d') }}</span>
        </div>

        <div class="leave-mobile-field">
            <span class="leave-mobile-label">إلى تاريخ</span>
            <span>{{ $request->end_date->format('Y/m/d') }}</span>
        </div>

        <div class="leave-mobile-field">
            <span class="leave-mobile-label">عدد الأيام</span>
            <span class="days-pill">{{ $request->days_count }} يوم</span>
        </div>

        <div class="leave-mobile-field">
            <span class="leave-mobile-label">الحالة</span>
            @php
                $progress = $workflowProgressById[$request->id] ?? null;
                $badgeAr = $progress['badge_ar'] ?? $request->status_name_ar;
                $badgeVariant = $progress['badge_variant'] ?? $request->status;
            @endphp
            <span class="status-pill status-pill--{{ $badgeVariant }}">{{ $badgeAr }}</span>
        </div>

        <div class="row-actions">
            @can('leave-request-show')
                <a class="btn-action btn-action--view" href="{{ route('admin.leave-requests.show', $request->id) }}" title="عرض">
                    <i class="ri-eye-line"></i>
                </a>
            @endcan
            @if ($request->status == 'pending' && ($canApproveNowById[$request->id] ?? false))
                <button type="button" class="btn-action btn-action--approve" data-bs-toggle="modal"
                    data-bs-target="#approve{{ $request->id }}" title="موافقة">
                    <i class="ri-check-line"></i>
                </button>
                <button type="button" class="btn-action btn-action--reject" data-bs-toggle="modal" data-bs-target="#reject{{ $request->id }}" title="رفض">
                    <i class="ri-close-line"></i>
                </button>
            @endif
            @if ($request->status == 'pending')
                @can('leave-request-edit')
                    <a class="btn-action btn-action--edit" href="{{ route('admin.leave-requests.edit', $request->id) }}" title="تعديل">
                        <i class="ri-pencil-line"></i>
                    </a>
                @endcan
            @endif
            @can('leave-request-delete')
                <button type="button" class="btn-action btn-action--delete" data-bs-toggle="modal" data-bs-target="#delete{{ $request->id }}" title="حذف">
                    <i class="ri-delete-bin-line"></i>
                </button>
            @endcan
        </div>
    </div>

    @include('admin.pages.leave-requests.delete')
    @if ($request->status == 'pending' && ($canApproveNowById[$request->id] ?? false))
        @include('admin.pages.leave-requests.reject')
        @include('admin.pages.leave-requests.approve')
    @endif
@empty
    <div class="empty-state">لا توجد طلبات إجازة</div>
@endforelse
