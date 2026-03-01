@extends('admin.layouts.master')

@section('page-title')
    تعديل سير العمل
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">تعديل سير العمل</h5>
                </div>
                <div>
                    <a href="{{ route('admin.workflows.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-right me-2"></i>العودة
                    </a>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.workflows.update', $workflow->id) }}">
                        @csrf
                        @method('PUT')
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">الاسم <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                       value="{{ old('name', $workflow->name) }}" required>
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">الاسم (عربي)</label>
                                <input type="text" name="name_ar" class="form-control" value="{{ old('name_ar', $workflow->name_ar) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">الكود</label>
                                <input type="text" name="code" class="form-control" value="{{ old('code', $workflow->code) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">النوع <span class="text-danger">*</span></label>
                                <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                                    <option value="leave_request" {{ old('type', $workflow->type) == 'leave_request' ? 'selected' : '' }}>طلب إجازة</option>
                                    <option value="expense_request" {{ old('type', $workflow->type) == 'expense_request' ? 'selected' : '' }}>طلب مصروف</option>
                                    <option value="task_approval" {{ old('type', $workflow->type) == 'task_approval' ? 'selected' : '' }}>موافقة مهمة</option>
                                    <option value="performance_review" {{ old('type', $workflow->type) == 'performance_review' ? 'selected' : '' }}>تقييم الأداء</option>
                                    <option value="custom" {{ old('type', $workflow->type) == 'custom' ? 'selected' : '' }}>مخصص</option>
                                </select>
                                @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">الوصف</label>
                                <textarea name="description" class="form-control" rows="3">{{ old('description', $workflow->description) }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_active" value="1" 
                                           id="is_active" {{ old('is_active', $workflow->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">نشط</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">تحديث</button>
                                <a href="{{ route('admin.workflows.index') }}" class="btn btn-secondary">إلغاء</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

