@extends('admin.layouts.master')

@section('page-title')
    تفاصيل عملية الاستقبال
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">تفاصيل عملية الاستقبال</h5>
                </div>
                <div>
                    <a href="{{ route('admin.onboarding-processes.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-right me-2"></i>العودة للقائمة
                    </a>
                    @can('onboarding-process-edit')
                    <a href="{{ route('admin.onboarding-processes.edit', $process->id) }}" class="btn btn-primary">
                        <i class="fas fa-edit me-2"></i>تعديل
                    </a>
                    @endcan
                </div>
            </div>

            <div class="row">
                <div class="col-xl-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">معلومات العملية</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">كود العملية:</label>
                                    <p class="form-control-plaintext">
                                        <strong>{{ $process->process_code }}</strong>
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">الموظف:</label>
                                    <p class="form-control-plaintext">
                                        <strong>{{ $process->employee->full_name ?? '-' }}</strong>
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">القالب:</label>
                                    <p class="form-control-plaintext">
                                        {{ $process->template->name_ar ?? $process->template->name ?? '-' }}
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">المسؤول:</label>
                                    <p class="form-control-plaintext">
                                        {{ $process->assignedTo->full_name ?? '-' }}
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">تاريخ البدء:</label>
                                    <p class="form-control-plaintext">
                                        {{ $process->start_date->format('Y-m-d') }}
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">تاريخ الإنجاز المتوقع:</label>
                                    <p class="form-control-plaintext">
                                        {{ $process->expected_completion_date->format('Y-m-d') }}
                                    </p>
                                </div>
                                @if ($process->actual_completion_date)
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">تاريخ الإنجاز الفعلي:</label>
                                    <p class="form-control-plaintext">
                                        {{ $process->actual_completion_date->format('Y-m-d') }}
                                    </p>
                                </div>
                                @endif
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">الحالة:</label>
                                    <p class="form-control-plaintext">
                                        <span class="badge bg-{{ $process->status == 'completed' ? 'success' : ($process->status == 'in_progress' ? 'primary' : ($process->status == 'on_hold' ? 'warning' : 'secondary')) }}">
                                            {{ $process->status_name_ar }}
                                        </span>
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">نسبة الإنجاز:</label>
                                    <p class="form-control-plaintext">
                                        <div class="progress" style="height: 25px;">
                                            <div class="progress-bar" role="progressbar" style="width: {{ $process->completion_percentage }}%">
                                                {{ $process->completion_percentage }}%
                                            </div>
                                        </div>
                                    </p>
                                </div>
                                @if ($process->notes)
                                <div class="col-12 mb-3">
                                    <label class="form-label fw-bold">ملاحظات:</label>
                                    <p class="form-control-plaintext">{{ $process->notes }}</p>
                                </div>
                                @endif
                                @if ($process->creator)
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">تم الإنشاء بواسطة:</label>
                                    <p class="form-control-plaintext">{{ $process->creator->name }}</p>
                                </div>
                                @endif
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">تاريخ الإنشاء:</label>
                                    <p class="form-control-plaintext">{{ $process->created_at->format('Y-m-d H:i') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if ($process->checklists && $process->checklists->count() > 0)
                    <div class="card mt-3">
                        <div class="card-header">
                            <h5 class="card-title mb-0">قائمة المهام ({{ $process->checklists->count() }})</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>المهمة</th>
                                            <th>النوع</th>
                                            <th>تاريخ الاستحقاق</th>
                                            <th>تاريخ الإنجاز</th>
                                            <th>الحالة</th>
                                            <th>منفذ بواسطة</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($process->checklists as $index => $checklist)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>
                                                    <strong>{{ $checklist->task->title_ar ?? $checklist->task->title ?? '-' }}</strong>
                                                    @if ($checklist->task->description)
                                                        <br><small class="text-muted">{{ $checklist->task->description }}</small>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="badge bg-info">{{ $checklist->task->task_type_name_ar ?? '-' }}</span>
                                                </td>
                                                <td>{{ $checklist->due_date ? $checklist->due_date->format('Y-m-d') : '-' }}</td>
                                                <td>{{ $checklist->completed_date ? $checklist->completed_date->format('Y-m-d') : '-' }}</td>
                                                <td>
                                                    <span class="badge bg-{{ $checklist->status == 'completed' ? 'success' : ($checklist->status == 'in_progress' ? 'primary' : ($checklist->status == 'skipped' ? 'warning' : 'secondary')) }}">
                                                        @if ($checklist->status == 'pending')
                                                            قيد الانتظار
                                                        @elseif ($checklist->status == 'in_progress')
                                                            قيد التنفيذ
                                                        @elseif ($checklist->status == 'completed')
                                                            مكتمل
                                                        @elseif ($checklist->status == 'skipped')
                                                            تم تخطيه
                                                        @else
                                                            {{ $checklist->status }}
                                                        @endif
                                                    </span>
                                                </td>
                                                <td>{{ $checklist->completedBy->name ?? '-' }}</td>
                                            </tr>
                                            @if ($checklist->notes)
                                            <tr>
                                                <td></td>
                                                <td colspan="6">
                                                    <small class="text-muted"><strong>ملاحظات:</strong> {{ $checklist->notes }}</small>
                                                </td>
                                            </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="card mt-3">
                        <div class="card-body">
                            <p class="text-muted text-center">لا توجد مهام في هذه العملية</p>
                        </div>
                    </div>
                    @endif
                </div>

                <div class="col-xl-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">إحصائيات</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label fw-bold">إجمالي المهام:</label>
                                <p class="form-control-plaintext">{{ $process->checklists->count() }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">المهام المكتملة:</label>
                                <p class="form-control-plaintext">
                                    {{ $process->checklists->where('status', 'completed')->count() }}
                                </p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">المهام قيد التنفيذ:</label>
                                <p class="form-control-plaintext">
                                    {{ $process->checklists->where('status', 'in_progress')->count() }}
                                </p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">المهام المعلقة:</label>
                                <p class="form-control-plaintext">
                                    {{ $process->checklists->where('status', 'pending')->count() }}
                                </p>
                            </div>
                        </div>
                    </div>

                    @if ($process->template)
                    <div class="card mt-3">
                        <div class="card-header">
                            <h5 class="card-title mb-0">معلومات القالب</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label fw-bold">اسم القالب:</label>
                                <p class="form-control-plaintext">
                                    {{ $process->template->name_ar ?? $process->template->name }}
                                </p>
                            </div>
                            @if ($process->template->description)
                            <div class="mb-3">
                                <label class="form-label fw-bold">الوصف:</label>
                                <p class="form-control-plaintext">{{ $process->template->description }}</p>
                            </div>
                            @endif
                            <a href="{{ route('admin.onboarding-templates.show', $process->template->id) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-eye me-2"></i>عرض القالب
                            </a>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@stop
