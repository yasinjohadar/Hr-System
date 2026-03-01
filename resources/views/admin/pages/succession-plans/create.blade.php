@extends('admin.layouts.master')

@section('page-title')
    إضافة خطة تعاقب جديدة
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
                    <h5 class="page-title fs-21 mb-1">إضافة خطة تعاقب جديدة</h5>
                </div>
                <div>
                    <a href="{{ route('admin.succession-plans.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-right me-2"></i>العودة للقائمة
                    </a>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.succession-plans.store') }}">
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">المنصب <span class="text-danger">*</span></label>
                                <select class="form-select @error('position_id') is-invalid @enderror" 
                                        name="position_id" id="position_id" required>
                                    <option value="">اختر المنصب</option>
                                    @foreach ($positions as $position)
                                        <option value="{{ $position->id }}" {{ old('position_id') == $position->id ? 'selected' : '' }}>
                                            {{ $position->title_ar ?? $position->title }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('position_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">الموظف الحالي</label>
                                <select class="form-select" name="current_employee_id">
                                    <option value="">اختر الموظف الحالي</option>
                                    @foreach ($employees as $employee)
                                        <option value="{{ $employee->id }}" {{ old('current_employee_id') == $employee->id ? 'selected' : '' }}>
                                            {{ $employee->full_name }} ({{ $employee->employee_code }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">كود الخطة</label>
                                <input type="text" name="plan_code" class="form-control" 
                                       value="{{ old('plan_code') }}" placeholder="سيتم إنشاؤه تلقائياً إذا تركت فارغاً">
                                <small class="text-muted">مثل: SP-2024-001</small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">تاريخ الهدف</label>
                                <input type="date" name="target_date" class="form-control" 
                                       value="{{ old('target_date') }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">الأولوية <span class="text-danger">*</span></label>
                                <select class="form-select @error('urgency') is-invalid @enderror" name="urgency" required>
                                    <option value="low" {{ old('urgency', 'medium') == 'low' ? 'selected' : '' }}>منخفض</option>
                                    <option value="medium" {{ old('urgency', 'medium') == 'medium' ? 'selected' : '' }}>متوسط</option>
                                    <option value="high" {{ old('urgency') == 'high' ? 'selected' : '' }}>عالي</option>
                                    <option value="critical" {{ old('urgency') == 'critical' ? 'selected' : '' }}>حرج</option>
                                </select>
                                @error('urgency')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">الحالة <span class="text-danger">*</span></label>
                                <select class="form-select @error('status') is-invalid @enderror" name="status" required>
                                    <option value="planning" {{ old('status', 'planning') == 'planning' ? 'selected' : '' }}>قيد التخطيط</option>
                                    <option value="in_progress" {{ old('status') == 'in_progress' ? 'selected' : '' }}>قيد التنفيذ</option>
                                    <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>مكتمل</option>
                                    <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>ملغي</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label">الوصف</label>
                                <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                            </div>

                            <div class="col-12">
                                <label class="form-label">ملاحظات</label>
                                <textarea name="notes" class="form-control" rows="3">{{ old('notes') }}</textarea>
                            </div>
                        </div>

                        <div class="text-end mt-4">
                            <a href="{{ route('admin.succession-plans.index') }}" class="btn btn-secondary px-4 me-2">إلغاء</a>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save me-2"></i>حفظ الخطة
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

