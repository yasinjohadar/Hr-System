@extends('admin.layouts.master')

@section('page-title')
    إضافة قالب استقبال جديد
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
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">إضافة قالب استقبال جديد</h5>
                </div>
                <div>
                    <a href="{{ route('admin.onboarding-templates.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-right me-2"></i>العودة للقائمة
                    </a>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.onboarding-templates.store') }}" id="onboardingTemplateForm">
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">الاسم <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                       value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">الاسم (عربي)</label>
                                <input type="text" name="name_ar" class="form-control" value="{{ old('name_ar') }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">النوع <span class="text-danger">*</span></label>
                                <select class="form-select @error('type') is-invalid @enderror" name="type" required>
                                    <option value="standard" {{ old('type', 'standard') == 'standard' ? 'selected' : '' }}>قياسي</option>
                                    <option value="executive" {{ old('type') == 'executive' ? 'selected' : '' }}>تنفيذي</option>
                                    <option value="contractor" {{ old('type') == 'contractor' ? 'selected' : '' }}>مقاول</option>
                                    <option value="intern" {{ old('type') == 'intern' ? 'selected' : '' }}>متدرّب</option>
                                    <option value="custom" {{ old('type') == 'custom' ? 'selected' : '' }}>مخصص</option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">الحالة</label>
                                <div class="form-check mt-3">
                                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active" 
                                           value="1" {{ old('is_active', 1) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        نشط
                                    </label>
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label">الوصف</label>
                                <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                            </div>

                            <div class="col-12">
                                <label class="form-label">الخطوات</label>
                                <div id="steps-container" class="border rounded p-3">
                                    <div class="step-item mb-3 p-3 border rounded" data-step-index="0">
                                        <div class="row g-2">
                                            <div class="col-md-4">
                                                <input type="text" name="steps[0][title]" class="form-control" placeholder="عنوان الخطوة" required>
                                            </div>
                                            <div class="col-md-6">
                                                <textarea name="steps[0][description]" class="form-control" rows="2" placeholder="وصف الخطوة"></textarea>
                                            </div>
                                            <div class="col-md-2">
                                                <button type="button" class="btn btn-danger btn-sm w-100 remove-step">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-sm btn-primary mt-2" id="add-step">
                                    <i class="fas fa-plus me-2"></i>إضافة خطوة
                                </button>
                            </div>
                        </div>

                        <div class="text-end mt-4">
                            <a href="{{ route('admin.onboarding-templates.index') }}" class="btn btn-secondary px-4 me-2">إلغاء</a>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save me-2"></i>حفظ القالب
                            </button>
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
        let stepIndex = 1;

        const addStepBtn = document.getElementById('add-step');
        if (addStepBtn) {
            addStepBtn.addEventListener('click', function() {
                const container = document.getElementById('steps-container');
                if (!container) return;
                
                const stepHtml = `
                    <div class="step-item mb-3 p-3 border rounded" data-step-index="${stepIndex}">
                        <div class="row g-2">
                            <div class="col-md-4">
                                <input type="text" name="steps[${stepIndex}][title]" class="form-control" placeholder="عنوان الخطوة" required>
                            </div>
                            <div class="col-md-6">
                                <textarea name="steps[${stepIndex}][description]" class="form-control" rows="2" placeholder="وصف الخطوة"></textarea>
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-danger btn-sm w-100 remove-step">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                `;
                container.insertAdjacentHTML('beforeend', stepHtml);
                stepIndex++;
            });
        }

        document.addEventListener('click', function(e) {
            if (e.target.closest('.remove-step')) {
                e.preventDefault();
                e.stopPropagation();
                const stepItem = e.target.closest('.step-item');
                if (stepItem) {
                    const allSteps = document.querySelectorAll('.step-item');
                    if (allSteps.length > 1) {
                        stepItem.remove();
                    } else {
                        alert('يجب أن يكون هناك خطوة واحدة على الأقل');
                    }
                }
            }
        });
    });
</script>
@stop

