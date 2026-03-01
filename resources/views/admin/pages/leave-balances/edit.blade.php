@extends('admin.layouts.master')

@section('page-title')
    تعديل رصيد إجازة
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
                <h5 class="page-title mb-0">تعديل رصيد إجازة</h5>
            </div>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.leave-balances.update', $leaveBalance->id) }}" id="leaveBalanceForm">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <select class="form-select @error('employee_id') is-invalid @enderror" 
                                            name="employee_id" id="employee_id" required>
                                        @foreach ($employees as $employee)
                                            <option value="{{ $employee->id }}" 
                                                {{ old('employee_id', $leaveBalance->employee_id) == $employee->id ? 'selected' : '' }}>
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
                                                {{ old('leave_type_id', $leaveBalance->leave_type_id) == $type->id ? 'selected' : '' }}>
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
                                    <input type="number" class="form-control @error('year') is-invalid @enderror" 
                                           name="year" id="year" placeholder="السنة" 
                                           value="{{ old('year', $leaveBalance->year) }}" required min="2020" max="2100">
                                    <label>السنة <span class="text-danger">*</span></label>
                                    @error('year')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="number" class="form-control @error('total_days') is-invalid @enderror" 
                                           name="total_days" id="total_days" placeholder="إجمالي الأيام" 
                                           value="{{ old('total_days', $leaveBalance->total_days) }}" required min="0">
                                    <label>إجمالي الأيام <span class="text-danger">*</span></label>
                                    @error('total_days')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="number" class="form-control @error('used_days') is-invalid @enderror" 
                                           name="used_days" id="used_days" placeholder="الأيام المستخدمة" 
                                           value="{{ old('used_days', $leaveBalance->used_days) }}" min="0">
                                    <label>الأيام المستخدمة</label>
                                    @error('used_days')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="number" class="form-control @error('carried_forward') is-invalid @enderror" 
                                           name="carried_forward" id="carried_forward" placeholder="الأيام المحمولة" 
                                           value="{{ old('carried_forward', $leaveBalance->carried_forward) }}" min="0">
                                    <label>الأيام المحمولة من العام السابق</label>
                                    @error('carried_forward')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-floating">
                                    <input type="number" class="form-control" 
                                           id="remaining_days" placeholder="الأيام المتبقية" 
                                           value="{{ $leaveBalance->remaining_days }}" readonly>
                                    <label>الأيام المتبقية</label>
                                    <small class="form-text text-muted">يتم الحساب تلقائياً</small>
                                </div>
                            </div>
                        </div>

                        <div class="text-end mt-4">
                            <a href="{{ route('admin.leave-balances.index') }}" class="btn btn-secondary px-4 me-2">إلغاء</a>
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
<script>
    // حساب الأيام المتبقية تلقائياً
    function calculateRemaining() {
        const totalDays = parseFloat(document.getElementById('total_days').value) || 0;
        const usedDays = parseFloat(document.getElementById('used_days').value) || 0;
        const carriedForward = parseFloat(document.getElementById('carried_forward').value) || 0;
        
        const remaining = totalDays + carriedForward - usedDays;
        document.getElementById('remaining_days').value = remaining;
    }

    document.getElementById('total_days').addEventListener('input', calculateRemaining);
    document.getElementById('used_days').addEventListener('input', calculateRemaining);
    document.getElementById('carried_forward').addEventListener('input', calculateRemaining);

    calculateRemaining();
</script>
@stop


