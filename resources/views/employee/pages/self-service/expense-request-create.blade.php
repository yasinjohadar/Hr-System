@extends('employee.layouts.master')

@section('page-title')
    طلب مصروفات جديد
@stop

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/employee-expenses.css') }}">
@endpush

@section('content')
    <div class="main-content app-content employee-expenses-page">
        <div class="container-fluid pt-4">

            <div class="card page-hero mb-4">
                <div class="card-body py-4">
                    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="page-hero-icon">
                                <i class="ri-wallet-3-line"></i>
                            </div>
                            <div>
                                <h4 class="mb-1 page-hero-title fw-bold">طلب مصروفات جديد</h4>
                                <p class="mb-0 page-hero-subtitle">أدخل تفاصيل المصروف وأرفق الإيصال إن وُجد</p>
                            </div>
                        </div>
                        <a href="{{ route('employee.expense-requests') }}" class="btn btn-hero-back">
                            <i class="ri-arrow-right-line me-1"></i>العودة للقائمة
                        </a>
                    </div>
                </div>
            </div>

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="form-panel">
                        <div class="form-panel-header">
                            <h5 class="fw-bold mb-0 text-dark">بيانات الطلب</h5>
                        </div>
                        <div class="form-panel-body">
                            <form id="expense-create-form" method="POST"
                                action="{{ route('employee.expense-requests.store') }}" enctype="multipart/form-data">
                                @csrf

                                <div class="form-section">
                                    <div class="form-section-title">
                                        <span class="section-num">1</span>
                                        تفاصيل المصروف
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label">تصنيف المصروف <span class="text-danger">*</span></label>
                                            <div class="field-wrap">
                                                <i class="ri-price-tag-3-line field-icon"></i>
                                                <select name="expense_category_id"
                                                    class="form-select @error('expense_category_id') is-invalid @enderror"
                                                    required>
                                                    <option value="">اختر التصنيف</option>
                                                    @foreach ($categories as $cat)
                                                        <option value="{{ $cat->id }}"
                                                            data-max-amount="{{ $cat->max_amount ? number_format($cat->max_amount, 2, '.', '') : '' }}"
                                                            data-requires-receipt="{{ $cat->requires_receipt ? '1' : '0' }}"
                                                            {{ old('expense_category_id') == $cat->id ? 'selected' : '' }}>
                                                            {{ $cat->name_ar ?? $cat->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div id="category-hint" class="category-hint"></div>
                                            @error('expense_category_id')
                                                <div class="text-danger fs-12 mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">المبلغ <span class="text-danger">*</span></label>
                                            <div class="field-wrap">
                                                <i class="ri-money-dollar-circle-line field-icon"></i>
                                                <input type="number" name="amount"
                                                    class="form-control @error('amount') is-invalid @enderror"
                                                    value="{{ old('amount') }}" step="0.01" min="0.01"
                                                    placeholder="0.00" required>
                                            </div>
                                            @error('amount')
                                                <div class="text-danger fs-12 mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">العملة</label>
                                            <div class="field-wrap">
                                                <i class="ri-exchange-dollar-line field-icon"></i>
                                                <select name="currency_id" class="form-select">
                                                    <option value="">اختر العملة</option>
                                                    @foreach ($currencies as $currency)
                                                        <option value="{{ $currency->id }}"
                                                            {{ old('currency_id') == $currency->id ? 'selected' : '' }}>
                                                            {{ $currency->name_ar ?? $currency->code }}
                                                            ({{ $currency->code }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">تاريخ المصروف <span class="text-danger">*</span></label>
                                            <div class="field-wrap">
                                                <i class="ri-calendar-line field-icon"></i>
                                                <input type="date" name="expense_date"
                                                    class="form-control @error('expense_date') is-invalid @enderror"
                                                    value="{{ old('expense_date', date('Y-m-d')) }}" required>
                                            </div>
                                            @error('expense_date')
                                                <div class="text-danger fs-12 mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">الوصف <span class="text-danger">*</span></label>
                                            <textarea name="description"
                                                class="form-control @error('description') is-invalid @enderror" rows="3"
                                                placeholder="اشرح طبيعة المصروف والغرض منه..." required>{{ old('description') }}</textarea>
                                            @error('description')
                                                <div class="text-danger fs-12 mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-section">
                                    <div class="form-section-title">
                                        <span class="section-num">2</span>
                                        الدفع والمورد
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label">طريقة الدفع</label>
                                            <div class="field-wrap">
                                                <i class="ri-bank-card-line field-icon"></i>
                                                <select name="payment_method" class="form-select">
                                                    <option value="">اختر طريقة الدفع</option>
                                                    <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>نقد</option>
                                                    <option value="card" {{ old('payment_method') == 'card' ? 'selected' : '' }}>بطاقة</option>
                                                    <option value="transfer" {{ old('payment_method') == 'transfer' ? 'selected' : '' }}>تحويل بنكي</option>
                                                    <option value="check" {{ old('payment_method') == 'check' ? 'selected' : '' }}>شيك</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">اسم المورد</label>
                                            <div class="field-wrap">
                                                <i class="ri-store-2-line field-icon"></i>
                                                <input type="text" name="vendor_name" class="form-control"
                                                    value="{{ old('vendor_name') }}" placeholder="اختياري">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-section">
                                    <div class="form-section-title">
                                        <span class="section-num">3</span>
                                        المرفقات والملاحظات
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <label class="form-label">الإيصال (اختياري)</label>
                                            <label class="file-drop" id="receipt-drop" for="receipt-input">
                                                <input type="file" name="receipt" id="receipt-input" class="d-none"
                                                    accept=".pdf,.jpg,.jpeg,.png">
                                                <div class="file-drop-icon"><i class="ri-upload-cloud-2-line"></i></div>
                                                <div class="fw-semibold text-dark mb-1" id="receipt-file-name">اسحب الملف أو انقر للاختيار</div>
                                                <div class="file-drop-hint">PDF, JPG, PNG — حد أقصى 10MB</div>
                                            </label>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">ملاحظات إضافية</label>
                                            <textarea name="notes" class="form-control" rows="2"
                                                placeholder="أي معلومات تساعد في مراجعة الطلب...">{{ old('notes') }}</textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-actions">
                                    <a href="{{ route('employee.expense-requests') }}" class="btn btn-cancel-expense">إلغاء</a>
                                    <button type="submit" class="btn btn-primary btn-submit-expense">
                                        <i class="ri-send-plane-line me-1"></i>إرسال الطلب
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="tips-card">
                        <h6><i class="ri-lightbulb-line me-1 text-primary"></i>نصائح قبل الإرسال</h6>
                        <ul class="tips-list">
                            <li><i class="ri-check-line"></i>تأكد من مطابقة المبلغ للإيصال الفعلي</li>
                            <li><i class="ri-check-line"></i>بعض التصنيفات تتطلب إرفاق إيصال إلزامي</li>
                            <li><i class="ri-check-line"></i>الوصف الواضح يسرّع الموافقة على الطلب</li>
                            <li><i class="ri-check-line"></i>يمكنك متابعة حالة الطلب من قائمة المصروفات</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@push('scripts')
    <script src="{{ asset('assets/js/employee-expenses.js') }}"></script>
@endpush
