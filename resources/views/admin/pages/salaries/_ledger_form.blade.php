@php
    $ledgerRows = old('ledger_lines');
    if (!is_array($ledgerRows)) {
        if (isset($salary) && $salary->ledgerLines->isNotEmpty()) {
            $ledgerRows = $salary->ledgerLines->map(function ($l) {
                return [
                    'line_type' => $l->line_type,
                    'label_ar' => $l->label_ar,
                    'amount' => $l->amount,
                    'employee_advance_id' => $l->employee_advance_id,
                ];
            })->all();
        } else {
            $ledgerRows = [['line_type' => '', 'label_ar' => '', 'amount' => '', 'employee_advance_id' => '']];
        }
    }
    if ($ledgerRows === []) {
        $ledgerRows = [['line_type' => '', 'label_ar' => '', 'amount' => '', 'employee_advance_id' => '']];
    }
@endphp

{{-- قسم بنود الراتب — نفس نمط admin-form-section في بقية النماذج --}}
<div class="admin-form-section mt-4">
    <div class="admin-form-section-head">
        <span class="admin-section-icon admin-section-icon-teal"><i class="ri-list-check-2"></i></span>
        <div>
            <h6 class="mb-0 fw-bold">بنود الراتب التفصيلية</h6>
            <small class="text-muted">خصومات، استرداد سلف، أقساط، بدلات إضافية… عند وجود بنود من نوع خصم/استرداد سلفة/قسط يُحسب حقل «الخصومات» تلقائياً من مجموعها.</small>
        </div>
    </div>
    <div>
        @error('ledger')
            <div class="alert alert-danger">{{ $message }}</div>
        @enderror
        <div class="table-responsive">
            <table class="admin-data-table admin-data-table-sm" id="ledger-table">
                <thead>
                    <tr>
                        <th style="min-width: 140px;">النوع</th>
                        <th>الوصف</th>
                        <th style="min-width: 110px;">المبلغ</th>
                        <th style="min-width: 180px;">السلفة (استرداد فقط)</th>
                        <th style="width: 48px;"></th>
                    </tr>
                </thead>
                <tbody id="ledger-rows">
                    @foreach ($ledgerRows as $i => $row)
                        <tr class="ledger-row">
                            <td>
                                <select name="ledger_lines[{{ $i }}][line_type]" class="form-select ledger-line-type">
                                    <option value="">—</option>
                                    <option value="allowance" @selected(($row['line_type'] ?? '') === 'allowance')>بدل</option>
                                    <option value="bonus" @selected(($row['line_type'] ?? '') === 'bonus')>مكافأة</option>
                                    <option value="deduction" @selected(($row['line_type'] ?? '') === 'deduction')>خصم</option>
                                    <option value="advance_recovery" @selected(($row['line_type'] ?? '') === 'advance_recovery')>استرداد سلفة</option>
                                    <option value="loan_installment" @selected(($row['line_type'] ?? '') === 'loan_installment')>قسط قرض</option>
                                    <option value="overtime" @selected(($row['line_type'] ?? '') === 'overtime')>ساعات إضافية</option>
                                    <option value="other" @selected(($row['line_type'] ?? '') === 'other')>أخرى</option>
                                </select>
                            </td>
                            <td>
                                <input type="text" name="ledger_lines[{{ $i }}][label_ar]" class="form-control"
                                    value="{{ $row['label_ar'] ?? '' }}" placeholder="وصف البند">
                            </td>
                            <td>
                                <input type="number" step="0.01" min="0" name="ledger_lines[{{ $i }}][amount]" class="form-control ledger-amount"
                                    value="{{ $row['amount'] ?? '' }}">
                            </td>
                            <td>
                                <select name="ledger_lines[{{ $i }}][employee_advance_id]" class="form-select ledger-advance-select">
                                    <option value="">—</option>
                                    @foreach ($activeAdvances as $adv)
                                        <option value="{{ $adv->id }}" data-employee-id="{{ $adv->employee_id }}"
                                            @selected(isset($row['employee_advance_id']) && (string) $row['employee_advance_id'] === (string) $adv->id)>
                                            #{{ $adv->id }} —
                                            {{ $adv->employee->full_name ?? $adv->employee->first_name . ' ' . $adv->employee->last_name }}
                                            (متبقي {{ number_format($adv->remaining_balance, 2) }})
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <button type="button" class="admin-btn admin-btn-danger admin-btn-sm ledger-remove-row" title="حذف الصف">&times;</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <button type="button" class="admin-btn admin-btn-primary admin-btn-sm mt-2" id="ledger-add-row">+ إضافة بند</button>
    </div>
</div>

<table class="d-none">
    <tbody id="ledger-row-template">
        <tr class="ledger-row">
            <td>
                <select name="ledger_lines[][line_type]" class="form-select ledger-line-type">
                    <option value="">—</option>
                    <option value="allowance">بدل</option>
                    <option value="bonus">مكافأة</option>
                    <option value="deduction">خصم</option>
                    <option value="advance_recovery">استرداد سلفة</option>
                    <option value="loan_installment">قسط قرض</option>
                    <option value="overtime">ساعات إضافية</option>
                    <option value="other">أخرى</option>
                </select>
            </td>
            <td>
                <input type="text" name="ledger_lines[][label_ar]" class="form-control" placeholder="وصف البند">
            </td>
            <td>
                <input type="number" step="0.01" min="0" name="ledger_lines[][amount]" class="form-control ledger-amount">
            </td>
            <td>
                <select name="ledger_lines[][employee_advance_id]" class="form-select ledger-advance-select">
                    <option value="">—</option>
                    @foreach ($activeAdvances as $adv)
                        <option value="{{ $adv->id }}" data-employee-id="{{ $adv->employee_id }}">
                            #{{ $adv->id }} —
                            {{ $adv->employee->full_name ?? $adv->employee->first_name . ' ' . $adv->employee->last_name }}
                            (متبقي {{ number_format($adv->remaining_balance, 2) }})
                        </option>
                    @endforeach
                </select>
            </td>
            <td>
                <button type="button" class="admin-btn admin-btn-danger admin-btn-sm ledger-remove-row" title="حذف الصف">&times;</button>
            </td>
        </tr>
    </tbody>
</table>
