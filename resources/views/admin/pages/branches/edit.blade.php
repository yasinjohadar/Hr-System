@extends('admin.layouts.master')

@section('page-title')
    تعديل فرع
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
                <h5 class="page-title mb-0">تعديل فرع: {{ $branch->name }}</h5>
            </div>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.branches.update', $branch->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <!-- معلومات أساسية -->
                            <div class="col-12">
                                <h6 class="text-primary mb-3">معلومات أساسية</h6>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           name="name" placeholder="اسم الفرع" value="{{ old('name', $branch->name) }}" required>
                                    <label>اسم الفرع <span class="text-danger">*</span></label>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control @error('code') is-invalid @enderror" 
                                           name="code" placeholder="كود الفرع" value="{{ old('code', $branch->code) }}">
                                    <label>كود الفرع</label>
                                    @error('code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-floating">
                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                              name="description" placeholder="الوصف" style="height: 100px">{{ old('description', $branch->description) }}</textarea>
                                    <label>الوصف</label>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- معلومات الموقع -->
                            <div class="col-12 mt-4">
                                <h6 class="text-primary mb-3">معلومات الموقع</h6>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control @error('address') is-invalid @enderror" 
                                           name="address" placeholder="العنوان" value="{{ old('address', $branch->address) }}">
                                    <label>العنوان</label>
                                    @error('address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control @error('city') is-invalid @enderror" 
                                           name="city" placeholder="المدينة" value="{{ old('city', $branch->city) }}">
                                    <label>المدينة</label>
                                    @error('city')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control @error('country') is-invalid @enderror" 
                                           name="country" placeholder="الدولة" value="{{ old('country', $branch->country ?? 'السعودية') }}">
                                    <label>الدولة</label>
                                    @error('country')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control @error('postal_code') is-invalid @enderror" 
                                           name="postal_code" placeholder="الرمز البريدي" value="{{ old('postal_code', $branch->postal_code) }}">
                                    <label>الرمز البريدي</label>
                                    @error('postal_code')
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
                                    <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                                           name="phone" placeholder="الهاتف" value="{{ old('phone', $branch->phone) }}">
                                    <label>الهاتف</label>
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                           name="email" placeholder="البريد الإلكتروني" value="{{ old('email', $branch->email) }}">
                                    <label>البريد الإلكتروني</label>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- المدير -->
                            <div class="col-12 mt-4">
                                <h6 class="text-primary mb-3">المدير</h6>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control @error('manager_name') is-invalid @enderror" 
                                           name="manager_name" placeholder="اسم المدير" value="{{ old('manager_name', $branch->manager_name) }}">
                                    <label>اسم المدير</label>
                                    @error('manager_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <select class="form-select @error('manager_id') is-invalid @enderror" name="manager_id">
                                        <option value="">اختر مدير من المستخدمين</option>
                                        @foreach ($managers as $manager)
                                            <option value="{{ $manager->id }}" {{ old('manager_id', $branch->manager_id) == $manager->id ? 'selected' : '' }}>
                                                {{ $manager->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <label>مدير الفرع (من المستخدمين)</label>
                                    @error('manager_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- إعدادات -->
                            <div class="col-12 mt-4">
                                <h6 class="text-primary mb-3">إعدادات</h6>
                            </div>

                            <div class="col-md-6">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_main" value="1" 
                                           id="is_main" {{ old('is_main', $branch->is_main) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_main">
                                        الفرع الرئيسي
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_active" value="1" 
                                           id="is_active" {{ old('is_active', $branch->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        تفعيل الفرع
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="text-end mt-4">
                            <a href="{{ route('admin.branches.index') }}" class="btn btn-secondary px-4 me-2">
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
@stop

