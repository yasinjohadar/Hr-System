@extends('admin.layouts.master')

@section('page-title')
    تفاصيل إعداد الضريبة
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="page-header d-flex justify-content-between align-items-center my-4">
                <h5 class="page-title mb-0">تفاصيل إعداد الضريبة</h5>
                <div>
                    @can('tax-setting-edit')
                    <a href="{{ route('admin.tax-settings.edit', $taxSetting->id) }}" class="btn btn-primary btn-sm">تعديل</a>
                    @endcan
                    <a href="{{ route('admin.tax-settings.index') }}" class="btn btn-secondary btn-sm">رجوع</a>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">معلومات إعداد الضريبة</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">الكود:</label>
                                    <p>{{ $taxSetting->code }}</p>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold">الاسم:</label>
                                    <p>{{ $taxSetting->name_ar ?? $taxSetting->name }}</p>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold">النوع:</label>
                                    <p>
                                        <span class="badge bg-info">{{ $taxSetting->type_name_ar }}</span>
                                    </p>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold">طريقة الحساب:</label>
                                    <p>
                                        @if($taxSetting->calculation_method == 'percentage')
                                            نسبة مئوية
                                        @elseif($taxSetting->calculation_method == 'slab')
                                            شرائح
                                        @else
                                            ثابت
                                        @endif
                                    </p>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold">النسبة/القيمة:</label>
                                    <p>
                                        @if($taxSetting->calculation_method == 'percentage')
                                            {{ $taxSetting->rate }}%
                                        @else
                                            {{ number_format($taxSetting->rate, 2) }}
                                        @endif
                                    </p>
                                </div>

                                @if($taxSetting->min_amount)
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">الحد الأدنى:</label>
                                    <p>{{ number_format($taxSetting->min_amount, 2) }}</p>
                                </div>
                                @endif

                                @if($taxSetting->max_amount)
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">الحد الأقصى:</label>
                                    <p>{{ number_format($taxSetting->max_amount, 2) }}</p>
                                </div>
                                @endif

                                <div class="col-md-6">
                                    <label class="form-label fw-bold">مبلغ الإعفاء:</label>
                                    <p>{{ number_format($taxSetting->exemption_amount, 2) }}</p>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold">الحالة:</label>
                                    <p>
                                        <span class="badge bg-{{ $taxSetting->is_active ? 'success' : 'secondary' }}">
                                            {{ $taxSetting->is_active ? 'نشط' : 'غير نشط' }}
                                        </span>
                                    </p>
                                </div>

                                @if($taxSetting->effective_from)
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">تاريخ البدء:</label>
                                    <p>{{ $taxSetting->effective_from->format('Y-m-d') }}</p>
                                </div>
                                @endif

                                @if($taxSetting->effective_to)
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">تاريخ الانتهاء:</label>
                                    <p>{{ $taxSetting->effective_to->format('Y-m-d') }}</p>
                                </div>
                                @endif

                                @if($taxSetting->description)
                                <div class="col-md-12">
                                    <label class="form-label fw-bold">الوصف:</label>
                                    <p>{{ $taxSetting->description }}</p>
                                </div>
                                @endif

                                @if($taxSetting->slabs && count($taxSetting->slabs) > 0)
                                <div class="col-md-12">
                                    <label class="form-label fw-bold">شرائح الضريبة:</label>
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>من</th>
                                                    <th>إلى</th>
                                                    <th>النسبة %</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($taxSetting->slabs as $slab)
                                                    <tr>
                                                        <td>{{ number_format($slab['min'] ?? 0, 2) }}</td>
                                                        <td>{{ number_format($slab['max'] ?? 0, 2) }}</td>
                                                        <td>{{ $slab['rate'] ?? 0 }}%</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                @endif

                                @if($taxSetting->creator)
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">أنشئ بواسطة:</label>
                                    <p>{{ $taxSetting->creator->name }}</p>
                                </div>
                                @endif

                                <div class="col-md-6">
                                    <label class="form-label fw-bold">تاريخ الإنشاء:</label>
                                    <p>{{ $taxSetting->created_at->format('Y-m-d H:i') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

