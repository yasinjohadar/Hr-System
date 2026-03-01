@extends('admin.layouts.master')

@section('page-title')
    إضافة إعداد ضريبة جديد
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
                <h5 class="page-title mb-0">إضافة إعداد ضريبة جديد</h5>
                <a href="{{ route('admin.tax-settings.index') }}" class="btn btn-secondary btn-sm">رجوع</a>
            </div>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.tax-settings.store') }}">
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">الكود <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('code') is-invalid @enderror" 
                                       name="code" value="{{ old('code') }}" required>
                                @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">الاسم <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       name="name" value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">الاسم بالعربية</label>
                                <input type="text" class="form-control" name="name_ar" value="{{ old('name_ar') }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">النوع <span class="text-danger">*</span></label>
                                <select class="form-select @error('type') is-invalid @enderror" name="type" required>
                                    <option value="">اختر النوع</option>
                                    <option value="income_tax" {{ old('type') == 'income_tax' ? 'selected' : '' }}>ضريبة الدخل</option>
                                    <option value="social_insurance" {{ old('type') == 'social_insurance' ? 'selected' : '' }}>التأمينات الاجتماعية</option>
                                    <option value="health_insurance" {{ old('type') == 'health_insurance' ? 'selected' : '' }}>التأمين الصحي</option>
                                    <option value="other" {{ old('type') == 'other' ? 'selected' : '' }}>أخرى</option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">طريقة الحساب <span class="text-danger">*</span></label>
                                <select class="form-select @error('calculation_method') is-invalid @enderror" name="calculation_method" required id="calculation_method">
                                    <option value="">اختر طريقة الحساب</option>
                                    <option value="percentage" {{ old('calculation_method') == 'percentage' ? 'selected' : '' }}>نسبة مئوية</option>
                                    <option value="slab" {{ old('calculation_method') == 'slab' ? 'selected' : '' }}>شرائح</option>
                                    <option value="fixed" {{ old('calculation_method') == 'fixed' ? 'selected' : '' }}>ثابت</option>
                                </select>
                                @error('calculation_method')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">النسبة/القيمة <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" class="form-control @error('rate') is-invalid @enderror" 
                                       name="rate" value="{{ old('rate') }}" required>
                                @error('rate')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">الحد الأدنى</label>
                                <input type="number" step="0.01" class="form-control" name="min_amount" value="{{ old('min_amount') }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">الحد الأقصى</label>
                                <input type="number" step="0.01" class="form-control" name="max_amount" value="{{ old('max_amount') }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">مبلغ الإعفاء</label>
                                <input type="number" step="0.01" class="form-control" name="exemption_amount" value="{{ old('exemption_amount', 0) }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">تاريخ البدء</label>
                                <input type="date" class="form-control" name="effective_from" value="{{ old('effective_from') }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">تاريخ الانتهاء</label>
                                <input type="date" class="form-control" name="effective_to" value="{{ old('effective_to') }}">
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">الوصف</label>
                                <textarea class="form-control" name="description" rows="3">{{ old('description') }}</textarea>
                            </div>

                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        نشط
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- قسم الشرائح (يظهر فقط عند اختيار "شرائح") -->
                        <div id="slabs-section" style="display: none;" class="mt-4">
                            <h6 class="mb-3">شرائح الضريبة</h6>
                            <div id="slabs-container">
                                <div class="slab-item mb-3 p-3 border rounded">
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label class="form-label">من</label>
                                            <input type="number" step="0.01" class="form-control" name="slabs[0][min]" placeholder="0">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">إلى</label>
                                            <input type="number" step="0.01" class="form-control" name="slabs[0][max]" placeholder="10000">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">النسبة %</label>
                                            <input type="number" step="0.01" class="form-control" name="slabs[0][rate]" placeholder="5">
                                        </div>
                                        <div class="col-md-1">
                                            <label class="form-label">&nbsp;</label>
                                            <button type="button" class="btn btn-danger btn-sm remove-slab" style="display: none;">حذف</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-secondary btn-sm" id="add-slab">إضافة شريحة</button>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">حفظ</button>
                            <a href="{{ route('admin.tax-settings.index') }}" class="btn btn-secondary">إلغاء</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const calculationMethod = document.getElementById('calculation_method');
    const slabsSection = document.getElementById('slabs-section');
    let slabIndex = 1;

    calculationMethod.addEventListener('change', function() {
        if (this.value === 'slab') {
            slabsSection.style.display = 'block';
        } else {
            slabsSection.style.display = 'none';
        }
    });

    // إضافة شريحة جديدة
    document.getElementById('add-slab').addEventListener('click', function() {
        const container = document.getElementById('slabs-container');
        const newSlab = document.createElement('div');
        newSlab.className = 'slab-item mb-3 p-3 border rounded';
        newSlab.innerHTML = `
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">من</label>
                    <input type="number" step="0.01" class="form-control" name="slabs[${slabIndex}][min]" placeholder="0">
                </div>
                <div class="col-md-4">
                    <label class="form-label">إلى</label>
                    <input type="number" step="0.01" class="form-control" name="slabs[${slabIndex}][max]" placeholder="10000">
                </div>
                <div class="col-md-3">
                    <label class="form-label">النسبة %</label>
                    <input type="number" step="0.01" class="form-control" name="slabs[${slabIndex}][rate]" placeholder="5">
                </div>
                <div class="col-md-1">
                    <label class="form-label">&nbsp;</label>
                    <button type="button" class="btn btn-danger btn-sm remove-slab">حذف</button>
                </div>
            </div>
        `;
        container.appendChild(newSlab);
        slabIndex++;

        // إظهار أزرار الحذف
        document.querySelectorAll('.remove-slab').forEach(btn => {
            btn.style.display = 'block';
        });
    });

    // حذف شريحة
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-slab')) {
            e.target.closest('.slab-item').remove();
            
            // إخفاء أزرار الحذف إذا كانت شريحة واحدة فقط
            if (document.querySelectorAll('.slab-item').length === 1) {
                document.querySelectorAll('.remove-slab').forEach(btn => {
                    btn.style.display = 'none';
                });
            }
        }
    });

    // التحقق من القيمة الأولية
    if (calculationMethod.value === 'slab') {
        slabsSection.style.display = 'block';
    }
});
</script>
@stop

