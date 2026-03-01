@extends('admin.layouts.master')

@section('page-title')
    تعديل سجل حضور
@stop

@section('css')
    <style>
        .form-floating label {
            right: auto;
            left: 0.75rem;
        }
    </style>
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
                <h5 class="page-title mb-0">تعديل سجل حضور</h5>
            </div>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.attendances.update', $attendance->id) }}" id="attendanceForm">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <select class="form-select @error('employee_id') is-invalid @enderror" 
                                            name="employee_id" id="employee_id" required>
                                        @foreach ($employees as $employee)
                                            <option value="{{ $employee->id }}" 
                                                {{ old('employee_id', $attendance->employee_id) == $employee->id ? 'selected' : '' }}>
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

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="date" class="form-control @error('attendance_date') is-invalid @enderror" 
                                           name="attendance_date" id="attendance_date" placeholder="تاريخ الحضور" 
                                           value="{{ old('attendance_date', $attendance->attendance_date->format('Y-m-d')) }}" required>
                                    <label>تاريخ الحضور <span class="text-danger">*</span></label>
                                    @error('attendance_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-floating">
                                    <input type="time" class="form-control @error('check_in') is-invalid @enderror" 
                                           name="check_in" id="check_in" placeholder="وقت الدخول" 
                                           value="{{ old('check_in', is_string($attendance->check_in) ? $attendance->check_in : ($attendance->check_in ? $attendance->check_in->format('H:i') : '')) }}">
                                    <label>وقت الدخول</label>
                                    @error('check_in')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-floating">
                                    <input type="time" class="form-control @error('check_out') is-invalid @enderror" 
                                           name="check_out" id="check_out" placeholder="وقت الخروج" 
                                           value="{{ old('check_out', is_string($attendance->check_out) ? $attendance->check_out : ($attendance->check_out ? $attendance->check_out->format('H:i') : '')) }}">
                                    <label>وقت الخروج</label>
                                    @error('check_out')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-floating">
                                    <input type="time" class="form-control @error('expected_check_in') is-invalid @enderror" 
                                           name="expected_check_in" id="expected_check_in" placeholder="وقت الدخول المتوقع" 
                                           value="{{ old('expected_check_in', $attendance->expected_check_in ?? '09:00') }}">
                                    <label>وقت الدخول المتوقع</label>
                                    @error('expected_check_in')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-floating">
                                    <input type="time" class="form-control @error('expected_check_out') is-invalid @enderror" 
                                           name="expected_check_out" id="expected_check_out" placeholder="وقت الخروج المتوقع" 
                                           value="{{ old('expected_check_out', $attendance->expected_check_out ?? '17:00') }}">
                                    <label>وقت الخروج المتوقع</label>
                                    @error('expected_check_out')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <select class="form-select @error('status') is-invalid @enderror" 
                                            name="status" id="status" required>
                                        <option value="present" {{ old('status', $attendance->status) == 'present' ? 'selected' : '' }}>حاضر</option>
                                        <option value="absent" {{ old('status', $attendance->status) == 'absent' ? 'selected' : '' }}>غائب</option>
                                        <option value="late" {{ old('status', $attendance->status) == 'late' ? 'selected' : '' }}>متأخر</option>
                                        <option value="half_day" {{ old('status', $attendance->status) == 'half_day' ? 'selected' : '' }}>نصف يوم</option>
                                        <option value="on_leave" {{ old('status', $attendance->status) == 'on_leave' ? 'selected' : '' }}>في إجازة</option>
                                        <option value="holiday" {{ old('status', $attendance->status) == 'holiday' ? 'selected' : '' }}>عطلة</option>
                                    </select>
                                    <label>حالة الحضور <span class="text-danger">*</span></label>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-floating">
                                    <textarea class="form-control @error('notes') is-invalid @enderror" 
                                              name="notes" placeholder="ملاحظات" style="height: 100px">{{ old('notes', $attendance->notes) }}</textarea>
                                    <label>ملاحظات</label>
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="text-end mt-4">
                            <a href="{{ route('admin.attendances.index') }}" class="btn btn-secondary px-4 me-2">إلغاء</a>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save me-2"></i>حفظ التعديلات
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

@section('script')
@stop

