@extends('admin.layouts.master')

@section('page-title')
    تفاصيل المستند
@stop

@section('css')
    <style>
        .doc-show-hero {
            background: linear-gradient(145deg, var(--bs-primary) 0%, rgba(var(--bs-primary-rgb), 0.88) 55%, rgb(15, 76, 129) 100%);
            color: #fff;
            border: none;
        }
        .doc-show-hero .text-white-75 { color: rgba(255,255,255,.85) !important; }
        .doc-meta-item {
            padding: 0.65rem 0;
            border-bottom: 1px solid var(--bs-border-color-translucent);
        }
        .doc-meta-item:last-child { border-bottom: 0; }
    </style>
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">تفاصيل المستند</h5>
                    <p class="text-muted small mb-0">{{ $document->title }} — {{ $document->employee->full_name ?? '' }}</p>
                </div>
                <div class="d-flex flex-wrap gap-2 mt-2 mt-md-0">
                    <a href="{{ route('admin.employee-documents.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-right me-1"></i>العودة للقائمة
                    </a>
                    @can('employee-document-edit')
                        <a href="{{ route('admin.employee-documents.edit', $document->id) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit me-1"></i>تعديل
                        </a>
                    @endcan
                </div>
            </div>

            <div class="row g-3">
                <div class="col-lg-4">
                    <div class="card doc-show-hero shadow-sm h-100">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex align-items-start gap-3 mb-3">
                                <span class="avatar avatar-md bg-white bg-opacity-25 rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width:3rem;height:3rem;">
                                    <i class="fas fa-file-alt fs-4"></i>
                                </span>
                                <div class="min-w-0">
                                    <div class="text-white-75 small mb-1">المستند</div>
                                    <div class="fw-semibold fs-6 text-truncate">{{ $document->title }}</div>
                                    <div class="small text-white-75">{{ $document->document_type_name_ar }}</div>
                                </div>
                            </div>
                            <div class="mb-3 pb-3 border-bottom border-white border-opacity-25">
                                <div class="text-white-75 small mb-1"><i class="fas fa-user me-1"></i>الموظف</div>
                                <div class="fs-5 fw-semibold">{{ $document->employee->full_name ?? '—' }}</div>
                                <div class="small text-white-75 font-monospace">{{ $document->employee->employee_code ?? '—' }}</div>
                            </div>
                            <div class="mb-3">
                                <div class="text-white-75 small mb-2">الحالة</div>
                                @if ($document->status == 'active')
                                    <span class="badge bg-success fs-14 px-3 py-2">نشط</span>
                                @elseif ($document->status == 'expired')
                                    <span class="badge bg-danger fs-14 px-3 py-2">منتهي</span>
                                @else
                                    <span class="badge bg-warning text-dark fs-14 px-3 py-2">قيد الانتظار</span>
                                @endif
                                @if ($document->is_expired)
                                    <span class="badge bg-danger bg-opacity-75 fs-12 px-2 py-1 ms-1">منتهي الصلاحية</span>
                                @endif
                            </div>
                            <div class="mt-auto pt-3 border-top border-white border-opacity-25">
                                <a href="{{ route('admin.employee-documents.download', $document->id) }}" class="btn btn-light btn-sm w-100">
                                    <i class="fas fa-download me-1"></i>تحميل المستند
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-light py-3 border-bottom">
                            <h6 class="mb-0 fw-semibold">
                                <i class="fas fa-info-circle text-primary me-2"></i>بيانات المستند
                            </h6>
                            <small class="text-muted">معلومات تفصيلية عن المستند وصلاحية الملف</small>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <tbody>
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle" style="width:40%">
                                                <i class="fas fa-file-alt text-muted me-2"></i>نوع المستند
                                            </th>
                                            <td class="pe-4 py-3 align-middle">
                                                <span class="badge bg-info-subtle text-dark border">{{ $document->document_type_name_ar }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-calendar-plus text-muted me-2"></i>تاريخ الإصدار
                                            </th>
                                            <td class="pe-4 py-3 align-middle fw-semibold">{{ $document->issue_date ? $document->issue_date->format('Y-m-d') : '—' }}</td>
                                        </tr>
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-calendar-xmark text-muted me-2"></i>تاريخ الانتهاء
                                            </th>
                                            <td class="pe-4 py-3 align-middle fw-semibold {{ $document->is_expired ? 'text-danger' : '' }}">
                                                {{ $document->expiry_date ? $document->expiry_date->format('Y-m-d') : 'لا يوجد' }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-file text-muted me-2"></i>اسم الملف
                                            </th>
                                            <td class="pe-4 py-3 align-middle font-monospace small">{{ $document->file_name }}</td>
                                        </tr>
                                        @if ($document->description)
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-align-left text-muted me-2"></i>الوصف
                                            </th>
                                            <td class="pe-4 py-3 align-middle">{{ $document->description }}</td>
                                        </tr>
                                        @endif
                                        @if ($document->notes)
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-sticky-note text-muted me-2"></i>ملاحظات
                                            </th>
                                            <td class="pe-4 py-3 align-middle">{{ $document->notes }}</td>
                                        </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-3 mt-1">
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-light py-3">
                            <h6 class="mb-0 fw-semibold">
                                <i class="fas fa-clock-rotate-left text-primary me-2"></i>بيانات إضافية
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row row-cols-1 row-cols-md-2 row-cols-xl-4 g-0">
                                <div class="col border-bottom border-end-md p-3">
                                    <div class="small text-muted mb-1"><i class="fas fa-user-pen me-1"></i>أنشأ بواسطة</div>
                                    <div class="fw-semibold">{{ $document->creator->name ?? '—' }}</div>
                                </div>
                                <div class="col border-bottom p-3">
                                    <div class="small text-muted mb-1"><i class="far fa-clock me-1"></i>تاريخ الإنشاء</div>
                                    <div class="fw-semibold font-monospace small">{{ $document->created_at ? $document->created_at->format('Y-m-d H:i') : '—' }}</div>
                                </div>
                                <div class="col border-bottom border-end-md border-md-bottom-0 p-3">
                                    <div class="small text-muted mb-1"><i class="fas fa-pen-to-square me-1"></i>آخر تحديث</div>
                                    <div class="fw-semibold font-monospace small">{{ $document->updated_at ? $document->updated_at->format('Y-m-d H:i') : '—' }}</div>
                                </div>
                                <div class="col border-bottom border-md-bottom-0 p-3">
                                    <div class="small text-muted mb-1"><i class="fas fa-hashtag me-1"></i>رقم المستند</div>
                                    <div class="fw-semibold font-monospace small">#{{ $document->id }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
