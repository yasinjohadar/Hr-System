@extends('admin.layouts.master')

@section('page-title')
    تفاصيل خطة التعاقب
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">تفاصيل خطة التعاقب</h5>
                </div>
                <div>
                    <a href="{{ route('admin.succession-plans.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-right me-2"></i>العودة للقائمة
                    </a>
                    @can('succession-plan-edit')
                    <a href="{{ route('admin.succession-plans.edit', $plan->id) }}" class="btn btn-primary">
                        <i class="fas fa-edit me-2"></i>تعديل
                    </a>
                    @endcan
                </div>
            </div>

            <div class="row">
                <div class="col-xl-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">معلومات الخطة</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">كود الخطة:</label>
                                    <p class="form-control-plaintext"><strong>{{ $plan->plan_code }}</strong></p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">المنصب:</label>
                                    <p class="form-control-plaintext">
                                        <strong>
                                            <a href="{{ route('admin.positions.show', $plan->position_id) }}">
                                                {{ $plan->position->title_ar ?? $plan->position->title }}
                                            </a>
                                        </strong>
                                    </p>
                                </div>
                                @if ($plan->currentEmployee)
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">الموظف الحالي:</label>
                                    <p class="form-control-plaintext">
                                        <a href="{{ route('admin.employees.show', $plan->current_employee_id) }}">
                                            {{ $plan->currentEmployee->full_name }}
                                        </a>
                                    </p>
                                </div>
                                @endif
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">الأولوية:</label>
                                    <p class="form-control-plaintext">
                                        <span class="badge bg-{{ $plan->urgency == 'critical' ? 'danger' : ($plan->urgency == 'high' ? 'warning' : ($plan->urgency == 'medium' ? 'info' : 'secondary')) }}">
                                            {{ $plan->urgency_name_ar }}
                                        </span>
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">الحالة:</label>
                                    <p class="form-control-plaintext">
                                        <span class="badge bg-{{ $plan->status == 'completed' ? 'success' : ($plan->status == 'in_progress' ? 'primary' : ($plan->status == 'cancelled' ? 'danger' : 'warning')) }}">
                                            {{ $plan->status_name_ar }}
                                        </span>
                                    </p>
                                </div>
                                @if ($plan->target_date)
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">تاريخ الهدف:</label>
                                    <p class="form-control-plaintext">{{ $plan->target_date->format('Y-m-d') }}</p>
                                </div>
                                @endif
                                @if ($plan->description)
                                <div class="col-12 mb-3">
                                    <label class="form-label fw-bold">الوصف:</label>
                                    <p class="form-control-plaintext">{{ $plan->description }}</p>
                                </div>
                                @endif
                                @if ($plan->notes)
                                <div class="col-12 mb-3">
                                    <label class="form-label fw-bold">ملاحظات:</label>
                                    <p class="form-control-plaintext">{{ $plan->notes }}</p>
                                </div>
                                @endif
                                @if ($plan->creator)
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">تم الإنشاء بواسطة:</label>
                                    <p class="form-control-plaintext">{{ $plan->creator->name }}</p>
                                </div>
                                @endif
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">تاريخ الإنشاء:</label>
                                    <p class="form-control-plaintext">{{ $plan->created_at->format('Y-m-d H:i') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if ($plan->candidates->count() > 0)
                    <div class="card mt-3">
                        <div class="card-header">
                            <h5 class="card-title mb-0">المرشحون ({{ $plan->candidates->count() }})</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>الموظف</th>
                                            <th>الاستعداد</th>
                                            <th>الملاحظات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($plan->candidates as $candidate)
                                            <tr>
                                                <td>
                                                    <a href="{{ route('admin.employees.show', $candidate->employee_id) }}">
                                                        {{ $candidate->employee->full_name }}
                                                    </a>
                                                </td>
                                                <td>
                                                    <span class="badge bg-{{ $candidate->readiness_level == 'ready' ? 'success' : ($candidate->readiness_level == 'near_ready' ? 'warning' : 'info') }}">
                                                        @if ($candidate->readiness_level == 'ready')
                                                            جاهز
                                                        @elseif ($candidate->readiness_level == 'near_ready')
                                                            قريب من الجاهزية
                                                        @else
                                                            يحتاج تطوير
                                                        @endif
                                                    </span>
                                                </td>
                                                <td>{{ $candidate->notes ?? '-' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
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
                                <label class="form-label fw-bold">عدد المرشحين:</label>
                                <p class="form-control-plaintext">
                                    <span class="badge bg-primary fs-14">{{ $plan->candidates->count() }}</span>
                                </p>
                            </div>
                            @if ($plan->target_date)
                            <div class="mb-3">
                                <label class="form-label fw-bold">الأيام المتبقية:</label>
                                <p class="form-control-plaintext">
                                    @php
                                        $daysRemaining = now()->diffInDays($plan->target_date, false);
                                    @endphp
                                    @if ($daysRemaining > 0)
                                        <span class="badge bg-success">{{ $daysRemaining }} يوم</span>
                                    @elseif ($daysRemaining == 0)
                                        <span class="badge bg-warning">اليوم</span>
                                    @else
                                        <span class="badge bg-danger">متأخر {{ abs($daysRemaining) }} يوم</span>
                                    @endif
                                </p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

