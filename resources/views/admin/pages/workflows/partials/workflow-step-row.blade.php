@php
    $step = $step ?? [];
    $approverType = old("steps.{$index}.approver_type", $step['approver_type'] ?? 'department_manager');
    $roleId = old("steps.{$index}.role_id", $step['role_id'] ?? null);
    $approverId = old("steps.{$index}.approver_id", $step['approver_id'] ?? null);
    $isRequired = old("steps.{$index}.is_required", $step['is_required'] ?? true);
    $canReject = old("steps.{$index}.can_reject", $step['can_reject'] ?? true);
    $stepId = $step['id'] ?? null;
    $displayOrder = is_numeric($index) ? ((int) $index + 1) : '__ORDER__';
@endphp

<div class="workflow-step-card" data-step-card data-index="{{ $index }}">
    <div class="workflow-step-card__header">
        <span class="workflow-step-card__order" data-step-order-label>الخطوة {{ $displayOrder }}</span>
        <div class="btn-group btn-group-sm">
            <button type="button" class="btn btn-light" data-move-up title="أعلى">
                <i class="ri-arrow-up-line"></i>
            </button>
            <button type="button" class="btn btn-light" data-move-down title="أسفل">
                <i class="ri-arrow-down-line"></i>
            </button>
            <button type="button" class="btn btn-light text-danger" data-remove-step title="حذف">
                <i class="ri-delete-bin-line"></i>
            </button>
        </div>
    </div>

    @if ($stepId)
        <input type="hidden" name="steps[{{ $index }}][id]" value="{{ $stepId }}">
    @endif

    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label">اسم الخطوة (عربي) <span class="text-danger">*</span></label>
            <input type="text"
                   name="steps[{{ $index }}][name_ar]"
                   class="form-control @error("steps.{$index}.name_ar") is-invalid @enderror"
                   value="{{ old("steps.{$index}.name_ar", $step['name_ar'] ?? '') }}"
                   required>
            @error("steps.{$index}.name_ar")<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
            <label class="form-label">نوع الموافق <span class="text-danger">*</span></label>
            <select name="steps[{{ $index }}][approver_type]"
                    class="form-select @error("steps.{$index}.approver_type") is-invalid @enderror"
                    data-approver-type
                    required>
                <option value="department_manager" @selected($approverType === 'department_manager')>مدير القسم (رئيس القسم)</option>
                <option value="employee_manager" @selected($approverType === 'employee_manager')>المدير المباشر للموظف</option>
                <option value="role" @selected($approverType === 'role')>دور (مثل مدير عام)</option>
                <option value="user" @selected($approverType === 'user')>مستخدم محدد</option>
            </select>
            @error("steps.{$index}.approver_type")<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6" data-role-field @if($approverType !== 'role') style="display:none" @endif>
            <label class="form-label">الدور <span class="text-danger">*</span></label>
            <select name="steps[{{ $index }}][role_id]"
                    class="form-select @error("steps.{$index}.role_id") is-invalid @enderror"
                    data-role-select>
                <option value="">— اختر دوراً —</option>
                @foreach ($roles as $role)
                    <option value="{{ $role->id }}" @selected((string) $roleId === (string) $role->id)>{{ $role->name }}</option>
                @endforeach
            </select>
            @error("steps.{$index}.role_id")<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6" data-user-field @if($approverType !== 'user') style="display:none" @endif>
            <label class="form-label">المستخدم <span class="text-danger">*</span></label>
            <select name="steps[{{ $index }}][approver_id]"
                    class="form-select @error("steps.{$index}.approver_id") is-invalid @enderror"
                    data-user-select>
                <option value="">— اختر مستخدماً —</option>
                @foreach ($users as $user)
                    <option value="{{ $user->id }}" @selected((string) $approverId === (string) $user->id)>
                        {{ $user->name }} @if($user->email) ({{ $user->email }}) @endif
                    </option>
                @endforeach
            </select>
            @error("steps.{$index}.approver_id")<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
            <div class="form-check mt-4">
                <input type="hidden" name="steps[{{ $index }}][is_required]" value="0">
                <input class="form-check-input" type="checkbox" name="steps[{{ $index }}][is_required]" value="1"
                       id="step_required_{{ $index }}" @checked(filter_var($isRequired, FILTER_VALIDATE_BOOLEAN))>
                <label class="form-check-label" for="step_required_{{ $index }}">خطوة إلزامية</label>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-check mt-4">
                <input type="hidden" name="steps[{{ $index }}][can_reject]" value="0">
                <input class="form-check-input" type="checkbox" name="steps[{{ $index }}][can_reject]" value="1"
                       id="step_reject_{{ $index }}" @checked(filter_var($canReject, FILTER_VALIDATE_BOOLEAN))>
                <label class="form-check-label" for="step_reject_{{ $index }}">يمكن الرفض في هذه الخطوة</label>
            </div>
        </div>
    </div>
</div>
