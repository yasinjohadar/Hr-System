@extends('admin.layouts.master')

@section('page-title')
    {{ $transfer->transfer_code }}
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid admin-page-shell">
            @include('admin.pages.users.partials.alerts')

            <div class="admin-page-banner">
                <div class="admin-page-banner-main">
                    <span class="admin-page-banner-icon"><i class="ri-exchange-dollar-line"></i></span>
                    <div class="admin-page-banner-text">
                        <h1>{{ $transfer->transfer_code }}</h1>
                        <p>{{ $transfer->type_name_ar }} — {{ $transfer->status_name_ar }}</p>
                    </div>
                </div>
                <div class="admin-page-banner-actions">
                    <a href="{{ route('admin.fund-transfers.index') }}" class="admin-btn admin-btn-light">القائمة</a>
                </div>
            </div>

            <div class="admin-page-card p-4 mb-3">
                <dl class="row mb-0">
                    <dt class="col-sm-3">المبلغ</dt>
                    <dd class="col-sm-9 fw-bold">{{ number_format((float) $transfer->amount, 2) }} {{ $transfer->currency->code ?? '' }}</dd>
                    <dt class="col-sm-3">من</dt>
                    <dd class="col-sm-9">
                        @if ($fromAccount instanceof \App\Models\CompanyBankAccount)
                            {{ $fromAccount->display_label }}
                        @else
                            —
                        @endif
                    </dd>
                    <dt class="col-sm-3">إلى</dt>
                    <dd class="col-sm-9">
                        @if ($toAccount instanceof \App\Models\CompanyBankAccount)
                            {{ $toAccount->display_label }}
                        @elseif ($toAccount instanceof \App\Models\EmployeeBankAccount)
                            {{ $toAccount->employee->full_name ?? 'موظف' }} — {{ $toAccount->bank_name }} ({{ $toAccount->account_number }})
                        @else
                            —
                        @endif
                    </dd>
                    <dt class="col-sm-3">المشروع / المرحلة</dt>
                    <dd class="col-sm-9">
                        {{ $transfer->project?->name ?? '—' }}
                        @if ($transfer->stage)
                            / {{ $transfer->stage->display_name }}
                        @endif
                    </dd>
                    <dt class="col-sm-3">طلب بواسطة</dt>
                    <dd class="col-sm-9">{{ $transfer->requester?->name ?? '—' }}</dd>
                    <dt class="col-sm-3">التنفيذ</dt>
                    <dd class="col-sm-9">{{ $transfer->executed_at?->format('Y-m-d H:i') ?? '—' }}</dd>
                    <dt class="col-sm-3">ملاحظات</dt>
                    <dd class="col-sm-9">{{ $transfer->notes ?: '—' }}</dd>
                    @if ($transfer->rejection_reason)
                        <dt class="col-sm-3">سبب الرفض</dt>
                        <dd class="col-sm-9 text-danger">{{ $transfer->rejection_reason }}</dd>
                    @endif
                </dl>
            </div>

            @if ($transfer->status === 'pending')
                @can('fund-transfer-approve')
                    <div class="d-flex flex-wrap gap-2">
                        <form action="{{ route('admin.fund-transfers.approve', $transfer) }}" method="POST"
                              data-confirm="الموافقة وتنفيذ التحويل؟"
                              data-confirm-title="موافقة التحويل"
                              data-confirm-type="success"
                              data-confirm-btn="موافقة وتنفيذ">
                            @csrf
                            <button type="submit" class="admin-btn admin-btn-primary">موافقة وتنفيذ</button>
                        </form>
                        <form action="{{ route('admin.fund-transfers.reject', $transfer) }}" method="POST" class="d-flex gap-2"
                              data-confirm="رفض هذا التحويل؟"
                              data-confirm-title="رفض التحويل"
                              data-confirm-type="warning"
                              data-confirm-btn="رفض">
                            @csrf
                            <input type="text" name="rejection_reason" class="form-control" placeholder="سبب الرفض (اختياري)">
                            <button type="submit" class="admin-btn admin-btn-danger">رفض</button>
                        </form>
                    </div>
                @endcan
            @endif
        </div>
    </div>
@stop
