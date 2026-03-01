@extends('admin.layouts.master')

@section('page-title')
    إضافة نوع مكافأة جديد
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">إضافة نوع مكافأة جديد</h5>
                </div>
                <div>
                    <a href="{{ route('admin.reward-types.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-right me-2"></i>العودة للقائمة
                    </a>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.reward-types.store') }}">
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
                                <label class="form-label">نوع المكافأة <span class="text-danger">*</span></label>
                                <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                                    <option value="monetary" {{ old('type', 'monetary') == 'monetary' ? 'selected' : '' }}>نقدي</option>
                                    <option value="non_monetary" {{ old('type') == 'non_monetary' ? 'selected' : '' }}>غير نقدي</option>
                                    <option value="points" {{ old('type') == 'points' ? 'selected' : '' }}>نقاط</option>
                                    <option value="recognition" {{ old('type') == 'recognition' ? 'selected' : '' }}>اعتراف</option>
                                    <option value="gift" {{ old('type') == 'gift' ? 'selected' : '' }}>هدية</option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">القيمة الافتراضية</label>
                                <input type="number" step="0.01" name="default_value" class="form-control" 
                                       value="{{ old('default_value') }}" min="0">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">النقاط الافتراضية</label>
                                <input type="number" name="default_points" class="form-control" 
                                       value="{{ old('default_points') }}" min="0">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">الحالة</label>
                                <select name="is_active" class="form-select">
                                    <option value="1" {{ old('is_active', 1) == 1 ? 'selected' : '' }}>نشط</option>
                                    <option value="0" {{ old('is_active') == 0 ? 'selected' : '' }}>غير نشط</option>
                                </select>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">الوصف</label>
                                <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                            </div>
                        </div>

                        <div class="text-end mt-4">
                            <a href="{{ route('admin.reward-types.index') }}" class="btn btn-secondary px-4 me-2">إلغاء</a>
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

