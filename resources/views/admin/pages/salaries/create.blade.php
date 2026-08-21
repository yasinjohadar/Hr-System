@extends('admin.layouts.master')

@section('page-title')
    إضافة راتب جديد
@stop

@section('css')
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid admin-page-shell">
            @include('admin.pages.users.partials.alerts')

            <div class="admin-page-banner">
                <div class="admin-page-banner-main">
                    <span class="admin-page-banner-icon"><i class="ri-money-dollar-circle-line"></i></span>
                    <div class="admin-page-banner-text">
                        <h1>إضافة راتب جديد</h1>
                        <p>أدخل بيانات الراتب وبنوده التفصيلية</p>
                    </div>
                </div>
                <div class="admin-page-banner-actions">
                    <a href="{{ route('admin.salaries.index') }}" class="admin-btn admin-btn-light">
                        <i class="ri-arrow-right-line"></i>
                        العودة للقائمة
                    </a>
                </div>
            </div>

            <div class="admin-page-card">
                <form class="admin-form" method="POST" action="{{ route('admin.salaries.store') }}" id="salaryForm">
                    @csrf

                    <div class="admin-form-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="admin-form-label" for="employee_id">الموظف <span class="text-danger">*</span></label>
                                <select class="form-select @error('employee_id') is-invalid @enderror" 
                                        name="employee_id" id="employee_id" required>
                                    <option value="">اختر الموظف</option>
                                    @foreach ($employees as $employee)
                                        <option value="{{ $employee->id }}" 
                                            data-salary="{{ $employee->salary ?? 0 }}"
                                            {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                                            {{ $employee->full_name ?? $employee->first_name . ' ' . $employee->last_name }} 
                                            ({{ $employee->employee_code ?? '' }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('employee_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3">
                                <label class="admin-form-label" for="salary_month">الشهر <span class="text-danger">*</span></label>
                                <select class="form-select @error('salary_month') is-invalid @enderror" 
                                        name="salary_month" id="salary_month" required>
                                    @for ($i = 1; $i <= 12; $i++)
                                        <option value="{{ $i }}" {{ old('salary_month', date('n')) == $i ? 'selected' : '' }}>
                                            {{ ['', 'يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'][$i] }}
                                        </option>
                                    @endfor
                                </select>
                                @error('salary_month')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3">
                                <label class="admin-form-label" for="salary_year">السنة <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('salary_year') is-invalid @enderror" 
                                       name="salary_year" id="salary_year" placeholder="السنة" 
                                       value="{{ old('salary_year', date('Y')) }}" required min="2020" max="2100">
                                @error('salary_year')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="admin-form-label" for="base_salary">الراتب الأساسي <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" class="form-control @error('base_salary') is-invalid @enderror" 
                                       name="base_salary" id="base_salary" placeholder="الراتب الأساسي" 
                                       value="{{ old('base_salary') }}" required min="0">
                                @error('base_salary')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="admin-form-label" for="allowances">البدلات</label>
                                <input type="number" step="0.01" class="form-control @error('allowances') is-invalid @enderror" 
                                       name="allowances" id="allowances" placeholder="البدلات" 
                                       value="{{ old('allowances', 0) }}" min="0">
                                @error('allowances')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="admin-form-label" for="bonuses">المكافآت</label>
                                <input type="number" step="0.01" class="form-control @error('bonuses') is-invalid @enderror" 
                                       name="bonuses" id="bonuses" placeholder="المكافآت" 
                                       value="{{ old('bonuses', 0) }}" min="0">
                                @error('bonuses')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="admin-form-label" for="overtime">ساعات إضافية</label>
                                <input type="number" step="0.01" class="form-control @error('overtime') is-invalid @enderror" 
                                       name="overtime" id="overtime" placeholder="ساعات إضافية" 
                                       value="{{ old('overtime', 0) }}" min="0">
                                @error('overtime')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="admin-form-label" for="deductions">الخصومات</label>
                                <input type="number" step="0.01" class="form-control @error('deductions') is-invalid @enderror" 
                                       name="deductions" id="deductions" placeholder="الخصومات" 
                                       value="{{ old('deductions', 0) }}" min="0">
                                @error('deductions')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="admin-form-label" for="total_salary">الراتب الإجمالي</label>
                                <input type="number" step="0.01" class="form-control" 
                                       id="total_salary" placeholder="الراتب الإجمالي" readonly>
                                <small class="form-text text-muted">يتم الحساب تلقائياً</small>
                            </div>

                            <div class="col-md-6">
                                <label class="admin-form-label" for="currency_id">العملة</label>
                                <select class="form-select @error('currency_id') is-invalid @enderror" 
                                        name="currency_id" id="currency_id">
                                    <option value="">اختر العملة</option>
                                    @foreach ($currencies as $currency)
                                        <option value="{{ $currency->id }}" 
                                            {{ old('currency_id', $baseCurrency?->id) == $currency->id ? 'selected' : '' }}>
                                            {{ $currency->name_ar ?? $currency->name }} ({{ $currency->code }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('currency_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="admin-form-label" for="payment_date">تاريخ الدفع</label>
                                <input type="date" class="form-control @error('payment_date') is-invalid @enderror" 
                                       name="payment_date" id="payment_date" placeholder="تاريخ الدفع" 
                                       value="{{ old('payment_date') }}">
                                @error('payment_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="admin-form-label" for="payment_status">حالة الدفع <span class="text-danger">*</span></label>
                                <select class="form-select @error('payment_status') is-invalid @enderror" 
                                        name="payment_status" id="payment_status" required>
                                    <option value="pending" {{ old('payment_status', 'pending') == 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                                    <option value="paid" {{ old('payment_status') == 'paid' ? 'selected' : '' }}>مدفوع</option>
                                    <option value="cancelled" {{ old('payment_status') == 'cancelled' ? 'selected' : '' }}>ملغي</option>
                                </select>
                                @error('payment_status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="admin-form-label">ملاحظات</label>
                                <textarea class="form-control @error('notes') is-invalid @enderror" 
                                          name="notes" placeholder="ملاحظات" style="height: 100px">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        @include('admin.pages.salaries._ledger_form', ['activeAdvances' => $activeAdvances])
                    </div>

                    <div class="admin-form-footer">
                        <a href="{{ route('admin.salaries.index') }}" class="admin-btn admin-btn-secondary">إلغاء</a>
                        <button type="submit" class="admin-btn admin-btn-primary">
                            <i class="ri-save-line"></i>
                            حفظ الراتب
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('js')
<script>
(function () {
    const deductionSideTypes = { deduction: 1, advance_recovery: 1, loan_installment: 1 };

    function calculateTotal() {
        const baseSalary = parseFloat(document.getElementById('base_salary').value) || 0;
        const allowances = parseFloat(document.getElementById('allowances').value) || 0;
        const bonuses = parseFloat(document.getElementById('bonuses').value) || 0;
        const overtime = parseFloat(document.getElementById('overtime').value) || 0;
        const deductions = parseFloat(document.getElementById('deductions').value) || 0;
        const total = baseSalary + allowances + bonuses + overtime - deductions;
        document.getElementById('total_salary').value = total.toFixed(2);
    }

    function syncDeductionsFromLedger() {
        const dedInput = document.getElementById('deductions');
        let sum = 0;
        document.querySelectorAll('#ledger-rows .ledger-row').forEach(function (tr) {
            const type = tr.querySelector('.ledger-line-type') && tr.querySelector('.ledger-line-type').value;
            const amt = parseFloat(tr.querySelector('.ledger-amount') && tr.querySelector('.ledger-amount').value) || 0;
            if (deductionSideTypes[type]) {
                sum += amt;
            }
        });
        if (sum > 0) {
            dedInput.value = sum.toFixed(2);
            dedInput.readOnly = true;
        } else {
            dedInput.readOnly = false;
        }
        calculateTotal();
    }

    function filterAdvanceOptions(employeeId) {
        const emp = String(employeeId || '');
        document.querySelectorAll('.ledger-advance-select').forEach(function (sel) {
            sel.querySelectorAll('option[data-employee-id]').forEach(function (opt) {
                opt.hidden = !!(opt.value && opt.getAttribute('data-employee-id') !== emp);
            });
        });
    }

    function bindLedgerRow(tr) {
        tr.querySelectorAll('.ledger-line-type, .ledger-amount').forEach(function (el) {
            el.addEventListener('input', syncDeductionsFromLedger);
            el.addEventListener('change', syncDeductionsFromLedger);
        });
        tr.querySelector('.ledger-remove-row') && tr.querySelector('.ledger-remove-row').addEventListener('click', function () {
            if (document.querySelectorAll('#ledger-rows .ledger-row').length <= 1) {
                tr.querySelectorAll('input').forEach(function (i) { i.value = ''; });
                tr.querySelectorAll('select').forEach(function (s) { s.selectedIndex = 0; });
            } else {
                tr.remove();
            }
            syncDeductionsFromLedger();
        });
    }

    document.getElementById('base_salary').addEventListener('input', calculateTotal);
    document.getElementById('allowances').addEventListener('input', calculateTotal);
    document.getElementById('bonuses').addEventListener('input', calculateTotal);
    document.getElementById('overtime').addEventListener('input', calculateTotal);
    document.getElementById('deductions').addEventListener('input', calculateTotal);

    document.getElementById('employee_id').addEventListener('change', function () {
        const selectedOption = this.options[this.selectedIndex];
        const employeeSalary = selectedOption.getAttribute('data-salary');
        if (employeeSalary && employeeSalary > 0) {
            document.getElementById('base_salary').value = employeeSalary;
            calculateTotal();
        }
        filterAdvanceOptions(this.value);
    });

    document.querySelectorAll('#ledger-rows .ledger-row').forEach(bindLedgerRow);

    const addBtn = document.getElementById('ledger-add-row');
    const tpl = document.querySelector('#ledger-row-template tr');
    if (addBtn && tpl) {
        addBtn.addEventListener('click', function () {
            const row = tpl.cloneNode(true);
            document.getElementById('ledger-rows').appendChild(row);
            bindLedgerRow(row);
            filterAdvanceOptions(document.getElementById('employee_id').value);
            syncDeductionsFromLedger();
        });
    }

    document.getElementById('ledger-rows').addEventListener('change', function (e) {
        if (e.target.classList.contains('ledger-line-type')) {
            syncDeductionsFromLedger();
        }
    });

    filterAdvanceOptions(document.getElementById('employee_id').value);
    syncDeductionsFromLedger();
    calculateTotal();
})();
</script>
@stop

