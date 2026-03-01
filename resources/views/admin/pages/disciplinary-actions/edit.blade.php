@extends('admin.layouts.master')

@section('page-title')
    تعديل الإجراء التأديبي
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">تعديل الإجراء التأديبي</h5>
                </div>
                <div>
                    <a href="{{ route('admin.disciplinary-actions.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-right me-2"></i>العودة للقائمة
                    </a>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.disciplinary-actions.update', $disciplinaryAction->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">الاسم <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                       value="{{ old('name', $disciplinaryAction->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">الاسم (عربي)</label>
                                <input type="text" name="name_ar" class="form-control" value="{{ old('name_ar', $disciplinaryAction->name_ar) }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">الكود</label>
                                <input type="text" name="code" class="form-control @error('code') is-invalid @enderror" 
                                       value="{{ old('code', $disciplinaryAction->code) }}">
                                @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">نوع الإجراء <span class="text-danger">*</span></label>
                                <select name="action_type" class="form-select @error('action_type') is-invalid @enderror" required id="action_type">
                                    <option value="verbal_warning" {{ old('action_type', $disciplinaryAction->action_type) == 'verbal_warning' ? 'selected' : '' }}>تحذير شفهي</option>
                                    <option value="written_warning" {{ old('action_type', $disciplinaryAction->action_type) == 'written_warning' ? 'selected' : '' }}>تحذير كتابي</option>
                                    <option value="final_warning" {{ old('action_type', $disciplinaryAction->action_type) == 'final_warning' ? 'selected' : '' }}>إنذار نهائي</option>
                                    <option value="deduction" {{ old('action_type', $disciplinaryAction->action_type) == 'deduction' ? 'selected' : '' }}>خصم</option>
                                    <option value="suspension" {{ old('action_type', $disciplinaryAction->action_type) == 'suspension' ? 'selected' : '' }}>إيقاف</option>
                                    <option value="termination" {{ old('action_type', $disciplinaryAction->action_type) == 'termination' ? 'selected' : '' }}>إنهاء خدمة</option>
                                </select>
                                @error('action_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">مستوى الخطورة <span class="text-danger">*</span></label>
                                <select name="severity_level" class="form-select @error('severity_level') is-invalid @enderror" required>
                                    <option value="1" {{ old('severity_level', $disciplinaryAction->severity_level) == 1 ? 'selected' : '' }}>منخفض</option>
                                    <option value="2" {{ old('severity_level', $disciplinaryAction->severity_level) == 2 ? 'selected' : '' }}>متوسط</option>
                                    <option value="3" {{ old('severity_level', $disciplinaryAction->severity_level) == 3 ? 'selected' : '' }}>عالي</option>
                                    <option value="4" {{ old('severity_level', $disciplinaryAction->severity_level) == 4 ? 'selected' : '' }}>عالي جداً</option>
                                    <option value="5" {{ old('severity_level', $disciplinaryAction->severity_level) == 5 ? 'selected' : '' }}>حرج</option>
                                </select>
                                @error('severity_level')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6" id="deduction_amount_field" style="display: {{ old('action_type', $disciplinaryAction->action_type) == 'deduction' ? 'block' : 'none' }};">
                                <label class="form-label">مبلغ الخصم (ر.س)</label>
                                <input type="number" name="deduction_amount" class="form-control" 
                                       value="{{ old('deduction_amount', $disciplinaryAction->deduction_amount) }}" step="0.01" min="0">
                            </div>

                            <div class="col-md-6" id="suspension_days_field" style="display: {{ old('action_type', $disciplinaryAction->action_type) == 'suspension' ? 'block' : 'none' }};">
                                <label class="form-label">أيام الإيقاف</label>
                                <input type="number" name="suspension_days" class="form-control" 
                                       value="{{ old('suspension_days', $disciplinaryAction->suspension_days) }}" min="0">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">الحالة</label>
                                <select name="is_active" class="form-select">
                                    <option value="1" {{ old('is_active', $disciplinaryAction->is_active) == 1 ? 'selected' : '' }}>نشط</option>
                                    <option value="0" {{ old('is_active', $disciplinaryAction->is_active) == 0 ? 'selected' : '' }}>غير نشط</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <div class="form-check mt-4">
                                    <input class="form-check-input" type="checkbox" name="requires_approval" 
                                           value="1" {{ old('requires_approval', $disciplinaryAction->requires_approval) ? 'checked' : '' }} id="requires_approval">
                                    <label class="form-check-label" for="requires_approval">
                                        يتطلب موافقة
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">الوصف</label>
                                <textarea name="description" class="form-control" rows="3">{{ old('description', $disciplinaryAction->description) }}</textarea>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">الوصف (عربي)</label>
                                <textarea name="description_ar" class="form-control" rows="3">{{ old('description_ar', $disciplinaryAction->description_ar) }}</textarea>
                            </div>
                        </div>

                        <div class="text-end mt-4">
                            <a href="{{ route('admin.disciplinary-actions.index') }}" class="btn btn-secondary px-4 me-2">إلغاء</a>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save me-2"></i>حفظ التغييرات
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // إظهار/إخفاء الحقول حسب نوع الإجراء
        document.getElementById('action_type').addEventListener('change', function() {
            const actionType = this.value;
            const deductionField = document.getElementById('deduction_amount_field');
            const suspensionField = document.getElementById('suspension_days_field');

            if (actionType === 'deduction') {
                deductionField.style.display = 'block';
                suspensionField.style.display = 'none';
            } else if (actionType === 'suspension') {
                deductionField.style.display = 'none';
                suspensionField.style.display = 'block';
            } else {
                deductionField.style.display = 'none';
                suspensionField.style.display = 'none';
            }
        });
    </script>
@stop

