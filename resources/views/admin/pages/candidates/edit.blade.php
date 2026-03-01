@extends('admin.layouts.master')

@section('page-title')
    تعديل مرشح
@stop

@section('css')
    <style>
        .form-floating label {
            right: auto;
            left: 0.75rem;
        }
    </style>
@stop

@section('content')
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
                <h5 class="page-title mb-0">تعديل مرشح</h5>
            </div>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.candidates.update', $candidate->id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="text" class="form-control @error('first_name') is-invalid @enderror" 
                                           name="first_name" placeholder="الاسم الأول" value="{{ old('first_name', $candidate->first_name) }}" required>
                                    <label>الاسم الأول <span class="text-danger">*</span></label>
                                    @error('first_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="text" class="form-control @error('last_name') is-invalid @enderror" 
                                           name="last_name" placeholder="اسم العائلة" value="{{ old('last_name', $candidate->last_name) }}" required>
                                    <label>اسم العائلة <span class="text-danger">*</span></label>
                                    @error('last_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                           name="email" placeholder="البريد الإلكتروني" value="{{ old('email', $candidate->email) }}" required>
                                    <label>البريد الإلكتروني <span class="text-danger">*</span></label>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                           name="phone" placeholder="الهاتف" value="{{ old('phone', $candidate->phone) }}" required>
                                    <label>الهاتف <span class="text-danger">*</span></label>
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="text" class="form-control" 
                                           name="alternate_phone" placeholder="هاتف بديل" value="{{ old('alternate_phone', $candidate->alternate_phone) }}">
                                    <label>هاتف بديل</label>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="text" class="form-control" 
                                           name="national_id" placeholder="رقم الهوية" value="{{ old('national_id', $candidate->national_id) }}">
                                    <label>رقم الهوية</label>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="date" class="form-control" 
                                           name="date_of_birth" placeholder="تاريخ الميلاد" 
                                           value="{{ old('date_of_birth', $candidate->date_of_birth ? $candidate->date_of_birth->format('Y-m-d') : '') }}">
                                    <label>تاريخ الميلاد</label>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-floating">
                                    <select class="form-select" name="gender" id="gender">
                                        <option value="">اختر الجنس</option>
                                        <option value="male" {{ old('gender', $candidate->gender) == 'male' ? 'selected' : '' }}>ذكر</option>
                                        <option value="female" {{ old('gender', $candidate->gender) == 'female' ? 'selected' : '' }}>أنثى</option>
                                    </select>
                                    <label>الجنس</label>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control" 
                                           name="current_position" placeholder="المنصب الحالي" value="{{ old('current_position', $candidate->current_position) }}">
                                    <label>المنصب الحالي</label>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control" 
                                           name="current_company" placeholder="الشركة الحالية" value="{{ old('current_company', $candidate->current_company) }}">
                                    <label>الشركة الحالية</label>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="number" class="form-control" 
                                           name="years_of_experience" placeholder="سنوات الخبرة" 
                                           value="{{ old('years_of_experience', $candidate->years_of_experience) }}" min="0">
                                    <label>سنوات الخبرة</label>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="text" class="form-control" 
                                           name="education_level" placeholder="المستوى التعليمي" value="{{ old('education_level', $candidate->education_level) }}">
                                    <label>المستوى التعليمي</label>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-floating">
                                    <select class="form-select" name="country_id" id="country_id">
                                        <option value="">اختر الدولة</option>
                                        @foreach ($countries as $country)
                                            <option value="{{ $country->id }}" {{ old('country_id', $candidate->country_id) == $country->id ? 'selected' : '' }}>
                                                {{ $country->name_ar ?? $country->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <label>الدولة</label>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="file" class="form-control @error('cv_path') is-invalid @enderror" 
                                           name="cv_path" accept=".pdf,.doc,.docx">
                                    <label>السيرة الذاتية (PDF, DOC, DOCX)</label>
                                    @if ($candidate->cv_path)
                                        <small class="text-muted">الملف الحالي: <a href="{{ Storage::url($candidate->cv_path) }}" target="_blank">عرض</a></small>
                                    @endif
                                    @error('cv_path')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="file" class="form-control" 
                                           name="photo" accept="image/*">
                                    <label>الصورة الشخصية</label>
                                    @if ($candidate->photo)
                                        <small class="text-muted">الصورة الحالية: <a href="{{ Storage::url($candidate->photo) }}" target="_blank">عرض</a></small>
                                    @endif
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-floating">
                                    <textarea class="form-control" name="address" placeholder="العنوان" style="height: 80px">{{ old('address', $candidate->address) }}</textarea>
                                    <label>العنوان</label>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-floating">
                                    <textarea class="form-control" name="notes" placeholder="ملاحظات" style="height: 100px">{{ old('notes', $candidate->notes) }}</textarea>
                                    <label>ملاحظات</label>
                                </div>
                            </div>
                        </div>

                        <div class="text-end mt-4">
                            <a href="{{ route('admin.candidates.index') }}" class="btn btn-secondary px-4 me-2">إلغاء</a>
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


