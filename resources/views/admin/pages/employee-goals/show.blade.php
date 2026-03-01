@extends('admin.layouts.master')

@section('page-title')
    تفاصيل الهدف
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">تفاصيل الهدف</h5>
                </div>
                <div>
                    <a href="{{ route('admin.employee-goals.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-right me-2"></i>العودة للقائمة
                    </a>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">{{ $goal->title }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">الموظف:</label>
                                    <p class="form-control-plaintext">{{ $goal->employee->full_name }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">النوع:</label>
                                    <p class="form-control-plaintext">
                                        <span class="badge bg-info">{{ $goal->type_name_ar }}</span>
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">الأولوية:</label>
                                    <p class="form-control-plaintext">
                                        <span class="badge bg-{{ $goal->priority == 'critical' ? 'danger' : ($goal->priority == 'high' ? 'warning' : ($goal->priority == 'medium' ? 'info' : 'secondary')) }}">
                                            {{ $goal->priority_name_ar }}
                                        </span>
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">الحالة:</label>
                                    <p class="form-control-plaintext">
                                        <span class="badge bg-{{ $goal->status == 'completed' ? 'success' : ($goal->status == 'in_progress' ? 'primary' : ($goal->status == 'cancelled' ? 'danger' : 'secondary')) }}">
                                            {{ $goal->status_name_ar }}
                                        </span>
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">تاريخ البدء:</label>
                                    <p class="form-control-plaintext">{{ $goal->start_date->format('Y-m-d') }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">تاريخ الهدف:</label>
                                    <p class="form-control-plaintext">{{ $goal->target_date->format('Y-m-d') }}</p>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label class="form-label fw-bold">التقدم:</label>
                                    <div class="progress" style="height: 30px;">
                                        <div class="progress-bar" role="progressbar" 
                                             style="width: {{ $goal->progress_percentage }}%"
                                             aria-valuenow="{{ $goal->progress_percentage }}" 
                                             aria-valuemin="0" aria-valuemax="100">
                                            {{ $goal->progress_percentage }}%
                                        </div>
                                    </div>
                                </div>
                                @if ($goal->description)
                                <div class="col-12 mb-3">
                                    <label class="form-label fw-bold">الوصف:</label>
                                    <p class="form-control-plaintext">{{ $goal->description }}</p>
                                </div>
                                @endif
                                @if ($goal->success_criteria)
                                <div class="col-12 mb-3">
                                    <label class="form-label fw-bold">معايير النجاح:</label>
                                    <p class="form-control-plaintext">{{ $goal->success_criteria }}</p>
                                </div>
                                @endif
                            </div>
                            <div class="mt-3">
                                @can('employee-goal-edit')
                                <a href="{{ route('admin.employee-goals.edit', $goal->id) }}" class="btn btn-warning">
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


