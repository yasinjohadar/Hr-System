@extends('admin.layouts.master')

@section('page-title')
    تعديل مكون الراتب
@stop

@section('content')
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li class="small">{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="page-header d-flex justify-content-between align-items-center my-4">
                <h5 class="page-title mb-0">تعديل مكون الراتب</h5>
                <a href="{{ route('admin.salary-components.index') }}" class="btn btn-secondary btn-sm">رجوع</a>
            </div>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.salary-components.update', $component->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">الكود <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('code') is-invalid @enderror" 
                                       name="code" value="{{ old('code', $component->code) }}" required>
                                @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">الاسم <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       name="name" value="{{ old('name', $component->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">الاسم بالعربية</label>
                                <input type="text" class="form-control" name="name_ar" value="{{ old('name_ar', $component->name_ar) }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">النوع <span class="text-danger">*</span></label>
                                <select class="form-select @error('type') is-invalid @enderror" name="type" required>
                                    <option value="">اختر النوع</option>
                                    <option value="allowance" {{ old('type', $component->type) == 'allowance' ? 'selected' : '' }}>بدل</option>
                                    <option value="deduction" {{ old('type', $component->type) == 'deduction' ? 'selected' : '' }}>خصم</option>
                                    <option value="bonus" {{ old('type', $component->type) == 'bonus' ? 'selected' : '' }}>مكافأة</option>
                                    <option value="overtime" {{ old('type', $component->type) == 'overtime' ? 'selected' : '' }}>ساعات إضافية</option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">طريقة الحساب <span class="text-danger">*</span></label>
                                <select class="form-select @error('calculation_type') is-invalid @enderror" name="calculation_type" required id="calculation_type">
                                    <option value="">اختر طريقة الحساب</option>
                                    <option value="fixed" {{ old('calculation_type', $component->calculation_type) == 'fixed' ? 'selected' : '' }}>ثابت</option>
                                    <option value="percentage" {{ old('calculation_type', $component->calculation_type) == 'percentage' ? 'selected' : '' }}>نسبة مئوية</option>
                                    <option value="formula" {{ old('calculation_type', $component->calculation_type) == 'formula' ? 'selected' : '' }}>صيغة</option>
                                    <option value="attendance_based" {{ old('calculation_type', $component->calculation_type) == 'attendance_based' ? 'selected' : '' }}>بناءً على الحضور</option>
                                    <option value="leave_based" {{ old('calculation_type', $component->calculation_type) == 'leave_based' ? 'selected' : '' }}>بناءً على الإجازات</option>
                                </select>
                                @error('calculation_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6" id="default_value_div">
                                <label class="form-label">القيمة الافتراضية</label>
                                <input type="number" step="0.01" class="form-control" name="default_value" value="{{ old('default_value', $component->default_value) }}" min="0">
                            </div>

                            <div class="col-md-6" id="percentage_div" style="display: none;">
                                <label class="form-label">النسبة المئوية (%)</label>
                                <input type="number" step="0.01" class="form-control" name="percentage" value="{{ old('percentage', $component->percentage) }}" min="0" max="100">
                            </div>

                            <div class="col-md-6" id="formula_div" style="display: none;">
                                <label class="form-label">الصيغة</label>
                                <input type="text" class="form-control" name="formula" value="{{ old('formula', $component->formula) }}" placeholder="مثال: {base_salary} * 0.1">
                                <small class="text-muted">استخدم {base_salary}, {present_days}, {working_days}</small>
                            </div>

                            <div class="col-md-6">
                                <div class="form-check mt-4">
                                    <input class="form-check-input" type="checkbox" name="is_taxable" value="1" id="is_taxable" {{ old('is_taxable', $component->is_taxable) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_taxable">خاضع للضريبة</label>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-check mt-4">
                                    <input class="form-check-input" type="checkbox" name="is_required" value="1" id="is_required" {{ old('is_required', $component->is_required) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_required">إلزامي</label>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-check mt-4">
                                    <input class="form-check-input" type="checkbox" name="apply_to_all" value="1" id="apply_to_all" {{ old('apply_to_all', $component->apply_to_all) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="apply_to_all">يطبق على جميع الموظفين</label>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-check mt-4">
                                    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" {{ old('is_active', $component->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">نشط</label>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">الوصف</label>
                                <textarea class="form-control" name="description" rows="3">{{ old('description', $component->description) }}</textarea>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">حفظ</button>
                            <a href="{{ route('admin.salary-components.index') }}" class="btn btn-secondary">إلغاء</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @section('js')
    <script>
        document.getElementById('calculation_type').addEventListener('change', function() {
            const type = this.value;
            document.getElementById('default_value_div').style.display = (type === 'fixed' || type === 'attendance_based' || type === 'leave_based') ? 'block' : 'none';
            document.getElementById('percentage_div').style.display = type === 'percentage' ? 'block' : 'none';
            document.getElementById('formula_div').style.display = type === 'formula' ? 'block' : 'none';
        });
        document.getElementById('calculation_type').dispatchEvent(new Event('change'));
    </script>
    @stop
@stop

