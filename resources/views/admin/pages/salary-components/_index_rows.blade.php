{{--
    صفوف جدول مكوّنات الراتب.

    يُصيَّر مرّتين: مع الصفحة الكاملة، وكـ html_rows في ردّ AJAX الفلترة
    (SalaryComponentController@index). لذلك لا يجوز أن يعتمد أي عنصر هنا
    على addEventListener عند التحميل — كل الربط بتفويض الأحداث على
    document (admin-confirm.js عبر data-delete-url).
--}}
@forelse ($components as $component)
    <tr>
        <td><span class="admin-badge admin-badge-muted">{{ $component->code }}</span></td>
        <td class="fw-semibold">{{ $component->name_ar ?? $component->name }}</td>
        <td>
            <span class="admin-badge admin-badge-{{ match ($component->type) {
                'allowance' => 'success',
                'deduction' => 'danger',
                'bonus' => 'role',
                'overtime' => 'warning',
                default => 'muted',
            } }}">
                {{ $component->type_name_ar }}
            </span>
        </td>
        <td>{{ $component->calculation_type_name_ar }}</td>
        <td>
            @if ($component->calculation_type === 'percentage')
                <strong>{{ $component->percentage }}%</strong>
            @elseif ($component->calculation_type === 'formula')
                <code class="small">{{ $component->formula ?: '—' }}</code>
            @else
                <strong>{{ number_format($component->default_value, 2) }}</strong>
            @endif
        </td>
        <td>
            @if ($component->is_active)
                <span class="admin-badge admin-badge-success">نشط</span>
            @else
                <span class="admin-badge admin-badge-muted">غير نشط</span>
            @endif
        </td>
        <td>
            <div class="admin-row-actions dropdown">
                <button class="admin-kebab-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="ri-more-2-fill"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    @can('salary-component-show')
                        <li>
                            <a class="dropdown-item" href="{{ route('admin.salary-components.show', $component->id) }}">
                                <i class="ri-eye-line text-info me-2"></i>عرض المكوّن
                            </a>
                        </li>
                    @endcan
                    @can('salary-component-edit')
                        <li>
                            <a class="dropdown-item" href="{{ route('admin.salary-components.edit', $component->id) }}">
                                <i class="ri-pencil-line text-primary me-2"></i>تعديل
                            </a>
                        </li>
                    @endcan
                    @can('salary-component-delete')
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            {{-- المودال المركزي بدل confirm() المتصفح --}}
                            <button type="button" class="dropdown-item text-danger border-0 bg-transparent w-100 text-start"
                                    data-delete-url="{{ route('admin.salary-components.destroy', $component->id) }}"
                                    data-delete-message="حذف المكوّن <strong>{{ $component->name_ar ?? $component->name }}</strong>؟">
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
        <td colspan="7">
            <div class="admin-empty-state">
                <i class="ri-list-settings-line"></i>
                لا توجد مكوّنات
            </div>
        </td>
    </tr>
@endforelse
