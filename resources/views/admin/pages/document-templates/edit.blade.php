@extends('admin.layouts.master')

@section('page-title')
    تعديل قالب المستند
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
                    <h5 class="page-title fs-21 mb-1">تعديل قالب المستند</h5>
                </div>
                <div>
                    <a href="{{ route('admin.document-templates.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-right me-2"></i>العودة للقائمة
                    </a>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.document-templates.update', $template->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">الاسم <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                       value="{{ old('name', $template->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">الاسم (عربي)</label>
                                <input type="text" name="name_ar" class="form-control" value="{{ old('name_ar', $template->name_ar) }}">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">الكود <span class="text-danger">*</span></label>
                                <input type="text" name="code" class="form-control @error('code') is-invalid @enderror" 
                                       value="{{ old('code', $template->code) }}" required>
                                @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">كود فريد للقالب (مثل: CONTRACT_001)</small>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">النوع <span class="text-danger">*</span></label>
                                <select class="form-select @error('type') is-invalid @enderror" name="type" required>
                                    <option value="contract" {{ old('type', $template->type) == 'contract' ? 'selected' : '' }}>عقد عمل</option>
                                    <option value="letter" {{ old('type', $template->type) == 'letter' ? 'selected' : '' }}>خطاب</option>
                                    <option value="certificate" {{ old('type', $template->type) == 'certificate' ? 'selected' : '' }}>شهادة</option>
                                    <option value="report" {{ old('type', $template->type) == 'report' ? 'selected' : '' }}>تقرير</option>
                                    <option value="custom" {{ old('type', $template->type) == 'custom' ? 'selected' : '' }}>مخصص</option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">صيغة الملف <span class="text-danger">*</span></label>
                                <select class="form-select @error('file_format') is-invalid @enderror" name="file_format" required>
                                    <option value="pdf" {{ old('file_format', $template->file_format) == 'pdf' ? 'selected' : '' }}>PDF</option>
                                    <option value="docx" {{ old('file_format', $template->file_format) == 'docx' ? 'selected' : '' }}>DOCX</option>
                                    <option value="html" {{ old('file_format', $template->file_format) == 'html' ? 'selected' : '' }}>HTML</option>
                                </select>
                                @error('file_format')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label">الوصف</label>
                                <textarea name="description" class="form-control" rows="2">{{ old('description', $template->description) }}</textarea>
                            </div>

                            <div class="col-12">
                                <label class="form-label">المحتوى <span class="text-danger">*</span></label>
                                <textarea name="content" id="content" class="form-control @error('content') is-invalid @enderror" 
                                          rows="10" required>{{ old('content', $template->content) }}</textarea>
                                @error('content')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">يمكنك استخدام المتغيرات مثل: {employee_name}, {date}, {position}, إلخ</small>
                            </div>

                            <div class="col-12">
                                <label class="form-label">المحتوى (عربي)</label>
                                <textarea name="content_ar" id="content_ar" class="form-control" rows="10">{{ old('content_ar', $template->content_ar) }}</textarea>
                            </div>

                            <div class="col-12">
                                <label class="form-label">الحالة</label>
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active" 
                                           value="1" {{ old('is_active', $template->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        نشط
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="text-end mt-4">
                            <a href="{{ route('admin.document-templates.index') }}" class="btn btn-secondary px-4 me-2">إلغاء</a>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save me-2"></i>حفظ التغييرات
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

