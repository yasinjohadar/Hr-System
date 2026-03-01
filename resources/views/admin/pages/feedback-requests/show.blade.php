@extends('admin.layouts.master')

@section('page-title')
    تفاصيل طلب التقييم
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">تفاصيل طلب التقييم: {{ $feedbackRequest->request_code }}</h5>
                </div>
                <div>
                    <a href="{{ route('admin.feedback-requests.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-right me-2"></i>العودة للقائمة
                    </a>
                    @can('feedback-request-edit')
                    <a href="{{ route('admin.feedback-requests.edit', $feedbackRequest->id) }}" class="btn btn-primary">
                        <i class="fas fa-edit me-2"></i>تعديل
                    </a>
                    @endcan
                </div>
            </div>

            <div class="row">
                <div class="col-xl-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">معلومات الطلب</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">كود الطلب:</label>
                                    <p class="form-control-plaintext"><strong>{{ $feedbackRequest->request_code }}</strong></p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">الموظف:</label>
                                    <p class="form-control-plaintext">
                                        <strong>
                                            <a href="{{ route('admin.employees.show', $feedbackRequest->employee_id) }}">
                                                {{ $feedbackRequest->employee->full_name }}
                                            </a>
                                        </strong>
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">نوع التقييم:</label>
                                    <p class="form-control-plaintext">
                                        <span class="badge bg-info">{{ $feedbackRequest->feedback_type_name_ar }}</span>
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">الحالة:</label>
                                    <p class="form-control-plaintext">
                                        <span class="badge bg-{{ $feedbackRequest->status == 'completed' ? 'success' : ($feedbackRequest->status == 'active' ? 'primary' : ($feedbackRequest->status == 'in_progress' ? 'warning' : ($feedbackRequest->status == 'cancelled' ? 'danger' : 'secondary'))) }}">
                                            {{ $feedbackRequest->status_name_ar }}
                                        </span>
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">تاريخ البدء:</label>
                                    <p class="form-control-plaintext">{{ $feedbackRequest->start_date->format('Y-m-d') }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">تاريخ الانتهاء:</label>
                                    <p class="form-control-plaintext">{{ $feedbackRequest->end_date->format('Y-m-d') }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">تقييم مجهول:</label>
                                    <p class="form-control-plaintext">
                                        <span class="badge bg-{{ $feedbackRequest->is_anonymous ? 'success' : 'secondary' }}">
                                            {{ $feedbackRequest->is_anonymous ? 'نعم' : 'لا' }}
                                        </span>
                                    </p>
                                </div>
                                @if ($feedbackRequest->instructions)
                                <div class="col-12 mb-3">
                                    <label class="form-label fw-bold">التعليمات:</label>
                                    <div class="form-control-plaintext border rounded p-3 bg-light">
                                        {!! nl2br(e($feedbackRequest->instructions)) !!}
                                    </div>
                                </div>
                                @endif
                                @if ($feedbackRequest->creator)
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">تم الإنشاء بواسطة:</label>
                                    <p class="form-control-plaintext">{{ $feedbackRequest->creator->name }}</p>
                                </div>
                                @endif
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">تاريخ الإنشاء:</label>
                                    <p class="form-control-plaintext">{{ $feedbackRequest->created_at->format('Y-m-d H:i') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if ($feedbackRequest->responses->count() > 0)
                    <div class="card mt-3">
                        <div class="card-header">
                            <h5 class="card-title mb-0">الردود ({{ $feedbackRequest->responses->count() }})</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>المقيم</th>
                                            <th>التقييم</th>
                                            <th>التعليقات</th>
                                            <th>التاريخ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($feedbackRequest->responses as $response)
                                            <tr>
                                                <td>
                                                    @if ($feedbackRequest->is_anonymous)
                                                        مجهول
                                                    @else
                                                        {{ $response->respondent->full_name ?? $response->respondent->name ?? 'غير معروف' }}
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($response->rating)
                                                        <span class="badge bg-primary">{{ $response->rating }}/5</span>
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>{{ Str::limit($response->comments, 50) ?? '-' }}</td>
                                                <td>{{ $response->created_at->format('Y-m-d H:i') }}</td>
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
                                <label class="form-label fw-bold">عدد الردود:</label>
                                <p class="form-control-plaintext">
                                    <span class="badge bg-primary fs-14">{{ $feedbackRequest->responses->count() }}</span>
                                </p>
                            </div>
                            @php
                                $daysRemaining = now()->diffInDays($feedbackRequest->end_date, false);
                            @endphp
                            <div class="mb-3">
                                <label class="form-label fw-bold">الأيام المتبقية:</label>
                                <p class="form-control-plaintext">
                                    @if ($daysRemaining > 0)
                                        <span class="badge bg-success">{{ $daysRemaining }} يوم</span>
                                    @elseif ($daysRemaining == 0)
                                        <span class="badge bg-warning">اليوم</span>
                                    @else
                                        <span class="badge bg-danger">منتهي</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

