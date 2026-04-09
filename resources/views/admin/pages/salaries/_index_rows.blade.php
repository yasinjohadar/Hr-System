@forelse ($salaries as $salary)
    <tr>
        <th scope="row">{{ ($salaries->firstItem() ?? 0) + $loop->index }}</th>
        <td>
            <strong>{{ $salary->employee->full_name ?? $salary->employee->first_name . ' ' . $salary->employee->last_name }}</strong>
            <br><small class="text-muted">{{ $salary->employee->employee_code ?? '' }}</small>
        </td>
        <td>
            {{ $salary->month_name }} {{ $salary->salary_year }}
        </td>
        <td>{{ number_format($salary->base_salary, 2) }} {{ $salary->currency->symbol_ar ?? $salary->currency->symbol ?? 'ر.س' }}</td>
        <td>{{ number_format($salary->allowances, 2) }}</td>
        <td>{{ number_format($salary->bonuses, 2) }}</td>
        <td class="text-danger">-{{ number_format($salary->deductions, 2) }}</td>
        <td>
            <strong class="text-success">{{ number_format($salary->total_salary, 2) }}</strong>
        </td>
        <td>
            @if ($salary->payment_status == 'paid')
                <span class="badge bg-success">مدفوع</span>
            @elseif ($salary->payment_status == 'pending')
                <span class="badge bg-warning">قيد الانتظار</span>
            @else
                <span class="badge bg-danger">ملغي</span>
            @endif
        </td>
        <td>
            {{ $salary->payment_date ? $salary->payment_date->format('Y-m-d') : '-' }}
        </td>
        <td>
            @can('salary-show')
                <a class="btn btn-info btn-sm me-1" href="{{ route('admin.salaries.show', $salary->id) }}" title="عرض">
                    <i class="fa-solid fa-eye"></i>
                </a>
            @endcan
            @can('salary-edit')
                <a class="btn btn-warning btn-sm me-1" href="{{ route('admin.salaries.edit', $salary->id) }}" title="تعديل">
                    <i class="fa-solid fa-pen-to-square"></i>
                </a>
            @endcan
            @can('salary-delete')
                <a class="btn btn-danger btn-sm me-1" data-bs-toggle="modal" data-bs-target="#delete{{ $salary->id }}" title="حذف">
                    <i class="fa-solid fa-trash-can"></i>
                </a>
            @endcan
        </td>
    </tr>
    @include('admin.pages.salaries.delete')
@empty
    <tr>
        <td colspan="11" class="text-center text-danger fw-bold">لا توجد بيانات متاحة</td>
    </tr>
@endforelse
