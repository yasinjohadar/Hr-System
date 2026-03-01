@extends('admin.layouts.master')

@section('page-title')
    إضافة وظيفة شاغرة جديدة
@stop

@section('css')
    <style>
        .form-floating label {
            right: auto;
            left: 0.75rem;
        }
    </style>
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
                <h5 class="page-title mb-0">إضافة وظيفة شاغرة جديدة</h5>
            </div>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.job-vacancies.store') }}">
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                           name="title" placeholder="عنوان الوظيفة" value="{{ old('title') }}" required>
                                    <label>عنوان الوظيفة (إنجليزي) <span class="text-danger">*</span></label>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control @error('title_ar') is-invalid @enderror" 
                                           name="title_ar" placeholder="عنوان الوظيفة بالعربية" value="{{ old('title_ar') }}">
                                    <label>عنوان الوظيفة (عربي)</label>
                                    @error('title_ar')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="text" class="form-control @error('code') is-invalid @enderror" 
                                           name="code" placeholder="كود الوظيفة" value="{{ old('code') }}" required>
                                    <label>كود الوظيفة <span class="text-danger">*</span></label>
                                    @error('code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-floating">
                                    <select class="form-select @error('department_id') is-invalid @enderror" 
                                            name="department_id" id="department_id">
                                        <option value="">اختر القسم</option>
                                        @foreach ($departments as $department)
                                            <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                                {{ $department->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <label>القسم</label>
                                    @error('department_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-floating">
                                    <select class="form-select @error('position_id') is-invalid @enderror" 
                                            name="position_id" id="position_id">
                                        <option value="">اختر المنصب</option>
                                        @foreach ($positions as $position)
                                            <option value="{{ $position->id }}" {{ old('position_id') == $position->id ? 'selected' : '' }}>
                                                {{ $position->title }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <label>المنصب</label>
                                    @error('position_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-floating">
                                    <select class="form-select @error('employment_type') is-invalid @enderror" 
                                            name="employment_type" id="employment_type" required>
                                        <option value="full_time" {{ old('employment_type', 'full_time') == 'full_time' ? 'selected' : '' }}>دوام كامل</option>
                                        <option value="part_time" {{ old('employment_type') == 'part_time' ? 'selected' : '' }}>دوام جزئي</option>
                                        <option value="contract" {{ old('employment_type') == 'contract' ? 'selected' : '' }}>عقد</option>
                                        <option value="intern" {{ old('employment_type') == 'intern' ? 'selected' : '' }}>تدريب</option>
                                        <option value="freelance" {{ old('employment_type') == 'freelance' ? 'selected' : '' }}>عمل حر</option>
                                    </select>
                                    <label>نوع التوظيف <span class="text-danger">*</span></label>
                                    @error('employment_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-floating">
                                    <select class="form-select @error('experience_level') is-invalid @enderror" 
                                            name="experience_level" id="experience_level" required>
                                        <option value="entry" {{ old('experience_level', 'mid') == 'entry' ? 'selected' : '' }}>مبتدئ</option>
                                        <option value="junior" {{ old('experience_level') == 'junior' ? 'selected' : '' }}>مبتدئ متقدم</option>
                                        <option value="mid" {{ old('experience_level', 'mid') == 'mid' ? 'selected' : '' }}>متوسط</option>
                                        <option value="senior" {{ old('experience_level') == 'senior' ? 'selected' : '' }}>خبير</option>
                                        <option value="lead" {{ old('experience_level') == 'lead' ? 'selected' : '' }}>قائد</option>
                                        <option value="executive" {{ old('experience_level') == 'executive' ? 'selected' : '' }}>تنفيذي</option>
                                    </select>
                                    <label>مستوى الخبرة <span class="text-danger">*</span></label>
                                    @error('experience_level')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="number" class="form-control" 
                                           name="years_of_experience" placeholder="سنوات الخبرة" 
                                           value="{{ old('years_of_experience') }}" min="0">
                                    <label>سنوات الخبرة المطلوبة</label>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-floating">
                                    <input type="number" step="0.01" class="form-control" 
                                           name="min_salary" placeholder="الراتب الأدنى" value="{{ old('min_salary') }}">
                                    <label>الراتب الأدنى</label>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-floating">
                                    <input type="number" step="0.01" class="form-control" 
                                           name="max_salary" placeholder="الراتب الأقصى" value="{{ old('max_salary') }}">
                                    <label>الراتب الأقصى</label>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-floating">
                                    <select class="form-select" name="currency_id" id="currency_id">
                                        <option value="">اختر العملة</option>
                                        @foreach ($currencies as $currency)
                                            <option value="{{ $currency->id }}" {{ old('currency_id') == $currency->id ? 'selected' : '' }}>
                                                {{ $currency->name_ar ?? $currency->name }} ({{ $currency->symbol_ar ?? $currency->symbol }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <label>العملة</label>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-floating">
                                    <input type="number" class="form-control @error('number_of_positions') is-invalid @enderror" 
                                           name="number_of_positions" placeholder="عدد المناصب" 
                                           value="{{ old('number_of_positions', 1) }}" min="1" required>
                                    <label>عدد المناصب <span class="text-danger">*</span></label>
                                    @error('number_of_positions')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="date" class="form-control @error('posted_date') is-invalid @enderror" 
                                           name="posted_date" placeholder="تاريخ النشر" 
                                           value="{{ old('posted_date', date('Y-m-d')) }}" required>
                                    <label>تاريخ النشر <span class="text-danger">*</span></label>
                                    @error('posted_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="date" class="form-control" 
                                           name="closing_date" placeholder="تاريخ الإغلاق" 
                                           value="{{ old('closing_date') }}">
                                    <label>تاريخ الإغلاق</label>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-floating">
                                    <select class="form-select @error('status') is-invalid @enderror" 
                                            name="status" id="status" required>
                                        <option value="draft" {{ old('status', 'draft') == 'draft' ? 'selected' : '' }}>مسودة</option>
                                        <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>منشور</option>
                                        <option value="closed" {{ old('status') == 'closed' ? 'selected' : '' }}>مغلق</option>
                                    </select>
                                    <label>الحالة <span class="text-danger">*</span></label>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control" 
                                           name="location" placeholder="المكان" value="{{ old('location') }}">
                                    <label>المكان</label>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-check mt-4">
                                    <input class="form-check-input" type="checkbox" name="is_remote" id="is_remote" 
                                           value="1" {{ old('is_remote') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_remote">
                                        عمل عن بُعد
                                    </label>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-floating">
                                    <textarea class="form-control" name="description" placeholder="الوصف" style="height: 100px">{{ old('description') }}</textarea>
                                    <label>الوصف (إنجليزي)</label>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-floating">
                                    <textarea class="form-control" name="description_ar" placeholder="الوصف بالعربية" style="height: 100px">{{ old('description_ar') }}</textarea>
                                    <label>الوصف (عربي)</label>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-floating">
                                    <textarea class="form-control" name="requirements" placeholder="المتطلبات" style="height: 100px">{{ old('requirements') }}</textarea>
                                    <label>المتطلبات</label>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-floating">
                                    <textarea class="form-control" name="responsibilities" placeholder="المسؤوليات" style="height: 100px">{{ old('responsibilities') }}</textarea>
                                    <label>المسؤوليات</label>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-floating">
                                    <textarea class="form-control" name="benefits" placeholder="المزايا" style="height: 100px">{{ old('benefits') }}</textarea>
                                    <label>المزايا</label>
                                </div>
                            </div>
                        </div>

                        <div class="text-end mt-4">
                            <a href="{{ route('admin.job-vacancies.index') }}" class="btn btn-secondary px-4 me-2">إلغاء</a>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save me-2"></i>حفظ الوظيفة
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop


