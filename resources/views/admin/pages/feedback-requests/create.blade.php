@extends('admin.layouts.master')

@section('page-title')
    إضافة طلب تقييم جديد
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
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">إضافة طلب تقييم جديد</h5>
                </div>
                <div>
                    <a href="{{ route('admin.feedback-requests.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-right me-2"></i>العودة للقائمة
                    </a>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.feedback-requests.store') }}">
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">الموظف <span class="text-danger">*</span></label>
                                <select class="form-select @error('employee_id') is-invalid @enderror" 
                                        name="employee_id" id="employee_id" required>
                                    <option value="">اختر الموظف</option>
                                    @foreach ($employees as $employee)
                                        <option value="{{ $employee->id }}" {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                                            {{ $employee->full_name }} ({{ $employee->employee_code }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('employee_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">نوع التقييم <span class="text-danger">*</span></label>
                                <select class="form-select @error('feedback_type') is-invalid @enderror" name="feedback_type" required>
                                    <option value="360_degree" {{ old('feedback_type', '360_degree') == '360_degree' ? 'selected' : '' }}>360 درجة</option>
                                    <option value="peer" {{ old('feedback_type') == 'peer' ? 'selected' : '' }}>زملاء</option>
                                    <option value="subordinate" {{ old('feedback_type') == 'subordinate' ? 'selected' : '' }}>مرؤوسين</option>
                                    <option value="self" {{ old('feedback_type') == 'self' ? 'selected' : '' }}>ذاتي</option>
                                    <option value="custom" {{ old('feedback_type') == 'custom' ? 'selected' : '' }}>مخصص</option>
                                </select>
                                @error('feedback_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">تاريخ البدء <span class="text-danger">*</span></label>
                                <input type="date" name="start_date" class="form-control @error('start_date') is-invalid @enderror" 
                                       value="{{ old('start_date') }}" required>
                                @error('start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">تاريخ الانتهاء <span class="text-danger">*</span></label>
                                <input type="date" name="end_date" class="form-control @error('end_date') is-invalid @enderror" 
                                       value="{{ old('end_date') }}" required>
                                @error('end_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label">التعليمات</label>
                                <textarea name="instructions" class="form-control" rows="4">{{ old('instructions') }}</textarea>
                                <small class="text-muted">تعليمات للمقيمين حول كيفية ملء التقييم</small>
                            </div>

                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="is_anonymous" id="is_anonymous" 
                                           value="1" {{ old('is_anonymous') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_anonymous">
                                        تقييم مجهول
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="text-end mt-4">
                            <a href="{{ route('admin.feedback-requests.index') }}" class="btn btn-secondary px-4 me-2">إلغاء</a>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save me-2"></i>حفظ الطلب
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

