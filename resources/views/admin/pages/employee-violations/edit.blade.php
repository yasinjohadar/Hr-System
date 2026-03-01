@extends('admin.layouts.master')

@section('page-title')
    تعديل المخالفة
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">تعديل المخالفة</h5>
                </div>
                <div>
                    <a href="{{ route('admin.employee-violations.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-right me-2"></i>العودة للقائمة
                    </a>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.employee-violations.update', $violation->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">الموظف <span class="text-danger">*</span></label>
                                <select name="employee_id" class="form-select @error('employee_id') is-invalid @enderror" required>
                                    <option value="">اختر الموظف</option>
                                    @foreach ($employees as $emp)
                                        <option value="{{ $emp->id }}" {{ old('employee_id', $violation->employee_id) == $emp->id ? 'selected' : '' }}>
                                            {{ $emp->full_name }} - {{ $emp->employee_code }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('employee_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">نوع المخالفة <span class="text-danger">*</span></label>
                                <select name="violation_type_id" class="form-select @error('violation_type_id') is-invalid @enderror" required>
                                    <option value="">اختر نوع المخالفة</option>
                                    @foreach ($violationTypes as $type)
                                        <option value="{{ $type->id }}" {{ old('violation_type_id', $violation->violation_type_id) == $type->id ? 'selected' : '' }}>
                                            {{ $type->name_ar ?? $type->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('violation_type_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">تاريخ المخالفة <span class="text-danger">*</span></label>
                                <input type="date" name="violation_date" class="form-control @error('violation_date') is-invalid @enderror" 
                                       value="{{ old('violation_date', $violation->violation_date->format('Y-m-d')) }}" required>
                                @error('violation_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">الخطورة <span class="text-danger">*</span></label>
                                <select name="severity" class="form-select @error('severity') is-invalid @enderror" required>
                                    <option value="low" {{ old('severity', $violation->severity) == 'low' ? 'selected' : '' }}>منخفض</option>
                                    <option value="medium" {{ old('severity', $violation->severity) == 'medium' ? 'selected' : '' }}>متوسط</option>
                                    <option value="high" {{ old('severity', $violation->severity) == 'high' ? 'selected' : '' }}>عالي</option>
                                    <option value="critical" {{ old('severity', $violation->severity) == 'critical' ? 'selected' : '' }}>حرج</option>
                                </select>
                                @error('severity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">الإجراء التأديبي</label>
                                <select name="disciplinary_action_id" class="form-select">
                                    <option value="">اختر الإجراء (اختياري)</option>
                                    @foreach ($disciplinaryActions as $action)
                                        <option value="{{ $action->id }}" {{ old('disciplinary_action_id', $violation->disciplinary_action_id) == $action->id ? 'selected' : '' }}>
                                            {{ $action->name_ar ?? $action->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">ربط بالحضور</label>
                                <select name="attendance_id" class="form-select">
                                    <option value="">لا يوجد</option>
                                    @foreach ($attendances as $attendance)
                                        <option value="{{ $attendance->id }}" {{ old('attendance_id', $violation->attendance_id) == $attendance->id ? 'selected' : '' }}>
                                            {{ $attendance->employee->full_name }} - {{ $attendance->attendance_date->format('Y-m-d') }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">ربط بالإجازة</label>
                                <select name="leave_request_id" class="form-select">
                                    <option value="">لا يوجد</option>
                                    @foreach ($leaveRequests as $leave)
                                        <option value="{{ $leave->id }}" {{ old('leave_request_id', $violation->leave_request_id) == $leave->id ? 'selected' : '' }}>
                                            {{ $leave->employee->full_name }} - {{ $leave->start_date->format('Y-m-d') }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="form-label">وصف المخالفة <span class="text-danger">*</span></label>
                                <textarea name="description" class="form-control @error('description') is-invalid @enderror" 
                                          rows="4" required>{{ old('description', $violation->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label">وصف المخالفة (عربي)</label>
                                <textarea name="description_ar" class="form-control" rows="4">{{ old('description_ar', $violation->description_ar) }}</textarea>
                            </div>

                            <div class="col-12">
                                <label class="form-label">الشهود</label>
                                <textarea name="witnesses" class="form-control" rows="2">{{ old('witnesses', $violation->witnesses) }}</textarea>
                            </div>

                            <div class="col-12">
                                <label class="form-label">ملاحظات</label>
                                <textarea name="notes" class="form-control" rows="3">{{ old('notes', $violation->notes) }}</textarea>
                            </div>
                        </div>

                        <div class="text-end mt-4">
                            <a href="{{ route('admin.employee-violations.index') }}" class="btn btn-secondary px-4 me-2">إلغاء</a>
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

