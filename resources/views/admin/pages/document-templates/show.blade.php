@extends('admin.layouts.master')

@section('page-title')
    تفاصيل قالب المستند
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">تفاصيل قالب المستند</h5>
                </div>
                <div>
                    <a href="{{ route('admin.document-templates.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-right me-2"></i>العودة للقائمة
                    </a>
                    @can('document-template-edit')
                    <a href="{{ route('admin.document-templates.edit', $template->id) }}" class="btn btn-primary">
                        <i class="fas fa-edit me-2"></i>تعديل
                    </a>
                    @endcan
                </div>
            </div>

            <div class="row">
                <div class="col-xl-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">معلومات القالب</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">الاسم:</label>
                                    <p class="form-control-plaintext">
                                        <strong>{{ $template->name_ar ?? $template->name }}</strong>
                                        @if ($template->name_ar && $template->name)
                                            <br><small class="text-muted">{{ $template->name }}</small>
                                        @endif
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">الكود:</label>
                                    <p class="form-control-plaintext">
                                        <span class="badge bg-info">{{ $template->code }}</span>
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">النوع:</label>
                                    <p class="form-control-plaintext">
                                        <span class="badge bg-primary">{{ $template->type_name_ar }}</span>
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">صيغة الملف:</label>
                                    <p class="form-control-plaintext">
                                        <span class="badge bg-secondary">{{ strtoupper($template->file_format) }}</span>
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">الحالة:</label>
                                    <p class="form-control-plaintext">
                                        <span class="badge bg-{{ $template->is_active ? 'success' : 'danger' }}">
                                            {{ $template->is_active ? 'نشط' : 'غير نشط' }}
                                        </span>
                                    </p>
                                </div>
                                @if ($template->description)
                                <div class="col-12 mb-3">
                                    <label class="form-label fw-bold">الوصف:</label>
                                    <p class="form-control-plaintext">{{ $template->description }}</p>
                                </div>
                                @endif
                                @if ($template->creator)
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">تم الإنشاء بواسطة:</label>
                                    <p class="form-control-plaintext">{{ $template->creator->name }}</p>
                                </div>
                                @endif
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">تاريخ الإنشاء:</label>
                                    <p class="form-control-plaintext">{{ $template->created_at->format('Y-m-d H:i') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mt-3">
                        <div class="card-header">
                            <h5 class="card-title mb-0">المحتوى</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label fw-bold">المحتوى (إنجليزي):</label>
                                <div class="border rounded p-3 bg-light" style="max-height: 400px; overflow-y: auto;">
                                    <pre class="mb-0" style="white-space: pre-wrap; font-family: inherit;">{{ $template->content }}</pre>
                                </div>
                            </div>
                            @if ($template->content_ar)
                            <div>
                                <label class="form-label fw-bold">المحتوى (عربي):</label>
                                <div class="border rounded p-3 bg-light" style="max-height: 400px; overflow-y: auto;">
                                    <pre class="mb-0" style="white-space: pre-wrap; font-family: inherit;">{{ $template->content_ar }}</pre>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>

                    @if ($template->variables && count($template->variables) > 0)
                    <div class="card mt-3">
                        <div class="card-header">
                            <h5 class="card-title mb-0">المتغيرات المتاحة</h5>
                        </div>
                        <div class="card-body">
                            <div class="list-group">
                                @foreach ($template->variables as $variable)
                                    <div class="list-group-item">
                                        <code>{$variable}</code>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

                <div class="col-xl-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">معلومات إضافية</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label fw-bold">المتغيرات الشائعة:</label>
                                <div class="list-group list-group-flush">
                                    <div class="list-group-item px-0 py-2">
                                        <code>{employee_name}</code> - اسم الموظف
                                    </div>
                                    <div class="list-group-item px-0 py-2">
                                        <code>{employee_code}</code> - كود الموظف
                                    </div>
                                    <div class="list-group-item px-0 py-2">
                                        <code>{position}</code> - المنصب
                                    </div>
                                    <div class="list-group-item px-0 py-2">
                                        <code>{department}</code> - القسم
                                    </div>
                                    <div class="list-group-item px-0 py-2">
                                        <code>{date}</code> - التاريخ
                                    </div>
                                    <div class="list-group-item px-0 py-2">
                                        <code>{company_name}</code> - اسم الشركة
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

