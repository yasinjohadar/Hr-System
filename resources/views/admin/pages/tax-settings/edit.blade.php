@extends('admin.layouts.master')

@section('page-title')
    تعديل إعداد الضريبة
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid admin-page-shell">
            @include('admin.pages.users.partials.alerts')

            <div class="admin-page-banner">
                <div class="admin-page-banner-main">
                    <span class="admin-page-banner-icon"><i class="ri-edit-box-line"></i></span>
                    <div class="admin-page-banner-text">
                        <h1>تعديل إعداد الضريبة</h1>
                        <p>{{ $taxSetting->name_ar ?? $taxSetting->name }}</p>
                    </div>
                </div>
                <div class="admin-page-banner-actions">
                    <a href="{{ route('admin.tax-settings.index') }}" class="admin-btn admin-btn-light">
                        <i class="ri-arrow-right-line"></i>
                        العودة للقائمة
                    </a>
                </div>
            </div>

            <div class="admin-page-card">
                <form class="admin-form" method="POST" action="{{ route('admin.tax-settings.update', $taxSetting->id) }}">
                    @csrf
                    @method('PUT')

                    <div class="admin-form-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="admin-form-label">الكود <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('code') is-invalid @enderror"
                                       name="code" value="{{ old('code', $taxSetting->code) }}" required>
                                @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="admin-form-label">الاسم <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                       name="name" value="{{ old('name', $taxSetting->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="admin-form-label">الاسم بالعربية</label>
                                <input type="text" class="form-control" name="name_ar" value="{{ old('name_ar', $taxSetting->name_ar) }}">
                            </div>

                            <div class="col-md-6">
                                <label class="admin-form-label">النوع <span class="text-danger">*</span></label>
                                <select class="form-select @error('type') is-invalid @enderror" name="type" required>
                                    <option value="">اختر النوع</option>
                                    <option value="income_tax" @selected(old('type', $taxSetting->type) === 'income_tax')>ضريبة الدخل</option>
                                    <option value="social_insurance" @selected(old('type', $taxSetting->type) === 'social_insurance')>التأمينات الاجتماعية</option>
                                    <option value="health_insurance" @selected(old('type', $taxSetting->type) === 'health_insurance')>التأمين الصحي</option>
                                    <option value="other" @selected(old('type', $taxSetting->type) === 'other')>أخرى</option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="admin-form-label">طريقة الحساب <span class="text-danger">*</span></label>
                                <select class="form-select @error('calculation_method') is-invalid @enderror" name="calculation_method" required id="calculation_method">
                                    <option value="">اختر طريقة الحساب</option>
                                    <option value="percentage" @selected(old('calculation_method', $taxSetting->calculation_method) === 'percentage')>نسبة مئوية</option>
                                    <option value="slab" @selected(old('calculation_method', $taxSetting->calculation_method) === 'slab')>شرائح</option>
                                    <option value="fixed" @selected(old('calculation_method', $taxSetting->calculation_method) === 'fixed')>ثابت</option>
                                </select>
                                @error('calculation_method')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="admin-form-label">النسبة/القيمة <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" class="form-control @error('rate') is-invalid @enderror"
                                       name="rate" value="{{ old('rate', $taxSetting->rate) }}" required>
                                @error('rate')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="admin-form-label">الحد الأدنى</label>
                                <input type="number" step="0.01" class="form-control" name="min_amount" value="{{ old('min_amount', $taxSetting->min_amount) }}">
                            </div>

                            <div class="col-md-6">
                                <label class="admin-form-label">الحد الأقصى</label>
                                <input type="number" step="0.01" class="form-control" name="max_amount" value="{{ old('max_amount', $taxSetting->max_amount) }}">
                            </div>

                            <div class="col-md-6">
                                <label class="admin-form-label">مبلغ الإعفاء</label>
                                <input type="number" step="0.01" class="form-control" name="exemption_amount" value="{{ old('exemption_amount', $taxSetting->exemption_amount) }}">
                            </div>

                            <div class="col-md-6">
                                <label class="admin-form-label">تاريخ البدء</label>
                                <input type="date" class="form-control" name="effective_from" value="{{ old('effective_from', $taxSetting->effective_from?->format('Y-m-d')) }}">
                            </div>

                            <div class="col-md-6">
                                <label class="admin-form-label">تاريخ الانتهاء</label>
                                <input type="date" class="form-control" name="effective_to" value="{{ old('effective_to', $taxSetting->effective_to?->format('Y-m-d')) }}">
                            </div>

                            <div class="col-md-12">
                                <label class="admin-form-label">الوصف</label>
                                <textarea class="form-control" name="description" rows="3">{{ old('description', $taxSetting->description) }}</textarea>
                            </div>

                            <div class="col-md-6">
                                <div class="form-check mt-4">
                                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" @checked(old('is_active', $taxSetting->is_active))>
                                    <label class="form-check-label" for="is_active">نشط</label>
                                </div>
                            </div>
                        </div>

                        {{-- قسم الشرائح — يظهر فقط عند اختيار طريقة الحساب "شرائح" --}}
                        <div id="slabs-section" class="admin-form-section mt-4" hidden>
                            <div class="admin-form-section-head">
                                <span class="admin-section-icon admin-section-icon-blue"><i class="ri-stack-line"></i></span>
                                <div>
                                    <h6 class="mb-0 fw-bold">شرائح الضريبة</h6>
                                    <small class="text-muted">حدّد نطاق كل شريحة ونسبتها</small>
                                </div>
                            </div>
                            <div id="slabs-container">
                                @forelse (($taxSetting->slabs ?: []) as $index => $slab)
                                    <div class="slab-item mb-3 p-3 border rounded">
                                        <div class="row g-3">
                                            <div class="col-md-4">
                                                <label class="admin-form-label">من</label>
                                                <input type="number" step="0.01" class="form-control" name="slabs[{{ $index }}][min]" value="{{ $slab['min'] ?? '' }}" placeholder="0">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="admin-form-label">إلى</label>
                                                <input type="number" step="0.01" class="form-control" name="slabs[{{ $index }}][max]" value="{{ $slab['max'] ?? '' }}" placeholder="10000">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="admin-form-label">النسبة %</label>
                                                <input type="number" step="0.01" class="form-control" name="slabs[{{ $index }}][rate]" value="{{ $slab['rate'] ?? '' }}" placeholder="5">
                                            </div>
                                            <div class="col-md-1">
                                                <label class="admin-form-label">&nbsp;</label>
                                                <button type="button" class="admin-btn admin-btn-danger admin-btn-sm remove-slab" @if (count($taxSetting->slabs) <= 1) hidden @endif>حذف</button>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="slab-item mb-3 p-3 border rounded">
                                        <div class="row g-3">
                                            <div class="col-md-4">
                                                <label class="admin-form-label">من</label>
                                                <input type="number" step="0.01" class="form-control" name="slabs[0][min]" placeholder="0">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="admin-form-label">إلى</label>
                                                <input type="number" step="0.01" class="form-control" name="slabs[0][max]" placeholder="10000">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="admin-form-label">النسبة %</label>
                                                <input type="number" step="0.01" class="form-control" name="slabs[0][rate]" placeholder="5">
                                            </div>
                                            <div class="col-md-1">
                                                <label class="admin-form-label">&nbsp;</label>
                                                <button type="button" class="admin-btn admin-btn-danger admin-btn-sm remove-slab" hidden>حذف</button>
                                            </div>
                                        </div>
                                    </div>
                                @endforelse
                            </div>
                            <button type="button" class="admin-btn admin-btn-secondary admin-btn-sm" id="add-slab">
                                <i class="ri-add-line"></i>
                                إضافة شريحة
                            </button>
                        </div>
                    </div>

                    <div class="admin-form-footer">
                        <a href="{{ route('admin.tax-settings.index') }}" class="admin-btn admin-btn-secondary">إلغاء</a>
                        <button type="submit" class="admin-btn admin-btn-primary">
                            <i class="ri-save-line"></i>
                            حفظ
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script>
        /*
         * إظهار/إخفاء قسم الشرائح حسب طريقة الحساب، وإضافة/حذف صفوف الشرائح.
         * نفس منطق create.blade.php — انظر تعليقه لسبب استخدام hidden.
         */
        (function () {
            const calculationMethod = document.getElementById('calculation_method');
            const slabsSection = document.getElementById('slabs-section');
            const addBtn = document.getElementById('add-slab');
            const container = document.getElementById('slabs-container');

            if (!calculationMethod || !slabsSection) {
                return;
            }

            let slabIndex = {{ $taxSetting->slabs ? count($taxSetting->slabs) : 1 }};

            function syncVisibility() {
                slabsSection.hidden = calculationMethod.value !== 'slab';
            }

            function syncRemoveButtons() {
                const items = container.querySelectorAll('.slab-item');
                container.querySelectorAll('.remove-slab').forEach(function (btn) {
                    btn.hidden = items.length <= 1;
                });
            }

            calculationMethod.addEventListener('change', syncVisibility);

            addBtn.addEventListener('click', function () {
                const row = document.createElement('div');
                row.className = 'slab-item mb-3 p-3 border rounded';
                row.innerHTML = `
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="admin-form-label">من</label>
                            <input type="number" step="0.01" class="form-control" name="slabs[${slabIndex}][min]" placeholder="0">
                        </div>
                        <div class="col-md-4">
                            <label class="admin-form-label">إلى</label>
                            <input type="number" step="0.01" class="form-control" name="slabs[${slabIndex}][max]" placeholder="10000">
                        </div>
                        <div class="col-md-3">
                            <label class="admin-form-label">النسبة %</label>
                            <input type="number" step="0.01" class="form-control" name="slabs[${slabIndex}][rate]" placeholder="5">
                        </div>
                        <div class="col-md-1">
                            <label class="admin-form-label">&nbsp;</label>
                            <button type="button" class="admin-btn admin-btn-danger admin-btn-sm remove-slab">حذف</button>
                        </div>
                    </div>
                `;
                container.appendChild(row);
                slabIndex++;
                syncRemoveButtons();
            });

            container.addEventListener('click', function (event) {
                const btn = event.target.closest('.remove-slab');
                if (btn) {
                    btn.closest('.slab-item').remove();
                    syncRemoveButtons();
                }
            });

            syncVisibility();
            syncRemoveButtons();
        })();
    </script>
@stop
