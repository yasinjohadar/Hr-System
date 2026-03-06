@extends('admin.layouts.master')

@section('page-title')
    إضافة سياسة جديدة
@stop

@section('content')
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="page-header d-flex justify-content-between align-items-center my-4">
                <h5 class="page-title mb-0">إضافة سياسة جديدة</h5>
                <a href="{{ route('admin.policies.index') }}" class="btn btn-secondary">العودة للقائمة</a>
            </div>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.policies.store') }}">
                        @csrf

                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">العنوان <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror"
                                    name="title" value="{{ old('title') }}" required maxlength="255">
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">الرابط (Slug)</label>
                                <input type="text" class="form-control @error('slug') is-invalid @enderror"
                                    name="slug" value="{{ old('slug') }}" placeholder="يُولَّد تلقائياً من العنوان إن تُرك فارغاً">
                                @error('slug')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">التصنيف</label>
                                <input type="text" class="form-control @error('category') is-invalid @enderror"
                                    name="category" value="{{ old('category') }}" maxlength="100" placeholder="مثال: لوائح الموظفين">
                                @error('category')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label">المحتوى</label>
                                <textarea class="form-control @error('content') is-invalid @enderror" name="content"
                                    rows="10" placeholder="نص السياسة أو اللائحة">{{ old('content') }}</textarea>
                                @error('content')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">تاريخ السريان</label>
                                <input type="date" class="form-control @error('effective_date') is-invalid @enderror"
                                    name="effective_date" value="{{ old('effective_date') }}">
                                @error('effective_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">الإصدار</label>
                                <input type="text" class="form-control @error('version') is-invalid @enderror"
                                    name="version" value="{{ old('version') }}" maxlength="50" placeholder="مثال: 1.0">
                                @error('version')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">مرفق (مسار الملف)</label>
                                <input type="text" class="form-control @error('document_path') is-invalid @enderror"
                                    name="document_path" value="{{ old('document_path') }}" placeholder="اختياري">
                                @error('document_path')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <div class="form-check">
                                    <input type="hidden" name="is_active" value="0">
                                    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" {{ old('is_active', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">السياسة نشطة (ظاهرة للموظفين للاعتراف)</label>
                                </div>
                            </div>
                        </div>

                        <div class="text-end mt-4">
                            <a href="{{ route('admin.policies.index') }}" class="btn btn-secondary px-4 me-2">إلغاء</a>
                            <button type="submit" class="btn btn-primary px-4"><i class="fas fa-save me-2"></i>حفظ السياسة</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop
