@forelse ($leaveRequests as $request)
    <tr>
        <th scope="row">{{ ($leaveRequests->firstItem() ?? 0) + $loop->index }}</th>
        <td>
            <strong>{{ $request->employee->full_name ?? $request->employee->first_name . ' ' . $request->employee->last_name }}</strong>
        </td>
        <td>
            <span class="badge bg-info">{{ $request->leaveType->name_ar ?? $request->leaveType->name }}</span>
        </td>
        <td>{{ $request->start_date->format('Y-m-d') }}</td>
        <td>{{ $request->end_date->format('Y-m-d') }}</td>
        <td><span class="badge bg-primary">{{ $request->days_count }} يوم</span></td>
        <td>
            @if ($request->status == 'approved')
                <span class="badge bg-success">موافق عليه</span>
            @elseif ($request->status == 'pending')
                <span class="badge bg-warning">قيد الانتظار</span>
            @elseif ($request->status == 'rejected')
                <span class="badge bg-danger">مرفوض</span>
            @else
                <span class="badge bg-secondary">ملغي</span>
            @endif
        </td>
        <td>
            @can('leave-request-show')
                <a class="btn btn-info btn-sm me-1" href="{{ route('admin.leave-requests.show', $request->id) }}" title="عرض">
                    <i class="fa-solid fa-eye"></i>
                </a>
            @endcan
            @if ($request->status == 'pending')
                @can('leave-request-approve')
                    <button type="button" class="btn btn-success btn-sm me-1" data-bs-toggle="modal"
                        data-bs-target="#approve{{ $request->id }}" title="موافقة">
                        <i class="fa-solid fa-check"></i>
                    </button>
                @endcan
                @can('leave-request-approve')
                    <button type="button" class="btn btn-danger btn-sm me-1" data-bs-toggle="modal" data-bs-target="#reject{{ $request->id }}" title="رفض">
                        <i class="fa-solid fa-times"></i>
                    </button>
                @endcan
                @can('leave-request-edit')
                    <a class="btn btn-warning btn-sm me-1" href="{{ route('admin.leave-requests.edit', $request->id) }}" title="تعديل">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </a>
                @endcan
            @endif
            @can('leave-request-delete')
                <a class="btn btn-danger btn-sm me-1" data-bs-toggle="modal" data-bs-target="#delete{{ $request->id }}" title="حذف">
                    <i class="fa-solid fa-trash-can"></i>
                </a>
            @endcan
        </td>
    </tr>
    @include('admin.pages.leave-requests.delete')
    @include('admin.pages.leave-requests.reject')
    @include('admin.pages.leave-requests.approve')
@empty
    <tr>
        <td colspan="8" class="text-center text-danger fw-bold">لا توجد بيانات متاحة</td>
    </tr>
@endforelse
