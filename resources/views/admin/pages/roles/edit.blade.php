@extends('admin.layouts.master')

@section('page-title')
    تعديل الدور
@stop

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/role-permissions.css') }}?v=4">
@endpush

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid admin-page-shell">
            @include('admin.pages.users.partials.alerts')

            <div class="admin-page-banner">
                <div class="admin-page-banner-main">
                    <span class="admin-page-banner-icon"><i class="ri-shield-user-line"></i></span>
                    <div class="admin-page-banner-text">
                        <h1>تعديل الدور: {{ $role->name }}</h1>
                        <p>تحديث اسم الدور وصلاحياته</p>
                    </div>
                </div>
                <div class="admin-page-banner-actions">
                    <a href="{{ route('roles.index') }}" class="admin-btn admin-btn-light">
                        <i class="ri-arrow-right-line"></i>
                        العودة للقائمة
                    </a>
                </div>
            </div>

            @if (!empty($roleTemplates))
                <div class="admin-page-card mb-3">
                    <div class="card-toolbar">
                        <div class="d-flex flex-wrap align-items-center gap-2 w-100">
                            <span class="text-muted small fw-semibold">تطبيق قالب صلاحيات جاهز:</span>
                            @foreach ($roleTemplates as $key => $template)
                                <form method="POST" action="{{ route('roles.apply-template', $role->id) }}" class="d-inline"
                                    data-confirm="سيتم استبدال صلاحيات هذا الدور بقالب «{{ $template['label'] ?? $key }}». متابعة؟"
                                    data-confirm-type="warning"
                                    data-confirm-btn="تطبيق القالب">
                                    @csrf
                                    <input type="hidden" name="template" value="{{ $key }}">
                                    <button type="submit" class="admin-btn admin-btn-secondary admin-btn-sm">
                                        <i class="ri-file-copy-line"></i>
                                        {{ $template['label'] ?? $key }}
                                    </button>
                                </form>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <div class="admin-page-card">
                <form method="POST" action="{{ route('roles.update', $role->id) }}" class="admin-form admin-form--role" id="role-edit-form">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="id" value="{{ $role->id }}">

                    <div class="admin-form-body">
                        <div class="admin-form-section">
                            <div class="admin-form-section-head">
                                <div class="admin-section-icon admin-section-icon-blue">
                                    <i class="ri-shield-user-line"></i>
                                </div>
                                <div>
                                    <h3>بيانات الدور</h3>
                                    <p>اسم الدور كما يظهر في النظام</p>
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="admin-form-field">
                                        <label class="admin-form-label">اسم الدور <span class="required">*</span></label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                                               name="name" value="{{ old('name', $role->name) }}" required>
                                        @error('name')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="admin-form-section admin-form-section--permissions">
                            <div class="admin-form-section-head">
                                <div class="admin-section-icon admin-section-icon-purple">
                                    <i class="ri-key-2-line"></i>
                                </div>
                                <div>
                                    <h3>الصلاحيات</h3>
                                    <p>حدد الصلاحيات الممنوحة لهذا الدور</p>
                                </div>
                            </div>

                            @include('admin.partials.role-permissions-picker', [
                                'permissionsGrouped' => $permissionsGrouped,
                                'selectedPermissions' => old('permissions', $role->permissions->pluck('name', 'name')->all()),
                            ])
                            @error('permissions')<div class="text-danger small mt-2">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="admin-form-footer">
                        <a href="{{ route('roles.index') }}" class="admin-btn admin-btn-secondary">
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
            AdminTables.initAdminForm(document.getElementById('role-edit-form'));
        }
    });
</script>
@endpush
