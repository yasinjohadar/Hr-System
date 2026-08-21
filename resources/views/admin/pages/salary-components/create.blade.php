@extends('admin.layouts.master')

@section('page-title')
    إضافة مكون راتب جديد
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid admin-page-shell">
            @include('admin.pages.users.partials.alerts')

            <div class="admin-page-banner">
                <div class="admin-page-banner-main">
                    <span class="admin-page-banner-icon"><i class="ri-add-circle-line"></i></span>
                    <div class="admin-page-banner-text">
                        <h1>إضافة مكوّن راتب جديد</h1>
                        <p>حدّد نوع المكوّن وطريقة احتسابه ونطاق تطبيقه</p>
                    </div>
                </div>
                <div class="admin-page-banner-actions">
                    <a href="{{ route('admin.salary-components.index') }}" class="admin-btn admin-btn-light">
                        <i class="ri-arrow-right-line"></i>
                        العودة للقائمة
                    </a>
                </div>
            </div>

            <div class="admin-page-card">
                <form class="admin-form" method="POST" action="{{ route('admin.salary-components.store') }}">
                    @csrf

                    <div class="admin-form-body">

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="admin-form-label">الكود <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('code') is-invalid @enderror" 
                                       name="code" value="{{ old('code') }}" required>
                                @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="admin-form-label">الاسم <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       name="name" value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="admin-form-label">الاسم بالعربية</label>
                                <input type="text" class="form-control" name="name_ar" value="{{ old('name_ar') }}">
                            </div>

                            <div class="col-md-6">
                                <label class="admin-form-label">النوع <span class="text-danger">*</span></label>
                                <select class="form-select @error('type') is-invalid @enderror" name="type" required>
                                    <option value="">اختر النوع</option>
                                    <option value="allowance" {{ old('type') == 'allowance' ? 'selected' : '' }}>بدل</option>
                                    <option value="deduction" {{ old('type') == 'deduction' ? 'selected' : '' }}>خصم</option>
                                    <option value="bonus" {{ old('bonus') == 'bonus' ? 'selected' : '' }}>مكافأة</option>
                                    <option value="overtime" {{ old('type') == 'overtime' ? 'selected' : '' }}>ساعات إضافية</option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="admin-form-label">طريقة الحساب <span class="text-danger">*</span></label>
                                <select class="form-select @error('calculation_type') is-invalid @enderror" name="calculation_type" required id="calculation_type">
                                    <option value="">اختر طريقة الحساب</option>
                                    <option value="fixed" {{ old('calculation_type') == 'fixed' ? 'selected' : '' }}>ثابت</option>
                                    <option value="percentage" {{ old('calculation_type') == 'percentage' ? 'selected' : '' }}>نسبة مئوية</option>
                                    <option value="formula" {{ old('calculation_type') == 'formula' ? 'selected' : '' }}>صيغة</option>
                                    <option value="attendance_based" {{ old('calculation_type') == 'attendance_based' ? 'selected' : '' }}>بناءً على الحضور</option>
                                    <option value="leave_based" {{ old('calculation_type') == 'leave_based' ? 'selected' : '' }}>بناءً على الإجازات</option>
                                </select>
                                @error('calculation_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6" id="default_value_div">
                                <label class="admin-form-label">القيمة الافتراضية</label>
                                <input type="number" step="0.01" class="form-control" name="default_value" value="{{ old('default_value', 0) }}" min="0">
                            </div>

                            <div class="col-md-6" id="percentage_div" hidden>
                                <label class="admin-form-label">النسبة المئوية (%)</label>
                                <input type="number" step="0.01" class="form-control" name="percentage" value="{{ old('percentage') }}" min="0" max="100">
                            </div>

                            <div class="col-md-6" id="formula_div" hidden>
                                <label class="admin-form-label">الصيغة</label>
                                <input type="text" class="form-control" name="formula" value="{{ old('formula') }}" placeholder="مثال: {base_salary} * 0.1">
                                <small class="text-muted">المتغيّرات المتاحة: base_salary، daily_rate، hourly_rate، hours، working_days، present_days، absent_days، leave_days، late_days — بأقواس {} أو بدونها</small>
                            </div>

                            <div class="col-md-6">
                                <div class="form-check mt-4">
                                    <input class="form-check-input" type="checkbox" name="is_taxable" value="1" id="is_taxable" {{ old('is_taxable') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_taxable">خاضع للضريبة</label>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-check mt-4">
                                    <input class="form-check-input" type="checkbox" name="is_required" value="1" id="is_required" {{ old('is_required') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_required">إلزامي</label>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-check mt-4">
                                    <input class="form-check-input" type="checkbox" name="apply_to_all" value="1" id="apply_to_all" {{ old('apply_to_all') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="apply_to_all">يطبق على جميع الموظفين</label>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <label class="admin-form-label">الوصف</label>
                                <textarea class="form-control" name="description" rows="3">{{ old('description') }}</textarea>
                            </div>
                        </div>

                    </div>

                    <div class="admin-form-footer">
                        <a href="{{ route('admin.salary-components.index') }}" class="admin-btn admin-btn-secondary">إلغاء</a>
                        <button type="submit" class="admin-btn admin-btn-primary">
                            <i class="ri-save-line"></i>
                            حفظ
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@stop

@section('js')
    <script>
        /*
         * إظهار حقل القيمة/النسبة/الصيغة حسب طريقة الحساب.
         *
         * نستخدم السمة hidden لا style.display: الحقول داخل col-md-6،
         * وإعادة display إلى 'block' تُخرجها من شبكة Bootstrap فيختلّ الصف.
         *
         * وكان السكربت أيضاً في قسم js متداخل داخل قسم content — تعشيش
         * غير صالح لأقسام Blade، نُقل إلى قسمه المستقل.
         */
        (function () {
            const select = document.getElementById('calculation_type');

            if (!select) {
                return;
            }

            const shows = {
                default_value_div: ['fixed', 'attendance_based', 'leave_based'],
                percentage_div: ['percentage'],
                formula_div: ['formula'],
            };

            function sync() {
                Object.keys(shows).forEach(function (id) {
                    const el = document.getElementById(id);
                    if (el) {
                        el.hidden = !shows[id].includes(select.value);
                    }
                });
            }

            select.addEventListener('change', sync);
            sync();
        })();
    </script>
@stop

