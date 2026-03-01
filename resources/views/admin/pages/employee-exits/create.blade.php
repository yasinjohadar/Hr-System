@extends('admin.layouts.master')

@section('page-title')
    طلب إنهاء خدمة جديد
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">طلب إنهاء خدمة جديد</h5>
                </div>
                <div>
                    <a href="{{ route('admin.employee-exits.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-right me-2"></i>العودة للقائمة
                    </a>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.employee-exits.store') }}">
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
                                <label class="form-label">نوع إنهاء الخدمة <span class="text-danger">*</span></label>
                                <select name="exit_type" class="form-select @error('exit_type') is-invalid @enderror" required>
                                    <option value="resignation" {{ old('exit_type', 'resignation') == 'resignation' ? 'selected' : '' }}>استقالة</option>
                                    <option value="termination" {{ old('exit_type') == 'termination' ? 'selected' : '' }}>إنهاء خدمة</option>
                                    <option value="retirement" {{ old('exit_type') == 'retirement' ? 'selected' : '' }}>تقاعد</option>
                                    <option value="end_of_contract" {{ old('exit_type') == 'end_of_contract' ? 'selected' : '' }}>انتهاء عقد</option>
                                    <option value="other" {{ old('exit_type') == 'other' ? 'selected' : '' }}>أخرى</option>
                                </select>
                                @error('exit_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">تاريخ الاستقالة <span class="text-danger">*</span></label>
                                <input type="date" name="resignation_date" class="form-control @error('resignation_date') is-invalid @enderror" 
                                       value="{{ old('resignation_date', date('Y-m-d')) }}" required>
                                @error('resignation_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">آخر يوم عمل <span class="text-danger">*</span></label>
                                <input type="date" name="last_working_day" class="form-control @error('last_working_day') is-invalid @enderror" 
                                       value="{{ old('last_working_day') }}" required>
                                @error('last_working_day')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">السبب</label>
                                <textarea name="reason" class="form-control" rows="3">{{ old('reason') }}</textarea>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">السبب (عربي)</label>
                                <textarea name="reason_ar" class="form-control" rows="3">{{ old('reason_ar') }}</textarea>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">ملاحظات</label>
                                <textarea name="notes" class="form-control" rows="3">{{ old('notes') }}</textarea>
                            </div>
                        </div>

                        <div class="text-end mt-4">
                            <a href="{{ route('admin.employee-exits.index') }}" class="btn btn-secondary px-4 me-2">إلغاء</a>
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


