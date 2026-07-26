@extends('admin.layouts.master')

@section('page-title')
    حساب بنكي جديد
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid admin-page-shell">
            @include('admin.pages.users.partials.alerts')

            <div class="admin-page-banner">
                <div class="admin-page-banner-main">
                    <span class="admin-page-banner-icon"><i class="ri-bank-line"></i></span>
                    <div class="admin-page-banner-text">
                        <h1>إنشاء حساب بنكي للشركة</h1>
                        <p>الرصيد الافتتاحي يُسجَّل عند الإنشاء فقط؛ بعدها عبر التحويلات</p>
                    </div>
                </div>
                <div class="admin-page-banner-actions">
                    <a href="{{ route('admin.company-bank-accounts.index') }}" class="admin-btn admin-btn-light">رجوع</a>
                </div>
            </div>

            <div class="admin-page-card">
                <form method="POST" action="{{ route('admin.company-bank-accounts.store') }}" class="admin-form">
                    @csrf
                    <div class="admin-form-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="admin-form-label">اسم الحساب *</label>
                                <input type="text" name="name" class="form-control" required value="{{ old('name') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="admin-form-label">اسم البنك *</label>
                                <input type="text" name="bank_name" class="form-control" required value="{{ old('bank_name') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="admin-form-label">رقم الحساب *</label>
                                <input type="text" name="account_number" class="form-control" required value="{{ old('account_number') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="admin-form-label">IBAN</label>
                                <input type="text" name="iban" class="form-control" value="{{ old('iban') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="admin-form-label">العملة</label>
                                <select name="currency_id" class="form-select">
                                    <option value="">—</option>
                                    @foreach ($currencies as $currency)
                                        <option value="{{ $currency->id }}" @selected(old('currency_id') == $currency->id)>{{ $currency->code }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="admin-form-label">الرصيد الافتتاحي</label>
                                <input type="number" step="0.01" min="0" name="balance" class="form-control" value="{{ old('balance', 0) }}">
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" name="is_active" value="1" id="is_active" @checked(old('is_active', true))>
                                    <label class="form-check-label" for="is_active">نشط</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="admin-form-label">ملاحظات</label>
                                <textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="admin-form-footer">
                        <button type="submit" class="admin-btn admin-btn-primary">حفظ</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop
