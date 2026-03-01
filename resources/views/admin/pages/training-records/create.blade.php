@extends('admin.layouts.master')

@section('page-title')
    إضافة سجل تدريب جديد
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
            <div class="page-header d-flex justify-content-between align-items-center my-4">
                <h5 class="page-title mb-0">إضافة سجل تدريب جديد</h5>
            </div>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.training-records.store') }}">
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <select class="form-select @error('training_id') is-invalid @enderror" 
                                            name="training_id" id="training_id" required>
                                        <option value="">اختر الدورة التدريبية</option>
                                        @foreach ($trainings as $training)
                                            <option value="{{ $training->id }}" {{ old('training_id') == $training->id ? 'selected' : '' }}>
                                                {{ $training->title_ar ?? $training->title }} ({{ $training->code }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <label>الدورة التدريبية <span class="text-danger">*</span></label>
                                    @error('training_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <select class="form-select @error('employee_id') is-invalid @enderror" 
                                            name="employee_id" id="employee_id" required>
                                        <option value="">اختر الموظف</option>
                                        @foreach ($employees as $employee)
                                            <option value="{{ $employee->id }}" {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                                                {{ $employee->full_name ?? $employee->first_name . ' ' . $employee->last_name }} 
                                                ({{ $employee->employee_code ?? '' }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <label>الموظف <span class="text-danger">*</span></label>
                                    @error('employee_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-floating">
                                    <select class="form-select @error('status') is-invalid @enderror" 
                                            name="status" id="status" required>
                                        <option value="registered" {{ old('status', 'registered') == 'registered' ? 'selected' : '' }}>مسجل</option>
                                        <option value="attending" {{ old('status') == 'attending' ? 'selected' : '' }}>يحضر</option>
                                        <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>مكتمل</option>
                                        <option value="failed" {{ old('status') == 'failed' ? 'selected' : '' }}>فاشل</option>
                                        <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>ملغي</option>
                                    </select>
                                    <label>الحالة <span class="text-danger">*</span></label>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="date" class="form-control" 
                                           name="registration_date" id="registration_date" placeholder="تاريخ التسجيل" 
                                           value="{{ old('registration_date', date('Y-m-d')) }}">
                                    <label>تاريخ التسجيل</label>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="date" class="form-control" 
                                           name="completion_date" id="completion_date" placeholder="تاريخ الإتمام" 
                                           value="{{ old('completion_date') }}">
                                    <label>تاريخ الإتمام</label>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="number" class="form-control" 
                                           name="score" id="score" placeholder="النتيجة/الدرجة" 
                                           value="{{ old('score') }}" min="0" max="100" step="0.01">
                                    <label>النتيجة/الدرجة (0-100)</label>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-check mt-4">
                                    <input class="form-check-input" type="checkbox" name="certificate_issued" id="certificate_issued" 
                                           value="1" {{ old('certificate_issued') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="certificate_issued">
                                        تم إصدار شهادة
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="date" class="form-control" 
                                           name="certificate_date" id="certificate_date" placeholder="تاريخ إصدار الشهادة" 
                                           value="{{ old('certificate_date') }}">
                                    <label>تاريخ إصدار الشهادة</label>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-floating">
                                    <textarea class="form-control" name="feedback" placeholder="ملاحظات الموظف" style="height: 100px">{{ old('feedback') }}</textarea>
                                    <label>ملاحظات الموظف</label>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-floating">
                                    <textarea class="form-control" name="evaluation" placeholder="تقييم المدرب" style="height: 100px">{{ old('evaluation') }}</textarea>
                                    <label>تقييم المدرب</label>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-floating">
                                    <textarea class="form-control" name="notes" placeholder="ملاحظات إضافية" style="height: 100px">{{ old('notes') }}</textarea>
                                    <label>ملاحظات إضافية</label>
                                </div>
                            </div>
                        </div>

                        <div class="text-end mt-4">
                            <a href="{{ route('admin.training-records.index') }}" class="btn btn-secondary px-4 me-2">إلغاء</a>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save me-2"></i>حفظ السجل
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop


