@extends('admin.layouts.master')

@section('page-title')
    تعديل طلب إجازة
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
                <h5 class="page-title mb-0">تعديل طلب إجازة</h5>
            </div>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.leave-requests.update', $leaveRequest->id) }}" id="leaveRequestForm">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <select class="form-select @error('employee_id') is-invalid @enderror" 
                                            name="employee_id" id="employee_id" required>
                                        @foreach ($employees as $employee)
                                            <option value="{{ $employee->id }}" 
                                                {{ old('employee_id', $leaveRequest->employee_id) == $employee->id ? 'selected' : '' }}>
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
                                    <select class="form-select @error('leave_type_id') is-invalid @enderror" 
                                            name="leave_type_id" id="leave_type_id" required>
                                        @foreach ($leaveTypes as $type)
                                            <option value="{{ $type->id }}" 
                                                {{ old('leave_type_id', $leaveRequest->leave_type_id) == $type->id ? 'selected' : '' }}>
                                                {{ $type->name_ar ?? $type->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <label>نوع الإجازة <span class="text-danger">*</span></label>
                                    @error('leave_type_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="date" class="form-control @error('start_date') is-invalid @enderror" 
                                           name="start_date" id="start_date" placeholder="تاريخ البداية" 
                                           value="{{ old('start_date', $leaveRequest->start_date->format('Y-m-d')) }}" required>
                                    <label>تاريخ البداية <span class="text-danger">*</span></label>
                                    @error('start_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="date" class="form-control @error('end_date') is-invalid @enderror" 
                                           name="end_date" id="end_date" placeholder="تاريخ النهاية" 
                                           value="{{ old('end_date', $leaveRequest->end_date->format('Y-m-d')) }}" required>
                                    <label>تاريخ النهاية <span class="text-danger">*</span></label>
                                    @error('end_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-floating">
                                    <textarea class="form-control @error('reason') is-invalid @enderror" 
                                              name="reason" placeholder="سبب الإجازة" style="height: 100px">{{ old('reason', $leaveRequest->reason) }}</textarea>
                                    <label>سبب الإجازة</label>
                                    @error('reason')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-floating">
                                    <textarea class="form-control @error('notes') is-invalid @enderror" 
                                              name="notes" placeholder="ملاحظات" style="height: 80px">{{ old('notes', $leaveRequest->notes) }}</textarea>
                                    <label>ملاحظات</label>
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="text-end mt-4">
                            <a href="{{ route('admin.leave-requests.index') }}" class="btn btn-secondary px-4 me-2">إلغاء</a>
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


