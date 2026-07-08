@extends('admin.layouts.master')

@section('page-title')
    إنشاء مستخدم جديد
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid admin-page-shell">
            @include('admin.pages.users.partials.alerts')

            <div class="admin-page-banner">
                <div class="admin-page-banner-main">
                    <span class="admin-page-banner-icon"><i class="ri-user-add-line"></i></span>
                    <div class="admin-page-banner-text">
                        <h1>إنشاء مستخدم جديد</h1>
                        <p>إضافة حساب جديد مع الأدوار والصلاحيات</p>
                    </div>
                </div>
                <div class="admin-page-banner-actions">
                    <a href="{{ route('users.index') }}" class="admin-btn admin-btn-light">
                        <i class="ri-arrow-right-line"></i>
                        العودة للقائمة
                    </a>
                </div>
            </div>

            <div class="admin-page-card">
                <form method="POST" action="{{ route('users.store') }}" enctype="multipart/form-data"
                    class="admin-form" id="user-form" autocomplete="off">
                    @csrf

                    @include('admin.pages.users.partials.form', ['isEdit' => false, 'roles' => $roles])

                    <div class="admin-form-footer">
                        <a href="{{ route('users.index') }}" class="admin-btn admin-btn-secondary">
                            <i class="ri-close-line"></i>
                            إلغاء
                        </a>
                        <button type="submit" class="admin-btn admin-btn-primary">
                            <i class="ri-save-line"></i>
                            حفظ المستخدم
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
