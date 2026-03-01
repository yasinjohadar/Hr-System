@extends('admin.layouts.master')

@section('page-title')
    تعديل التوزيع
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">تعديل التوزيع</h5>
                </div>
                <div>
                    <a href="{{ route('admin.asset-assignments.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-right me-2"></i>العودة للقائمة
                    </a>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.asset-assignments.update', $assignment->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">الأصل</label>
                                <input type="text" class="form-control" 
                                       value="{{ $assignment->asset->asset_code }} - {{ $assignment->asset->name_ar ?? $assignment->asset->name }}" disabled>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">الموظف</label>
                                <input type="text" class="form-control" 
                                       value="{{ $assignment->employee->full_name }}" disabled>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">تاريخ التوزيع <span class="text-danger">*</span></label>
                                <input type="date" name="assigned_date" class="form-control @error('assigned_date') is-invalid @enderror" 
                                       value="{{ old('assigned_date', $assignment->assigned_date->format('Y-m-d')) }}" required>
                                @error('assigned_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">تاريخ الاسترجاع المتوقع</label>
                                <input type="date" name="expected_return_date" class="form-control" 
                                       value="{{ old('expected_return_date', $assignment->expected_return_date?->format('Y-m-d')) }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">حالة الأصل عند التوزيع <span class="text-danger">*</span></label>
                                <select name="condition_on_assignment" class="form-select @error('condition_on_assignment') is-invalid @enderror" required>
                                    <option value="excellent" {{ old('condition_on_assignment', $assignment->condition_on_assignment) == 'excellent' ? 'selected' : '' }}>ممتاز</option>
                                    <option value="good" {{ old('condition_on_assignment', $assignment->condition_on_assignment) == 'good' ? 'selected' : '' }}>جيد</option>
                                    <option value="fair" {{ old('condition_on_assignment', $assignment->condition_on_assignment) == 'fair' ? 'selected' : '' }}>متوسط</option>
                                    <option value="poor" {{ old('condition_on_assignment', $assignment->condition_on_assignment) == 'poor' ? 'selected' : '' }}>ضعيف</option>
                                </select>
                                @error('condition_on_assignment')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">حالة التوزيع</label>
                                <input type="text" class="form-control" 
                                       value="{{ $assignment->assignment_status_name_ar }}" disabled>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">ملاحظات التوزيع</label>
                                <textarea name="assignment_notes" class="form-control" rows="3">{{ old('assignment_notes', $assignment->assignment_notes) }}</textarea>
                            </div>
                        </div>

                        <div class="text-end mt-4">
                            <a href="{{ route('admin.asset-assignments.index') }}" class="btn btn-secondary px-4 me-2">إلغاء</a>
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


