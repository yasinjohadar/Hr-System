@extends('admin.layouts.master')

@section('page-title')
    إضافة مكافأة موظف جديدة
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">إضافة مكافأة موظف جديدة</h5>
                </div>
                <div>
                    <a href="{{ route('admin.employee-rewards.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-right me-2"></i>العودة للقائمة
                    </a>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.employee-rewards.store') }}">
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">الموظف <span class="text-danger">*</span></label>
                                <select class="form-select @error('employee_id') is-invalid @enderror" 
                                        name="employee_id" id="employee_id" required>
                                    <option value="">اختر الموظف</option>
                                    @foreach ($employees as $employee)
                                        <option value="{{ $employee->id }}" {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                                            {{ $employee->full_name }} ({{ $employee->employee_code }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('employee_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">نوع المكافأة <span class="text-danger">*</span></label>
                                <select class="form-select @error('reward_type_id') is-invalid @enderror" 
                                        name="reward_type_id" id="reward_type_id" required>
                                    <option value="">اختر نوع المكافأة</option>
                                    @foreach ($rewardTypes as $rewardType)
                                        <option value="{{ $rewardType->id }}" {{ old('reward_type_id') == $rewardType->id ? 'selected' : '' }}>
                                            {{ $rewardType->name_ar ?? $rewardType->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('reward_type_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">العنوان <span class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" 
                                       value="{{ old('title') }}" required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">تاريخ المكافأة <span class="text-danger">*</span></label>
                                <input type="date" name="reward_date" class="form-control @error('reward_date') is-invalid @enderror" 
                                       value="{{ old('reward_date', date('Y-m-d')) }}" required>
                                @error('reward_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">السبب <span class="text-danger">*</span></label>
                                <select class="form-select @error('reason') is-invalid @enderror" 
                                        name="reason" required>
                                    <option value="performance" {{ old('reason', 'performance') == 'performance' ? 'selected' : '' }}>أداء</option>
                                    <option value="achievement" {{ old('reason') == 'achievement' ? 'selected' : '' }}>إنجاز</option>
                                    <option value="milestone" {{ old('reason') == 'milestone' ? 'selected' : '' }}>معلم</option>
                                    <option value="recognition" {{ old('reason') == 'recognition' ? 'selected' : '' }}>اعتراف</option>
                                    <option value="other" {{ old('reason') == 'other' ? 'selected' : '' }}>أخرى</option>
                                </select>
                                @error('reason')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">القيمة النقدية</label>
                                <input type="number" step="0.01" name="monetary_value" class="form-control" 
                                       value="{{ old('monetary_value') }}" min="0">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">العملة</label>
                                <select class="form-select" name="currency_id">
                                    <option value="">اختر العملة</option>
                                    @foreach ($currencies as $currency)
                                        <option value="{{ $currency->id }}" {{ old('currency_id') == $currency->id ? 'selected' : '' }}>
                                            {{ $currency->name_ar ?? $currency->name }} ({{ $currency->symbol_ar ?? $currency->symbol }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">النقاط</label>
                                <input type="number" name="points" class="form-control" 
                                       value="{{ old('points') }}" min="0">
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">الوصف</label>
                                <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">ملاحظات</label>
                                <textarea name="notes" class="form-control" rows="3">{{ old('notes') }}</textarea>
                            </div>
                        </div>

                        <div class="text-end mt-4">
                            <a href="{{ route('admin.employee-rewards.index') }}" class="btn btn-secondary px-4 me-2">إلغاء</a>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save me-2"></i>حفظ المكافأة
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

