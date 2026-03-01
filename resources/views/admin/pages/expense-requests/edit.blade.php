@extends('admin.layouts.master')

@section('page-title')
    تعديل طلب المصروف
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">تعديل طلب المصروف</h5>
                </div>
                <div>
                    <a href="{{ route('admin.expense-requests.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-right me-2"></i>العودة للقائمة
                    </a>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.expense-requests.update', $expenseRequest->id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">الموظف</label>
                                <input type="text" class="form-control" value="{{ $expenseRequest->employee->full_name }}" disabled>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">تصنيف المصروف <span class="text-danger">*</span></label>
                                <select name="expense_category_id" class="form-select @error('expense_category_id') is-invalid @enderror" required>
                                    <option value="">اختر التصنيف</option>
                                    @foreach ($categories as $cat)
                                        <option value="{{ $cat->id }}" {{ old('expense_category_id', $expenseRequest->expense_category_id) == $cat->id ? 'selected' : '' }}>
                                            {{ $cat->name_ar ?? $cat->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('expense_category_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">المبلغ <span class="text-danger">*</span></label>
                                <input type="number" name="amount" class="form-control @error('amount') is-invalid @enderror" 
                                       value="{{ old('amount', $expenseRequest->amount) }}" step="0.01" min="0.01" required>
                                @error('amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">العملة</label>
                                <select name="currency_id" class="form-select">
                                    <option value="">اختر العملة</option>
                                    @foreach ($currencies as $currency)
                                        <option value="{{ $currency->id }}" {{ old('currency_id', $expenseRequest->currency_id) == $currency->id ? 'selected' : '' }}>
                                            {{ $currency->name_ar ?? $currency->name }} ({{ $currency->code }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">تاريخ المصروف <span class="text-danger">*</span></label>
                                <input type="date" name="expense_date" class="form-control @error('expense_date') is-invalid @enderror" 
                                       value="{{ old('expense_date', $expenseRequest->expense_date->format('Y-m-d')) }}" required>
                                @error('expense_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">طريقة الدفع</label>
                                <select name="payment_method" class="form-select">
                                    <option value="">اختر طريقة الدفع</option>
                                    <option value="cash" {{ old('payment_method', $expenseRequest->payment_method) == 'cash' ? 'selected' : '' }}>نقد</option>
                                    <option value="card" {{ old('payment_method', $expenseRequest->payment_method) == 'card' ? 'selected' : '' }}>بطاقة</option>
                                    <option value="transfer" {{ old('payment_method', $expenseRequest->payment_method) == 'transfer' ? 'selected' : '' }}>تحويل بنكي</option>
                                    <option value="check" {{ old('payment_method', $expenseRequest->payment_method) == 'check' ? 'selected' : '' }}>شيك</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">اسم المورد/المؤسسة</label>
                                <input type="text" name="vendor_name" class="form-control" value="{{ old('vendor_name', $expenseRequest->vendor_name) }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">كود المشروع</label>
                                <input type="text" name="project_code" class="form-control" value="{{ old('project_code', $expenseRequest->project_code) }}">
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">الوصف <span class="text-danger">*</span></label>
                                <textarea name="description" class="form-control @error('description') is-invalid @enderror" 
                                          rows="3" required>{{ old('description', $expenseRequest->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">الوصف (عربي)</label>
                                <textarea name="description_ar" class="form-control" rows="3">{{ old('description_ar', $expenseRequest->description_ar) }}</textarea>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">الإيصال/الفواتير</label>
                                @if ($expenseRequest->receipt_path)
                                    <div class="mb-2">
                                        <a href="{{ asset('storage/' . $expenseRequest->receipt_path) }}" target="_blank" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye me-1"></i>عرض الإيصال الحالي
                                        </a>
                                    </div>
                                @endif
                                <input type="file" name="receipt" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                                <small class="text-muted">اتركه فارغاً للاحتفاظ بالإيصال الحالي</small>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">ملاحظات</label>
                                <textarea name="notes" class="form-control" rows="3">{{ old('notes', $expenseRequest->notes) }}</textarea>
                            </div>
                        </div>

                        <div class="text-end mt-4">
                            <a href="{{ route('admin.expense-requests.index') }}" class="btn btn-secondary px-4 me-2">إلغاء</a>
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

