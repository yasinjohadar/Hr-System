@extends('admin.layouts.master')

@section('page-title')
    تعديل الهدف
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">تعديل الهدف</h5>
                </div>
                <div>
                    <a href="{{ route('admin.employee-goals.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-right me-2"></i>العودة للقائمة
                    </a>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.employee-goals.update', $goal->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">الموظف</label>
                                <input type="text" class="form-control" value="{{ $goal->employee->full_name }}" disabled>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">تقييم الأداء (اختياري)</label>
                                <select name="performance_review_id" class="form-select">
                                    <option value="">لا يوجد</option>
                                    @foreach ($reviews as $review)
                                        <option value="{{ $review->id }}" {{ old('performance_review_id', $goal->performance_review_id) == $review->id ? 'selected' : '' }}>
                                            {{ $review->employee->full_name }} - {{ $review->review_date->format('Y-m-d') }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">عنوان الهدف <span class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" 
                                       value="{{ old('title', $goal->title) }}" required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">الوصف</label>
                                <textarea name="description" class="form-control" rows="3">{{ old('description', $goal->description) }}</textarea>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">النوع <span class="text-danger">*</span></label>
                                <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                                    <option value="personal" {{ old('type', $goal->type) == 'personal' ? 'selected' : '' }}>شخصي</option>
                                    <option value="team" {{ old('type', $goal->type) == 'team' ? 'selected' : '' }}>فريق</option>
                                    <option value="department" {{ old('type', $goal->type) == 'department' ? 'selected' : '' }}>قسم</option>
                                    <option value="company" {{ old('type', $goal->type) == 'company' ? 'selected' : '' }}>شركة</option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">الأولوية <span class="text-danger">*</span></label>
                                <select name="priority" class="form-select @error('priority') is-invalid @enderror" required>
                                    <option value="low" {{ old('priority', $goal->priority) == 'low' ? 'selected' : '' }}>منخفضة</option>
                                    <option value="medium" {{ old('priority', $goal->priority) == 'medium' ? 'selected' : '' }}>متوسطة</option>
                                    <option value="high" {{ old('priority', $goal->priority) == 'high' ? 'selected' : '' }}>عالية</option>
                                    <option value="critical" {{ old('priority', $goal->priority) == 'critical' ? 'selected' : '' }}>حرجة</option>
                                </select>
                                @error('priority')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">نسبة التقدم <span class="text-danger">*</span></label>
                                <input type="number" name="progress_percentage" class="form-control @error('progress_percentage') is-invalid @enderror" 
                                       value="{{ old('progress_percentage', $goal->progress_percentage) }}" 
                                       min="0" max="100" required>
                                @error('progress_percentage')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">تاريخ البدء <span class="text-danger">*</span></label>
                                <input type="date" name="start_date" class="form-control @error('start_date') is-invalid @enderror" 
                                       value="{{ old('start_date', $goal->start_date->format('Y-m-d')) }}" required>
                                @error('start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">تاريخ الهدف <span class="text-danger">*</span></label>
                                <input type="date" name="target_date" class="form-control @error('target_date') is-invalid @enderror" 
                                       value="{{ old('target_date', $goal->target_date->format('Y-m-d')) }}" required>
                                @error('target_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">معايير النجاح</label>
                                <textarea name="success_criteria" class="form-control" rows="3">{{ old('success_criteria', $goal->success_criteria) }}</textarea>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">ملاحظات</label>
                                <textarea name="notes" class="form-control" rows="3">{{ old('notes', $goal->notes) }}</textarea>
                            </div>
                        </div>

                        <div class="text-end mt-4">
                            <a href="{{ route('admin.employee-goals.index') }}" class="btn btn-secondary px-4 me-2">إلغاء</a>
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


