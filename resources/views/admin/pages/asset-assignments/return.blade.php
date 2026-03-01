@extends('admin.layouts.master')

@section('page-title')
    استرجاع الأصل
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">استرجاع الأصل</h5>
                </div>
                <div>
                    <a href="{{ route('admin.asset-assignments.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-right me-2"></i>العودة للقائمة
                    </a>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">معلومات التوزيع</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">الأصل:</label>
                            <p class="form-control-plaintext">
                                <strong>{{ $assignment->asset->asset_code }}</strong> - {{ $assignment->asset->name_ar ?? $assignment->asset->name }}
                            </p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">الموظف:</label>
                            <p class="form-control-plaintext">{{ $assignment->employee->full_name }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">تاريخ التوزيع:</label>
                            <p class="form-control-plaintext">{{ $assignment->assigned_date->format('Y-m-d') }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">حالة الأصل عند التوزيع:</label>
                            <p class="form-control-plaintext">
                                <span class="badge bg-info">{{ $assignment->condition_on_assignment_name_ar }}</span>
                            </p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('admin.asset-assignments.return', $assignment->id) }}">
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">تاريخ الاسترجاع <span class="text-danger">*</span></label>
                                <input type="date" name="actual_return_date" class="form-control @error('actual_return_date') is-invalid @enderror" 
                                       value="{{ old('actual_return_date', date('Y-m-d')) }}" required>
                                @error('actual_return_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">حالة الأصل عند الاسترجاع <span class="text-danger">*</span></label>
                                <select name="condition_on_return" class="form-select @error('condition_on_return') is-invalid @enderror" required>
                                    <option value="excellent">ممتاز</option>
                                    <option value="good" selected>جيد</option>
                                    <option value="fair">متوسط</option>
                                    <option value="poor">ضعيف</option>
                                    <option value="damaged">معطل</option>
                                </select>
                                @error('condition_on_return')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">ملاحظات الاسترجاع</label>
                                <textarea name="return_notes" class="form-control" rows="3">{{ old('return_notes') }}</textarea>
                            </div>
                        </div>

                        <div class="text-end mt-4">
                            <a href="{{ route('admin.asset-assignments.index') }}" class="btn btn-secondary px-4 me-2">إلغاء</a>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-undo me-2"></i>استرجاع الأصل
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop


