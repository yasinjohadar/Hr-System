{{--
    صفوف جدول إعدادات الضرائب.

    يُصيَّر مرّتين: مع الصفحة الكاملة، وكـ html_rows في ردّ AJAX الفلترة
    (TaxSettingController@index). لذلك لا يجوز أن يعتمد أي عنصر هنا على
    addEventListener عند التحميل — كل الربط بتفويض الأحداث على document
    (admin-confirm.js عبر data-delete-url).
--}}
@forelse ($taxSettings as $tax)
    <tr>
        <td><span class="admin-badge admin-badge-muted">{{ $tax->code ?? '—' }}</span></td>
        <td class="fw-semibold">{{ $tax->name_ar ?? $tax->name }}</td>
        <td>
            <span class="admin-badge admin-badge-{{ match ($tax->type) {
                'income_tax' => 'danger',
                'social_insurance' => 'role',
                'health_insurance' => 'success',
                default => 'muted',
            } }}">
                {{ $tax->type_name_ar }}
            </span>
        </td>
        <td>
            @if ($tax->calculation_method === 'percentage')
                نسبة مئوية
            @elseif ($tax->calculation_method === 'slab')
                شرائح
            @else
                ثابت
            @endif
        </td>
        <td>
            @if ($tax->calculation_method === 'percentage')
                <strong>{{ $tax->rate }}%</strong>
            @elseif ($tax->calculation_method === 'slab')
                <span class="admin-badge admin-badge-role">{{ count($tax->slabs ?? []) }} شريحة</span>
            @else
                <strong>{{ number_format($tax->rate, 2) }}</strong>
            @endif
        </td>
        <td>
            @if ($tax->is_active)
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
                    @can('tax-setting-show')
                        <li>
                            <a class="dropdown-item" href="{{ route('admin.tax-settings.show', $tax->id) }}">
                                <i class="ri-eye-line text-info me-2"></i>عرض الإعداد
                            </a>
                        </li>
                    @endcan
                    @can('tax-setting-edit')
                        <li>
                            <a class="dropdown-item" href="{{ route('admin.tax-settings.edit', $tax->id) }}">
                                <i class="ri-pencil-line text-primary me-2"></i>تعديل
                            </a>
                        </li>
                    @endcan
                    @can('tax-setting-delete')
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            {{-- المودال المركزي بدل confirm() المتصفح --}}
                            <button type="button" class="dropdown-item text-danger border-0 bg-transparent w-100 text-start"
                                    data-delete-url="{{ route('admin.tax-settings.destroy', $tax->id) }}"
                                    data-delete-message="حذف إعداد <strong>{{ $tax->name_ar ?? $tax->name }}</strong>؟">
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
                <i class="ri-percent-line"></i>
                لا توجد إعدادات ضرائب
            </div>
        </td>
    </tr>
@endforelse
