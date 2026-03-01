@extends('admin.layouts.master')

@section('page-title')
    تفاصيل قالب الاستقبال
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">تفاصيل قالب الاستقبال</h5>
                </div>
                <div>
                    <a href="{{ route('admin.onboarding-templates.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-right me-2"></i>العودة للقائمة
                    </a>
                    @can('onboarding-template-edit')
                    <a href="{{ route('admin.onboarding-templates.edit', $template->id) }}" class="btn btn-primary">
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
                                    <label class="form-label fw-bold">النوع:</label>
                                    <p class="form-control-plaintext">
                                        <span class="badge bg-info">{{ $template->type_name_ar }}</span>
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

                    @if ($template->steps && count($template->steps) > 0)
                    <div class="card mt-3">
                        <div class="card-header">
                            <h5 class="card-title mb-0">الخطوات ({{ count($template->steps) }})</h5>
                        </div>
                        <div class="card-body">
                            <div class="list-group">
                                @foreach ($template->steps as $index => $step)
                                    <div class="list-group-item">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1">
                                                    <span class="badge bg-primary me-2">{{ $index + 1 }}</span>
                                                    {{ $step['title'] ?? 'بدون عنوان' }}
                                                </h6>
                                                @if (isset($step['description']) && $step['description'])
                                                    <p class="mb-0 text-muted">{{ $step['description'] }}</p>
                                                @endif
                                            </div>
                                        </div>
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
                            <h5 class="card-title mb-0">العمليات المستخدمة ({{ $template->processes->count() }})</h5>
                        </div>
                        <div class="card-body">
                            @if ($template->processes->count() > 0)
                                <div class="list-group">
                                    @foreach ($template->processes as $process)
                                        <a href="{{ route('admin.onboarding-processes.show', $process->id) }}" 
                                           class="list-group-item list-group-item-action">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <strong>{{ $process->employee->full_name ?? 'غير محدد' }}</strong>
                                                    <br>
                                                    <small class="text-muted">
                                                        {{ $process->start_date->format('Y-m-d') }}
                                                    </small>
                                                </div>
                                                <span class="badge bg-{{ $process->status == 'completed' ? 'success' : ($process->status == 'in_progress' ? 'primary' : 'warning') }}">
                                                    {{ $process->status == 'completed' ? 'مكتمل' : ($process->status == 'in_progress' ? 'قيد التنفيذ' : 'مخطط') }}
                                                </span>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-muted text-center">لا توجد عمليات مستخدمة لهذا القالب</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

