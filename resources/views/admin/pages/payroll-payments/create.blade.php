@extends('admin.layouts.master')

@section('page-title')
    إضافة سجل دفع جديد
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
                <h5 class="page-title mb-0">إضافة سجل دفع جديد</h5>
                <a href="{{ route('admin.payroll-payments.index') }}" class="btn btn-secondary btn-sm">رجوع</a>
            </div>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.payroll-payments.store') }}">
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">كشف الراتب <span class="text-danger">*</span></label>
                                <select class="form-select @error('payroll_id') is-invalid @enderror" name="payroll_id" required id="payroll_id">
                                    <option value="">اختر كشف الراتب</option>
                                    @foreach ($payrolls as $p)
                                        <option value="{{ $p->id }}" 
                                                {{ old('payroll_id', $payroll?->id) == $p->id ? 'selected' : '' }}
                                                data-net-salary="{{ $p->net_salary }}"
                                                data-employee="{{ $p->employee->full_name }}">
                                            {{ $p->payroll_code }} - {{ $p->employee->full_name }} ({{ number_format($p->net_salary, 2) }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('payroll_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                @if($payroll)
                                <small class="text-muted">الراتب الصافي: {{ number_format($payroll->net_salary, 2) }} {{ $payroll->currency->code ?? '' }}</small>
                                @endif
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">المبلغ <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" class="form-control @error('amount') is-invalid @enderror" 
                                       name="amount" value="{{ old('amount', $payroll?->net_salary) }}" required id="amount">
                                @error('amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">العملة</label>
                                <select class="form-select" name="currency_id">
                                    <option value="">اختر العملة</option>
                                    @foreach ($currencies as $currency)
                                        <option value="{{ $currency->id }}" {{ old('currency_id', $payroll?->currency_id) == $currency->id ? 'selected' : '' }}>
                                            {{ $currency->name }} ({{ $currency->code }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">طريقة الدفع <span class="text-danger">*</span></label>
                                <select class="form-select @error('payment_method') is-invalid @enderror" name="payment_method" required id="payment_method">
                                    <option value="">اختر طريقة الدفع</option>
                                    <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>نقدي</option>
                                    <option value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>تحويل بنكي</option>
                                    <option value="cheque" {{ old('payment_method') == 'cheque' ? 'selected' : '' }}>شيك</option>
                                    <option value="card" {{ old('payment_method') == 'card' ? 'selected' : '' }}>بطاقة</option>
                                    <option value="other" {{ old('payment_method') == 'other' ? 'selected' : '' }}>أخرى</option>
                                </select>
                                @error('payment_method')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">تاريخ الدفع <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('payment_date') is-invalid @enderror" 
                                       name="payment_date" value="{{ old('payment_date', date('Y-m-d')) }}" required>
                                @error('payment_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">رقم المرجع</label>
                                <input type="text" class="form-control" name="reference_number" value="{{ old('reference_number') }}">
                            </div>

                            <div class="col-md-6" id="bank_account_div" style="display: none;">
                                <label class="form-label">الحساب البنكي</label>
                                <select class="form-select" name="bank_account_id" id="bank_account_id">
                                    <option value="">اختر الحساب البنكي</option>
                                </select>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">ملاحظات الدفع</label>
                                <textarea class="form-control" name="payment_notes" rows="3">{{ old('payment_notes') }}</textarea>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">حفظ</button>
                            <a href="{{ route('admin.payroll-payments.index') }}" class="btn btn-secondary">إلغاء</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const payrollSelect = document.getElementById('payroll_id');
    const amountInput = document.getElementById('amount');
    const paymentMethodSelect = document.getElementById('payment_method');
    const bankAccountDiv = document.getElementById('bank_account_div');
    const bankAccountSelect = document.getElementById('bank_account_id');

    // تحديث المبلغ عند اختيار كشف الراتب
    payrollSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (selectedOption.value) {
            const netSalary = selectedOption.getAttribute('data-net-salary');
            amountInput.value = parseFloat(netSalary).toFixed(2);
        }
    });

    // إظهار/إخفاء الحساب البنكي عند اختيار طريقة الدفع
    paymentMethodSelect.addEventListener('change', function() {
        if (this.value === 'bank_transfer') {
            bankAccountDiv.style.display = 'block';
            // هنا يمكن جلب الحسابات البنكية للموظف المحدد
        } else {
            bankAccountDiv.style.display = 'none';
        }
    });
});
</script>
@stop

