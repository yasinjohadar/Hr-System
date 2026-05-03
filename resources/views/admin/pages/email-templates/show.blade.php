@extends('admin.layouts.master')

@section('page-title')
    تفاصيل قالب البريد الإلكتروني
@stop

@section('css')
    <style>
        .email-template-show-hero {
            background: linear-gradient(145deg, var(--bs-primary) 0%, rgba(var(--bs-primary-rgb), 0.88) 55%, rgb(15, 76, 129) 100%);
            color: #fff;
            border: none;
        }
        .email-template-show-hero .text-white-75 { color: rgba(255,255,255,.85) !important; }
    </style>
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">تفاصيل قالب البريد الإلكتروني</h5>
                    <p class="text-muted small mb-0">{{ $template->name_ar ?? $template->name }}@if ($template->code) — {{ $template->code }}@endif</p>
                </div>
                <div class="d-flex flex-wrap gap-2 mt-2 mt-md-0">
                    <a href="{{ route('admin.email-templates.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-right me-1"></i>العودة للقائمة
                    </a>
                    @can('email-template-edit')
                        <a href="{{ route('admin.email-templates.edit', $template->id) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit me-1"></i>تعديل
                        </a>
                    @endcan
                </div>
            </div>

            <div class="row g-3">
                <div class="col-lg-4">
                    <div class="card email-template-show-hero shadow-sm h-100">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex align-items-start gap-3 mb-3">
                                <span class="avatar avatar-md bg-white bg-opacity-25 rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width:3rem;height:3rem;">
                                    <i class="fas fa-envelope fs-4"></i>
                                </span>
                                <div class="min-w-0">
                                    <div class="text-white-75 small mb-1">قالب البريد</div>
                                    <div class="fw-semibold fs-6 text-truncate">{{ $template->name_ar ?? $template->name }}</div>
                                    @if ($template->code) <div class="small text-white-75 font-monospace">{{ $template->code }}</div> @endif
                                </div>
                            </div>
                            <div class="mb-3 pb-3 border-bottom border-white border-opacity-25">
                                <div class="text-white-75 small mb-1"><i class="fas fa-shapes me-1"></i>النوع</div>
                                <div class="fw-semibold">{{ $template->type_name_ar }}</div>
                            </div>
                            <div class="mb-3">
                                <div class="text-white-75 small mb-2">الحالة</div>
                                <span class="badge bg-{{ $template->is_active ? 'success' : 'danger' }} fs-14 px-3 py-2">
                                    {{ $template->is_active ? 'نشط' : 'غير نشط' }}
                                </span>
                            </div>
                            <div class="mt-auto pt-3 border-top border-white border-opacity-25">
                                <div class="text-white-75 small mb-1">المتغيرات</div>
                                <div class="fs-3 fw-bold lh-1">{{ is_array($template->variables) ? count($template->variables) : 0 }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-light py-3 border-bottom">
                            <h6 class="mb-0 fw-semibold">
                                <i class="fas fa-circle-info text-primary me-2"></i>بيانات القالب
                            </h6>
                            <small class="text-muted">تفاصيل القالب الأساسية</small>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <tbody>
                                        @if ($template->name_ar && $template->name)
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle" style="width:40%">
                                                <i class="fas fa-language text-muted me-2"></i>الاسم (EN)
                                            </th>
                                            <td class="pe-4 py-3 align-middle fw-semibold">{{ $template->name }}</td>
                                        </tr>
                                        @endif
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-shapes text-muted me-2"></i>النوع
                                            </th>
                                            <td class="pe-4 py-3 align-middle">
                                                <span class="badge bg-info-subtle text-dark border">{{ $template->type_name_ar }}</span>
                                            </td>
                                        </tr>
                                        @if ($template->subject_ar || $template->subject)
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-heading text-muted me-2"></i>الموضوع
                                            </th>
                                            <td class="pe-4 py-3 align-middle fw-semibold">{{ $template->subject_ar ?? $template->subject }}</td>
                                        </tr>
                                        @endif
                                        @if ($template->creator)
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-user-pen text-muted me-2"></i>أنشأ بواسطة
                                            </th>
                                            <td class="pe-4 py-3 align-middle fw-semibold">{{ $template->creator->name }}</td>
                                        </tr>
                                        @endif
                                        @if (is_array($template->variables) && count($template->variables) > 0)
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-code text-muted me-2"></i>المتغيرات
                                            </th>
                                            <td class="pe-4 py-3 align-middle">
                                                @foreach ($template->variables as $variable)
                                                    <code class="me-1">{{ $variable }}</code>
                                                @endforeach
                                            </td>
                                        </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if ($template->body_ar || $template->body)
            <div class="row g-3 mt-1">
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-light py-3 border-bottom">
                            <h6 class="mb-0 fw-semibold"><i class="fas fa-file-lines text-muted me-2"></i>محتوى القالب</h6>
                        </div>
                        <div class="card-body">
                            @if ($template->body_ar)
                                <div class="mb-3 pb-3 border-bottom">
                                    <div class="small text-muted mb-2 fw-semibold">النص العربي</div>
                                    <div class="bg-light rounded-3 p-3" style="white-space:pre-wrap;">{{ $template->body_ar }}</div>
                                </div>
                            @endif
                            @if ($template->body)
                                <div>
                                    <div class="small text-muted mb-2 fw-semibold">النص الإنجليزي</div>
                                    <div class="bg-light rounded-3 p-3" style="white-space:pre-wrap;">{{ $template->body }}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <div class="row g-3 mt-1 mb-4">
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-light py-3">
                            <h6 class="mb-0 fw-semibold"><i class="fas fa-clock-rotate-left text-primary me-2"></i>بيانات السجل</h6>
                        </div>
                        <div class="card-body">
                            <div class="row row-cols-1 row-cols-md-2 g-0">
                                <div class="col border-bottom border-end-md p-3">
                                    <div class="small text-muted mb-1"><i class="far fa-clock me-1"></i>تاريخ الإنشاء</div>
                                    <div class="fw-semibold font-monospace small">{{ $template->created_at->format('Y-m-d H:i') }}</div>
                                </div>
                                <div class="col border-bottom p-3">
                                    <div class="small text-muted mb-1"><i class="fas fa-pen-to-square me-1"></i>آخر تحديث</div>
                                    <div class="fw-semibold font-monospace small">{{ $template->updated_at->format('Y-m-d H:i') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
