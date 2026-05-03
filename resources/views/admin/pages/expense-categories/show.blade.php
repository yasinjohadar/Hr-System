@extends('admin.layouts.master')

@section('page-title')
    تفاصيل تصنيف المصروف
@stop

@section('css')
    <style>
        .expense-cat-show-hero {
            background: linear-gradient(145deg, var(--bs-primary) 0%, rgba(var(--bs-primary-rgb), 0.88) 55%, rgb(15, 76, 129) 100%);
            color: #fff;
            border: none;
        }
        .expense-cat-show-hero .text-white-75 { color: rgba(255,255,255,.85) !important; }
    </style>
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">تفاصيل تصنيف المصروف</h5>
                    <p class="text-muted small mb-0">{{ $category->name_ar ?? $category->name }}</p>
                </div>
                <div class="d-flex flex-wrap gap-2 mt-2 mt-md-0">
                    <a href="{{ route('admin.expense-categories.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-right me-1"></i>العودة للقائمة
                    </a>
                    @can('expense-category-edit')
                        <a href="{{ route('admin.expense-categories.edit', $category->id) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit me-1"></i>تعديل
                        </a>
                    @endcan
                </div>
            </div>

            <div class="row g-3">
                <div class="col-lg-4">
                    <div class="card expense-cat-show-hero shadow-sm h-100">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex align-items-start gap-3 mb-3">
                                <span class="avatar avatar-md bg-white bg-opacity-25 rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width:3rem;height:3rem;">
                                    <i class="fas fa-tags fs-4"></i>
                                </span>
                                <div class="min-w-0">
                                    <div class="text-white-75 small mb-1">تصنيف المصروف</div>
                                    <div class="fw-semibold fs-6 text-truncate">{{ $category->name_ar ?? $category->name }}</div>
                                    @if ($category->code) <div class="small text-white-75 font-monospace">{{ $category->code }}</div> @endif
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="text-white-75 small mb-2">الحالة</div>
                                <span class="badge bg-{{ $category->is_active ? 'success' : 'danger' }} fs-14 px-3 py-2">
                                    {{ $category->is_active ? 'نشط' : 'غير نشط' }}
                                </span>
                            </div>
                            <div class="mt-auto pt-3 border-top border-white border-opacity-25">
                                <div class="text-white-75 small mb-1">الحد الأقصى</div>
                                <div class="fs-3 fw-bold lh-1">{{ $category->max_amount ? number_format($category->max_amount, 2) : 'غير محدد' }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-light py-3 border-bottom">
                            <h6 class="mb-0 fw-semibold"><i class="fas fa-circle-info text-primary me-2"></i>إعدادات التصنيف</h6>
                            <small class="text-muted">الموافقات والمتطلبات</small>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <tbody>
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle" style="width:40%">
                                                <i class="fas fa-layer-group text-muted me-2"></i>مستويات الموافقة
                                            </th>
                                            <td class="pe-4 py-3 align-middle fw-semibold">{{ $category->approval_levels }}</td>
                                        </tr>
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-receipt text-muted me-2"></i>يتطلب إيصال
                                            </th>
                                            <td class="pe-4 py-3 align-middle">
                                                <span class="badge bg-{{ $category->requires_receipt ? 'success-subtle text-success' : 'secondary-subtle text-dark' }} border">
                                                    {{ $category->requires_receipt ? 'نعم' : 'لا' }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-user-check text-muted me-2"></i>يتطلب موافقة
                                            </th>
                                            <td class="pe-4 py-3 align-middle">
                                                <span class="badge bg-{{ $category->requires_approval ? 'success-subtle text-success' : 'secondary-subtle text-dark' }} border">
                                                    {{ $category->requires_approval ? 'نعم' : 'لا' }}
                                                </span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if ($category->description)
            <div class="row g-3 mt-1">
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-light py-3 border-bottom"><h6 class="mb-0 fw-semibold"><i class="fas fa-align-left text-muted me-2"></i>الوصف</h6></div>
                        <div class="card-body"><p class="mb-0">{{ $category->description }}</p></div>
                    </div>
                </div>
            </div>
            @endif

            <div class="row g-3 mt-1 mb-4">
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-light py-3"><h6 class="mb-0 fw-semibold"><i class="fas fa-clock-rotate-left text-primary me-2"></i>بيانات السجل</h6></div>
                        <div class="card-body">
                            <div class="row row-cols-1 row-cols-md-3 g-0">
                                <div class="col border-bottom border-end-md p-3">
                                    <div class="small text-muted mb-1"><i class="fas fa-file-invoice me-1"></i>عدد الطلبات</div>
                                    <div class="fw-semibold">{{ $category->expenseRequests->count() }}</div>
                                </div>
                                <div class="col border-bottom border-end-md p-3">
                                    <div class="small text-muted mb-1"><i class="far fa-clock me-1"></i>تاريخ الإنشاء</div>
                                    <div class="fw-semibold font-monospace small">{{ $category->created_at->format('Y-m-d H:i') }}</div>
                                </div>
                                <div class="col border-bottom p-3">
                                    <div class="small text-muted mb-1"><i class="fas fa-pen-to-square me-1"></i>آخر تحديث</div>
                                    <div class="fw-semibold font-monospace small">{{ $category->updated_at->format('Y-m-d H:i') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
