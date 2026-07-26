@extends('admin.layouts.master')

@section('page-title')
    {{ $account->name }}
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid admin-page-shell">
            @include('admin.pages.users.partials.alerts')

            <div class="admin-page-banner">
                <div class="admin-page-banner-main">
                    <span class="admin-page-banner-icon"><i class="ri-bank-line"></i></span>
                    <div class="admin-page-banner-text">
                        <h1>{{ $account->name }}</h1>
                        <p>{{ $account->bank_name }} — {{ $account->account_number }}</p>
                    </div>
                </div>
                <div class="admin-page-banner-actions">
                    <a href="{{ route('admin.company-bank-accounts.index') }}" class="admin-btn admin-btn-light">القائمة</a>
                    @can('company-bank-account-edit')
                        <a href="{{ route('admin.company-bank-accounts.edit', $account) }}" class="admin-btn admin-btn-primary">تعديل</a>
                    @endcan
                </div>
            </div>

            <div class="admin-page-card p-4">
                <dl class="row mb-0">
                    <dt class="col-sm-3">الرصيد</dt>
                    <dd class="col-sm-9 fw-bold">{{ number_format((float) $account->balance, 2) }} {{ $account->currency->code ?? '' }}</dd>
                    <dt class="col-sm-3">IBAN</dt>
                    <dd class="col-sm-9">{{ $account->iban ?: '—' }}</dd>
                    <dt class="col-sm-3">الحالة</dt>
                    <dd class="col-sm-9">{{ $account->is_active ? 'نشط' : 'موقوف' }}</dd>
                    <dt class="col-sm-3">ملاحظات</dt>
                    <dd class="col-sm-9">{{ $account->notes ?: '—' }}</dd>
                </dl>
            </div>
        </div>
    </div>
@stop
