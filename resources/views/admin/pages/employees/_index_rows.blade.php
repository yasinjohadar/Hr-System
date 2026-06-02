@forelse ($employees as $employee)
    <tr>
        <td class="text-muted">{{ ($employees->firstItem() ?? 0) + $loop->index }}</td>
        <td><span class="fw-semibold text-primary">{{ $employee->employee_code }}</span></td>
        <td>
            <a href="{{ route('admin.employees.show', $employee->id) }}" class="fw-semibold text-decoration-none">
                {{ $employee->full_name }}
            </a>
        </td>
        <td>
            @if ($employee->department)
                <span class="badge bg-info-transparent">{{ $employee->department->name }}</span>
            @else
                <span class="text-muted">—</span>
            @endif
        </td>
        <td>
            @if ($employee->position)
                <span class="badge bg-primary-transparent">{{ $employee->position->title }}</span>
            @else
                <span class="text-muted">—</span>
            @endif
        </td>
        <td>
            @if ($employee->personal_email)
                <a href="mailto:{{ $employee->personal_email }}" class="text-primary text-decoration-none small text-truncate d-inline-block" style="max-width: 160px;">
                    {{ $employee->personal_email }}
                </a>
            @else
                <span class="text-muted">—</span>
            @endif
        </td>
        <td>
            @if ($employee->personal_phone)
                <span class="small">{{ $employee->personal_phone }}</span>
            @else
                <span class="text-muted">—</span>
            @endif
        </td>
        <td class="small text-muted">
            {{ $employee->hire_date ? $employee->hire_date->format('Y-m-d') : '—' }}
        </td>
        <td>
            <div class="employee-status-cell">
                @can('employee-edit')
                    <div class="form-check form-switch employee-status-switch-wrap mb-0">
                        <input type="checkbox"
                            class="form-check-input employee-status-switch"
                            role="switch"
                            id="employee-status-{{ $employee->id }}"
                            data-employee-id="{{ $employee->id }}"
                            {{ $employee->is_active ? 'checked' : '' }}>
                        <label class="form-check-label small employee-status-label" for="employee-status-{{ $employee->id }}">
                            {{ $employee->is_active ? 'مفعّل' : 'معطّل' }}
                        </label>
                    </div>
                @else
                    <span class="badge bg-{{ $employee->is_active ? 'success' : 'secondary' }}-transparent">
                        {{ $employee->is_active ? 'مفعّل' : 'معطّل' }}
                    </span>
                @endcan
                @if ($employee->employment_status && $employee->employment_status !== 'active')
                    <span class="employment-status-hint badge bg-warning-transparent mt-1">
                        @if ($employee->employment_status === 'on_leave')
                            في إجازة
                        @elseif($employee->employment_status === 'terminated')
                            منتهي
                        @elseif($employee->employment_status === 'resigned')
                            استقال
                        @else
                            {{ $employee->employment_status }}
                        @endif
                    </span>
                @endif
            </div>
        </td>
        <td class="actions-cell">
            <div class="dropdown">
                <button class="btn actions-dropdown-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="ri-more-2-fill text-muted"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-actions shadow-sm">
                    @can('employee-show')
                        <li>
                            <a class="dropdown-item" href="{{ route('admin.employees.show', $employee->id) }}">
                                <i class="ri-eye-line text-info me-2"></i>عرض التفاصيل
                            </a>
                        </li>
                    @endcan
                    @can('employee-edit')
                        <li>
                            <a class="dropdown-item" href="{{ route('admin.employees.edit', $employee->id) }}">
                                <i class="ri-edit-line text-primary me-2"></i>تعديل
                            </a>
                        </li>
                    @endcan
                    @if ($employee->user_id && $employee->user && $employee->user->is_active)
                        @can('employee-show')
                            <li>
                                <a href="#" class="dropdown-item employee-login-code-btn"
                                    data-employee-id="{{ $employee->id }}"
                                    data-employee-name="{{ $employee->full_name }}">
                                    <i class="ri-link text-success me-2"></i>كود تسجيل الدخول
                                </a>
                            </li>
                            <li>
                                <form action="{{ route('admin.employees.login-as', $employee) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="dropdown-item">
                                        <i class="ri-login-box-line text-secondary me-2"></i>الدخول كموظف
                                    </button>
                                </form>
                            </li>
                        @endcan
                    @endif
                    @can('employee-delete')
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a href="#" class="dropdown-item text-danger" data-bs-toggle="modal" data-bs-target="#delete{{ $employee->id }}">
                                <i class="ri-delete-bin-line me-2"></i>حذف الموظف
                            </a>
                        </li>
                    @endcan
                </ul>
            </div>
        </td>
    </tr>

    @include('admin.pages.employees.delete')
@empty
    <tr>
        <td colspan="10" class="text-center text-muted py-5">
            <i class="ri-user-search-line fs-1 d-block mb-2 opacity-50"></i>
            لا توجد نتائج تطابق بحثك
        </td>
    </tr>
@endforelse
