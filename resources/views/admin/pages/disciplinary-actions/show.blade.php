@extends('admin.layouts.master')

@section('page-title')
    تفاصيل الإجراء التأديبي
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">تفاصيل الإجراء التأديبي</h5>
                </div>
                <div>
                    <a href="{{ route('admin.disciplinary-actions.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-right me-2"></i>العودة للقائمة
                    </a>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">{{ $disciplinaryAction->name_ar ?? $disciplinaryAction->name }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">الاسم:</label>
                                    <p class="form-control-plaintext">{{ $disciplinaryAction->name }}</p>
                                </div>
                                @if ($disciplinaryAction->name_ar)
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">الاسم (عربي):</label>
                                    <p class="form-control-plaintext">{{ $disciplinaryAction->name_ar }}</p>
                                </div>
                                @endif
                                @if ($disciplinaryAction->code)
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">الكود:</label>
                                    <p class="form-control-plaintext">{{ $disciplinaryAction->code }}</p>
                                </div>
                                @endif
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">نوع الإجراء:</label>
                                    <p class="form-control-plaintext">
                                        <span class="badge bg-info">{{ $disciplinaryAction->action_type_name_ar }}</span>
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">مستوى الخطورة:</label>
                                    <p class="form-control-plaintext">
                                        <span class="badge bg-{{ $disciplinaryAction->severity_level >= 4 ? 'danger' : ($disciplinaryAction->severity_level >= 3 ? 'warning' : 'info') }}">
                                            {{ $disciplinaryAction->severity_level_name_ar }}
                                        </span>
                                    </p>
                                </div>
                                @if ($disciplinaryAction->deduction_amount)
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">مبلغ الخصم:</label>
                                    <p class="form-control-plaintext">{{ number_format($disciplinaryAction->deduction_amount, 2) }} ر.س</p>
                                </div>
                                @endif
                                @if ($disciplinaryAction->suspension_days)
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">أيام الإيقاف:</label>
                                    <p class="form-control-plaintext">{{ $disciplinaryAction->suspension_days }} يوم</p>
                                </div>
                                @endif
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">يتطلب موافقة:</label>
                                    <p class="form-control-plaintext">
                                        <span class="badge bg-{{ $disciplinaryAction->requires_approval ? 'warning' : 'secondary' }}">
                                            {{ $disciplinaryAction->requires_approval ? 'نعم' : 'لا' }}
                                        </span>
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">الحالة:</label>
                                    <p class="form-control-plaintext">
                                        <span class="badge bg-{{ $disciplinaryAction->is_active ? 'success' : 'danger' }}">
                                            {{ $disciplinaryAction->is_active ? 'نشط' : 'غير نشط' }}
                                        </span>
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">عدد الاستخدامات:</label>
                                    <p class="form-control-plaintext">{{ $disciplinaryAction->employeeViolations->count() }}</p>
                                </div>
                                @if ($disciplinaryAction->description)
                                <div class="col-12 mb-3">
                                    <label class="form-label fw-bold">الوصف:</label>
                                    <p class="form-control-plaintext">{{ $disciplinaryAction->description }}</p>
                                </div>
                                @endif
                            </div>
                            <div class="mt-3">
                                @can('disciplinary-action-edit')
                                <a href="{{ route('admin.disciplinary-actions.edit', $disciplinaryAction->id) }}" class="btn btn-info">
                                    <i class="fas fa-edit me-2"></i>تعديل
                                </a>
                                @endcan
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

