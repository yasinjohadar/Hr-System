@extends('employee.layouts.master')

@section('page-title')
    الأهداف
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">أهدافي</h5>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">قائمة الأهداف ({{ $goals->count() }})</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @forelse ($goals as $goal)
                            <div class="col-md-6 mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="card-title">{{ $goal->title }}</h6>
                                        <p class="mb-2">
                                            <span class="badge bg-info">{{ $goal->type_name_ar }}</span>
                                            <span class="badge bg-{{ $goal->priority == 'critical' ? 'danger' : ($goal->priority == 'high' ? 'warning' : ($goal->priority == 'medium' ? 'info' : 'secondary')) }}">
                                                {{ $goal->priority_name_ar }}
                                            </span>
                                        </p>
                                        <p class="mb-2"><strong>تاريخ الهدف:</strong> {{ $goal->target_date->format('Y-m-d') }}</p>
                                        <div class="mb-2">
                                            <label class="form-label small">التقدم:</label>
                                            <div class="progress" style="height: 20px;">
                                                <div class="progress-bar" role="progressbar" 
                                                     style="width: {{ $goal->progress_percentage }}%"
                                                     aria-valuenow="{{ $goal->progress_percentage }}" 
                                                     aria-valuemin="0" aria-valuemax="100">
                                                    {{ $goal->progress_percentage }}%
                                                </div>
                                            </div>
                                        </div>
                                        <p class="mb-0">
                                            <span class="badge bg-{{ $goal->status == 'completed' ? 'success' : ($goal->status == 'in_progress' ? 'primary' : ($goal->status == 'cancelled' ? 'danger' : 'secondary')) }}">
                                                {{ $goal->status_name_ar }}
                                            </span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <p class="text-center text-muted">لا توجد أهداف مسجلة</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop


