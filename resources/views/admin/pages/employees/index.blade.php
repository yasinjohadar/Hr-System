@extends('admin.layouts.master')

@section('page-title')
    قائمة الموظفين
@stop

@section('content')
    <div class="main-content app-content admin-employees">
        <div class="container-fluid">
            @include('admin.pages.employees.partials.alerts')

            @php
                $heroActions = '';
                if (auth()->user()->can('employee-create')) {
                    $heroActions .= '<a href="' . route('admin.employees.create') . '" class="btn btn-light btn-sm"><i class="ri-user-add-line me-1"></i>إضافة موظف جديد</a>';
                }
                if (auth()->user()->can('export-data')) {
                    $heroActions .= '<a href="' . route('admin.export.employees') . '" class="btn btn-outline-light btn-sm"><i class="ri-file-excel-2-line me-1"></i>تصدير Excel</a>';
                }
            @endphp

            @include('admin.pages.employees.partials.page-hero', [
                'heroTitle' => 'كافة الموظفين',
                'heroSubtitle' => 'إدارة بيانات الموظفين والأقسام والمناصب',
                'heroActions' => $heroActions,
            ])

            @include('admin.pages.employees.partials.stats-strip')

            <div class="card custom-card">
                <div class="card-body">
                    @include('admin.pages.employees.partials.filters')

                    <div id="employeesTableContainer">
                        @include('admin.pages.employees.partials._table')
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('admin.pages.employees.partials.modals')
@stop

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin-employees.css') }}">
@endpush

@push('scripts')
    <script>
        window.adminEmployeesConfig = {
            indexUrl: @json(route('admin.employees.index')),
            toggleUrlTemplate: @json(route('admin.employees.toggle-active', ['employee' => '__ID__'])),
            loginCodeUrlTemplate: @json(route('admin.employees.login-code', ['employee' => '__ID__'])),
        };
    </script>
    <script src="{{ asset('assets/js/admin-employees.js') }}"></script>
@endpush
