@extends('admin.layouts.master')

@section('page-title')
    تفاصيل الحدث
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="page-header d-flex justify-content-between align-items-center my-4">
                <h5 class="page-title mb-0">تفاصيل الحدث</h5>
                <div>
                    @can('calendar-edit')
                    @if($event->created_by === auth()->id() || auth()->user()->hasPermissionTo('calendar-edit-all'))
                    <a href="{{ route('admin.calendar-events.edit', $event->id) }}" class="btn btn-primary btn-sm">تعديل</a>
                    @endif
                    @endcan
                    <a href="{{ route('admin.calendar-events.index') }}" class="btn btn-secondary btn-sm">رجوع</a>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">معلومات الحدث</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">العنوان:</label>
                                    <p>{{ $event->title_ar ?? $event->title }}</p>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold">نوع الحدث:</label>
                                    <p>
                                        <span class="badge bg-info">{{ $event->type_name_ar }}</span>
                                    </p>
                                </div>

                                @if($event->employee)
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">الموظف:</label>
                                    <p>{{ $event->employee->full_name }}</p>
                                </div>
                                @endif

                                @if($event->department)
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">القسم:</label>
                                    <p>{{ $event->department->name_ar ?? $event->department->name }}</p>
                                </div>
                                @endif

                                <div class="col-md-6">
                                    <label class="form-label fw-bold">تاريخ ووقت البدء:</label>
                                    <p>{{ $event->start_date->format('Y-m-d H:i') }}</p>
                                </div>

                                @if($event->end_date)
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">تاريخ ووقت الانتهاء:</label>
                                    <p>{{ $event->end_date->format('Y-m-d H:i') }}</p>
                                </div>
                                @endif

                                <div class="col-md-6">
                                    <label class="form-label fw-bold">لون الحدث:</label>
                                    <p>
                                        <span class="badge" style="background-color: {{ $event->color }}; color: white;">{{ $event->color }}</span>
                                    </p>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold">حدث طوال اليوم:</label>
                                    <p>{{ $event->is_all_day ? 'نعم' : 'لا' }}</p>
                                </div>

                                @if($event->is_reminder)
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">التذكير:</label>
                                    <p>نعم - {{ $event->reminder_minutes }} دقيقة قبل الحدث</p>
                                </div>
                                @endif

                                @if($event->description)
                                <div class="col-md-12">
                                    <label class="form-label fw-bold">الوصف:</label>
                                    <p>{{ $event->description }}</p>
                                </div>
                                @endif

                                @if($event->creator)
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">أنشئ بواسطة:</label>
                                    <p>{{ $event->creator->name }}</p>
                                </div>
                                @endif

                                <div class="col-md-6">
                                    <label class="form-label fw-bold">الحالة:</label>
                                    <p>
                                        <span class="badge bg-{{ $event->is_active ? 'success' : 'secondary' }}">
                                            {{ $event->is_active ? 'نشط' : 'غير نشط' }}
                                        </span>
                                    </p>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold">تاريخ الإنشاء:</label>
                                    <p>{{ $event->created_at->format('Y-m-d H:i') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

