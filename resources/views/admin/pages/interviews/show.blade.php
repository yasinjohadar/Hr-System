@extends('admin.layouts.master')

@section('page-title')
    تفاصيل المقابلة
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">تفاصيل المقابلة</h5>
                </div>
                <div>
                    <a href="{{ route('admin.interviews.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-right me-2"></i>العودة للقائمة
                    </a>
                    @can('interview-edit')
                    <a href="{{ route('admin.interviews.edit', $interview->id) }}" class="btn btn-primary">
                        <i class="fas fa-edit me-2"></i>تعديل
                    </a>
                    @endcan
                </div>
            </div>

            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">معلومات المقابلة</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">المرشح:</label>
                                    <p class="form-control-plaintext"><strong>{{ $interview->candidate->full_name }}</strong></p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">الوظيفة:</label>
                                    <p class="form-control-plaintext"><strong>{{ $interview->jobVacancy->title_ar ?? $interview->jobVacancy->title }}</strong></p>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">نوع المقابلة:</label>
                                    <p class="form-control-plaintext"><span class="badge bg-info">{{ $interview->type_name_ar }}</span></p>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">جولة المقابلة:</label>
                                    <p class="form-control-plaintext"><span class="badge bg-secondary">{{ $interview->round_name_ar }}</span></p>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">الحالة:</label>
                                    <p class="form-control-plaintext">
                                        <span class="badge bg-{{ $interview->status == 'completed' ? 'success' : ($interview->status == 'cancelled' ? 'danger' : 'warning') }}">
                                            {{ $interview->status_name_ar }}
                                        </span>
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">التاريخ والوقت:</label>
                                    <p class="form-control-plaintext">
                                        {{ $interview->interview_date->format('Y-m-d') }}
                                        @if ($interview->interview_time)
                                            - {{ $interview->interview_time->format('H:i') }}
                                        @endif
                                    </p>
                                </div>
                                @if ($interview->location)
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">المكان:</label>
                                    <p class="form-control-plaintext">{{ $interview->location }}</p>
                                </div>
                                @endif
                                @if ($interview->overall_rating)
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">التقييم الإجمالي:</label>
                                    <p class="form-control-plaintext">
                                        <span class="badge bg-success">{{ $interview->overall_rating }}/5</span>
                                    </p>
                                </div>
                                @endif
                                @if ($interview->recommendation_status)
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">التوصية:</label>
                                    <p class="form-control-plaintext">
                                        <span class="badge bg-{{ $interview->recommendation_status == 'hire' ? 'success' : ($interview->recommendation_status == 'reject' ? 'danger' : 'warning') }}">
                                            {{ $interview->recommendation_status == 'hire' ? 'توظيف' : ($interview->recommendation_status == 'reject' ? 'رفض' : 'قيد المراجعة') }}
                                        </span>
                                    </p>
                                </div>
                                @endif
                                @if ($interview->interview_notes)
                                <div class="col-12 mb-3">
                                    <label class="form-label fw-bold">ملاحظات المقابلة:</label>
                                    <p class="form-control-plaintext">{{ $interview->interview_notes }}</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop


