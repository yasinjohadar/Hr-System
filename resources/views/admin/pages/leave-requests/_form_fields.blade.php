@php
    $lr = $leaveRequest ?? null;
@endphp

<div class="form-section">
    <div class="form-section-title">
        <i class="ri-user-line"></i>
        <span>بيانات الطلب</span>
    </div>
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label" for="employee_id">الموظف <span class="required">*</span></label>
            <select class="form-select @error('employee_id') is-invalid @enderror"
                    name="employee_id" id="employee_id" required>
                @if (!$lr)
                    <option value="">اختر الموظف</option>
                @endif
                @foreach ($employees as $employee)
                    <option value="{{ $employee->id }}"
                        {{ (string) old('employee_id', $lr?->employee_id) === (string) $employee->id ? 'selected' : '' }}>
                        {{ $employee->full_name ?? $employee->first_name . ' ' . $employee->last_name }}
                        @if ($employee->employee_code ?? null)
                            ({{ $employee->employee_code }})
                        @endif
                    </option>
                @endforeach
            </select>
            @error('employee_id')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>
        <div class="col-md-6">
            <label class="form-label" for="leave_type_id">نوع الإجازة <span class="required">*</span></label>
            <select class="form-select @error('leave_type_id') is-invalid @enderror"
                    name="leave_type_id" id="leave_type_id" required>
                @if (!$lr)
                    <option value="">اختر نوع الإجازة</option>
                @endif
                @foreach ($leaveTypes as $type)
                    <option value="{{ $type->id }}"
                        {{ (string) old('leave_type_id', $lr?->leave_type_id) === (string) $type->id ? 'selected' : '' }}>
                        {{ $type->name_ar ?? $type->name }}
                        @if ($type->max_days ?? null)
                            ({{ $type->max_days }} يوم)
                        @endif
                    </option>
                @endforeach
            </select>
            @error('leave_type_id')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<div class="form-section">
    <div class="form-section-title">
        <i class="ri-calendar-line"></i>
        <span>فترة الإجازة</span>
    </div>
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label" for="start_date">تاريخ البداية <span class="required">*</span></label>
            <input type="date" class="form-control @error('start_date') is-invalid @enderror"
                   name="start_date" id="start_date"
                   value="{{ old('start_date', $lr?->start_date?->format('Y-m-d')) }}" required>
            @error('start_date')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>
        <div class="col-md-6">
            <label class="form-label" for="end_date">تاريخ النهاية <span class="required">*</span></label>
            <input type="date" class="form-control @error('end_date') is-invalid @enderror"
                   name="end_date" id="end_date"
                   value="{{ old('end_date', $lr?->end_date?->format('Y-m-d')) }}" required>
            @error('end_date')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
            <span class="days-preview is-hidden" id="leave-days-preview" aria-live="polite"></span>
        </div>
    </div>
</div>

<div class="form-section">
    <div class="form-section-title">
        <i class="ri-file-text-line"></i>
        <span>تفاصيل إضافية</span>
    </div>
    <div class="row g-3">
        <div class="col-12">
            <label class="form-label" for="reason">سبب الإجازة</label>
            <textarea class="form-control @error('reason') is-invalid @enderror"
                      name="reason" id="reason" rows="3">{{ old('reason', $lr?->reason) }}</textarea>
            @error('reason')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>
        <div class="col-12">
            <label class="form-label" for="notes">ملاحظات</label>
            <textarea class="form-control @error('notes') is-invalid @enderror"
                      name="notes" id="notes" rows="2">{{ old('notes', $lr?->notes) }}</textarea>
            @error('notes')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>
