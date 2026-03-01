@extends('admin.layouts.master')

@section('page-title')
    إضافة طلب مصروف جديد
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">إضافة طلب مصروف جديد</h5>
                </div>
                <div>
                    <a href="{{ route('admin.expense-requests.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-right me-2"></i>العودة للقائمة
                    </a>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.expense-requests.store') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">الموظف <span class="text-danger">*</span></label>
                                <select name="employee_id" class="form-select @error('employee_id') is-invalid @enderror" required>
                                    <option value="">اختر الموظف</option>
                                    @foreach ($employees as $emp)
                                        <option value="{{ $emp->id }}" {{ old('employee_id') == $emp->id ? 'selected' : '' }}>
                                            {{ $emp->full_name }} ({{ $emp->employee_code }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('employee_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">تصنيف المصروف <span class="text-danger">*</span></label>
                                <select name="expense_category_id" class="form-select @error('expense_category_id') is-invalid @enderror" required id="expense_category_id">
                                    <option value="">اختر التصنيف</option>
                                    @foreach ($categories as $cat)
                                        <option value="{{ $cat->id }}" {{ old('expense_category_id') == $cat->id ? 'selected' : '' }}
                                                data-max-amount="{{ $cat->max_amount }}"
                                                data-requires-receipt="{{ $cat->requires_receipt ? '1' : '0' }}">
                                            {{ $cat->name_ar ?? $cat->name }}
                                            @if ($cat->max_amount)
                                                (حد أقصى: {{ number_format($cat->max_amount, 2) }} ر.س)
                                            @endif
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
                                       value="{{ old('amount') }}" step="0.01" min="0.01" required id="amount">
                                @error('amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted" id="maxAmountHint"></small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">العملة</label>
                                <select name="currency_id" class="form-select">
                                    <option value="">اختر العملة</option>
                                    @foreach ($currencies as $currency)
                                        <option value="{{ $currency->id }}" {{ old('currency_id') == $currency->id ? 'selected' : '' }}>
                                            {{ $currency->name_ar ?? $currency->name }} ({{ $currency->code }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">تاريخ المصروف <span class="text-danger">*</span></label>
                                <input type="date" name="expense_date" class="form-control @error('expense_date') is-invalid @enderror" 
                                       value="{{ old('expense_date', date('Y-m-d')) }}" required>
                                @error('expense_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">طريقة الدفع</label>
                                <select name="payment_method" class="form-select">
                                    <option value="">اختر طريقة الدفع</option>
                                    <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>نقد</option>
                                    <option value="card" {{ old('payment_method') == 'card' ? 'selected' : '' }}>بطاقة</option>
                                    <option value="transfer" {{ old('payment_method') == 'transfer' ? 'selected' : '' }}>تحويل بنكي</option>
                                    <option value="check" {{ old('payment_method') == 'check' ? 'selected' : '' }}>شيك</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">اسم المورد/المؤسسة</label>
                                <input type="text" name="vendor_name" class="form-control" value="{{ old('vendor_name') }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">كود المشروع</label>
                                <input type="text" name="project_code" class="form-control" value="{{ old('project_code') }}">
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">الوصف <span class="text-danger">*</span></label>
                                <textarea name="description" class="form-control @error('description') is-invalid @enderror" 
                                          rows="3" required>{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">الوصف (عربي)</label>
                                <textarea name="description_ar" class="form-control" rows="3">{{ old('description_ar') }}</textarea>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">الإيصال/الفواتير</label>
                                <input type="file" name="receipt" class="form-control @error('receipt') is-invalid @enderror" 
                                       accept=".pdf,.jpg,.jpeg,.png" id="receipt">
                                @error('receipt')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">صيغ مدعومة: PDF, JPG, PNG (حد أقصى 10MB)</small>
                                <div id="receiptPreview" class="mt-2"></div>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">ملاحظات</label>
                                <textarea name="notes" class="form-control" rows="3">{{ old('notes') }}</textarea>
                            </div>
                        </div>

                        <div class="text-end mt-4">
                            <a href="{{ route('admin.expense-requests.index') }}" class="btn btn-secondary px-4 me-2">إلغاء</a>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save me-2"></i>حفظ
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // التحقق من الحد الأقصى عند تغيير التصنيف
        document.getElementById('expense_category_id').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const maxAmount = selectedOption.getAttribute('data-max-amount');
            const requiresReceipt = selectedOption.getAttribute('data-requires-receipt');
            const amountInput = document.getElementById('amount');
            const maxAmountHint = document.getElementById('maxAmountHint');
            const receiptInput = document.getElementById('receipt');

            if (maxAmount && maxAmount !== 'null') {
                maxAmountHint.textContent = 'الحد الأقصى: ' + parseFloat(maxAmount).toFixed(2) + ' ر.س';
                maxAmountHint.className = 'text-warning';
                amountInput.setAttribute('max', maxAmount);
            } else {
                maxAmountHint.textContent = '';
                amountInput.removeAttribute('max');
            }

            if (requiresReceipt === '1') {
                receiptInput.setAttribute('required', 'required');
                receiptInput.parentElement.querySelector('small').innerHTML = 'صيغ مدعومة: PDF, JPG, PNG (حد أقصى 10MB) <span class="text-danger">*</span>';
            } else {
                receiptInput.removeAttribute('required');
                receiptInput.parentElement.querySelector('small').textContent = 'صيغ مدعومة: PDF, JPG, PNG (حد أقصى 10MB)';
            }
        });

        // معاينة الإيصال
        document.getElementById('receipt').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const preview = document.getElementById('receiptPreview');
            
            if (file) {
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        preview.innerHTML = '<img src="' + e.target.result + '" class="img-thumbnail" style="max-width: 200px; max-height: 200px;">';
                    };
                    reader.readAsDataURL(file);
                } else {
                    preview.innerHTML = '<span class="badge bg-info">' + file.name + '</span>';
                }
            } else {
                preview.innerHTML = '';
            }
        });
    </script>
@stop

