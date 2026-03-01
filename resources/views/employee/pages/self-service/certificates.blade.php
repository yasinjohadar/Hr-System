@extends('employee.layouts.master')

@section('page-title')
    الشهادات
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">شهاداتي</h5>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">قائمة الشهادات ({{ $certificates->count() }})</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @forelse ($certificates as $certificate)
                            <div class="col-md-6 mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="card-title">{{ $certificate->certificate_name_ar ?? $certificate->certificate_name }}</h6>
                                        <p class="mb-2"><strong>الجهة المانحة:</strong> {{ $certificate->issuing_organization }}</p>
                                        @if ($certificate->certificate_number)
                                            <p class="mb-2"><strong>رقم الشهادة:</strong> {{ $certificate->certificate_number }}</p>
                                        @endif
                                        <p class="mb-2"><strong>تاريخ الإصدار:</strong> {{ $certificate->issue_date->format('Y-m-d') }}</p>
                                        <p class="mb-2">
                                            <strong>تاريخ الانتهاء:</strong> 
                                            @if ($certificate->does_not_expire)
                                                <span class="text-muted">لا تنتهي</span>
                                            @elseif ($certificate->expiry_date)
                                                <span class="{{ $certificate->isExpired() ? 'text-danger' : '' }}">
                                                    {{ $certificate->expiry_date->format('Y-m-d') }}
                                                </span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </p>
                                        <p class="mb-0">
                                            <span class="badge bg-{{ $certificate->status == 'active' ? 'success' : 'danger' }}">
                                                {{ $certificate->status == 'active' ? 'نشط' : 'منتهي' }}
                                            </span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <p class="text-center text-muted">لا توجد شهادات مسجلة</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop


