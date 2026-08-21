@extends('admin.layouts.master')

@section('page-title')
    إضافة دولة جديدة
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid admin-page-shell">
            @include('admin.pages.users.partials.alerts')

            <div class="admin-page-banner">
                <div class="admin-page-banner-main">
                    <span class="admin-page-banner-icon"><i class="ri-earth-line"></i></span>
                    <div class="admin-page-banner-text">
                        <h1>إضافة دولة جديدة</h1>
                        <p>أدخل بيانات الدولة وكودها ورمز هاتفها وعملتها</p>
                    </div>
                </div>
                <div class="admin-page-banner-actions">
                    <a href="{{ route('admin.countries.index') }}" class="admin-btn admin-btn-light">
                        <i class="ri-arrow-right-line"></i>
                        العودة للقائمة
                    </a>
                </div>
            </div>

            <div class="admin-page-card">
                <form class="admin-form" method="POST" action="{{ route('admin.countries.store') }}">
                    @csrf

                    <div class="admin-form-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="admin-form-label">اسم الدولة (إنجليزي) <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                       name="name" placeholder="Saudi Arabia" value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="admin-form-label">اسم الدولة (عربي)</label>
                                <input type="text" class="form-control @error('name_ar') is-invalid @enderror"
                                       name="name_ar" placeholder="المملكة العربية السعودية" value="{{ old('name_ar') }}">
                                @error('name_ar')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="admin-form-label">كود الدولة (2 أحرف) <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('code') is-invalid @enderror"
                                       name="code" placeholder="SA" value="{{ old('code') }}" required maxlength="2">
                                <small class="text-muted">مثال: SA, US, EG</small>
                                @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="admin-form-label">كود الدولة (3 أحرف)</label>
                                <input type="text" class="form-control @error('code3') is-invalid @enderror"
                                       name="code3" placeholder="SAU" value="{{ old('code3') }}" maxlength="3">
                                <small class="text-muted">مثال: SAU, USA, EGY</small>
                                @error('code3')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="admin-form-label">رمز الهاتف</label>
                                <input type="text" class="form-control @error('phone_code') is-invalid @enderror"
                                       name="phone_code" placeholder="+966" value="{{ old('phone_code') }}">
                                <small class="text-muted">مثال: +966, +1</small>
                                @error('phone_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="admin-form-label">رمز العملة</label>
                                <input type="text" class="form-control @error('currency_code') is-invalid @enderror"
                                       name="currency_code" placeholder="SAR" value="{{ old('currency_code') }}" maxlength="3">
                                <small class="text-muted">مثال: SAR, USD</small>
                                @error('currency_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="admin-form-label">العلم (Emoji)</label>
                                <input type="text" class="form-control @error('flag') is-invalid @enderror"
                                       name="flag" placeholder="🇸🇦" value="{{ old('flag') }}" maxlength="10">
                                <small class="text-muted">مثال: 🇸🇦</small>
                                @error('flag')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="admin-form-label">ترتيب العرض</label>
                                <input type="number" class="form-control @error('sort_order') is-invalid @enderror"
                                       name="sort_order" value="{{ old('sort_order', 0) }}">
                                @error('sort_order')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 d-flex align-items-end">
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" name="is_active" value="1"
                                           id="is_active" @checked(old('is_active', true))>
                                    <label class="form-check-label" for="is_active">تفعيل الدولة</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="admin-form-footer">
                        <a href="{{ route('admin.countries.index') }}" class="admin-btn admin-btn-secondary">إلغاء</a>
                        <button type="submit" class="admin-btn admin-btn-primary">
                            <i class="ri-save-line"></i>
                            حفظ الدولة
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop
