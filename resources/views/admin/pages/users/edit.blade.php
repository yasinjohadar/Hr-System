@extends('admin.layouts.master')

@section('page-title')
    تعديل المستخدم
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid admin-page-shell">
            @include('admin.pages.users.partials.alerts')

            <div class="admin-page-banner">
                <div class="admin-page-banner-main">
                    <span class="admin-page-banner-icon"><i class="ri-user-settings-line"></i></span>
                    <div class="admin-page-banner-text">
                        <h1>تعديل المستخدم</h1>
                        <p>{{ $user->name }}</p>
                    </div>
                </div>
                <div class="admin-page-banner-actions">
                    <a href="{{ route('users.show', $user->id) }}" class="admin-btn admin-btn-light">
                        <i class="ri-eye-line"></i>
                        عرض
                    </a>
                    <a href="{{ route('users.index') }}" class="admin-btn admin-btn-light">
                        <i class="ri-arrow-right-line"></i>
                        القائمة
                    </a>
                </div>
            </div>

            <div class="admin-page-card">
                <form method="POST" action="{{ route('users.update', $user->id) }}" enctype="multipart/form-data"
                    class="admin-form" id="user-form" autocomplete="off">
                    @csrf
                    @method('PUT')

                    @include('admin.pages.users.partials.form', ['isEdit' => true, 'user' => $user, 'roles' => $roles])

                    <div class="admin-form-footer">
                        <a href="{{ route('users.index') }}" class="admin-btn admin-btn-secondary">
                            <i class="ri-close-line"></i>
                            إلغاء
                        </a>
                        <button type="submit" class="admin-btn admin-btn-primary">
                            <i class="ri-save-line"></i>
                            حفظ التعديلات
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (window.AdminTables?.initAdminForm) {
            AdminTables.initAdminForm(document.getElementById('user-form'));
        }
    });
</script>
@endpush
