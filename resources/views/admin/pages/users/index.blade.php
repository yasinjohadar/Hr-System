@extends('admin.layouts.master')

@section('page-title')
    قائمة المستخدمين
@stop

@section('content')
    <div class="main-content app-content admin-users">
        <div class="container-fluid">
            @include('admin.pages.users.partials.alerts')

            @include('admin.pages.users.partials.page-hero', [
                'heroTitle' => 'كافة المستخدمين',
                'heroSubtitle' => 'إدارة حسابات النظام والأدوار والصلاحيات',
                'heroActions' => '<a href="' . route('users.create') . '" class="btn btn-light btn-sm"><i class="ri-user-add-line me-1"></i>إنشاء مستخدم جديد</a>',
            ])

            @include('admin.pages.users.partials.stats-strip')

            <div class="card custom-card">
                <div class="card-body">
                    @include('admin.pages.users.partials.filters')

                    <div id="usersTableContainer">
                        @include('admin.pages.users.partials._table')
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('admin.pages.users.partials.modals')
@stop

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin-users.css') }}">
@endpush

@push('scripts')
    <script>
        window.adminUsersConfig = {
            searchUrl: @json(route('admin.users.search')),
            loginCodeUrlTemplate: @json(route('admin.users.login-code', ['user' => '__ID__'])),
        };
    </script>
    <script src="{{ asset('assets/js/admin-users.js') }}"></script>
@endpush
