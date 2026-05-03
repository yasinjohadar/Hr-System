@extends('admin.layouts.master')

@section('page-title')
    تعديل قالب البريد الإلكتروني
@stop

@section('content')
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">تعديل قالب البريد الإلكتروني</h5>
                    <p class="text-muted small mb-0">{{ $template->name_ar ?? $template->name }}@if ($template->code) — {{ $template->code }}@endif</p>
                </div>
                <div class="d-flex flex-wrap gap-2 mt-2 mt-md-0">
                    <a href="{{ route('admin.email-templates.show', $template->id) }}" class="btn btn-light btn-sm">
                        <i class="fas fa-eye me-1"></i>عرض
                    </a>
                    <a href="{{ route('admin.email-templates.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-right me-1"></i>العودة للقائمة
                    </a>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.email-templates.update', $template->id) }}">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-lg-8">
                        <div class="card shadow-sm border-0">
                            <div class="card-header bg-light py-3 border-bottom">
                                <h6 class="mb-0 fw-semibold"><i class="fas fa-circle-info text-primary me-2"></i>بيانات القالب</h6>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">الاسم العربي</label>
                                        <input type="text" name="name_ar" class="form-control" value="{{ old('name_ar', $template->name_ar) }}" maxlength="255">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">الاسم (EN) <span class="text-danger">*</span></label>
                                        <input type="text" name="name" class="form-control" required value="{{ old('name', $template->name) }}" maxlength="255">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">الكود <span class="text-danger">*</span></label>
                                        <input type="text" name="code" class="form-control" required value="{{ old('code', $template->code) }}" maxlength="50">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">النوع <span class="text-danger">*</span></label>
                                        <select name="type" class="form-select" required>
                                            <option value="welcome" @selected(old('type', $template->type) === 'welcome')>ترحيب</option>
                                            <option value="leave_approved" @selected(old('type', $template->type) === 'leave_approved')>موافقة إجازة</option>
                                            <option value="leave_rejected" @selected(old('type', $template->type) === 'leave_rejected')>رفض إجازة</option>
                                            <option value="salary" @selected(old('type', $template->type) === 'salary')>راتب</option>
                                            <option value="birthday" @selected(old('type', $template->type) === 'birthday')>عيد ميلاد</option>
                                            <option value="anniversary" @selected(old('type', $template->type) === 'anniversary')>ذكرى سنوية</option>
                                            <option value="custom" @selected(old('type', $template->type) === 'custom')>مخصص</option>
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-semibold">الموضوع العربي</label>
                                        <input type="text" name="subject_ar" class="form-control" value="{{ old('subject_ar', $template->subject_ar) }}" maxlength="255">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-semibold">الموضوع (EN) <span class="text-danger">*</span></label>
                                        <input type="text" name="subject" class="form-control" required value="{{ old('subject', $template->subject) }}" maxlength="255">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="card shadow-sm border-0">
                            <div class="card-header bg-light py-3 border-bottom">
                                <h6 class="mb-0 fw-semibold"><i class="fas fa-cog text-primary me-2"></i>الإعدادات</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">الحالة</label>
                                    <div class="form-check form-switch">
                                        <input type="hidden" name="is_active" value="0">
                                        <input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active" @checked(old('is_active', $template->is_active))>
                                        <label class="form-check-label" for="is_active">نشط</label>
                                    </div>
                                </div>
                                <div class="mb-0">
                                    <label class="form-label fw-semibold">المتغيرات</label>
                                    <input type="text" name="variables" class="form-control" value="{{ old('variables', is_array($template->variables) ? implode(',', $template->variables) : $template->variables) }}" placeholder="name, date, link">
                                    <small class="text-muted">افصل بين المتغيرات بفاصلة (,)</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mt-1">
                    <div class="col-12">
                        <div class="card shadow-sm border-0">
                            <div class="card-header bg-light py-3 border-bottom">
                                <h6 class="mb-0 fw-semibold"><i class="fas fa-file-lines text-primary me-2"></i>محتوى القالب</h6>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">النص العربي</label>
                                        <textarea name="body_ar" class="form-control" rows="8">{{ old('body_ar', $template->body_ar) }}</textarea>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">النص الإنجليزي <span class="text-danger">*</span></label>
                                        <textarea name="body" class="form-control" required rows="8">{{ old('body', $template->body) }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2 mt-3 mb-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>حفظ التغييرات
                    </button>
                    <a href="{{ route('admin.email-templates.show', $template->id) }}" class="btn btn-secondary">
                        إلغاء
                    </a>
                </div>
            </form>
        </div>
    </div>
@stop
