@extends('admin.layouts.master')

@section('page-title')
    تفاصيل الاستبيان
@stop

@section('css')
    <style>
        .survey-show-hero {
            background: linear-gradient(145deg, var(--bs-primary) 0%, rgba(var(--bs-primary-rgb), 0.88) 55%, rgb(15, 76, 129) 100%);
            color: #fff;
            border: none;
        }
        .survey-show-hero .text-white-75 { color: rgba(255,255,255,.85) !important; }
    </style>
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">تفاصيل الاستبيان</h5>
                    <p class="text-muted small mb-0">{{ $survey->title_ar ?? $survey->title }} — {{ $survey->survey_code }}</p>
                </div>
                <div class="d-flex flex-wrap gap-2 mt-2 mt-md-0">
                    <a href="{{ route('admin.surveys.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-right me-1"></i>العودة للقائمة
                    </a>
                    @can('survey-edit')
                        <a href="{{ route('admin.surveys.edit', $survey->id) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit me-1"></i>تعديل
                        </a>
                    @endcan
                </div>
            </div>

            <div class="row g-3">
                <div class="col-lg-4">
                    <div class="card survey-show-hero shadow-sm h-100">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex align-items-start gap-3 mb-3">
                                <span class="avatar avatar-md bg-white bg-opacity-25 rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width:3rem;height:3rem;">
                                    <i class="fas fa-square-poll-vertical fs-4"></i>
                                </span>
                                <div class="min-w-0">
                                    <div class="text-white-75 small mb-1">الاستبيان</div>
                                    <div class="fw-semibold fs-6 text-truncate">{{ $survey->title_ar ?? $survey->title }}</div>
                                    <div class="small text-white-75 font-monospace">{{ $survey->survey_code }}</div>
                                </div>
                            </div>
                            <div class="mb-3 pb-3 border-bottom border-white border-opacity-25">
                                <div class="text-white-75 small mb-1"><i class="fas fa-shapes me-1"></i>النوع</div>
                                <div class="fw-semibold">{{ $survey->type_name_ar }}</div>
                            </div>
                            <div class="mb-3">
                                <div class="text-white-75 small mb-2">الحالة</div>
                                <span class="badge bg-{{ match($survey->status) { 'draft' => 'secondary', 'active' => 'success', 'closed' => 'info', 'cancelled' => 'danger', default => 'secondary' } }} fs-14 px-3 py-2">
                                    {{ $survey->status_name_ar }}
                                </span>
                            </div>
                            <div class="mt-auto pt-3 border-top border-white border-opacity-25">
                                <div class="text-white-75 small mb-1">الردود</div>
                                <div class="fs-3 fw-bold lh-1">{{ $survey->total_responses ?? $survey->responses->count() }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-light py-3 border-bottom">
                            <h6 class="mb-0 fw-semibold">
                                <i class="fas fa-circle-info text-primary me-2"></i>تفاصيل الاستبيان
                            </h6>
                            <small class="text-muted">التواريخ والجمهور المستهدف</small>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <tbody>
                                        @if ($survey->title_ar && $survey->title)
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle" style="width:40%">
                                                <i class="fas fa-language text-muted me-2"></i>العنوان (EN)
                                            </th>
                                            <td class="pe-4 py-3 align-middle fw-semibold">{{ $survey->title }}</td>
                                        </tr>
                                        @endif
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-shapes text-muted me-2"></i>النوع
                                            </th>
                                            <td class="pe-4 py-3 align-middle">
                                                <span class="badge bg-info-subtle text-dark border">{{ $survey->type_name_ar }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="far fa-calendar-check text-muted me-2"></i>تاريخ البداية
                                            </th>
                                            <td class="pe-4 py-3 align-middle fw-semibold">{{ $survey->start_date->format('Y-m-d') }}</td>
                                        </tr>
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="far fa-calendar-xmark text-muted me-2"></i>تاريخ النهاية
                                            </th>
                                            <td class="pe-4 py-3 align-middle fw-semibold">{{ $survey->end_date->format('Y-m-d') }}</td>
                                        </tr>
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-eye-slash text-muted me-2"></i>تصويت مجهول
                                            </th>
                                            <td class="pe-4 py-3 align-middle">
                                                <span class="badge bg-{{ $survey->is_anonymous ? 'warning-subtle text-dark' : 'secondary-subtle text-dark' }} border">
                                                    {{ $survey->is_anonymous ? 'نعم' : 'لا' }}
                                                </span>
                                            </td>
                                        </tr>
                                        @if ($survey->target_audience)
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-users text-muted me-2"></i>الجمهور المستهدف
                                            </th>
                                            <td class="pe-4 py-3 align-middle fw-semibold">{{ $survey->target_audience }}</td>
                                        </tr>
                                        @endif
                                        @if ($survey->creator)
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-user-pen text-muted me-2"></i>أنشأ بواسطة
                                            </th>
                                            <td class="pe-4 py-3 align-middle fw-semibold">{{ $survey->creator->name }}</td>
                                        </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if ($survey->description)
            <div class="row g-3 mt-1">
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-light py-3 border-bottom">
                            <h6 class="mb-0 fw-semibold"><i class="fas fa-align-left text-muted me-2"></i>الوصف</h6>
                        </div>
                        <div class="card-body"><p class="mb-0">{{ $survey->description }}</p></div>
                    </div>
                </div>
            </div>
            @endif

            @if ($survey->questions && $survey->questions->count() > 0)
            <div class="row g-3 mt-1">
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-light py-3 border-bottom">
                            <h6 class="mb-0 fw-semibold">
                                <i class="fas fa-circle-question text-primary me-2"></i>الأسئلة
                                <span class="badge bg-primary ms-2">{{ $survey->questions->count() }}</span>
                            </h6>
                        </div>
                        <div class="card-body p-3">
                            @foreach ($survey->questions as $question)
                                <div class="d-flex gap-3 {{ $loop->last ? '' : 'mb-3 pb-3 border-bottom' }}">
                                    <span class="avatar avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width:2rem;height:2rem;">
                                        <span class="fs-14 fw-bold">{{ $question->question_order }}</span>
                                    </span>
                                    <div class="min-w-0 flex-grow-1">
                                        <div class="fw-semibold">{{ $question->question_text_ar ?? $question->question_text }}</div>
                                        @if ($question->help_text)
                                            <div class="text-muted small mt-1">{{ $question->help_text }}</div>
                                        @endif
                                        <div class="d-flex flex-wrap gap-1 mt-2">
                                            <span class="badge bg-info-subtle text-dark border">{{ $question->question_type_name_ar }}</span>
                                            @if ($question->is_required)
                                                <span class="badge bg-danger-subtle text-danger border">مطلوب</span>
                                            @endif
                                            @if (is_array($question->options) && count($question->options) > 0)
                                                @foreach ($question->options as $option)
                                                    <span class="badge bg-light text-dark border">{{ $option }}</span>
                                                @endforeach
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <div class="row g-3 mt-1 mb-4">
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-light py-3">
                            <h6 class="mb-0 fw-semibold"><i class="fas fa-clock-rotate-left text-primary me-2"></i>بيانات السجل</h6>
                        </div>
                        <div class="card-body">
                            <div class="row row-cols-1 row-cols-md-3 g-0">
                                <div class="col border-bottom border-end-md p-3">
                                    <div class="small text-muted mb-1"><i class="fas fa-reply me-1"></i>عدد الردود</div>
                                    <div class="fw-semibold">{{ $survey->total_responses ?? $survey->responses->count() }}</div>
                                </div>
                                <div class="col border-bottom border-end-md p-3">
                                    <div class="small text-muted mb-1"><i class="far fa-clock me-1"></i>تاريخ الإنشاء</div>
                                    <div class="fw-semibold font-monospace small">{{ $survey->created_at->format('Y-m-d H:i') }}</div>
                                </div>
                                <div class="col border-bottom p-3">
                                    <div class="small text-muted mb-1"><i class="fas fa-pen-to-square me-1"></i>آخر تحديث</div>
                                    <div class="fw-semibold font-monospace small">{{ $survey->updated_at->format('Y-m-d H:i') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
