@extends('admin.layouts.master')

@section('page-title')
    تعديل كشف الراتب
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid admin-page-shell">
            @include('admin.pages.users.partials.alerts')

            <div class="admin-page-banner">
                <div class="admin-page-banner-main">
                    <span class="admin-page-banner-icon"><i class="ri-edit-box-line"></i></span>
                    <div class="admin-page-banner-text">
                        <h1>تعديل كشف الراتب</h1>
                        <p>{{ $payroll->payroll_code }} — {{ $payroll->employee->full_name ?? '' }}</p>
                    </div>
                </div>
                <div class="admin-page-banner-actions">
                    <a href="{{ route('admin.payrolls.show', $payroll->id) }}" class="admin-btn admin-btn-light">
                        <i class="ri-arrow-right-line"></i>
                        عودة للكشف
                    </a>
                </div>
            </div>

            <div class="admin-page-card">
                <form class="admin-form" method="POST" action="{{ route('admin.payrolls.update', $payroll->id) }}">
                    @csrf
                    @method('PUT')

                    <div class="admin-form-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="admin-form-label">الراتب الأساسي</label>
                                <input type="number" step="0.01" class="form-control @error('base_salary') is-invalid @enderror" 
                                       name="base_salary" value="{{ old('base_salary', $payroll->base_salary) }}" min="0">
                                @error('base_salary')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="admin-form-label">العملة</label>
                                <select class="form-select" name="currency_id">
                                    <option value="">اختر العملة</option>
                                    @foreach ($currencies as $currency)
                                        <option value="{{ $currency->id }}" {{ $payroll->currency_id == $currency->id ? 'selected' : '' }}>
                                            {{ $currency->name }} ({{ $currency->code }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-12">
                                <label class="admin-form-label">ملاحظات</label>
                                <textarea class="form-control" name="notes" rows="3">{{ old('notes', $payroll->notes) }}</textarea>
                            </div>
                        </div>

                    </div>

                    <div class="admin-form-footer">
                        <a href="{{ route('admin.payrolls.show', $payroll->id) }}" class="admin-btn admin-btn-secondary">إلغاء</a>
                        <button type="submit" class="admin-btn admin-btn-primary">
                            <i class="ri-save-line"></i>
                            حفظ التغييرات
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

