@extends('admin.layouts.master')

@section('page-title')
    إضافة عملة جديدة
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
                <h5 class="page-title mb-0">إضافة عملة جديدة</h5>
            </div>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.currencies.store') }}">
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           name="name" placeholder="اسم العملة بالإنجليزية" value="{{ old('name') }}" required>
                                    <label>اسم العملة (إنجليزي) <span class="text-danger">*</span></label>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control @error('name_ar') is-invalid @enderror" 
                                           name="name_ar" placeholder="اسم العملة بالعربية" value="{{ old('name_ar') }}">
                                    <label>اسم العملة (عربي)</label>
                                    @error('name_ar')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="text" class="form-control @error('code') is-invalid @enderror" 
                                           name="code" placeholder="كود العملة" value="{{ old('code') }}" required maxlength="3">
                                    <label>كود العملة (3 أحرف) <span class="text-danger">*</span></label>
                                    <small class="form-text text-muted">مثال: SAR, USD, EUR</small>
                                    @error('code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="text" class="form-control @error('symbol') is-invalid @enderror" 
                                           name="symbol" placeholder="رمز العملة" value="{{ old('symbol') }}" maxlength="10">
                                    <label>الرمز (إنجليزي)</label>
                                    <small class="form-text text-muted">مثال: $, €, £</small>
                                    @error('symbol')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="text" class="form-control @error('symbol_ar') is-invalid @enderror" 
                                           name="symbol_ar" placeholder="رمز العملة بالعربية" value="{{ old('symbol_ar') }}" maxlength="10">
                                    <label>الرمز (عربي)</label>
                                    <small class="form-text text-muted">مثال: ر.س</small>
                                    @error('symbol_ar')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="number" class="form-control @error('decimal_places') is-invalid @enderror" 
                                           name="decimal_places" placeholder="عدد الأرقام العشرية" value="{{ old('decimal_places', 2) }}" min="0" max="4">
                                    <label>عدد الأرقام العشرية</label>
                                    @error('decimal_places')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="number" step="0.0001" class="form-control @error('exchange_rate') is-invalid @enderror" 
                                           name="exchange_rate" placeholder="سعر الصرف" value="{{ old('exchange_rate', 1.0000) }}" min="0">
                                    <label>سعر الصرف</label>
                                    <small class="form-text text-muted">مقابل العملة الأساسية</small>
                                    @error('exchange_rate')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="number" class="form-control @error('sort_order') is-invalid @enderror" 
                                           name="sort_order" placeholder="ترتيب العرض" value="{{ old('sort_order', 0) }}">
                                    <label>ترتيب العرض</label>
                                    @error('sort_order')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-check form-switch mt-4">
                                    <input class="form-check-input" type="checkbox" name="is_base_currency" value="1" 
                                           id="is_base_currency" {{ old('is_base_currency') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_base_currency">
                                        عملة أساسية
                                    </label>
                                    <small class="form-text text-muted d-block">العملة الأساسية سعر صرفها دائماً 1.0000</small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-check form-switch mt-4">
                                    <input class="form-check-input" type="checkbox" name="is_active" value="1" 
                                           id="is_active" {{ old('is_active', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        تفعيل العملة
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="text-end mt-4">
                            <a href="{{ route('admin.currencies.index') }}" class="btn btn-secondary px-4 me-2">إلغاء</a>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save me-2"></i>حفظ العملة
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

