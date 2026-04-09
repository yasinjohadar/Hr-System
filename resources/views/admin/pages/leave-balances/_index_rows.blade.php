@forelse ($leaveBalances as $balance)
    <tr>
        <th scope="row">{{ ($leaveBalances->firstItem() ?? 0) + $loop->index }}</th>
        <td>
            <strong>{{ $balance->employee->full_name ?? $balance->employee->first_name . ' ' . $balance->employee->last_name }}</strong>
        </td>
        <td>
            <span class="badge rounded-pill bg-info bg-opacity-10 text-info border border-info border-opacity-25">
                {{ $balance->leaveType->name_ar ?? $balance->leaveType->name }}
            </span>
        </td>
        <td class="text-nowrap">{{ $balance->year }}</td>
        <td class="text-center text-nowrap">
            <span class="fw-semibold">{{ $balance->total_days }}</span>
            <span class="text-muted small">يوم</span>
        </td>
        <td class="text-center text-nowrap">
            <span class="fw-semibold">{{ $balance->used_days }}</span>
            <span class="text-muted small">يوم</span>
        </td>
        <td class="text-center text-nowrap">
            @if ($balance->remaining_days > 0)
                <span class="fw-semibold text-success">{{ $balance->remaining_days }}</span>
            @else
                <span class="fw-semibold text-danger">{{ $balance->remaining_days }}</span>
            @endif
            <span class="text-muted small"> يوم</span>
        </td>
        <td class="text-center text-nowrap">
            <span class="fw-semibold">{{ $balance->carried_forward }}</span>
            <span class="text-muted small">يوم</span>
        </td>
        <td>
            @can('leave-balance-edit')
                <a class="btn btn-outline-warning btn-sm me-1" href="{{ route('admin.leave-balances.edit', $balance->id) }}" title="تعديل">
                    <i class="fa-solid fa-pen-to-square"></i>
                </a>
            @endcan
            @can('leave-balance-delete')
                <button type="button" class="btn btn-outline-danger btn-sm me-1" data-bs-toggle="modal" data-bs-target="#delete{{ $balance->id }}" title="حذف">
                    <i class="fa-solid fa-trash-can"></i>
                </button>
            @endcan
        </td>
    </tr>
    @include('admin.pages.leave-balances.delete')
@empty
    <tr>
        <td colspan="9" class="text-center text-danger fw-bold">لا توجد بيانات متاحة</td>
    </tr>
@endforelse
