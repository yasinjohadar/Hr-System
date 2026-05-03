@extends('admin.layouts.master')

@section('page-title') تفاصيل الإجراء التأديبي @stop

@section('css')
    <style>
        .da-show-hero { background: linear-gradient(145deg, var(--bs-primary) 0%, rgba(var(--bs-primary-rgb), 0.88) 55%, rgb(15, 76, 129) 100%); color: #fff; border: none; }
        .da-show-hero .text-white-75 { color: rgba(255,255,255,.85) !important; }
    </style>
@stop

@section('content')
    <div class="main-content app-content"><div class="container-fluid">
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <div class="my-auto"><h5 class="page-title fs-21 mb-1">تفاصيل الإجراء التأديبي</h5><p class="text-muted small mb-0">{{ $disciplinaryAction->name_ar ?? $disciplinaryAction->name }}</p></div>
            <div class="d-flex flex-wrap gap-2 mt-2 mt-md-0">
                <a href="{{ route('admin.disciplinary-actions.index') }}" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-right me-1"></i>العودة</a>
                @can('disciplinary-action-edit')<a href="{{ route('admin.disciplinary-actions.edit', $disciplinaryAction->id) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit me-1"></i>تعديل</a>@endcan
            </div>
        </div>
        <div class="row g-3">
            <div class="col-lg-4">
                <div class="card da-show-hero shadow-sm h-100"><div class="card-body d-flex flex-column">
                    <div class="d-flex align-items-start gap-3 mb-3">
                        <span class="avatar avatar-md bg-white bg-opacity-25 rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width:3rem;height:3rem;"><i class="fas fa-gavel fs-4"></i></span>
                        <div class="min-w-0"><div class="text-white-75 small mb-1">الإجراء التأديبي</div><div class="fw-semibold fs-6 text-truncate">{{ $disciplinaryAction->name_ar ?? $disciplinaryAction->name }}</div>@if($disciplinaryAction->code)<div class="small text-white-75 font-monospace">{{ $disciplinaryAction->code }}</div>@endif</div>
                    </div>
                    <div class="mb-3 pb-3 border-bottom border-white border-opacity-25"><div class="text-white-75 small mb-1"><i class="fas fa-exclamation-circle me-1"></i>مستوى الخطورة</div><span class="badge bg-{{ $disciplinaryAction->severity_level >= 4 ? 'danger' : ($disciplinaryAction->severity_level >= 3 ? 'warning' : 'info') }} fs-14 px-3 py-2">{{ $disciplinaryAction->severity_level_name_ar }}</span></div>
                    <div class="mb-3"><div class="text-white-75 small mb-2">الحالة</div><span class="badge bg-{{ $disciplinaryAction->is_active ? 'success' : 'danger' }} fs-14 px-3 py-2">{{ $disciplinaryAction->is_active ? 'نشط' : 'غير نشط' }}</span></div>
                    <div class="mt-auto pt-3 border-top border-white border-opacity-25"><div class="text-white-75 small mb-1">عدد الاستخدامات</div><div class="display-6 fw-bold lh-1">{{ $disciplinaryAction->employeeViolations->count() }}</div></div>
                </div></div>
            </div>
            <div class="col-lg-8">
                <div class="card shadow-sm border-0 h-100"><div class="card-header bg-light py-3 border-bottom"><h6 class="mb-0 fw-semibold"><i class="fas fa-circle-info text-primary me-2"></i>تفاصيل الإجراء</h6><small class="text-muted">نوع الإجراء والعقوبات</small></div>
                <div class="card-body p-0"><div class="table-responsive"><table class="table table-hover mb-0"><tbody>
                    <tr><th class="ps-4 py-3 align-middle" style="width:40%"><i class="fas fa-tag text-muted me-2"></i>نوع الإجراء</th><td class="pe-4 py-3 align-middle"><span class="badge bg-info-subtle text-dark border">{{ $disciplinaryAction->action_type_name_ar }}</span></td></tr>
                    @if($disciplinaryAction->deduction_amount)<tr><th class="ps-4 py-3 align-middle"><i class="fas fa-coins text-muted me-2"></i>مبلغ الخصم</th><td class="pe-4 py-3 align-middle fw-semibold">{{ number_format($disciplinaryAction->deduction_amount, 2) }} ر.س</td></tr>@endif
                    @if($disciplinaryAction->suspension_days)<tr><th class="ps-4 py-3 align-middle"><i class="fas fa-calendar-xmark text-muted me-2"></i>أيام الإيقاف</th><td class="pe-4 py-3 align-middle fw-semibold">{{ $disciplinaryAction->suspension_days }} يوم</td></tr>@endif
                    <tr><th class="ps-4 py-3 align-middle"><i class="fas fa-user-check text-muted me-2"></i>يتطلب موافقة</th><td class="pe-4 py-3 align-middle"><span class="badge bg-{{ $disciplinaryAction->requires_approval ? 'warning-subtle text-warning-emphasis' : 'secondary-subtle text-dark' }} border">{{ $disciplinaryAction->requires_approval ? 'نعم' : 'لا' }}</span></td></tr>
                </tbody></table></div></div></div>
            </div>
        </div>
        @if($disciplinaryAction->description)<div class="row g-3 mt-1"><div class="col-12"><div class="card shadow-sm border-0"><div class="card-header bg-light py-3"><h6 class="mb-0 fw-semibold"><i class="fas fa-align-left text-muted me-2"></i>الوصف</h6></div><div class="card-body"><p class="mb-0">{{ $disciplinaryAction->description }}</p></div></div></div></div>@endif
        <div class="row g-3 mt-1 mb-4"><div class="col-12"><div class="card shadow-sm border-0"><div class="card-header bg-light py-3"><h6 class="mb-0 fw-semibold"><i class="fas fa-clock-rotate-left text-primary me-2"></i>بيانات السجل</h6></div><div class="card-body"><div class="row row-cols-1 row-cols-md-2 g-0"><div class="col border-bottom border-end-md p-3"><div class="small text-muted mb-1"><i class="far fa-clock me-1"></i>تاريخ الإنشاء</div><div class="fw-semibold font-monospace small">{{ $disciplinaryAction->created_at->format('Y-m-d H:i') }}</div></div><div class="col border-bottom p-3"><div class="small text-muted mb-1"><i class="fas fa-pen-to-square me-1"></i>آخر تحديث</div><div class="fw-semibold font-monospace small">{{ $disciplinaryAction->updated_at->format('Y-m-d H:i') }}</div></div></div></div></div></div></div></div></div>
@stop
