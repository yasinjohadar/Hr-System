@extends('admin.layouts.master')

@section('page-title')
    تعديل نوع المخالفة
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">تعديل نوع المخالفة</h5>
                </div>
                <div>
                    <a href="{{ route('admin.violation-types.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-right me-2"></i>العودة للقائمة
                    </a>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.violation-types.update', $violationType->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">الاسم <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                       value="{{ old('name', $violationType->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">الاسم (عربي)</label>
                                <input type="text" name="name_ar" class="form-control" value="{{ old('name_ar', $violationType->name_ar) }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">الكود</label>
                                <input type="text" name="code" class="form-control @error('code') is-invalid @enderror" 
                                       value="{{ old('code', $violationType->code) }}">
                                @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">مستوى الخطورة <span class="text-danger">*</span></label>
                                <select name="severity_level" class="form-select @error('severity_level') is-invalid @enderror" required>
                                    <option value="1" {{ old('severity_level', $violationType->severity_level) == 1 ? 'selected' : '' }}>منخفض</option>
                                    <option value="2" {{ old('severity_level', $violationType->severity_level) == 2 ? 'selected' : '' }}>متوسط</option>
                                    <option value="3" {{ old('severity_level', $violationType->severity_level) == 3 ? 'selected' : '' }}>عالي</option>
                                    <option value="4" {{ old('severity_level', $violationType->severity_level) == 4 ? 'selected' : '' }}>عالي جداً</option>
                                    <option value="5" {{ old('severity_level', $violationType->severity_level) == 5 ? 'selected' : '' }}>حرج</option>
                                </select>
                                @error('severity_level')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">الحالة</label>
                                <select name="is_active" class="form-select">
                                    <option value="1" {{ old('is_active', $violationType->is_active) == 1 ? 'selected' : '' }}>نشط</option>
                                    <option value="0" {{ old('is_active', $violationType->is_active) == 0 ? 'selected' : '' }}>غير نشط</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <div class="form-check mt-4">
                                    <input class="form-check-input" type="checkbox" name="requires_warning" 
                                           value="1" {{ old('requires_warning', $violationType->requires_warning) ? 'checked' : '' }} id="requires_warning">
                                    <label class="form-check-label" for="requires_warning">
                                        يتطلب تحذير
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">الوصف</label>
                                <textarea name="description" class="form-control" rows="3">{{ old('description', $violationType->description) }}</textarea>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">الوصف (عربي)</label>
                                <textarea name="description_ar" class="form-control" rows="3">{{ old('description_ar', $violationType->description_ar) }}</textarea>
                            </div>
                        </div>

                        <div class="text-end mt-4">
                            <a href="{{ route('admin.violation-types.index') }}" class="btn btn-secondary px-4 me-2">إلغاء</a>
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

