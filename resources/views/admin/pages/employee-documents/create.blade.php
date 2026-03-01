@extends('admin.layouts.master')

@section('page-title')
    إضافة مستند جديد
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">إضافة مستند جديد</h5>
                </div>
                <div>
                    <a href="{{ route('admin.employee-documents.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-right me-2"></i>العودة للقائمة
                    </a>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.employee-documents.store') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">الموظف <span class="text-danger">*</span></label>
                                <select name="employee_id" class="form-select @error('employee_id') is-invalid @enderror" required>
                                    <option value="">اختر الموظف</option>
                                    @foreach ($employees as $emp)
                                        <option value="{{ $emp->id }}" {{ old('employee_id') == $emp->id ? 'selected' : '' }}>
                                            {{ $emp->full_name }} ({{ $emp->employee_code }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('employee_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">نوع المستند <span class="text-danger">*</span></label>
                                <select name="document_type" class="form-select @error('document_type') is-invalid @enderror" required>
                                    <option value="">اختر النوع</option>
                                    <option value="contract" {{ old('document_type') == 'contract' ? 'selected' : '' }}>عقد عمل</option>
                                    <option value="certificate" {{ old('document_type') == 'certificate' ? 'selected' : '' }}>شهادة</option>
                                    <option value="visa" {{ old('document_type') == 'visa' ? 'selected' : '' }}>تأشيرة</option>
                                    <option value="id" {{ old('document_type') == 'id' ? 'selected' : '' }}>هوية</option>
                                    <option value="passport" {{ old('document_type') == 'passport' ? 'selected' : '' }}>جواز سفر</option>
                                    <option value="license" {{ old('document_type') == 'license' ? 'selected' : '' }}>رخصة</option>
                                    <option value="other" {{ old('document_type') == 'other' ? 'selected' : '' }}>أخرى</option>
                                </select>
                                @error('document_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">العنوان <span class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" 
                                       value="{{ old('title') }}" required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">الوصف</label>
                                <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">تاريخ الإصدار</label>
                                <input type="date" name="issue_date" class="form-control" value="{{ old('issue_date') }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">تاريخ انتهاء الصلاحية</label>
                                <input type="date" name="expiry_date" class="form-control" value="{{ old('expiry_date') }}">
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">الملف <span class="text-danger">*</span></label>
                                <input type="file" name="file_path" class="form-control @error('file_path') is-invalid @enderror" 
                                       accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" required>
                                <small class="text-muted">الحد الأقصى: 10MB | الصيغ المدعومة: PDF, DOC, DOCX, JPG, PNG</small>
                                @error('file_path')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <div class="form-check">
                                    <input type="checkbox" name="is_required" class="form-check-input" value="1" 
                                           {{ old('is_required') ? 'checked' : '' }}>
                                    <label class="form-check-label">مستند مطلوب</label>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">ملاحظات</label>
                                <textarea name="notes" class="form-control" rows="3">{{ old('notes') }}</textarea>
                            </div>
                        </div>

                        <div class="text-end mt-4">
                            <a href="{{ route('admin.employee-documents.index') }}" class="btn btn-secondary px-4 me-2">إلغاء</a>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save me-2"></i>حفظ
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop


