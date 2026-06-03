@forelse ($attendances as $attendance)
    @php
        $employeeName = $attendance->employee->full_name ?? trim($attendance->employee->first_name . ' ' . $attendance->employee->last_name);
        $checkIn = $attendance->check_in
            ? (is_string($attendance->check_in) ? $attendance->check_in : $attendance->check_in->format('H:i'))
            : null;
        $checkOut = $attendance->check_out
            ? (is_string($attendance->check_out) ? $attendance->check_out : $attendance->check_out->format('H:i'))
            : null;
        $overtime = $attendance->overtime_minutes > 0
            ? sprintf('%d:%02d', intdiv($attendance->overtime_minutes, 60), $attendance->overtime_minutes % 60)
            : null;
    @endphp
    <div class="att-table-row">
        <span class="row-index">{{ ($attendances->firstItem() ?? 0) + $loop->index }}</span>

        <div class="att-mobile-field">
            <span class="att-mobile-label">الموظف</span>
            <div class="min-w-0">
                <div class="cell-employee" title="{{ $employeeName }}">{{ $employeeName }}</div>
                @if ($attendance->employee->employee_code ?? null)
                    <div class="cell-employee-code">{{ $attendance->employee->employee_code }}</div>
                @endif
            </div>
        </div>

        <div class="att-mobile-field">
            <span class="att-mobile-label">التاريخ</span>
            <span>{{ $attendance->attendance_date->format('Y/m/d') }}</span>
        </div>

        <div class="att-mobile-field">
            <span class="att-mobile-label">وقت الدخول</span>
            <span>
                @if ($checkIn)
                    <span class="metric-pill metric-pill--in">{{ $checkIn }}</span>
                @else
                    <span class="cell-muted">—</span>
                @endif
            </span>
        </div>

        <div class="att-mobile-field">
            <span class="att-mobile-label">وقت الخروج</span>
            <span>
                @if ($checkOut)
                    <span class="metric-pill metric-pill--out">{{ $checkOut }}</span>
                @else
                    <span class="cell-muted">—</span>
                @endif
            </span>
        </div>

        <div class="att-mobile-field">
            <span class="att-mobile-label">ساعات العمل</span>
            <span>
                @if ($attendance->hours_worked > 0)
                    <span class="metric-pill metric-pill--hours">{{ $attendance->hours_worked_formatted }}</span>
                @else
                    <span class="cell-muted">—</span>
                @endif
            </span>
        </div>

        <div class="att-mobile-field">
            <span class="att-mobile-label">التأخير</span>
            <span>
                @if ($attendance->late_minutes > 0)
                    <span class="metric-pill metric-pill--late">{{ $attendance->late_minutes }} د</span>
                @else
                    <span class="cell-muted">—</span>
                @endif
            </span>
        </div>

        <div class="att-mobile-field">
            <span class="att-mobile-label">ساعات إضافية</span>
            <span>
                @if ($overtime)
                    <span class="metric-pill metric-pill--ot">{{ $overtime }}</span>
                @else
                    <span class="cell-muted">—</span>
                @endif
            </span>
        </div>

        <div class="att-mobile-field">
            <span class="att-mobile-label">الحالة</span>
            <span class="status-pill status-pill--{{ $attendance->status }}">{{ $attendance->status_ar }}</span>
        </div>

        <div class="row-actions">
            @can('attendance-show')
                <a class="btn-action btn-action--view" href="{{ route('admin.attendances.show', $attendance->id) }}" title="عرض">
                    <i class="ri-eye-line"></i>
                </a>
            @endcan
            @can('attendance-edit')
                <a class="btn-action btn-action--edit" href="{{ route('admin.attendances.edit', $attendance->id) }}" title="تعديل">
                    <i class="ri-pencil-line"></i>
                </a>
            @endcan
            @can('attendance-delete')
                <button type="button" class="btn-action btn-action--delete" data-bs-toggle="modal" data-bs-target="#delete{{ $attendance->id }}" title="حذف">
                    <i class="ri-delete-bin-line"></i>
                </button>
            @endcan
        </div>
    </div>

    @include('admin.pages.attendances.delete')
@empty
    <div class="empty-state">لا توجد سجلات حضور في الفترة المحددة</div>
@endforelse
