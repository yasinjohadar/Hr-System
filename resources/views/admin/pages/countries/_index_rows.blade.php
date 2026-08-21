{{--
    صفوف جدول الدول.

    يُصيَّر مرّتين: مع الصفحة الكاملة، وكـ html_rows في ردّ AJAX الفلترة
    (CountryController@index). لذلك لا يجوز أن يعتمد أي عنصر هنا على
    addEventListener عند التحميل — كل الربط بتفويض الأحداث على document
    (admin-confirm.js عبر data-delete-url).
--}}
@forelse ($countries as $country)
    <tr>
        <td>{{ $loop->iteration + ($countries->currentPage() - 1) * $countries->perPage() }}</td>
        <td>
            @if ($country->flag)
                <span class="fs-4">{{ $country->flag }}</span>
            @else
                <span class="admin-badge admin-badge-muted">{{ $country->code }}</span>
            @endif
        </td>
        <td>
            <div class="fw-semibold">{{ $country->name_ar ?? $country->name }}</div>
            @if ($country->name_ar && $country->name_ar !== $country->name)
                <small class="text-muted">{{ $country->name }}</small>
            @endif
        </td>
        <td><span class="admin-badge admin-badge-role">{{ $country->code }}</span></td>
        <td>{{ $country->phone_code ? '+' . ltrim($country->phone_code, '+') : '—' }}</td>
        <td>{{ $country->currency_code ?? '—' }}</td>
        <td>
            @if ($country->is_active)
                <span class="admin-badge admin-badge-success">نشط</span>
            @else
                <span class="admin-badge admin-badge-danger">غير نشط</span>
            @endif
        </td>
        <td>
            <div class="admin-row-actions dropdown">
                <button class="admin-kebab-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="ri-more-2-fill"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    @can('country-show')
                        <li>
                            <a class="dropdown-item" href="{{ route('admin.countries.show', $country->id) }}">
                                <i class="ri-eye-line text-info me-2"></i>عرض الدولة
                            </a>
                        </li>
                    @endcan
                    @can('country-edit')
                        <li>
                            <a class="dropdown-item" href="{{ route('admin.countries.edit', $country->id) }}">
                                <i class="ri-pencil-line text-primary me-2"></i>تعديل
                            </a>
                        </li>
                    @endcan
                    @can('country-delete')
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            {{-- المودال المركزي بدل مودال منفصل لكل صف --}}
                            <button type="button" class="dropdown-item text-danger border-0 bg-transparent w-100 text-start"
                                    data-delete-url="{{ route('admin.countries.destroy', $country->id) }}"
                                    data-delete-message="حذف الدولة <strong>{{ $country->name_ar ?? $country->name }}</strong>؟">
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
        <td colspan="8">
            <div class="admin-empty-state">
                <i class="ri-earth-line"></i>
                لا توجد دول
            </div>
        </td>
    </tr>
@endforelse
