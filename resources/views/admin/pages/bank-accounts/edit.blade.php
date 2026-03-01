@extends('admin.layouts.master')

@section('page-title')
    تعديل الحساب البنكي
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
                <h5 class="page-title mb-0">تعديل الحساب البنكي</h5>
                <a href="{{ route('admin.bank-accounts.index') }}" class="btn btn-secondary btn-sm">رجوع</a>
            </div>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.bank-accounts.update', $bankAccount->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">الموظف <span class="text-danger">*</span></label>
                                <select class="form-select @error('employee_id') is-invalid @enderror" name="employee_id" required>
                                    <option value="">اختر الموظف</option>
                                    @foreach ($employees as $employee)
                                        <option value="{{ $employee->id }}" {{ old('employee_id', $bankAccount->employee_id) == $employee->id ? 'selected' : '' }}>
                                            {{ $employee->full_name }} - {{ $employee->employee_number }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('employee_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">اسم البنك <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('bank_name') is-invalid @enderror" 
                                       name="bank_name" value="{{ old('bank_name', $bankAccount->bank_name) }}" required>
                                @error('bank_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">اسم البنك بالعربية</label>
                                <input type="text" class="form-control" name="bank_name_ar" value="{{ old('bank_name_ar', $bankAccount->bank_name_ar) }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">رقم الحساب <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('account_number') is-invalid @enderror" 
                                       name="account_number" value="{{ old('account_number', $bankAccount->account_number) }}" required>
                                @error('account_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">IBAN</label>
                                <input type="text" class="form-control" name="iban" value="{{ old('iban', $bankAccount->iban) }}" maxlength="34">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">SWIFT Code</label>
                                <input type="text" class="form-control" name="swift_code" value="{{ old('swift_code', $bankAccount->swift_code) }}" maxlength="11">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">اسم صاحب الحساب</label>
                                <input type="text" class="form-control" name="account_holder_name" value="{{ old('account_holder_name', $bankAccount->account_holder_name) }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">اسم الفرع</label>
                                <input type="text" class="form-control" name="branch_name" value="{{ old('branch_name', $bankAccount->branch_name) }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">عنوان الفرع</label>
                                <input type="text" class="form-control" name="branch_address" value="{{ old('branch_address', $bankAccount->branch_address) }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">نوع الحساب <span class="text-danger">*</span></label>
                                <select class="form-select @error('account_type') is-invalid @enderror" name="account_type" required>
                                    <option value="">اختر نوع الحساب</option>
                                    <option value="savings" {{ old('account_type', $bankAccount->account_type) == 'savings' ? 'selected' : '' }}>توفير</option>
                                    <option value="current" {{ old('account_type', $bankAccount->account_type) == 'current' ? 'selected' : '' }}>جاري</option>
                                    <option value="salary" {{ old('account_type', $bankAccount->account_type) == 'salary' ? 'selected' : '' }}>راتب</option>
                                </select>
                                @error('account_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">العملة <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('currency_code') is-invalid @enderror" 
                                       name="currency_code" value="{{ old('currency_code', $bankAccount->currency_code) }}" maxlength="3" required>
                                @error('currency_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <div class="form-check mt-4">
                                    <input class="form-check-input" type="checkbox" name="is_primary" id="is_primary" value="1" {{ old('is_primary', $bankAccount->is_primary) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_primary">
                                        حساب أساسي
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-check mt-4">
                                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $bankAccount->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        نشط
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">ملاحظات</label>
                                <textarea class="form-control" name="notes" rows="3">{{ old('notes', $bankAccount->notes) }}</textarea>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">حفظ</button>
                            <a href="{{ route('admin.bank-accounts.index') }}" class="btn btn-secondary">إلغاء</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

