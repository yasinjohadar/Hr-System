{{--
    صفوف جدول الرواتب.

    يُصيَّر مرّتين: مع الصفحة الكاملة، وكـ html_rows في ردّ AJAX الفلترة
    (SalaryController@index). لذلك أي عنصر يحتاج ربط JS يجب أن يعتمد على
    تفويض الأحداث لا على addEventListener عند التحميل — ولهذا استُخدم
    المودال المركزي data-delete-url بدل مودال لكل صف: المودال المُصيَّر
    داخل tbody يُستبدل مع كل فلترة فتضيع روابطه.
--}}
@forelse ($salaries as $salary)
    <tr>
        <td>{{ ($salaries->firstItem() ?? 0) + $loop->index }}</td>
        <td>
            <div class="fw-semibold">{{ $salary->employee->full_name ?? $salary->employee->first_name . ' ' . $salary->employee->last_name }}</div>
            @if ($salary->employee->employee_code ?? null)
                <span class="admin-badge admin-badge-muted">{{ $salary->employee->employee_code }}</span>
            @endif
        </td>
        <td>{{ $salary->month_name }} {{ $salary->salary_year }}</td>
        <td>{{ number_format($salary->base_salary, 2) }} {{ $salary->currency->symbol_ar ?? $salary->currency->symbol ?? 'ر.س' }}</td>
        <td>{{ number_format($salary->allowances, 2) }}</td>
        <td>{{ number_format($salary->bonuses, 2) }}</td>
        <td class="text-danger">-{{ number_format($salary->deductions, 2) }}</td>
        <td><strong class="text-success">{{ number_format($salary->total_salary, 2) }}</strong></td>
        <td>
            @if ($salary->payment_status === 'paid')
                <span class="admin-badge admin-badge-success">مدفوع</span>
            @elseif ($salary->payment_status === 'pending')
                <span class="admin-badge admin-badge-warning">قيد الانتظار</span>
            @else
                <span class="admin-badge admin-badge-danger">ملغي</span>
            @endif
        </td>
        <td>{{ $salary->payment_date ? $salary->payment_date->format('Y-m-d') : '—' }}</td>
        <td>
            <div class="admin-row-actions dropdown">
                <button class="admin-kebab-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="ri-more-2-fill"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    @can('salary-show')
                        <li>
                            <a class="dropdown-item" href="{{ route('admin.salaries.show', $salary->id) }}">
                                <i class="ri-eye-line text-info me-2"></i>عرض الراتب
                            </a>
                        </li>
                    @endcan
                    @can('salary-edit')
                        <li>
                            <a class="dropdown-item" href="{{ route('admin.salaries.edit', $salary->id) }}">
                                <i class="ri-pencil-line text-primary me-2"></i>تعديل
                            </a>
                        </li>
                    @endcan
                    @can('salary-delete')
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <button type="button" class="dropdown-item text-danger border-0 bg-transparent w-100 text-start"
                                    data-delete-url="{{ route('admin.salaries.destroy', $salary->id) }}"
                                    data-delete-message="حذف راتب <strong>{{ $salary->employee->full_name ?? '' }}</strong> لشهر {{ $salary->month_name }} {{ $salary->salary_year }}؟">
                                <i class="ri-delete-bin-line me-2"></i>حذف
                            </button>
                        </li>
                    @endcan
                </ul>
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="11">
            <div class="admin-empty-state">
                <i class="ri-money-dollar-circle-line"></i>
                لا توجد بيانات متاحة
            </div>
        </td>
    </tr>
@endforelse
