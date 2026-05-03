@extends('admin.layouts.master')

@section('page-title')
    تفاصيل الدورة التدريبية
@stop

@section('css')
    <style>
        .training-show-hero {
            background: linear-gradient(145deg, var(--bs-primary) 0%, rgba(var(--bs-primary-rgb), 0.88) 55%, rgb(15, 76, 129) 100%);
            color: #fff;
            border: none;
        }
        .training-show-hero .text-white-75 { color: rgba(255,255,255,.85) !important; }
    </style>
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">تفاصيل الدورة التدريبية</h5>
                    <p class="text-muted small mb-0">{{ $training->title_ar ?? $training->title }} — {{ $training->code }}</p>
                </div>
                <div class="d-flex flex-wrap gap-2 mt-2 mt-md-0">
                    <a href="{{ route('admin.trainings.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-right me-1"></i>العودة للقائمة
                    </a>
                    @can('training-edit')
                        <a href="{{ route('admin.trainings.edit', $training->id) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit me-1"></i>تعديل
                        </a>
                    @endcan
                </div>
            </div>

            <div class="row g-3">
                <div class="col-lg-4">
                    <div class="card training-show-hero shadow-sm h-100">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex align-items-start gap-3 mb-3">
                                <span class="avatar avatar-md bg-white bg-opacity-25 rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width:3rem;height:3rem;">
                                    <i class="fas fa-graduation-cap fs-4"></i>
                                </span>
                                <div class="min-w-0">
                                    <div class="text-white-75 small mb-1">الدورة التدريبية</div>
                                    <div class="fw-semibold fs-6 text-truncate">{{ $training->title_ar ?? $training->title }}</div>
                                    <div class="small text-white-75 font-monospace">{{ $training->code }}</div>
                                </div>
                            </div>
                            <div class="mb-3 pb-3 border-bottom border-white border-opacity-25">
                                <div class="text-white-75 small mb-1"><i class="fas fa-tag me-1"></i>نوع التدريب</div>
                                <div class="fw-semibold">{{ $training->type_ar }}</div>
                            </div>
                            <div class="mb-3">
                                <div class="text-white-75 small mb-2">الحالة</div>
                                @switch($training->status)
                                    @case('completed')
                                        <span class="badge bg-success fs-14 px-3 py-2">مكتمل</span>
                                        @break
                                    @case('ongoing')
                                        <span class="badge bg-primary fs-14 px-3 py-2">قيد التنفيذ</span>
                                        @break
                                    @case('planned')
                                        <span class="badge bg-info fs-14 px-3 py-2">مخطط</span>
                                        @break
                                    @default
                                        <span class="badge bg-danger fs-14 px-3 py-2">ملغي</span>
                                @endswitch
                            </div>
                            <div class="mt-auto pt-3 border-top border-white border-opacity-25">
                                <div class="text-white-75 small mb-1">المشاركون</div>
                                <div class="fs-3 fw-bold lh-1">{{ $training->participants_count }}</div>
                                @if ($training->max_participants)
                                    <div class="small text-white-75 mt-1">/ {{ $training->max_participants }}</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-light py-3 border-bottom">
                            <h6 class="mb-0 fw-semibold">
                                <i class="fas fa-circle-info text-primary me-2"></i>تفاصيل الدورة
                            </h6>
                            <small class="text-muted">التواريخ والوقت والمدرب والتكلفة</small>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <tbody>
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle" style="width:35%">
                                                <i class="fas fa-calendar-range text-muted me-2"></i>فترة الدورة
                                            </th>
                                            <td class="pe-4 py-3 align-middle fw-semibold">
                                                {{ $training->start_date->format('Y-m-d') }}
                                                <span class="text-muted mx-1">—</span>
                                                {{ $training->end_date->format('Y-m-d') }}
                                            </td>
                                        </tr>
                                        @if ($training->start_time || $training->end_time)
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-clock text-muted me-2"></i>الوقت
                                            </th>
                                            <td class="pe-4 py-3 align-middle fw-semibold">
                                                {{ $training->start_time ? $training->start_time->format('H:i') : '' }}
                                                @if ($training->start_time && $training->end_time)
                                                    <span class="text-muted mx-1">—</span>
                                                @endif
                                                {{ $training->end_time ? $training->end_time->format('H:i') : '' }}
                                            </td>
                                        </tr>
                                        @endif
                                        @if ($training->duration_hours)
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-hourglass-half text-muted me-2"></i>المدة
                                            </th>
                                            <td class="pe-4 py-3 align-middle fw-semibold">{{ $training->duration_hours }} ساعة</td>
                                        </tr>
                                        @endif
                                        @if ($training->instructor)
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-chalkboard-user text-muted me-2"></i>المدرب
                                            </th>
                                            <td class="pe-4 py-3 align-middle fw-semibold">
                                                {{ $training->instructor->full_name ?? $training->instructor->first_name . ' ' . $training->instructor->last_name }}
                                            </td>
                                        </tr>
                                        @endif
                                        @if ($training->provider)
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-building text-muted me-2"></i>مقدم التدريب
                                            </th>
                                            <td class="pe-4 py-3 align-middle fw-semibold">{{ $training->provider }}</td>
                                        </tr>
                                        @endif
                                        @if ($training->location)
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-location-dot text-muted me-2"></i>مكان التدريب
                                            </th>
                                            <td class="pe-4 py-3 align-middle fw-semibold">{{ $training->location }}</td>
                                        </tr>
                                        @endif
                                        @if ($training->cost > 0)
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-money-bill-wave text-muted me-2"></i>التكلفة
                                            </th>
                                            <td class="pe-4 py-3 align-middle fw-semibold">
                                                {{ number_format($training->cost, 2) }}
                                                @if ($training->currency)
                                                    {{ $training->currency->symbol_ar ?? $training->currency->symbol }}
                                                @endif
                                            </td>
                                        </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if ($training->description || $training->description_ar || $training->objectives)
            <div class="row g-3 mt-1">
                @if ($training->description || $training->description_ar)
                <div class="col-md-6">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-light py-3 border-bottom">
                            <h6 class="mb-0 fw-semibold">
                                <i class="fas fa-align-left text-muted me-2"></i>الوصف
                            </h6>
                        </div>
                        <div class="card-body">
                            <p class="mb-0">{{ $training->description_ar ?? $training->description }}</p>
                        </div>
                    </div>
                </div>
                @endif
                @if ($training->objectives)
                <div class="col-md-6">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-light py-3 border-bottom">
                            <h6 class="mb-0 fw-semibold">
                                <i class="fas fa-bullseye text-muted me-2"></i>الأهداف
                            </h6>
                        </div>
                        <div class="card-body">
                            <p class="mb-0">{{ $training->objectives }}</p>
                        </div>
                    </div>
                </div>
                @endif
            </div>
            @endif

            @if ($training->content || $training->materials)
            <div class="row g-3 mt-1">
                @if ($training->content)
                <div class="col-md-6">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-light py-3 border-bottom">
                            <h6 class="mb-0 fw-semibold">
                                <i class="fas fa-book-open text-muted me-2"></i>المحتوى
                            </h6>
                        </div>
                        <div class="card-body">
                            <p class="mb-0">{{ $training->content }}</p>
                        </div>
                    </div>
                </div>
                @endif
                @if ($training->materials)
                <div class="col-md-6">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-light py-3 border-bottom">
                            <h6 class="mb-0 fw-semibold">
                                <i class="fas fa-folder-open text-muted me-2"></i>المواد التدريبية
                            </h6>
                        </div>
                        <div class="card-body">
                            <p class="mb-0">{{ $training->materials }}</p>
                        </div>
                    </div>
                </div>
                @endif
            </div>
            @endif

            @if ($training->notes)
            <div class="row g-3 mt-1">
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-light py-3 border-bottom">
                            <h6 class="mb-0 fw-semibold">
                                <i class="fas fa-sticky-note text-muted me-2"></i>ملاحظات
                            </h6>
                        </div>
                        <div class="card-body">
                            <p class="mb-0">{{ $training->notes }}</p>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            @if ($training->trainingRecords->count() > 0)
            <div class="row g-3 mt-1">
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-light py-3">
                            <h6 class="mb-0 fw-semibold">
                                <i class="fas fa-users text-primary me-2"></i>المشاركون في الدورة
                            </h6>
                            <small class="text-muted">قائمة الموظفين المسجلين ونتائجهم</small>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="ps-4">#</th>
                                            <th>الموظف</th>
                                            <th>الحالة</th>
                                            <th class="text-end">النتيجة</th>
                                            <th class="pe-4">تاريخ التسجيل</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($training->trainingRecords as $record)
                                            <tr>
                                                <td class="ps-4 text-muted small">{{ $loop->iteration }}</td>
                                                <td class="fw-semibold">{{ $record->employee->full_name ?? $record->employee->first_name . ' ' . $record->employee->last_name }}</td>
                                                <td>
                                                    @if ($record->status == 'completed')
                                                        <span class="badge bg-success-subtle text-success border">مكتمل</span>
                                                    @elseif ($record->status == 'attending')
                                                        <span class="badge bg-primary-subtle text-primary border">يحضر</span>
                                                    @elseif ($record->status == 'registered')
                                                        <span class="badge bg-info-subtle text-dark border">مسجل</span>
                                                    @elseif ($record->status == 'failed')
                                                        <span class="badge bg-danger-subtle text-danger border">فاشل</span>
                                                    @else
                                                        <span class="badge bg-secondary-subtle text-dark border">ملغي</span>
                                                    @endif
                                                </td>
                                                <td class="text-end">
                                                    @if ($record->score)
                                                        <span class="badge bg-{{ $record->score >= 80 ? 'success' : ($record->score >= 60 ? 'warning' : 'danger') }}">
                                                            {{ number_format($record->score, 2) }}%
                                                        </span>
                                                    @else
                                                        <span class="text-muted">—</span>
                                                    @endif
                                                </td>
                                                <td class="pe-4 text-muted small">
                                                    {{ $record->registration_date ? $record->registration_date->format('Y-m-d') : '—' }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <div class="row g-3 mt-1 mb-4">
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-light py-3">
                            <h6 class="mb-0 fw-semibold">
                                <i class="fas fa-clock-rotate-left text-primary me-2"></i>بيانات السجل
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row row-cols-1 row-cols-md-2 g-0">
                                <div class="col border-bottom border-end-md p-3">
                                    <div class="small text-muted mb-1"><i class="far fa-clock me-1"></i>تاريخ الإنشاء</div>
                                    <div class="fw-semibold font-monospace small">{{ $training->created_at->format('Y-m-d H:i') }}</div>
                                </div>
                                <div class="col border-bottom p-3">
                                    <div class="small text-muted mb-1"><i class="fas fa-pen-to-square me-1"></i>آخر تحديث</div>
                                    <div class="fw-semibold font-monospace small">{{ $training->updated_at->format('Y-m-d H:i') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
