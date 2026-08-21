@extends('admin.layouts.master')

@section('page-title')
    إنشاء كشف راتب جديد
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid admin-page-shell">
            @include('admin.pages.users.partials.alerts')

            <div class="admin-page-banner">
                <div class="admin-page-banner-main">
                    <span class="admin-page-banner-icon"><i class="ri-file-add-line"></i></span>
                    <div class="admin-page-banner-text">
                        <h1>إنشاء كشف راتب جديد</h1>
                        <p>اختر الموظف والشهر والسنة لإنشاء كشف الراتب</p>
                    </div>
                </div>
                <div class="admin-page-banner-actions">
                    <a href="{{ route('admin.payrolls.index') }}" class="admin-btn admin-btn-light">
                        <i class="ri-arrow-right-line"></i>
                        العودة للقائمة
                    </a>
                </div>
            </div>

            <div class="admin-page-card">
                <form class="admin-form" method="POST" action="{{ route('admin.payrolls.store') }}">
                    @csrf

                    <div class="admin-form-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="admin-form-label">الموظف <span class="text-danger">*</span></label>
                                <select class="form-select @error('employee_id') is-invalid @enderror" name="employee_id" required>
                                    <option value="">اختر الموظف</option>
                                    @foreach ($employees as $employee)
                                        <option value="{{ $employee->id }}" {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                                            {{ $employee->full_name }} ({{ $employee->employee_code ?? '' }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('employee_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3">
                                <label class="admin-form-label">الشهر <span class="text-danger">*</span></label>
                                <select class="form-select @error('payroll_month') is-invalid @enderror" name="payroll_month" required>
                                    @for ($i = 1; $i <= 12; $i++)
                                        <option value="{{ $i }}" {{ old('payroll_month', date('n')) == $i ? 'selected' : '' }}>
                                            {{ ['', 'يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'][$i] }}
                                        </option>
                                    @endfor
                                </select>
                                @error('payroll_month')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3">
                                <label class="admin-form-label">السنة <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('payroll_year') is-invalid @enderror" 
                                       name="payroll_year" value="{{ old('payroll_year', date('Y')) }}" 
                                       min="2020" max="2100" required>
                                @error('payroll_year')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="admin-form-label">العملة</label>
                                <select class="form-select" name="currency_id">
                                    <option value="">اختر العملة</option>
                                    @foreach ($currencies as $currency)
                                        <option value="{{ $currency->id }}" {{ old('currency_id') == $currency->id ? 'selected' : '' }}>
                                            {{ $currency->name }} ({{ $currency->code }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                    </div>

                    <div class="admin-form-footer">
                        <a href="{{ route('admin.payrolls.index') }}" class="admin-btn admin-btn-secondary">إلغاء</a>
                        <button type="submit" class="admin-btn admin-btn-primary">
                            <i class="ri-save-line"></i>
                            إنشاء كشف الراتب
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

