@extends('admin.layouts.master')

@section('page-title')
    تفاصيل عملية الاستقبال
@stop

@section('css')
    <style>
        .onboarding-process-show-hero {
            background: linear-gradient(145deg, var(--bs-primary) 0%, rgba(var(--bs-primary-rgb), 0.88) 55%, rgb(15, 76, 129) 100%);
            color: #fff;
            border: none;
        }
        .onboarding-process-show-hero .text-white-75 { color: rgba(255,255,255,.85) !important; }
    </style>
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">تفاصيل عملية الاستقبال</h5>
                    <p class="text-muted small mb-0">{{ $process->process_code }} — {{ $process->employee->full_name ?? '—' }}</p>
                </div>
                <div class="d-flex flex-wrap gap-2 mt-2 mt-md-0">
                    <a href="{{ route('admin.onboarding-processes.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-right me-1"></i>العودة للقائمة
                    </a>
                    @can('onboarding-process-edit')
                        <a href="{{ route('admin.onboarding-processes.edit', $process->id) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit me-1"></i>تعديل
                        </a>
                    @endcan
                </div>
            </div>

            <div class="row g-3">
                <div class="col-lg-4">
                    <div class="card onboarding-process-show-hero shadow-sm h-100">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex align-items-start gap-3 mb-3">
                                <span class="avatar avatar-md bg-white bg-opacity-25 rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width:3rem;height:3rem;">
                                    <i class="fas fa-person-walking-arrow-right fs-4"></i>
                                </span>
                                <div class="min-w-0">
                                    <div class="text-white-75 small mb-1">عملية الاستقبال</div>
                                    <div class="fw-semibold fs-6 text-truncate">{{ $process->employee->full_name ?? '—' }}</div>
                                    <div class="small text-white-75 font-monospace">{{ $process->process_code }}</div>
                                </div>
                            </div>
                            <div class="mb-3 pb-3 border-bottom border-white border-opacity-25">
                                <div class="text-white-75 small mb-1"><i class="fas fa-clipboard-check me-1"></i>القالب</div>
                                <div class="fw-semibold">{{ $process->template->name_ar ?? $process->template->name ?? '—' }}</div>
                            </div>
                            <div class="mb-3">
                                <div class="text-white-75 small mb-2">الحالة</div>
                                <span class="badge bg-{{ $process->status == 'completed' ? 'success' : ($process->status == 'in_progress' ? 'primary' : ($process->status == 'on_hold' ? 'warning' : 'secondary')) }} fs-14 px-3 py-2">
                                    {{ $process->status_name_ar }}
                                </span>
                            </div>
                            <div class="mt-auto pt-3 border-top border-white border-opacity-25">
                                <div class="text-white-75 small mb-1">نسبة الإنجاز</div>
                                <div class="fs-3 fw-bold lh-1">{{ $process->completion_percentage }}%</div>
                                <div class="progress mt-2 bg-white bg-opacity-25" style="height:6px;">
                                    <div class="progress-bar bg-white" style="width:{{ $process->completion_percentage }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-light py-3 border-bottom">
                            <h6 class="mb-0 fw-semibold">
                                <i class="fas fa-circle-info text-primary me-2"></i>تفاصيل العملية
                            </h6>
                            <small class="text-muted">التواريخ والمسؤولون</small>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <tbody>
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle" style="width:40%">
                                                <i class="fas fa-user text-muted me-2"></i>الموظف
                                            </th>
                                            <td class="pe-4 py-3 align-middle fw-semibold">{{ $process->employee->full_name ?? '—' }}</td>
                                        </tr>
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-user-check text-muted me-2"></i>المسؤول
                                            </th>
                                            <td class="pe-4 py-3 align-middle fw-semibold">{{ $process->assignedTo->full_name ?? '—' }}</td>
                                        </tr>
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-play text-muted me-2"></i>تاريخ البدء
                                            </th>
                                            <td class="pe-4 py-3 align-middle fw-semibold">{{ $process->start_date->format('Y-m-d') }}</td>
                                        </tr>
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-flag-checkered text-muted me-2"></i>تاريخ الإنجاز المتوقع
                                            </th>
                                            <td class="pe-4 py-3 align-middle fw-semibold">{{ $process->expected_completion_date->format('Y-m-d') }}</td>
                                        </tr>
                                        @if ($process->actual_completion_date)
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-calendar-check text-success me-2"></i>تاريخ الإنجاز الفعلي
                                            </th>
                                            <td class="pe-4 py-3 align-middle fw-semibold text-success">{{ $process->actual_completion_date->format('Y-m-d') }}</td>
                                        </tr>
                                        @endif
                                        @if ($process->creator)
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-user-pen text-muted me-2"></i>أنشأ بواسطة
                                            </th>
                                            <td class="pe-4 py-3 align-middle fw-semibold">{{ $process->creator->name }}</td>
                                        </tr>
                                        @endif
                                        @if ($process->notes)
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-sticky-note text-muted me-2"></i>ملاحظات
                                            </th>
                                            <td class="pe-4 py-3 align-middle">{{ $process->notes }}</td>
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
                <div class="col-md-4">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-light py-3 border-bottom">
                            <h6 class="mb-0 fw-semibold">
                                <i class="fas fa-chart-simple text-primary me-2"></i>إحصائيات المهام
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row row-cols-2 g-0">
                                <div class="col border-bottom border-end p-3 text-center">
                                    <div class="small text-muted mb-1">الإجمالي</div>
                                    <div class="fs-4 fw-bold text-primary">{{ $process->checklists->count() }}</div>
                                </div>
                                <div class="col border-bottom p-3 text-center">
                                    <div class="small text-muted mb-1">مكتملة</div>
                                    <div class="fs-4 fw-bold text-success">{{ $process->checklists->where('status', 'completed')->count() }}</div>
                                </div>
                                <div class="col border-end p-3 text-center">
                                    <div class="small text-muted mb-1">قيد التنفيذ</div>
                                    <div class="fs-4 fw-bold text-primary">{{ $process->checklists->where('status', 'in_progress')->count() }}</div>
                                </div>
                                <div class="col p-3 text-center">
                                    <div class="small text-muted mb-1">معلقة</div>
                                    <div class="fs-4 fw-bold text-warning">{{ $process->checklists->where('status', 'pending')->count() }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @if ($process->template)
                <div class="col-md-4">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-light py-3 border-bottom">
                            <h6 class="mb-0 fw-semibold">
                                <i class="fas fa-clipboard-check text-primary me-2"></i>معلومات القالب
                            </h6>
                        </div>
                        <div class="card-body d-flex flex-column">
                            <div class="fw-semibold mb-1">{{ $process->template->name_ar ?? $process->template->name }}</div>
                            @if ($process->template->description)
                                <div class="text-muted small mb-3">{{ $process->template->description }}</div>
                            @endif
                            @if ($process->template->type_name_ar)
                                <div class="mb-3">
                                    <span class="badge bg-info-subtle text-dark border">{{ $process->template->type_name_ar }}</span>
                                </div>
                            @endif
                            <div class="mt-auto">
                                <a href="{{ route('admin.onboarding-templates.show', $process->template->id) }}" class="btn btn-outline-primary btn-sm w-100">
                                    <i class="fas fa-eye me-1"></i>عرض القالب
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <div class="col-md-{{ $process->template ? '4' : '8' }}">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-light py-3">
                            <h6 class="mb-0 fw-semibold"><i class="fas fa-clock-rotate-left text-primary me-2"></i>بيانات السجل</h6>
                        </div>
                        <div class="card-body">
                            <div class="row row-cols-1 g-0">
                                <div class="col border-bottom p-3">
                                    <div class="small text-muted mb-1"><i class="far fa-clock me-1"></i>تاريخ الإنشاء</div>
                                    <div class="fw-semibold font-monospace small">{{ $process->created_at->format('Y-m-d H:i') }}</div>
                                </div>
                                <div class="col p-3">
                                    <div class="small text-muted mb-1"><i class="fas fa-pen-to-square me-1"></i>آخر تحديث</div>
                                    <div class="fw-semibold font-monospace small">{{ $process->updated_at->format('Y-m-d H:i') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if ($process->checklists && $process->checklists->count() > 0)
            <div class="row g-3 mt-1 mb-4">
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-light py-3 border-bottom">
                            <h6 class="mb-0 fw-semibold">
                                <i class="fas fa-list-check text-primary me-2"></i>قائمة المهام
                                <span class="badge bg-primary ms-2">{{ $process->checklists->count() }}</span>
                            </h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="ps-4">#</th>
                                            <th>المهمة</th>
                                            <th>النوع</th>
                                            <th>الاستحقاق</th>
                                            <th>الإنجاز</th>
                                            <th>الحالة</th>
                                            <th>منفذ بواسطة</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($process->checklists as $index => $checklist)
                                            <tr>
                                                <td class="ps-4 align-middle text-muted small">{{ $index + 1 }}</td>
                                                <td class="align-middle">
                                                    <div class="fw-semibold">{{ $checklist->task->title_ar ?? $checklist->task->title ?? '—' }}</div>
                                                    @if ($checklist->task->description)
                                                        <small class="text-muted">{{ $checklist->task->description }}</small>
                                                    @endif
                                                    @if ($checklist->notes)
                                                        <br><small class="text-muted"><i class="fas fa-sticky-note me-1"></i>{{ $checklist->notes }}</small>
                                                    @endif
                                                </td>
                                                <td class="align-middle">
                                                    <span class="badge bg-info-subtle text-dark border">{{ $checklist->task->task_type_name_ar ?? '—' }}</span>
                                                </td>
                                                <td class="align-middle font-monospace small">{{ $checklist->due_date ? $checklist->due_date->format('Y-m-d') : '—' }}</td>
                                                <td class="align-middle font-monospace small">{{ $checklist->completed_date ? $checklist->completed_date->format('Y-m-d') : '—' }}</td>
                                                <td class="align-middle">
                                                    @switch($checklist->status)
                                                        @case('pending')
                                                            <span class="badge bg-secondary-subtle text-dark border">قيد الانتظار</span>
                                                            @break
                                                        @case('in_progress')
                                                            <span class="badge bg-primary-subtle text-primary border">قيد التنفيذ</span>
                                                            @break
                                                        @case('completed')
                                                            <span class="badge bg-success-subtle text-success border">مكتمل</span>
                                                            @break
                                                        @case('skipped')
                                                            <span class="badge bg-warning-subtle text-dark border">تم تخطيه</span>
                                                            @break
                                                        @default
                                                            <span class="badge bg-light text-dark border">{{ $checklist->status }}</span>
                                                    @endswitch
                                                </td>
                                                <td class="pe-4 align-middle">{{ $checklist->completedBy->name ?? '—' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @else
            <div class="row g-3 mt-1 mb-4">
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-body text-center text-muted py-4">لا توجد مهام في هذه العملية</div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
@stop
