@extends('admin.layouts.master')

@section('page-title')
    تفاصيل الحساب البنكي
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="page-header d-flex justify-content-between align-items-center my-4">
                <h5 class="page-title mb-0">تفاصيل الحساب البنكي</h5>
                <div>
                    @can('bank-account-edit')
                    <a href="{{ route('admin.bank-accounts.edit', $bankAccount->id) }}" class="btn btn-primary btn-sm">تعديل</a>
                    @endcan
                    <a href="{{ route('admin.bank-accounts.index') }}" class="btn btn-secondary btn-sm">رجوع</a>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">معلومات الحساب البنكي</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">الموظف:</label>
                                    <p>{{ $bankAccount->employee->full_name }} ({{ $bankAccount->employee->employee_number }})</p>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold">اسم البنك:</label>
                                    <p>{{ $bankAccount->bank_name_ar ?? $bankAccount->bank_name }}</p>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold">رقم الحساب:</label>
                                    <p>{{ $bankAccount->account_number }}</p>
                                </div>

                                @if($bankAccount->iban)
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">IBAN:</label>
                                    <p>{{ $bankAccount->iban }}</p>
                                </div>
                                @endif

                                @if($bankAccount->swift_code)
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">SWIFT Code:</label>
                                    <p>{{ $bankAccount->swift_code }}</p>
                                </div>
                                @endif

                                @if($bankAccount->account_holder_name)
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">اسم صاحب الحساب:</label>
                                    <p>{{ $bankAccount->account_holder_name }}</p>
                                </div>
                                @endif

                                @if($bankAccount->branch_name)
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">اسم الفرع:</label>
                                    <p>{{ $bankAccount->branch_name }}</p>
                                </div>
                                @endif

                                @if($bankAccount->branch_address)
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">عنوان الفرع:</label>
                                    <p>{{ $bankAccount->branch_address }}</p>
                                </div>
                                @endif

                                <div class="col-md-6">
                                    <label class="form-label fw-bold">نوع الحساب:</label>
                                    <p>
                                        <span class="badge bg-info">{{ $bankAccount->account_type_name_ar }}</span>
                                    </p>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold">العملة:</label>
                                    <p>{{ $bankAccount->currency_code }}</p>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold">حساب أساسي:</label>
                                    <p>
                                        @if($bankAccount->is_primary)
                                            <span class="badge bg-success">نعم</span>
                                        @else
                                            <span class="badge bg-secondary">لا</span>
                                        @endif
                                    </p>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold">الحالة:</label>
                                    <p>
                                        <span class="badge bg-{{ $bankAccount->is_active ? 'success' : 'secondary' }}">
                                            {{ $bankAccount->is_active ? 'نشط' : 'غير نشط' }}
                                        </span>
                                    </p>
                                </div>

                                @if($bankAccount->notes)
                                <div class="col-md-12">
                                    <label class="form-label fw-bold">ملاحظات:</label>
                                    <p>{{ $bankAccount->notes }}</p>
                                </div>
                                @endif

                                @if($bankAccount->creator)
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">أنشئ بواسطة:</label>
                                    <p>{{ $bankAccount->creator->name }}</p>
                                </div>
                                @endif

                                <div class="col-md-6">
                                    <label class="form-label fw-bold">تاريخ الإنشاء:</label>
                                    <p>{{ $bankAccount->created_at->format('Y-m-d H:i') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

