@extends('admin.layouts.master')

@section('page-title')
    تفاصيل المستند
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">تفاصيل المستند</h5>
                </div>
                <div>
                    <a href="{{ route('admin.employee-documents.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-right me-2"></i>العودة للقائمة
                    </a>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">{{ $document->title }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">الموظف:</label>
                                    <p class="form-control-plaintext">{{ $document->employee->full_name }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">نوع المستند:</label>
                                    <p class="form-control-plaintext">
                                        <span class="badge bg-info">{{ $document->document_type_name_ar }}</span>
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">تاريخ الإصدار:</label>
                                    <p class="form-control-plaintext">{{ $document->issue_date ? $document->issue_date->format('Y-m-d') : '-' }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">تاريخ انتهاء الصلاحية:</label>
                                    <p class="form-control-plaintext {{ $document->is_expired ? 'text-danger' : '' }}">
                                        {{ $document->expiry_date ? $document->expiry_date->format('Y-m-d') : 'لا يوجد' }}
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">الحالة:</label>
                                    <p class="form-control-plaintext">
                                        <span class="badge bg-{{ $document->status == 'active' ? 'success' : ($document->status == 'expired' ? 'danger' : 'warning') }}">
                                            {{ $document->status == 'active' ? 'نشط' : ($document->status == 'expired' ? 'منتهي' : 'قيد الانتظار') }}
                                        </span>
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">اسم الملف:</label>
                                    <p class="form-control-plaintext">{{ $document->file_name }}</p>
                                </div>
                                @if ($document->description)
                                <div class="col-12 mb-3">
                                    <label class="form-label fw-bold">الوصف:</label>
                                    <p class="form-control-plaintext">{{ $document->description }}</p>
                                </div>
                                @endif
                                @if ($document->notes)
                                <div class="col-12 mb-3">
                                    <label class="form-label fw-bold">ملاحظات:</label>
                                    <p class="form-control-plaintext">{{ $document->notes }}</p>
                                </div>
                                @endif
                            </div>
                            <div class="mt-3">
                                <a href="{{ route('admin.employee-documents.download', $document->id) }}" class="btn btn-primary">
                                    <i class="fas fa-download me-2"></i>تحميل المستند
                                </a>
                                @can('employee-document-edit')
                                <a href="{{ route('admin.employee-documents.edit', $document->id) }}" class="btn btn-warning">
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


