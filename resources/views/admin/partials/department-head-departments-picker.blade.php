@php
    $selected = $selectedDepartmentIds ?? old('department_ids', []);
    $cardThemes = ['blue', 'teal', 'purple', 'amber', 'emerald', 'cyan', 'indigo', 'rose'];
@endphp

<div class="dh-dept-picker" data-dept-picker>
    <div class="dh-dept-picker__toolbar">
        <div class="search-input-wrap dh-dept-picker__search">
            <i class="ri-search-line"></i>
            <input type="text" class="form-control" placeholder="بحث في الأقسام..." data-dept-search autocomplete="off">
        </div>
        <button type="button" class="admin-btn admin-btn-secondary admin-btn-sm" data-dept-select-all>
            <i class="ri-checkbox-multiple-line"></i>
            تحديد الظاهر
        </button>
        <button type="button" class="admin-btn admin-btn-secondary admin-btn-sm" data-dept-deselect-all>
            <i class="ri-close-circle-line"></i>
            إلغاء الظاهر
        </button>
    </div>

    <div class="dh-dept-picker__grid">
        @foreach ($departments as $department)
            @php
                $theme = $cardThemes[$loop->index % count($cardThemes)];
                $isSelected = in_array($department->id, (array) $selected);
                $hasOtherManager = $department->manager_id && (int) $department->manager_id !== (int) ($currentUserId ?? 0);
            @endphp
            <label class="dh-dept-card dh-dept-card--{{ $theme }}"
                   data-dept-item
                   data-name="{{ $department->name }}"
                   data-code="{{ $department->code ?? '' }}">
                <input type="checkbox" class="dh-dept-card__checkbox" name="department_ids[]"
                       value="{{ $department->id }}"
                       @checked($isSelected)>
                <span class="dh-dept-card__check"><i class="ri-check-line"></i></span>
                <span class="dh-dept-card__icon"><i class="ri-building-4-line"></i></span>
                <span class="dh-dept-card__name">{{ $department->name }}</span>
                @if ($department->code)
                    <span class="dh-dept-card__code">{{ $department->code }}</span>
                @endif
                @if ($hasOtherManager && $department->manager)
                    <span class="dh-dept-card__warn">
                        <i class="ri-alert-line"></i>
                        المدير الحالي: {{ $department->manager->name }}
                    </span>
                @endif
            </label>
        @endforeach
    </div>

    <p class="dh-dept-picker__empty" data-dept-empty style="display: none;">لا توجد أقسام مطابقة للبحث</p>
</div>
