@extends('admin.layouts.master')

@section('page-title')
    تحويل مالي جديد
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid admin-page-shell">
            @include('admin.pages.users.partials.alerts')

            <div class="admin-page-banner">
                <div class="admin-page-banner-main">
                    <span class="admin-page-banner-icon"><i class="ri-exchange-dollar-line"></i></span>
                    <div class="admin-page-banner-text">
                        <h1>إنشاء تحويل / صرف</h1>
                        <p>المبالغ حتى {{ number_format($threshold, 2) }} تُنفَّذ فوراً؛ الأعلى تحتاج موافقة</p>
                    </div>
                </div>
                <div class="admin-page-banner-actions">
                    <a href="{{ route('admin.fund-transfers.index') }}" class="admin-btn admin-btn-light">رجوع</a>
                </div>
            </div>

            <div class="admin-page-card">
                <form method="POST" action="{{ route('admin.fund-transfers.store') }}" class="admin-form" id="fund-transfer-form">
                    @csrf
                    <div class="admin-form-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="admin-form-label">نوع الحركة *</label>
                                <select name="type" id="transfer_type" class="form-select" required>
                                    <option value="internal" @selected(old('type') === 'internal')>تحويل داخلي (شركة → شركة)</option>
                                    <option value="disbursement" @selected(old('type') === 'disbursement')>صرف لموظف</option>
                                    <option value="adjustment" @selected(old('type') === 'adjustment')>تسوية / إيداع لحساب شركة</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="admin-form-label">المبلغ *</label>
                                <input type="number" step="0.01" min="0.01" name="amount" class="form-control" required value="{{ old('amount') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="admin-form-label">العملة</label>
                                <select name="currency_id" class="form-select">
                                    <option value="">—</option>
                                    @foreach ($currencies as $currency)
                                        <option value="{{ $currency->id }}" @selected(old('currency_id') == $currency->id)>{{ $currency->code }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6" data-field="from_company">
                                <label class="admin-form-label">من حساب شركة</label>
                                <select name="from_account_id" id="from_account_id" class="form-select">
                                    <option value="">— اختر —</option>
                                    @foreach ($companyAccounts as $acc)
                                        <option value="{{ $acc->id }}" @selected(old('from_account_id') == $acc->id)>
                                            {{ $acc->display_label }} — رصيد {{ number_format((float) $acc->balance, 2) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('from_account_id')<div class="text-danger small">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6" data-field="to_company">
                                <label class="admin-form-label">إلى حساب شركة</label>
                                <select id="to_company_id" class="form-select">
                                    <option value="">— اختر —</option>
                                    @foreach ($companyAccounts as $acc)
                                        <option value="{{ $acc->id }}" @selected(old('to_account_id') == $acc->id && old('type', 'internal') !== 'disbursement')>
                                            {{ $acc->display_label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6 d-none" data-field="to_employee">
                                <label class="admin-form-label">إلى حساب موظف</label>
                                <select id="to_employee_id" class="form-select">
                                    <option value="">— اختر —</option>
                                    @foreach ($employeeAccounts as $acc)
                                        <option value="{{ $acc->id }}" @selected(old('to_account_id') == $acc->id && old('type') === 'disbursement')>
                                            {{ $acc->employee->full_name ?? 'موظف' }} — {{ $acc->bank_name }} ({{ $acc->account_number }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <input type="hidden" name="to_account_id" id="to_account_id_hidden" value="{{ old('to_account_id') }}">
                            @error('to_account_id')<div class="col-12 text-danger small">{{ $message }}</div>@enderror

                            <div class="col-md-6">
                                <label class="admin-form-label">المشروع (اختياري)</label>
                                <select name="project_id" id="project_id" class="form-select">
                                    <option value="">—</option>
                                    @foreach ($projects as $project)
                                        <option value="{{ $project->id }}"
                                            data-stages='@json($project->stages->map(fn ($s) => ["id" => $s->id, "name" => $s->display_name]))'
                                            @selected(old('project_id', request('project_id')) == $project->id)>
                                            {{ $project->name_ar ?? $project->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="admin-form-label">المرحلة (اختياري)</label>
                                <select name="project_stage_id" id="project_stage_id" class="form-select">
                                    <option value="">—</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="admin-form-label">ملاحظات</label>
                                <textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="admin-form-footer">
                        <button type="submit" class="admin-btn admin-btn-primary">تنفيذ / إرسال للموافقة</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@push('scripts')
<script>
(function () {
    const typeSelect = document.getElementById('transfer_type');
    const fromCompany = document.querySelector('[data-field="from_company"]');
    const toCompany = document.querySelector('[data-field="to_company"]');
    const toEmployee = document.querySelector('[data-field="to_employee"]');
    const fromSelect = document.getElementById('from_account_id');
    const toCompanySelect = document.getElementById('to_company_id');
    const toEmployeeSelect = document.getElementById('to_employee_id');
    const toHidden = document.getElementById('to_account_id_hidden');
    const projectSelect = document.getElementById('project_id');
    const stageSelect = document.getElementById('project_stage_id');

    function syncToHidden() {
        const type = typeSelect.value;
        toHidden.value = type === 'disbursement' ? (toEmployeeSelect.value || '') : (toCompanySelect.value || '');
    }

    function syncType() {
        const type = typeSelect.value;
        fromCompany.classList.toggle('d-none', type === 'adjustment');
        toCompany.classList.toggle('d-none', type === 'disbursement');
        toEmployee.classList.toggle('d-none', type !== 'disbursement');
        if (fromSelect) fromSelect.disabled = type === 'adjustment';
        syncToHidden();
    }

    function syncStages() {
        const opt = projectSelect.selectedOptions[0];
        const current = stageSelect.value;
        stageSelect.innerHTML = '<option value="">—</option>';
        if (!opt || !opt.dataset.stages) return;
        try {
            JSON.parse(opt.dataset.stages).forEach((s) => {
                const o = document.createElement('option');
                o.value = s.id;
                o.textContent = s.name;
                if (String(current) === String(s.id)) o.selected = true;
                stageSelect.appendChild(o);
            });
        } catch (e) {}
    }

    typeSelect.addEventListener('change', syncType);
    toCompanySelect.addEventListener('change', syncToHidden);
    toEmployeeSelect.addEventListener('change', syncToHidden);
    projectSelect.addEventListener('change', syncStages);
    document.getElementById('fund-transfer-form').addEventListener('submit', syncToHidden);
    syncType();
    syncStages();
})();
</script>
@endpush
