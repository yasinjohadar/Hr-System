@extends('admin.layouts.master')

@section('page-title')
    تفاصيل قالب الاستقبال
@stop

@section('css')
    <style>
        .onboarding-show-hero {
            background: linear-gradient(145deg, var(--bs-primary) 0%, rgba(var(--bs-primary-rgb), 0.88) 55%, rgb(15, 76, 129) 100%);
            color: #fff;
            border: none;
        }
        .onboarding-show-hero .text-white-75 { color: rgba(255,255,255,.85) !important; }
    </style>
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">تفاصيل قالب الاستقبال</h5>
                    <p class="text-muted small mb-0">{{ $template->name_ar ?? $template->name }} — {{ $template->type_name_ar }}</p>
                </div>
                <div class="d-flex flex-wrap gap-2 mt-2 mt-md-0">
                    <a href="{{ route('admin.onboarding-templates.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-right me-1"></i>العودة للقائمة
                    </a>
                    @can('onboarding-template-edit')
                        <a href="{{ route('admin.onboarding-templates.edit', $template->id) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit me-1"></i>تعديل
                        </a>
                    @endcan
                </div>
            </div>

            <div class="row g-3">
                <div class="col-lg-4">
                    <div class="card onboarding-show-hero shadow-sm h-100">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex align-items-start gap-3 mb-3">
                                <span class="avatar avatar-md bg-white bg-opacity-25 rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width:3rem;height:3rem;">
                                    <i class="fas fa-clipboard-check fs-4"></i>
                                </span>
                                <div class="min-w-0">
                                    <div class="text-white-75 small mb-1">قالب الاستقبال</div>
                                    <div class="fw-semibold fs-6 text-truncate">{{ $template->name_ar ?? $template->name }}</div>
                                    @if ($template->name_ar && $template->name)
                                        <div class="small text-white-75">{{ $template->name }}</div>
                                    @endif
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
                                <div class="text-white-75 small mb-1">عدد الخطوات</div>
                                <div class="fs-3 fw-bold lh-1">{{ is_array($template->steps) ? count($template->steps) : 0 }}</div>
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
                            <small class="text-muted">معلومات القالب الأساسية</small>
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
                                            <td class="pe-4 py-3 align-middle fw-semibold">{{ $template->type_name_ar }}</td>
                                        </tr>
                                        @if ($template->creator)
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-user-pen text-muted me-2"></i>تم الإنشاء بواسطة
                                            </th>
                                            <td class="pe-4 py-3 align-middle fw-semibold">{{ $template->creator->name }}</td>
                                        </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if ($template->description)
            <div class="row g-3 mt-1">
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-light py-3 border-bottom">
                            <h6 class="mb-0 fw-semibold"><i class="fas fa-align-left text-muted me-2"></i>الوصف</h6>
                        </div>
                        <div class="card-body"><p class="mb-0">{{ $template->description }}</p></div>
                    </div>
                </div>
            </div>
            @endif

            @if ($template->steps && count($template->steps) > 0)
            <div class="row g-3 mt-1">
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-light py-3 border-bottom">
                            <h6 class="mb-0 fw-semibold">
                                <i class="fas fa-list-ol text-primary me-2"></i>الخطوات
                                <span class="badge bg-primary ms-2">{{ count($template->steps) }}</span>
                            </h6>
                            <small class="text-muted">خطوات عملية الاستقبال</small>
                        </div>
                        <div class="card-body p-3">
                            @foreach ($template->steps as $index => $step)
                                <div class="d-flex gap-3 {{ $index < count($template->steps) - 1 ? 'mb-3 pb-3 border-bottom' : '' }}">
                                    <span class="avatar avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width:2rem;height:2rem;">
                                        <span class="fs-14 fw-bold">{{ $index + 1 }}</span>
                                    </span>
                                    <div class="min-w-0">
                                        <div class="fw-semibold">{{ $step['title'] ?? 'بدون عنوان' }}</div>
                                        @if (isset($step['description']) && $step['description'])
                                            <div class="text-muted small mt-1">{{ $step['description'] }}</div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <div class="row g-3 mt-1">
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-light py-3 border-bottom">
                            <h6 class="mb-0 fw-semibold">
                                <i class="fas fa-users-gear text-primary me-2"></i>العمليات المستخدمة
                                <span class="badge bg-info ms-2">{{ $template->processes->count() }}</span>
                            </h6>
                            <small class="text-muted">عمليات الاستقبال المرتبطة بهذا القالب</small>
                        </div>
                        <div class="card-body p-0">
                            @if ($template->processes->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="ps-4">الموظف</th>
                                                <th>تاريخ البداية</th>
                                                <th>الحالة</th>
                                                <th class="pe-4"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($template->processes as $process)
                                                <tr>
                                                    <td class="ps-4 align-middle fw-semibold">{{ $process->employee->full_name ?? '—' }}</td>
                                                    <td class="align-middle">{{ $process->start_date->format('Y-m-d') }}</td>
                                                    <td class="align-middle">
                                                        @switch($process->status)
                                                            @case('completed')
                                                                <span class="badge bg-success-subtle text-success border">مكتمل</span>
                                                                @break
                                                            @case('in_progress')
                                                                <span class="badge bg-primary-subtle text-primary border">قيد التنفيذ</span>
                                                                @break
                                                            @default
                                                                <span class="badge bg-warning-subtle text-dark border">مخطط</span>
                                                        @endswitch
                                                    </td>
                                                    <td class="pe-4 align-middle text-end">
                                                        <a href="{{ route('admin.onboarding-processes.show', $process->id) }}" class="btn btn-sm btn-light">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center text-muted py-4">لا توجد عمليات مرتبطة بهذا القالب</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-3 mt-1 mb-4">
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-light py-3">
                            <h6 class="mb-0 fw-semibold"><i class="fas fa-clock-rotate-left text-primary me-2"></i>بيانات السجل</h6>
                        </div>
                        <div class="card-body">
                            <div class="row row-cols-1 row-cols-md-3 g-0">
                                <div class="col border-bottom border-end-md p-3">
                                    <div class="small text-muted mb-1"><i class="fas fa-users-gear me-1"></i>العمليات المرتبطة</div>
                                    <div class="fw-semibold">{{ $template->processes->count() }}</div>
                                </div>
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
