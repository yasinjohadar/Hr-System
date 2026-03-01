@extends('admin.layouts.master')

@section('page-title')
    تعديل المهارة
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">تعديل المهارة</h5>
                </div>
                <div>
                    <a href="{{ route('admin.employee-skills.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-right me-2"></i>العودة للقائمة
                    </a>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.employee-skills.update', $skill->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">الموظف</label>
                                <input type="text" class="form-control" value="{{ $skill->employee->full_name }}" disabled>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">اسم المهارة <span class="text-danger">*</span></label>
                                <input type="text" name="skill_name" class="form-control @error('skill_name') is-invalid @enderror" 
                                       value="{{ old('skill_name', $skill->skill_name) }}" required>
                                @error('skill_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">اسم المهارة (عربي)</label>
                                <input type="text" name="skill_name_ar" class="form-control" 
                                       value="{{ old('skill_name_ar', $skill->skill_name_ar) }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">مستوى الكفاءة <span class="text-danger">*</span></label>
                                <select name="proficiency_level" class="form-select @error('proficiency_level') is-invalid @enderror" required>
                                    <option value="beginner" {{ old('proficiency_level', $skill->proficiency_level) == 'beginner' ? 'selected' : '' }}>مبتدئ</option>
                                    <option value="intermediate" {{ old('proficiency_level', $skill->proficiency_level) == 'intermediate' ? 'selected' : '' }}>متوسط</option>
                                    <option value="advanced" {{ old('proficiency_level', $skill->proficiency_level) == 'advanced' ? 'selected' : '' }}>متقدم</option>
                                    <option value="expert" {{ old('proficiency_level', $skill->proficiency_level) == 'expert' ? 'selected' : '' }}>خبير</option>
                                </select>
                                @error('proficiency_level')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">سنوات الخبرة</label>
                                <input type="number" name="years_of_experience" class="form-control" 
                                       value="{{ old('years_of_experience', $skill->years_of_experience) }}" min="0">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">تاريخ اكتساب المهارة</label>
                                <input type="date" name="acquired_date" class="form-control" 
                                       value="{{ old('acquired_date', $skill->acquired_date?->format('Y-m-d')) }}">
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">الوصف</label>
                                <textarea name="description" class="form-control" rows="3">{{ old('description', $skill->description) }}</textarea>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">ملاحظات</label>
                                <textarea name="notes" class="form-control" rows="3">{{ old('notes', $skill->notes) }}</textarea>
                            </div>
                        </div>

                        <div class="text-end mt-4">
                            <a href="{{ route('admin.employee-skills.index') }}" class="btn btn-secondary px-4 me-2">إلغاء</a>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save me-2"></i>حفظ التغييرات
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop


