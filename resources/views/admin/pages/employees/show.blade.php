@extends('admin.layouts.master')

@section('page-title')
    تفاصيل الموظف
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">تفاصيل الموظف</h5>
                </div>
                <div>
                    @can('employee-edit')
                    <a href="{{ route('admin.employees.edit', $employee->id) }}" class="btn btn-primary me-2">
                        <i class="fas fa-edit me-2"></i>تعديل
                    </a>
                    @endcan
                    <a href="{{ route('admin.employees.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-right me-2"></i>العودة للقائمة
                    </a>
                </div>
            </div>

            <div class="row">
                <!-- معلومات أساسية -->
                <div class="col-xl-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">المعلومات الأساسية</h5>
                        </div>
                        <div class="card-body text-center">
                            @if($employee->photo)
                                <img src="{{ asset('storage/' . $employee->photo) }}" alt="صورة الموظف" class="img-thumbnail rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                            @else
                                <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 150px; height: 150px;">
                                    <i class="fas fa-user fa-4x text-muted"></i>
                                </div>
                            @endif
                            <h4 class="mb-1">{{ $employee->full_name }}</h4>
                            <p class="text-muted mb-3">{{ $employee->employee_code }}</p>
                            <span class="badge bg-{{ $employee->is_active ? 'success' : 'danger' }} fs-14">
                                {{ $employee->is_active ? 'نشط' : 'غير نشط' }}
                            </span>
                        </div>
                    </div>

                    <!-- معلومات الاتصال -->
                    <div class="card mt-3">
                        <div class="card-header">
                            <h5 class="card-title mb-0">معلومات الاتصال</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label fw-bold">البريد الإلكتروني:</label>
                                <p class="form-control-plaintext">{{ $employee->user->email ?? $employee->personal_email ?? '-' }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">الهاتف:</label>
                                <p class="form-control-plaintext">{{ $employee->phone ?? $employee->personal_phone ?? '-' }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">العنوان:</label>
                                <p class="form-control-plaintext">{{ $employee->address ?? '-' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- معلومات الوظيفة -->
                <div class="col-xl-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">معلومات الوظيفة</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">القسم:</label>
                                    <p class="form-control-plaintext">{{ $employee->department->name_ar ?? $employee->department->name ?? '-' }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">المنصب:</label>
                                    <p class="form-control-plaintext">{{ $employee->position->title_ar ?? $employee->position->title ?? '-' }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">الفرع:</label>
                                    <p class="form-control-plaintext">{{ $employee->branch->name ?? '-' }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">المدير المباشر:</label>
                                    <p class="form-control-plaintext">{{ $employee->manager->full_name ?? '-' }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">تاريخ التوظيف:</label>
                                    <p class="form-control-plaintext">{{ $employee->hire_date ? $employee->hire_date->format('Y-m-d') : '-' }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">نوع التوظيف:</label>
                                    <p class="form-control-plaintext">{{ $employee->employment_type_name_ar ?? $employee->employment_type ?? '-' }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">الراتب الأساسي:</label>
                                    <p class="form-control-plaintext">{{ $employee->base_salary ? number_format($employee->base_salary, 2) . ' ر.س' : '-' }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">الحالة الوظيفية:</label>
                                    <p class="form-control-plaintext">{{ $employee->employment_status_name_ar ?? $employee->employment_status ?? '-' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- معلومات شخصية -->
                    <div class="card mt-3">
                        <div class="card-header">
                            <h5 class="card-title mb-0">المعلومات الشخصية</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">تاريخ الميلاد:</label>
                                    <p class="form-control-plaintext">{{ $employee->date_of_birth ? $employee->date_of_birth->format('Y-m-d') : '-' }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">الجنس:</label>
                                    <p class="form-control-plaintext">{{ $employee->gender == 'male' ? 'ذكر' : ($employee->gender == 'female' ? 'أنثى' : '-') }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">الجنسية:</label>
                                    <p class="form-control-plaintext">{{ $employee->nationality ?? '-' }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">الحالة الاجتماعية:</label>
                                    <p class="form-control-plaintext">{{ $employee->marital_status_name_ar ?? $employee->marital_status ?? '-' }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">رقم الهوية:</label>
                                    <p class="form-control-plaintext">{{ $employee->national_id ?? '-' }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">رقم الجواز:</label>
                                    <p class="form-control-plaintext">{{ $employee->passport_number ?? '-' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- معلومات الطوارئ -->
                    @if($employee->emergency_contact_name)
                    <div class="card mt-3">
                        <div class="card-header">
                            <h5 class="card-title mb-0">جهة الاتصال في حالات الطوارئ</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">الاسم:</label>
                                    <p class="form-control-plaintext">{{ $employee->emergency_contact_name }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">الهاتف:</label>
                                    <p class="form-control-plaintext">{{ $employee->emergency_contact_phone ?? '-' }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">العلاقة:</label>
                                    <p class="form-control-plaintext">{{ $employee->emergency_contact_relation ?? '-' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@stop
