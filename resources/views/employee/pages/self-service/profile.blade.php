@extends('employee.layouts.master')

@section('page-title')
    الملف الشخصي
@stop

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/employee-profile.css') }}">
@endpush

@section('content')
    <div class="main-content app-content employee-profile-page">
        <div class="container-fluid pt-4">

            <div class="card page-hero mb-4">
                <div class="card-body py-4">
                    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="page-hero-icon">
                                <i class="ri-user-settings-line"></i>
                            </div>
                            <div>
                                <h4 class="mb-1 page-hero-title fw-bold">الملف الشخصي</h4>
                                <p class="mb-0 page-hero-subtitle">حدّث بيانات التواصل والطوارئ — البيانات الوظيفية للعرض فقط</p>
                            </div>
                        </div>
                        <a href="{{ route('employee.dashboard') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="ri-arrow-right-line me-1"></i>العودة للوحة التحكم
                        </a>
                    </div>
                </div>
            </div>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
                    <i class="ri-checkbox-circle-line me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
                    <i class="ri-error-warning-line me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <form method="POST" action="{{ route('employee.profile.update') }}">
                @csrf
                @method('PUT')

                <div class="row g-4">
                    <div class="col-lg-4">
                        <div class="profile-sidebar">
                            @if ($employee->photo)
                                <img src="{{ asset('storage/' . $employee->photo) }}" alt="" class="profile-sidebar-avatar">
                            @else
                                <div class="profile-sidebar-avatar--placeholder mx-auto">
                                    {{ substr($employee->first_name, 0, 1) }}
                                </div>
                            @endif
                            <h5 class="fw-bold mb-1">{{ $employee->full_name }}</h5>
                            <p class="text-muted fs-13 font-monospace mb-2">{{ $employee->employee_code }}</p>
                            <span class="badge bg-primary-transparent text-primary">{{ $employee->position->title ?? '—' }}</span>

                            <div class="profile-sidebar-meta">
                                <div class="profile-sidebar-row">
                                    <span class="profile-sidebar-label">القسم</span>
                                    <span class="profile-sidebar-value">{{ $employee->department->name ?? '—' }}</span>
                                </div>
                                <div class="profile-sidebar-row">
                                    <span class="profile-sidebar-label">الفرع</span>
                                    <span class="profile-sidebar-value">{{ $employee->branch->name ?? '—' }}</span>
                                </div>
                                <div class="profile-sidebar-row">
                                    <span class="profile-sidebar-label">البريد الوظيفي</span>
                                    <span class="profile-sidebar-value text-break">{{ $employee->work_email ?? '—' }}</span>
                                </div>
                                <div class="profile-sidebar-row">
                                    <span class="profile-sidebar-label">التوظيف</span>
                                    <span class="profile-sidebar-value">{{ $employee->hire_date->format('Y/m/d') }}</span>
                                </div>
                                <div class="profile-sidebar-row mb-0">
                                    <span class="profile-sidebar-label">الخدمة</span>
                                    <span class="profile-sidebar-value">
                                        {{ $yearsOfService }} {{ $yearsOfService === 1 ? 'سنة' : 'سنوات' }}
                                        @if ($monthsOfService > 0)
                                            · {{ $monthsOfService }} {{ $monthsOfService === 1 ? 'شهر' : 'شهر' }}
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-8">
                        <div class="form-section">
                            <div class="form-section-header">
                                <i class="ri-id-card-line me-1 text-primary"></i>البيانات الأساسية (للقراءة)
                            </div>
                            <div class="form-section-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">الاسم الكامل</label>
                                        <input type="text" class="form-control" value="{{ $employee->full_name }}" disabled>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">كود الموظف</label>
                                        <input type="text" class="form-control" value="{{ $employee->employee_code }}" disabled>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-section">
                            <div class="form-section-header">
                                <i class="ri-contacts-line me-1 text-primary"></i>بيانات التواصل
                            </div>
                            <div class="form-section-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">البريد الإلكتروني الشخصي</label>
                                        <input type="email" name="personal_email"
                                            class="form-control @error('personal_email') is-invalid @enderror"
                                            value="{{ old('personal_email', $employee->personal_email) }}"
                                            placeholder="example@email.com">
                                        @error('personal_email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">رقم الهاتف الشخصي</label>
                                        <input type="text" name="personal_phone"
                                            class="form-control @error('personal_phone') is-invalid @enderror"
                                            value="{{ old('personal_phone', $employee->personal_phone) }}"
                                            placeholder="+966...">
                                        @error('personal_phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">العنوان</label>
                                        <textarea name="address" class="form-control @error('address') is-invalid @enderror" rows="3"
                                            placeholder="العنوان الكامل">{{ old('address', $employee->address) }}</textarea>
                                        @error('address')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-section mb-0">
                            <div class="form-section-header">
                                <i class="ri-alarm-warning-line me-1 text-primary"></i>جهة الاتصال في الطوارئ
                            </div>
                            <div class="form-section-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">الاسم</label>
                                        <input type="text" name="emergency_contact_name" class="form-control"
                                            value="{{ old('emergency_contact_name', $employee->emergency_contact_name) }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">رقم الهاتف</label>
                                        <input type="text" name="emergency_contact_phone" class="form-control"
                                            value="{{ old('emergency_contact_phone', $employee->emergency_contact_phone) }}">
                                    </div>
                                </div>
                            </div>
                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="ri-save-line me-1"></i>حفظ التغييرات
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@stop
