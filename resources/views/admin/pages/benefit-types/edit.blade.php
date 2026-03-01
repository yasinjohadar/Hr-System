@extends('admin.layouts.master')

@section('page-title')
    تعديل نوع ميزة
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
                <h5 class="page-title mb-0">تعديل نوع ميزة</h5>
            </div>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.benefit-types.update', $benefitType->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           name="name" placeholder="اسم الميزة" value="{{ old('name', $benefitType->name) }}" required>
                                    <label>اسم الميزة (إنجليزي) <span class="text-danger">*</span></label>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control @error('name_ar') is-invalid @enderror" 
                                           name="name_ar" placeholder="اسم الميزة بالعربية" value="{{ old('name_ar', $benefitType->name_ar) }}">
                                    <label>اسم الميزة (عربي)</label>
                                    @error('name_ar')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="text" class="form-control @error('code') is-invalid @enderror" 
                                           name="code" placeholder="كود الميزة" value="{{ old('code', $benefitType->code) }}" required>
                                    <label>كود الميزة <span class="text-danger">*</span></label>
                                    @error('code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-floating">
                                    <select class="form-select @error('type') is-invalid @enderror" 
                                            name="type" id="type" required>
                                        <option value="monetary" {{ old('type', $benefitType->type) == 'monetary' ? 'selected' : '' }}>نقدي</option>
                                        <option value="in_kind" {{ old('type', $benefitType->type) == 'in_kind' ? 'selected' : '' }}>عيني</option>
                                        <option value="service" {{ old('type', $benefitType->type) == 'service' ? 'selected' : '' }}>خدمة</option>
                                        <option value="insurance" {{ old('type', $benefitType->type) == 'insurance' ? 'selected' : '' }}>تأمين</option>
                                        <option value="allowance" {{ old('type', $benefitType->type) == 'allowance' ? 'selected' : '' }}>بدل</option>
                                    </select>
                                    <label>نوع الميزة <span class="text-danger">*</span></label>
                                    @error('type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="number" step="0.01" class="form-control" 
                                           name="default_value" placeholder="القيمة الافتراضية" value="{{ old('default_value', $benefitType->default_value) }}">
                                    <label>القيمة الافتراضية</label>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <select class="form-select" name="currency_id" id="currency_id">
                                        <option value="">اختر العملة</option>
                                        @foreach ($currencies as $currency)
                                            <option value="{{ $currency->id }}" {{ old('currency_id', $benefitType->currency_id) == $currency->id ? 'selected' : '' }}>
                                                {{ $currency->name_ar ?? $currency->name }} ({{ $currency->symbol_ar ?? $currency->symbol }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <label>العملة</label>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="number" class="form-control" 
                                           name="sort_order" placeholder="ترتيب العرض" 
                                           value="{{ old('sort_order', $benefitType->sort_order) }}" min="0">
                                    <label>ترتيب العرض</label>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-check mt-4">
                                    <input class="form-check-input" type="checkbox" name="is_taxable" id="is_taxable" 
                                           value="1" {{ old('is_taxable', $benefitType->is_taxable) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_taxable">
                                        خاضع للضريبة
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-check mt-4">
                                    <input class="form-check-input" type="checkbox" name="is_mandatory" id="is_mandatory" 
                                           value="1" {{ old('is_mandatory', $benefitType->is_mandatory) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_mandatory">
                                        إلزامي لجميع الموظفين
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-check mt-4">
                                    <input class="form-check-input" type="checkbox" name="requires_approval" id="requires_approval" 
                                           value="1" {{ old('requires_approval', $benefitType->requires_approval) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="requires_approval">
                                        يتطلب موافقة
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-check mt-4">
                                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active" 
                                           value="1" {{ old('is_active', $benefitType->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        نشط
                                    </label>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-floating">
                                    <textarea class="form-control" name="description" placeholder="الوصف" style="height: 100px">{{ old('description', $benefitType->description) }}</textarea>
                                    <label>الوصف (إنجليزي)</label>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-floating">
                                    <textarea class="form-control" name="description_ar" placeholder="الوصف بالعربية" style="height: 100px">{{ old('description_ar', $benefitType->description_ar) }}</textarea>
                                    <label>الوصف (عربي)</label>
                                </div>
                            </div>
                        </div>

                        <div class="text-end mt-4">
                            <a href="{{ route('admin.benefit-types.index') }}" class="btn btn-secondary px-4 me-2">إلغاء</a>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save me-2"></i>حفظ التعديلات
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop


