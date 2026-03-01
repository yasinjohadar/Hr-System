@extends('admin.layouts.master')

@section('page-title')
    تفاصيل الدورة التدريبية
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">تفاصيل الدورة التدريبية: {{ $training->title_ar ?? $training->title }}</h5>
                </div>
                <div>
                    <a href="{{ route('admin.trainings.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-right me-2"></i>العودة للقائمة
                    </a>
                    @can('training-edit')
                    <a href="{{ route('admin.trainings.edit', $training->id) }}" class="btn btn-primary">
                        <i class="fas fa-edit me-2"></i>تعديل
                    </a>
                    @endcan
                </div>
            </div>

            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">معلومات الدورة التدريبية</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">عنوان الدورة:</label>
                                    <p class="form-control-plaintext">
                                        <strong>{{ $training->title_ar ?? $training->title }}</strong>
                                        @if ($training->title_ar && $training->title)
                                            <br><small class="text-muted">{{ $training->title }}</small>
                                        @endif
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">كود الدورة:</label>
                                    <p class="form-control-plaintext">
                                        <span class="badge bg-info">{{ $training->code }}</span>
                                    </p>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">نوع التدريب:</label>
                                    <p class="form-control-plaintext">
                                        <span class="badge bg-secondary">{{ $training->type_ar }}</span>
                                    </p>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">الحالة:</label>
                                    <p class="form-control-plaintext">
                                        @if ($training->status == 'completed')
                                            <span class="badge bg-success">مكتمل</span>
                                        @elseif ($training->status == 'ongoing')
                                            <span class="badge bg-primary">قيد التنفيذ</span>
                                        @elseif ($training->status == 'planned')
                                            <span class="badge bg-info">مخطط</span>
                                        @else
                                            <span class="badge bg-danger">ملغي</span>
                                        @endif
                                    </p>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">المشاركون:</label>
                                    <p class="form-control-plaintext">
                                        <span class="badge bg-primary">
                                            {{ $training->participants_count }}
                                            @if ($training->max_participants)
                                                / {{ $training->max_participants }}
                                            @endif
                                        </span>
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">تاريخ البدء:</label>
                                    <p class="form-control-plaintext">{{ $training->start_date->format('Y-m-d') }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">تاريخ الانتهاء:</label>
                                    <p class="form-control-plaintext">{{ $training->end_date->format('Y-m-d') }}</p>
                                </div>
                                @if ($training->start_time || $training->end_time)
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">الوقت:</label>
                                    <p class="form-control-plaintext">
                                        @if ($training->start_time)
                                            {{ $training->start_time->format('H:i') }}
                                        @endif
                                        @if ($training->start_time && $training->end_time)
                                            -
                                        @endif
                                        @if ($training->end_time)
                                            {{ $training->end_time->format('H:i') }}
                                        @endif
                                    </p>
                                </div>
                                @endif
                                @if ($training->duration_hours)
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">المدة:</label>
                                    <p class="form-control-plaintext">{{ $training->duration_hours }} ساعة</p>
                                </div>
                                @endif
                                @if ($training->instructor)
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">المدرب:</label>
                                    <p class="form-control-plaintext">
                                        {{ $training->instructor->full_name ?? $training->instructor->first_name . ' ' . $training->instructor->last_name }}
                                    </p>
                                </div>
                                @endif
                                @if ($training->provider)
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">مقدم التدريب:</label>
                                    <p class="form-control-plaintext">{{ $training->provider }}</p>
                                </div>
                                @endif
                                @if ($training->location)
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">مكان التدريب:</label>
                                    <p class="form-control-plaintext">{{ $training->location }}</p>
                                </div>
                                @endif
                                @if ($training->cost > 0)
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">التكلفة:</label>
                                    <p class="form-control-plaintext">
                                        {{ number_format($training->cost, 2) }}
                                        @if ($training->currency)
                                            {{ $training->currency->symbol_ar ?? $training->currency->symbol }}
                                        @endif
                                    </p>
                                </div>
                                @endif
                                @if ($training->description || $training->description_ar)
                                <div class="col-12 mb-3">
                                    <label class="form-label fw-bold">الوصف:</label>
                                    <p class="form-control-plaintext">{{ $training->description_ar ?? $training->description }}</p>
                                </div>
                                @endif
                                @if ($training->objectives)
                                <div class="col-12 mb-3">
                                    <label class="form-label fw-bold">الأهداف:</label>
                                    <p class="form-control-plaintext">{{ $training->objectives }}</p>
                                </div>
                                @endif
                                @if ($training->content)
                                <div class="col-12 mb-3">
                                    <label class="form-label fw-bold">المحتوى:</label>
                                    <p class="form-control-plaintext">{{ $training->content }}</p>
                                </div>
                                @endif
                                @if ($training->materials)
                                <div class="col-12 mb-3">
                                    <label class="form-label fw-bold">المواد التدريبية:</label>
                                    <p class="form-control-plaintext">{{ $training->materials }}</p>
                                </div>
                                @endif
                                @if ($training->notes)
                                <div class="col-12 mb-3">
                                    <label class="form-label fw-bold">ملاحظات:</label>
                                    <p class="form-control-plaintext">{{ $training->notes }}</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if ($training->trainingRecords->count() > 0)
                    <div class="card mt-3">
                        <div class="card-header">
                            <h5 class="card-title mb-0">المشاركون في الدورة</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>الموظف</th>
                                            <th>الحالة</th>
                                            <th>النتيجة</th>
                                            <th>تاريخ التسجيل</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($training->trainingRecords as $record)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $record->employee->full_name ?? $record->employee->first_name . ' ' . $record->employee->last_name }}</td>
                                                <td>
                                                    @if ($record->status == 'completed')
                                                        <span class="badge bg-success">مكتمل</span>
                                                    @elseif ($record->status == 'attending')
                                                        <span class="badge bg-primary">يحضر</span>
                                                    @elseif ($record->status == 'registered')
                                                        <span class="badge bg-info">مسجل</span>
                                                    @elseif ($record->status == 'failed')
                                                        <span class="badge bg-danger">فاشل</span>
                                                    @else
                                                        <span class="badge bg-secondary">ملغي</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($record->score)
                                                        <span class="badge bg-{{ $record->score >= 80 ? 'success' : ($record->score >= 60 ? 'warning' : 'danger') }}">
                                                            {{ number_format($record->score, 2) }}%
                                                        </span>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>{{ $record->registration_date ? $record->registration_date->format('Y-m-d') : '-' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@stop


