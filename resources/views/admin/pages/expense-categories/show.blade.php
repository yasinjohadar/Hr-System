@extends('admin.layouts.master')

@section('page-title')
    تفاصيل تصنيف المصروف
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">تفاصيل تصنيف المصروف</h5>
                </div>
                <div>
                    <a href="{{ route('admin.expense-categories.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-right me-2"></i>العودة للقائمة
                    </a>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">{{ $category->name_ar ?? $category->name }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">الاسم:</label>
                                    <p class="form-control-plaintext">{{ $category->name }}</p>
                                </div>
                                @if ($category->name_ar)
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">الاسم (عربي):</label>
                                    <p class="form-control-plaintext">{{ $category->name_ar }}</p>
                                </div>
                                @endif
                                @if ($category->code)
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">الكود:</label>
                                    <p class="form-control-plaintext">{{ $category->code }}</p>
                                </div>
                                @endif
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">الحد الأقصى:</label>
                                    <p class="form-control-plaintext">{{ $category->max_amount ? number_format($category->max_amount, 2) . ' ر.س' : 'غير محدد' }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">مستويات الموافقة:</label>
                                    <p class="form-control-plaintext">{{ $category->approval_levels }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">يتطلب إيصال:</label>
                                    <p class="form-control-plaintext">
                                        <span class="badge bg-{{ $category->requires_receipt ? 'success' : 'secondary' }}">
                                            {{ $category->requires_receipt ? 'نعم' : 'لا' }}
                                        </span>
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">يتطلب موافقة:</label>
                                    <p class="form-control-plaintext">
                                        <span class="badge bg-{{ $category->requires_approval ? 'success' : 'secondary' }}">
                                            {{ $category->requires_approval ? 'نعم' : 'لا' }}
                                        </span>
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">الحالة:</label>
                                    <p class="form-control-plaintext">
                                        <span class="badge bg-{{ $category->is_active ? 'success' : 'danger' }}">
                                            {{ $category->is_active ? 'نشط' : 'غير نشط' }}
                                        </span>
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">عدد الطلبات:</label>
                                    <p class="form-control-plaintext">{{ $category->expenseRequests->count() }}</p>
                                </div>
                                @if ($category->description)
                                <div class="col-12 mb-3">
                                    <label class="form-label fw-bold">الوصف:</label>
                                    <p class="form-control-plaintext">{{ $category->description }}</p>
                                </div>
                                @endif
                            </div>
                            <div class="mt-3">
                                @can('expense-category-edit')
                                <a href="{{ route('admin.expense-categories.edit', $category->id) }}" class="btn btn-info">
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

