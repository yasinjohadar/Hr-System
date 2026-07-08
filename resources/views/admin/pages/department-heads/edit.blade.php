@extends('admin.layouts.master')

@section('page-title')
    تعديل رئيس قسم
@stop

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin-department-heads.css') }}?v=1">
@endpush

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid admin-page-shell">
            @include('admin.pages.users.partials.alerts')

            <div class="admin-page-banner">
                <div class="admin-page-banner-main">
                    <span class="admin-page-banner-icon"><i class="ri-building-line"></i></span>
                    <div class="admin-page-banner-text">
                        <h1>تعديل أقسام: {{ $head->name }}</h1>
                        <p>{{ $head->email }} — إدارة الأقسام المُعيَّنة لرئيس القسم</p>
                    </div>
                </div>
                <div class="admin-page-banner-actions">
                    <a href="{{ route('admin.department-heads.show', $head->id) }}" class="admin-btn admin-btn-light">
                        <i class="ri-arrow-right-line"></i>
                        ملف رئيس القسم
                    </a>
                </div>
            </div>

            <div class="admin-page-card">
                <form method="POST" action="{{ route('admin.department-heads.update', $head->id) }}" class="admin-form" id="department-head-edit-form">
                    @csrf
                    @method('PUT')

                    <div class="admin-form-body">
                        <div class="admin-form-section admin-form-section--departments">
                            <div class="admin-form-section-head">
                                <div class="admin-section-icon admin-section-icon-teal">
                                    <i class="ri-building-4-line"></i>
                                </div>
                                <div>
                                    <h3>الأقسام المُدارة</h3>
                                    <p>إلغاء تحديد جميع الأقسام يزيل التعيين وقد يُلغى دور رئيس القسم</p>
                                </div>
                            </div>

                            @include('admin.partials.department-head-departments-picker', [
                                'departments' => $departments,
                                'selectedDepartmentIds' => old('department_ids', $managedDepartmentIds),
                                'currentUserId' => $head->id,
                            ])
                            @error('department_ids')<div class="text-danger small mt-2">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="admin-form-footer">
                        <a href="{{ route('admin.department-heads.show', $head->id) }}" class="admin-btn admin-btn-secondary">إلغاء</a>
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
    <script src="{{ asset('assets/js/admin-department-heads.js') }}?v=1"></script>
@endpush
