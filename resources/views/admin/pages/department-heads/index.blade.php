@extends('admin.layouts.master')

@section('page-title')
    رؤساء الأقسام
@stop

@section('content')
    <div class="main-content app-content admin-users">
        <div class="container-fluid">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @include('admin.pages.users.partials.page-hero', [
                'heroTitle' => 'رؤساء الأقسام',
                'heroSubtitle' => 'إدارة حسابات رؤساء الأقسام وتعيين الأقسام والصلاحيات',
                'heroActions' => auth()->user()->can('department-head-manage')
                    ? '<a href="' . route('admin.department-heads.create') . '" class="btn btn-light btn-sm"><i class="ri-user-add-line me-1"></i>تعيين رئيس قسم</a>'
                    : '',
            ])

            <div class="row mb-4 g-3">
                <div class="col-md-4">
                    <div class="card custom-card mb-0">
                        <div class="card-body py-3">
                            <span class="text-muted small">إجمالي رؤساء الأقسام (دور)</span>
                            <h4 class="mb-0 mt-1">{{ $stats['total'] }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card custom-card mb-0">
                        <div class="card-body py-3">
                            <span class="text-muted small">معيّنون على أقسام</span>
                            <h4 class="mb-0 mt-1">{{ $stats['with_departments'] }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card custom-card mb-0">
                        <div class="card-body py-3">
                            <span class="text-muted small">أقسام نشطة بلا مدير</span>
                            <h4 class="mb-0 mt-1 text-warning">{{ $stats['unassigned_departments'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card custom-card">
                <div class="card-body">
                    <form action="{{ route('admin.department-heads.index') }}" method="GET" class="row g-2 align-items-center mb-4">
                        <div class="col-md-5">
                            <input type="text" name="search" class="form-control" placeholder="بحث بالاسم، البريد، أو رمز الموظف"
                                   value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3">
                            <select name="status" class="form-select">
                                <option value="">كل الحالات</option>
                                <option value="active" @selected(request('status') === 'active')>نشط</option>
                                <option value="inactive" @selected(request('status') === 'inactive')>غير نشط</option>
                            </select>
                        </div>
                        <div class="col-md-4 d-flex gap-2">
                            <button type="submit" class="btn btn-primary btn-sm">بحث</button>
                            <a href="{{ route('admin.department-heads.index') }}" class="btn btn-outline-secondary btn-sm">مسح</a>
                        </div>
                    </form>

                    @include('admin.pages.department-heads.partials._table')
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
            loginCodeUrlTemplate: @json(route('admin.users.login-code', ['user' => '__ID__'])),
        };
    </script>
    <script src="{{ asset('assets/js/admin-users.js') }}"></script>
    <script>
        document.addEventListener('click', function (e) {
            const btn = e.target.closest('.toggle-password');
            if (!btn) return;
            const input = document.getElementById(btn.dataset.target);
            if (!input) return;
            const icon = btn.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                if (icon) icon.className = 'fas fa-eye-slash';
            } else {
                input.type = 'password';
                if (icon) icon.className = 'fas fa-eye';
            }
        });
    </script>
@endpush
