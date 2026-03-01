@extends('admin.layouts.master')

@section('page-title')
    تعديل موظف
@stop

@section('css')
    <style>
        .form-floating label {
            right: auto;
            left: 0.75rem;
        }
        select.form-select {
            padding: 0.75rem;
        }
        .photo-preview {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #e9ecef;
        }
        .photo-upload {
            position: relative;
            display: inline-block;
        }
        .photo-upload input[type="file"] {
            position: absolute;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }
        .photo-upload-label {
            cursor: pointer;
            display: inline-block;
            padding: 8px 16px;
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            color: #6c757d;
            transition: all 0.3s;
        }
        .photo-upload-label:hover {
            background: #e9ecef;
            color: #495057;
        }
    </style>
@stop

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li class="small">{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="page-header d-flex justify-content-between align-items-center my-4">
                <h5 class="page-title mb-0">تعديل موظف: {{ $employee->full_name }}</h5>
            </div>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.employees.update', $employee->id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <!-- معلومات الموظف -->
                            <div class="col-12">
                                <h6 class="text-primary mb-3">معلومات الموظف</h6>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control @error('employee_code') is-invalid @enderror" 
                                           name="employee_code" placeholder="رقم الموظف" value="{{ old('employee_code', $employee->employee_code) }}" required>
                                    <label>رقم الموظف <span class="text-danger">*</span></label>
                                    @error('employee_code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control" value="{{ $employee->user->email ?? '' }}" disabled>
                                    <label>البريد الإلكتروني (للمستخدم المرتبط)</label>
                                </div>
                            </div>

                            <!-- المعلومات الشخصية -->
                            <div class="col-12 mt-4">
                                <h6 class="text-primary mb-3">المعلومات الشخصية</h6>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control @error('first_name') is-invalid @enderror" 
                                           name="first_name" placeholder="الاسم الأول" value="{{ old('first_name', $employee->first_name) }}" required>
                                    <label>الاسم الأول <span class="text-danger">*</span></label>
                                    @error('first_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control @error('last_name') is-invalid @enderror" 
                                           name="last_name" placeholder="اسم العائلة" value="{{ old('last_name', $employee->last_name) }}" required>
                                    <label>اسم العائلة <span class="text-danger">*</span></label>
                                    @error('last_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control @error('national_id') is-invalid @enderror" 
                                           name="national_id" placeholder="رقم الهوية" value="{{ old('national_id', $employee->national_id) }}">
                                    <label>رقم الهوية</label>
                                    @error('national_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="date" class="form-control @error('date_of_birth') is-invalid @enderror" 
                                           name="date_of_birth" value="{{ old('date_of_birth', $employee->date_of_birth ? $employee->date_of_birth->format('Y-m-d') : '') }}">
                                    <label>تاريخ الميلاد</label>
                                    @error('date_of_birth')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <select class="form-select @error('gender') is-invalid @enderror" name="gender">
                                        <option value="">اختر الجنس</option>
                                        <option value="male" {{ old('gender', $employee->gender) == 'male' ? 'selected' : '' }}>ذكر</option>
                                        <option value="female" {{ old('gender', $employee->gender) == 'female' ? 'selected' : '' }}>أنثى</option>
                                    </select>
                                    <label>الجنس</label>
                                    @error('gender')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- المعلومات الوظيفية -->
                            <div class="col-12 mt-4">
                                <h6 class="text-primary mb-3">المعلومات الوظيفية</h6>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <select class="form-select @error('department_id') is-invalid @enderror" name="department_id">
                                        <option value="">اختر القسم</option>
                                        @foreach ($departments as $dept)
                                            <option value="{{ $dept->id }}" {{ old('department_id', $employee->department_id) == $dept->id ? 'selected' : '' }}>
                                                {{ $dept->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <label>القسم</label>
                                    @error('department_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <select class="form-select @error('position_id') is-invalid @enderror" name="position_id">
                                        <option value="">اختر المنصب</option>
                                        @foreach ($positions as $pos)
                                            <option value="{{ $pos->id }}" {{ old('position_id', $employee->position_id) == $pos->id ? 'selected' : '' }}>
                                                {{ $pos->title }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <label>المنصب</label>
                                    @error('position_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <select class="form-select @error('manager_id') is-invalid @enderror" name="manager_id">
                                        <option value="">اختر المدير</option>
                                        @foreach ($managers as $manager)
                                            <option value="{{ $manager->id }}" {{ old('manager_id', $employee->manager_id) == $manager->id ? 'selected' : '' }}>
                                                {{ $manager->full_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <label>المدير المباشر</label>
                                    @error('manager_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="date" class="form-control @error('hire_date') is-invalid @enderror" 
                                           name="hire_date" value="{{ old('hire_date', $employee->hire_date ? $employee->hire_date->format('Y-m-d') : '') }}" required>
                                    <label>تاريخ التوظيف <span class="text-danger">*</span></label>
                                    @error('hire_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <select class="form-select @error('employment_type') is-invalid @enderror" name="employment_type" required>
                                        <option value="full_time" {{ old('employment_type', $employee->employment_type) == 'full_time' ? 'selected' : '' }}>دوام كامل</option>
                                        <option value="part_time" {{ old('employment_type', $employee->employment_type) == 'part_time' ? 'selected' : '' }}>دوام جزئي</option>
                                        <option value="contract" {{ old('employment_type', $employee->employment_type) == 'contract' ? 'selected' : '' }}>عقد</option>
                                        <option value="intern" {{ old('employment_type', $employee->employment_type) == 'intern' ? 'selected' : '' }}>متدرّب</option>
                                        <option value="freelance" {{ old('employment_type', $employee->employment_type) == 'freelance' ? 'selected' : '' }}>مستقل</option>
                                    </select>
                                    <label>نوع التوظيف <span class="text-danger">*</span></label>
                                    @error('employment_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <select class="form-select @error('employment_status') is-invalid @enderror" name="employment_status">
                                        <option value="active" {{ old('employment_status', $employee->employment_status) == 'active' ? 'selected' : '' }}>نشط</option>
                                        <option value="on_leave" {{ old('employment_status', $employee->employment_status) == 'on_leave' ? 'selected' : '' }}>في إجازة</option>
                                        <option value="terminated" {{ old('employment_status', $employee->employment_status) == 'terminated' ? 'selected' : '' }}>منتهي</option>
                                        <option value="resigned" {{ old('employment_status', $employee->employment_status) == 'resigned' ? 'selected' : '' }}>استقال</option>
                                    </select>
                                    <label>الحالة الوظيفية</label>
                                    @error('employment_status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="number" step="0.01" class="form-control @error('salary') is-invalid @enderror" 
                                           name="salary" placeholder="الراتب" value="{{ old('salary', $employee->salary) }}">
                                    <label>الراتب</label>
                                    @error('salary')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- معلومات الاتصال -->
                            <div class="col-12 mt-4">
                                <h6 class="text-primary mb-3">معلومات الاتصال</h6>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="email" class="form-control @error('personal_email') is-invalid @enderror" 
                                           name="personal_email" placeholder="البريد الشخصي" value="{{ old('personal_email', $employee->personal_email) }}">
                                    <label>البريد الشخصي</label>
                                    @error('personal_email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="tel" class="form-control @error('personal_phone') is-invalid @enderror" 
                                           name="personal_phone" placeholder="الهاتف الشخصي" value="{{ old('personal_phone', $employee->personal_phone) }}">
                                    <label>الهاتف الشخصي</label>
                                    @error('personal_phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- صورة الموظف -->
                            <div class="col-md-6">
                                <label class="form-label">صورة الموظف</label>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="photo-upload">
                                        <img id="photo-preview" src="{{ $employee->photo ? asset('storage/' . $employee->photo) : asset('assets/images/faces/default-avatar.jpg') }}" 
                                             alt="صورة الموظف" class="photo-preview">
                                        <input type="file" name="photo" id="photo-input" accept="image/*" 
                                               onchange="previewPhoto(this)">
                                    </div>
                                    <div>
                                        <label for="photo-input" class="photo-upload-label">
                                            <i class="fas fa-camera me-2"></i>اختر صورة
                                        </label>
                                    </div>
                                </div>
                                @error('photo')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- تفعيل الحساب -->
                            <div class="col-md-6">
                                <div class="form-check form-switch mt-4">
                                    <input class="form-check-input" type="checkbox" name="is_active" value="1" 
                                           id="is_active" {{ old('is_active', $employee->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        تفعيل الموظف
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="text-end mt-4">
                            <a href="{{ route('admin.employees.index') }}" class="btn btn-secondary px-4 me-2">
                                إلغاء
                            </a>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save me-2"></i>حفظ التعديلات
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

@section('script')
    <script>
        function previewPhoto(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('photo-preview').src = e.target.result;
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
@stop

