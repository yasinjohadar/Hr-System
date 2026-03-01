@extends('admin.layouts.master')

@section('page-title')
    إضافة ميزة موظف جديدة
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
                <h5 class="page-title mb-0">إضافة ميزة موظف جديدة</h5>
            </div>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.employee-benefits.store') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <select class="form-select @error('employee_id') is-invalid @enderror" 
                                            name="employee_id" id="employee_id" required>
                                        <option value="">اختر الموظف</option>
                                        @foreach ($employees as $employee)
                                            <option value="{{ $employee->id }}" {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                                                {{ $employee->full_name }} ({{ $employee->employee_code }})
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
                                    <select class="form-select @error('benefit_type_id') is-invalid @enderror" 
                                            name="benefit_type_id" id="benefit_type_id" required>
                                        <option value="">اختر نوع الميزة</option>
                                        @foreach ($benefitTypes as $benefitType)
                                            <option value="{{ $benefitType->id }}" {{ old('benefit_type_id') == $benefitType->id ? 'selected' : '' }}>
                                                {{ $benefitType->name_ar ?? $benefitType->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <label>نوع الميزة <span class="text-danger">*</span></label>
                                    @error('benefit_type_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="number" step="0.01" class="form-control" 
                                           name="value" placeholder="القيمة" value="{{ old('value') }}">
                                    <label>القيمة</label>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-floating">
                                    <select class="form-select" name="currency_id" id="currency_id">
                                        <option value="">اختر العملة</option>
                                        @foreach ($currencies as $currency)
                                            <option value="{{ $currency->id }}" {{ old('currency_id') == $currency->id ? 'selected' : '' }}>
                                                {{ $currency->name_ar ?? $currency->name }} ({{ $currency->symbol_ar ?? $currency->symbol }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <label>العملة</label>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-floating">
                                    <select class="form-select @error('status') is-invalid @enderror" 
                                            name="status" id="status" required>
                                        <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>نشط</option>
                                        <option value="suspended" {{ old('status') == 'suspended' ? 'selected' : '' }}>معلق</option>
                                        <option value="expired" {{ old('status') == 'expired' ? 'selected' : '' }}>منتهي</option>
                                        <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>ملغي</option>
                                    </select>
                                    <label>الحالة <span class="text-danger">*</span></label>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="date" class="form-control @error('start_date') is-invalid @enderror" 
                                           name="start_date" placeholder="تاريخ البدء" 
                                           value="{{ old('start_date', date('Y-m-d')) }}" required>
                                    <label>تاريخ البدء <span class="text-danger">*</span></label>
                                    @error('start_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="date" class="form-control" 
                                           name="end_date" placeholder="تاريخ الانتهاء" 
                                           value="{{ old('end_date') }}">
                                    <label>تاريخ الانتهاء (اتركه فارغاً للدائم)</label>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="file" class="form-control" 
                                           name="document_path" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                    <label>المستند المرفق</label>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-floating">
                                    <textarea class="form-control" name="notes" placeholder="ملاحظات" style="height: 100px">{{ old('notes') }}</textarea>
                                    <label>ملاحظات</label>
                                </div>
                            </div>
                        </div>

                        <div class="text-end mt-4">
                            <a href="{{ route('admin.employee-benefits.index') }}" class="btn btn-secondary px-4 me-2">إلغاء</a>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save me-2"></i>حفظ الميزة
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop


