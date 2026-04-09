@forelse ($attendances as $attendance)
    <tr>
        <th scope="row">{{ ($attendances->firstItem() ?? 0) + $loop->index }}</th>
        <td>
            <strong>{{ $attendance->employee->full_name ?? $attendance->employee->first_name . ' ' . $attendance->employee->last_name }}</strong>
            <br><small class="text-muted">{{ $attendance->employee->employee_code ?? '' }}</small>
        </td>
        <td>{{ $attendance->attendance_date->format('Y-m-d') }}</td>
        <td>
            @if ($attendance->check_in)
                <span class="badge bg-success">{{ is_string($attendance->check_in) ? $attendance->check_in : $attendance->check_in->format('H:i') }}</span>
            @else
                <span class="text-muted">-</span>
            @endif
        </td>
        <td>
            @if ($attendance->check_out)
                <span class="badge bg-info">{{ is_string($attendance->check_out) ? $attendance->check_out : $attendance->check_out->format('H:i') }}</span>
            @else
                <span class="text-muted">-</span>
            @endif
        </td>
        <td>
            @if ($attendance->hours_worked > 0)
                <span class="badge bg-primary">{{ $attendance->hours_worked_formatted }}</span>
            @else
                <span class="text-muted">-</span>
            @endif
        </td>
        <td>
            @if ($attendance->late_minutes > 0)
                <span class="badge bg-warning">{{ $attendance->late_minutes }} دقيقة</span>
            @else
                <span class="text-muted">-</span>
            @endif
        </td>
        <td>
            @if ($attendance->overtime_minutes > 0)
                <span class="badge bg-success">{{ floor($attendance->overtime_minutes / 60) }}:{{ str_pad($attendance->overtime_minutes % 60, 2, '0', STR_PAD_LEFT) }}</span>
            @else
                <span class="text-muted">-</span>
            @endif
        </td>
        <td>
            @if ($attendance->status == 'present')
                <span class="badge bg-success">حاضر</span>
            @elseif ($attendance->status == 'absent')
                <span class="badge bg-danger">غائب</span>
            @elseif ($attendance->status == 'late')
                <span class="badge bg-warning">متأخر</span>
            @elseif ($attendance->status == 'half_day')
                <span class="badge bg-info">نصف يوم</span>
            @elseif ($attendance->status == 'on_leave')
                <span class="badge bg-secondary">في إجازة</span>
            @else
                <span class="badge bg-primary">عطلة</span>
            @endif
        </td>
        <td>
            @can('attendance-show')
                <a class="btn btn-info btn-sm me-1" href="{{ route('admin.attendances.show', $attendance->id) }}" title="عرض">
                    <i class="fa-solid fa-eye"></i>
                </a>
            @endcan
            @can('attendance-edit')
                <a class="btn btn-warning btn-sm me-1" href="{{ route('admin.attendances.edit', $attendance->id) }}" title="تعديل">
                    <i class="fa-solid fa-pen-to-square"></i>
                </a>
            @endcan
            @can('attendance-delete')
                <a class="btn btn-danger btn-sm me-1" data-bs-toggle="modal" data-bs-target="#delete{{ $attendance->id }}" title="حذف">
                    <i class="fa-solid fa-trash-can"></i>
                </a>
            @endcan
        </td>
    </tr>
    @include('admin.pages.attendances.delete')
@empty
    <tr>
        <td colspan="10" class="text-center text-danger fw-bold">لا توجد بيانات متاحة</td>
    </tr>
@endforelse
