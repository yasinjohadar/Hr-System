@extends('employee.layouts.master')

@section('page-title')
    فتح تذكرة جديدة
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">فتح تذكرة جديدة</h5>
                </div>
                <div>
                    <a href="{{ route('employee.tickets') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-right me-2"></i>العودة للقائمة
                    </a>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('employee.tickets.store') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">الموضوع <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" required maxlength="255">
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">التصنيف <span class="text-danger">*</span></label>
                            <select name="category" class="form-select @error('category') is-invalid @enderror" required>
                                <option value="">اختر التصنيف</option>
                                <option value="technical" {{ old('category') == 'technical' ? 'selected' : '' }}>تقني</option>
                                <option value="hr" {{ old('category') == 'hr' ? 'selected' : '' }}>موارد بشرية</option>
                                <option value="it" {{ old('category') == 'it' ? 'selected' : '' }}>تقنية معلومات</option>
                                <option value="facilities" {{ old('category') == 'facilities' ? 'selected' : '' }}>مرافق</option>
                                <option value="other" {{ old('category') == 'other' ? 'selected' : '' }}>أخرى</option>
                            </select>
                            @error('category')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">الأولوية <span class="text-danger">*</span></label>
                            <select name="priority" class="form-select @error('priority') is-invalid @enderror" required>
                                <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>منخفض</option>
                                <option value="medium" {{ old('priority') == 'medium' ? 'selected' : '' }}>متوسط</option>
                                <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>عالي</option>
                                <option value="urgent" {{ old('priority') == 'urgent' ? 'selected' : '' }}>عاجل</option>
                            </select>
                            @error('priority')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">الوصف <span class="text-danger">*</span></label>
                            <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="5" required>{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="text-end">
                            <a href="{{ route('employee.tickets') }}" class="btn btn-secondary px-4 me-2">إلغاء</a>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-paper-plane me-2"></i>فتح التذكرة
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop
