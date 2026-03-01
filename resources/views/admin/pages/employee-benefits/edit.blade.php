@extends('admin.layouts.master')

@section('page-title')
    تعديل ميزة موظف
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
                <h5 class="page-title mb-0">تعديل ميزة موظف</h5>
            </div>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.employee-benefits.update', $employeeBenefit->id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="number" step="0.01" class="form-control" 
                                           name="value" placeholder="القيمة" value="{{ old('value', $employeeBenefit->value) }}">
                                    <label>القيمة</label>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-floating">
                                    <select class="form-select" name="currency_id" id="currency_id">
                                        <option value="">اختر العملة</option>
                                        @foreach ($currencies as $currency)
                                            <option value="{{ $currency->id }}" {{ old('currency_id', $employeeBenefit->currency_id) == $currency->id ? 'selected' : '' }}>
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
                                        <option value="active" {{ old('status', $employeeBenefit->status) == 'active' ? 'selected' : '' }}>نشط</option>
                                        <option value="suspended" {{ old('status', $employeeBenefit->status) == 'suspended' ? 'selected' : '' }}>معلق</option>
                                        <option value="expired" {{ old('status', $employeeBenefit->status) == 'expired' ? 'selected' : '' }}>منتهي</option>
                                        <option value="cancelled" {{ old('status', $employeeBenefit->status) == 'cancelled' ? 'selected' : '' }}>ملغي</option>
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
                                           value="{{ old('start_date', $employeeBenefit->start_date->format('Y-m-d')) }}" required>
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
                                           value="{{ old('end_date', $employeeBenefit->end_date ? $employeeBenefit->end_date->format('Y-m-d') : '') }}">
                                    <label>تاريخ الانتهاء</label>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="file" class="form-control" 
                                           name="document_path" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                    <label>المستند المرفق</label>
                                    @if ($employeeBenefit->document_path)
                                        <small class="text-muted">الملف الحالي: <a href="{{ Storage::url($employeeBenefit->document_path) }}" target="_blank">عرض</a></small>
                                    @endif
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-floating">
                                    <textarea class="form-control" name="notes" placeholder="ملاحظات" style="height: 100px">{{ old('notes', $employeeBenefit->notes) }}</textarea>
                                    <label>ملاحظات</label>
                                </div>
                            </div>

                            @if ($employeeBenefit->benefitType->requires_approval && !$employeeBenefit->approved_by)
                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="approve" id="approve" value="1">
                                    <label class="form-check-label" for="approve">
                                        الموافقة على هذه الميزة
                                    </label>
                                </div>
                            </div>
                            @endif
                        </div>

                        <div class="text-end mt-4">
                            <a href="{{ route('admin.employee-benefits.index') }}" class="btn btn-secondary px-4 me-2">إلغاء</a>
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


