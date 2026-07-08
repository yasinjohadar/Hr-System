@extends('admin.layouts.master')

@section('page-title')
    تعيين رئيس قسم
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
                    <span class="admin-page-banner-icon"><i class="ri-user-add-line"></i></span>
                    <div class="admin-page-banner-text">
                        <h1>تعيين رئيس قسم جديد</h1>
                        <p>اختر مستخدماً وحدد الأقسام التي سيُديرها</p>
                    </div>
                </div>
                <div class="admin-page-banner-actions">
                    <a href="{{ route('admin.department-heads.index') }}" class="admin-btn admin-btn-light">
                        <i class="ri-arrow-right-line"></i>
                        العودة للقائمة
                    </a>
                </div>
            </div>

            <div class="admin-page-card">
                <form method="POST" action="{{ route('admin.department-heads.store') }}" class="admin-form" id="department-head-create-form">
                    @csrf

                    <div class="admin-form-body">
                        <div class="admin-form-section">
                            <div class="admin-form-section-head">
                                <div class="admin-section-icon admin-section-icon-blue">
                                    <i class="ri-user-line"></i>
                                </div>
                                <div>
                                    <h3>المستخدم</h3>
                                    <p>اختر الحساب الذي سيُعيَّن رئيس قسم</p>
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-8">
                                    <div class="admin-form-field">
                                        <label class="admin-form-label">المستخدم <span class="required">*</span></label>
                                        <select name="user_id" class="form-select @error('user_id') is-invalid @enderror" required>
                                            <option value="">— اختر مستخدماً —</option>
                                            @foreach ($users as $user)
                                                <option value="{{ $user->id }}" @selected(old('user_id') == $user->id)>
                                                    {{ $user->name }} ({{ $user->email }})
                                                    @if ($user->employee)
                                                        — {{ $user->employee->full_name }}
                                                    @endif
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('user_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                        <small class="text-muted d-block mt-1">لا يمكن اختيار مدير النظام (admin).</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="admin-form-section admin-form-section--departments">
                            <div class="admin-form-section-head">
                                <div class="admin-section-icon admin-section-icon-teal">
                                    <i class="ri-building-4-line"></i>
                                </div>
                                <div>
                                    <h3>الأقسام المُدارة</h3>
                                    <p>حدد الأقسام التي سيُديرها رئيس القسم — سيُستبدل المدير الحالي إن وُجد</p>
                                </div>
                            </div>

                            @include('admin.partials.department-head-departments-picker', [
                                'departments' => $departments,
                                'selectedDepartmentIds' => old('department_ids', []),
                                'currentUserId' => old('user_id'),
                            ])
                            @error('department_ids')<div class="text-danger small mt-2">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="admin-form-footer">
                        <a href="{{ route('admin.department-heads.index') }}" class="admin-btn admin-btn-secondary">إلغاء</a>
                        <button type="submit" class="admin-btn admin-btn-primary">
                            <i class="ri-save-line"></i>
                            حفظ التعيين
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
