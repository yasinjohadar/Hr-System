@extends('employee.layouts.master')

@section('page-title')
    الملف الشخصي
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">الملف الشخصي</h5>
                </div>
                <div>
                    <a href="{{ route('employee.dashboard') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-right me-2"></i>العودة للوحة التحكم
                    </a>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">معلوماتي الشخصية</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('employee.profile.update') }}">
                                @csrf
                                @method('PUT')

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">الاسم الكامل</label>
                                        <input type="text" class="form-control" value="{{ $employee->full_name }}" disabled>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">كود الموظف</label>
                                        <input type="text" class="form-control" value="{{ $employee->employee_code }}" disabled>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">البريد الإلكتروني الشخصي</label>
                                        <input type="email" name="personal_email" class="form-control @error('personal_email') is-invalid @enderror" 
                                               value="{{ old('personal_email', $employee->personal_email) }}">
                                        @error('personal_email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">رقم الهاتف الشخصي</label>
                                        <input type="text" name="personal_phone" class="form-control @error('personal_phone') is-invalid @enderror" 
                                               value="{{ old('personal_phone', $employee->personal_phone) }}">
                                        @error('personal_phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label">العنوان</label>
                                        <textarea name="address" class="form-control" rows="2">{{ old('address', $employee->address) }}</textarea>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">اسم جهة الاتصال في حالات الطوارئ</label>
                                        <input type="text" name="emergency_contact_name" class="form-control" 
                                               value="{{ old('emergency_contact_name', $employee->emergency_contact_name) }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">رقم هاتف جهة الاتصال</label>
                                        <input type="text" name="emergency_contact_phone" class="form-control" 
                                               value="{{ old('emergency_contact_phone', $employee->emergency_contact_phone) }}">
                                    </div>
                                </div>

                                <div class="text-end mt-4">
                                    <button type="submit" class="btn btn-primary px-4">
                                        <i class="fas fa-save me-2"></i>حفظ التغييرات
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop


