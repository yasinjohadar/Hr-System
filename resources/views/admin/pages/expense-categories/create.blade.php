@extends('admin.layouts.master')

@section('page-title')
    إضافة تصنيف مصروف جديد
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">إضافة تصنيف مصروف جديد</h5>
                </div>
                <div>
                    <a href="{{ route('admin.expense-categories.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-right me-2"></i>العودة للقائمة
                    </a>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.expense-categories.store') }}">
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
                                <label class="form-label">الكود</label>
                                <input type="text" name="code" class="form-control @error('code') is-invalid @enderror" 
                                       value="{{ old('code') }}">
                                @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">الحد الأقصى (ر.س)</label>
                                <input type="number" name="max_amount" class="form-control" 
                                       value="{{ old('max_amount') }}" step="0.01" min="0">
                                <small class="text-muted">اتركه فارغاً إذا لم يكن هناك حد أقصى</small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">مستويات الموافقة <span class="text-danger">*</span></label>
                                <select name="approval_levels" class="form-select @error('approval_levels') is-invalid @enderror" required>
                                    <option value="1" {{ old('approval_levels', 1) == 1 ? 'selected' : '' }}>مستوى واحد</option>
                                    <option value="2" {{ old('approval_levels') == 2 ? 'selected' : '' }}>مستويان</option>
                                    <option value="3" {{ old('approval_levels') == 3 ? 'selected' : '' }}>ثلاثة مستويات</option>
                                    <option value="4" {{ old('approval_levels') == 4 ? 'selected' : '' }}>أربعة مستويات</option>
                                    <option value="5" {{ old('approval_levels') == 5 ? 'selected' : '' }}>خمسة مستويات</option>
                                </select>
                                @error('approval_levels')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">الحالة</label>
                                <select name="is_active" class="form-select">
                                    <option value="1" {{ old('is_active', 1) == 1 ? 'selected' : '' }}>نشط</option>
                                    <option value="0" {{ old('is_active') == 0 ? 'selected' : '' }}>غير نشط</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <div class="form-check mt-4">
                                    <input class="form-check-input" type="checkbox" name="requires_receipt" 
                                           value="1" {{ old('requires_receipt', true) ? 'checked' : '' }} id="requires_receipt">
                                    <label class="form-check-label" for="requires_receipt">
                                        يتطلب إيصال
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-check mt-4">
                                    <input class="form-check-input" type="checkbox" name="requires_approval" 
                                           value="1" {{ old('requires_approval', true) ? 'checked' : '' }} id="requires_approval">
                                    <label class="form-check-label" for="requires_approval">
                                        يتطلب موافقة
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">الوصف</label>
                                <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">الوصف (عربي)</label>
                                <textarea name="description_ar" class="form-control" rows="3">{{ old('description_ar') }}</textarea>
                            </div>
                        </div>

                        <div class="text-end mt-4">
                            <a href="{{ route('admin.expense-categories.index') }}" class="btn btn-secondary px-4 me-2">إلغاء</a>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save me-2"></i>حفظ
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

