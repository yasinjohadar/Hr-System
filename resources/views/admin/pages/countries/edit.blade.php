@extends('admin.layouts.master')

@section('page-title')
    تعديل دولة
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
                <h5 class="page-title mb-0">تعديل دولة: {{ $country->name_ar ?? $country->name }}</h5>
            </div>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.countries.update', $country->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           name="name" placeholder="اسم الدولة بالإنجليزية" value="{{ old('name', $country->name) }}" required>
                                    <label>اسم الدولة (إنجليزي) <span class="text-danger">*</span></label>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control @error('name_ar') is-invalid @enderror" 
                                           name="name_ar" placeholder="اسم الدولة بالعربية" value="{{ old('name_ar', $country->name_ar) }}">
                                    <label>اسم الدولة (عربي)</label>
                                    @error('name_ar')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="text" class="form-control @error('code') is-invalid @enderror" 
                                           name="code" placeholder="كود الدولة" value="{{ old('code', $country->code) }}" required maxlength="2">
                                    <label>كود الدولة (2 أحرف) <span class="text-danger">*</span></label>
                                    @error('code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="text" class="form-control @error('code3') is-invalid @enderror" 
                                           name="code3" placeholder="كود الدولة 3 أحرف" value="{{ old('code3', $country->code3) }}" maxlength="3">
                                    <label>كود الدولة (3 أحرف)</label>
                                    @error('code3')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="text" class="form-control @error('phone_code') is-invalid @enderror" 
                                           name="phone_code" placeholder="رمز الهاتف" value="{{ old('phone_code', $country->phone_code) }}">
                                    <label>رمز الهاتف</label>
                                    @error('phone_code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control @error('currency_code') is-invalid @enderror" 
                                           name="currency_code" placeholder="رمز العملة" value="{{ old('currency_code', $country->currency_code) }}" maxlength="3">
                                    <label>رمز العملة</label>
                                    @error('currency_code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control @error('flag') is-invalid @enderror" 
                                           name="flag" placeholder="العلم" value="{{ old('flag', $country->flag) }}" maxlength="10">
                                    <label>العلم (Emoji)</label>
                                    @error('flag')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="number" class="form-control @error('sort_order') is-invalid @enderror" 
                                           name="sort_order" placeholder="ترتيب العرض" value="{{ old('sort_order', $country->sort_order) }}">
                                    <label>ترتيب العرض</label>
                                    @error('sort_order')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-check form-switch mt-4">
                                    <input class="form-check-input" type="checkbox" name="is_active" value="1" 
                                           id="is_active" {{ old('is_active', $country->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        تفعيل الدولة
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="text-end mt-4">
                            <a href="{{ route('admin.countries.index') }}" class="btn btn-secondary px-4 me-2">إلغاء</a>
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

@section('script')
@stop

