@extends('employee.layouts.master')

@section('page-title')
    عقدي
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">عقدي</h5>
                </div>
            </div>

            @if($currentContract)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">العقد الحالي</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">نوع العقد</label>
                                <p class="form-control-plaintext">{{ $currentContract->contract_type_label }}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">الحالة</label>
                                <p><span class="badge bg-{{ $currentContract->status === 'active' ? 'success' : 'secondary' }}">{{ $currentContract->status_label }}</span></p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">تاريخ البداية</label>
                                <p class="form-control-plaintext">{{ $currentContract->start_date?->format('Y-m-d') ?? '-' }}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">تاريخ النهاية</label>
                                <p class="form-control-plaintext">{{ $currentContract->end_date?->format('Y-m-d') ?? '-' }}</p>
                            </div>
                            @if($currentContract->end_date && $currentContract->days_remaining !== null)
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">الأيام المتبقية</label>
                                    <p class="form-control-plaintext">{{ $currentContract->days_remaining }} يوم</p>
                                </div>
                            @endif
                            @if($currentContract->notes)
                                <div class="col-12 mb-3">
                                    <label class="form-label fw-bold">ملاحظات</label>
                                    <p class="form-control-plaintext">{{ $currentContract->notes }}</p>
                                </div>
                            @endif
                            @if($currentContract->document_path)
                                <div class="col-12">
                                    <a href="{{ asset('storage/' . $currentContract->document_path) }}" target="_blank" class="btn btn-primary">
                                        <i class="fas fa-download me-2"></i>تحميل مستند العقد
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @else
                <div class="card mb-4">
                    <div class="card-body text-center text-muted">
                        <p class="mb-0">لا يوجد عقد نشط حالياً.</p>
                    </div>
                </div>
            @endif

            @if($contracts->isNotEmpty())
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">سجل العقود</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>نوع العقد</th>
                                        <th>تاريخ البداية</th>
                                        <th>تاريخ النهاية</th>
                                        <th>الحالة</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($contracts as $c)
                                        <tr>
                                            <td>{{ $c->contract_type_label }}</td>
                                            <td>{{ $c->start_date?->format('Y-m-d') ?? '-' }}</td>
                                            <td>{{ $c->end_date?->format('Y-m-d') ?? '-' }}</td>
                                            <td><span class="badge bg-{{ $c->status === 'active' ? 'success' : 'secondary' }}">{{ $c->status_label }}</span></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@stop
