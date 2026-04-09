@forelse ($employees as $employee)
    <tr>
        <th scope="row">{{ ($employees->firstItem() ?? 0) + $loop->index }}</th>
        <td>
            <strong>{{ $employee->employee_code }}</strong>
        </td>
        <td>
            <a href="{{ route('admin.employees.show', $employee->id) }}"
                class="text-decoration-none">
                {{ $employee->full_name }}
            </a>
        </td>
        <td>
            @if ($employee->department)
                <span class="badge bg-info">{{ $employee->department->name }}</span>
            @else
                <span class="text-muted">-</span>
            @endif
        </td>
        <td>
            @if ($employee->position)
                <span class="badge bg-primary">{{ $employee->position->title }}</span>
            @else
                <span class="text-muted">-</span>
            @endif
        </td>
        <td>
            @if ($employee->personal_email)
                <a href="mailto:{{ $employee->personal_email }}"
                    class="text-primary text-decoration-none">
                    {{ $employee->personal_email }}
                </a>
            @else
                <span class="text-muted">-</span>
            @endif
        </td>
        <td>
            @if ($employee->personal_phone)
                {{ $employee->personal_phone }}
            @else
                <span class="text-muted">-</span>
            @endif
        </td>
        <td>
            {{ $employee->hire_date ? $employee->hire_date->format('Y-m-d') : '-' }}
        </td>
        <td>
            @if ($employee->employment_status === 'active')
                <span class="badge bg-success">نشط</span>
            @elseif($employee->employment_status === 'on_leave')
                <span class="badge bg-warning text-dark">في إجازة</span>
            @elseif($employee->employment_status === 'terminated')
                <span class="badge bg-danger">منتهي</span>
            @elseif($employee->employment_status === 'resigned')
                <span class="badge bg-secondary">استقال</span>
            @else
                <span class="badge bg-secondary">غير معروف</span>
            @endif
        </td>
        <td>
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox"
                       {{ $employee->is_active ? 'checked' : '' }}
                       disabled>
                <label class="form-check-label">
                    {{ $employee->is_active ? 'نشط' : 'غير نشط' }}
                </label>
            </div>
        </td>
        <td>
            @can('employee-edit')
                <a class="btn btn-info btn-sm me-1"
                    href="{{ route('admin.employees.edit', $employee->id) }}"
                    title="تعديل الموظف">
                    <i class="fa-solid fa-pen-to-square"></i>
                </a>
            @endcan
            @can('employee-show')
                <a class="btn btn-success btn-sm me-1"
                    href="{{ route('admin.employees.show', $employee->id) }}"
                    title="عرض التفاصيل">
                    <i class="fa-solid fa-eye"></i>
                </a>
            @endcan
            @if($employee->user_id && $employee->user && $employee->user->is_active)
                @can('employee-show')
                    <form action="{{ route('admin.employees.login-as', $employee) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-secondary btn-sm me-1" title="الدخول كموظف">
                            <i class="fa-solid fa-right-to-bracket"></i>
                        </button>
                    </form>
                @endcan
            @endif
            @can('employee-delete')
                <a class="btn btn-danger btn-sm me-1" data-bs-toggle="modal"
                    data-bs-target="#delete{{ $employee->id }}"
                    title="حذف الموظف">
                    <i class="fa-solid fa-trash-can"></i>
                </a>
            @endcan
        </td>
    </tr>

    @include('admin.pages.employees.delete')
@empty
    <tr>
        <td colspan="11" class="text-center text-danger fw-bold">لا توجد
            بيانات متاحة
        </td>
    </tr>
@endforelse
