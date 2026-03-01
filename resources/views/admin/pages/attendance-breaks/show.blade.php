@extends('admin.layouts.master')

@section('page-title')
    تفاصيل الاستراحة
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="page-header d-flex justify-content-between align-items-center my-4">
                <h5 class="page-title mb-0">تفاصيل الاستراحة</h5>
                <div>
                    @can('attendance-break-edit')
                    <a href="{{ route('admin.attendance-breaks.edit', $break->id) }}" class="btn btn-primary btn-sm">تعديل</a>
                    @endcan
                    <a href="{{ route('admin.attendance-breaks.index') }}" class="btn btn-secondary btn-sm">رجوع</a>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">معلومات الاستراحة</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">الموظف:</label>
                                    <p>{{ $break->attendance->employee->full_name }}</p>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold">تاريخ الحضور:</label>
                                    <p>{{ $break->attendance->attendance_date }}</p>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold">نوع الاستراحة:</label>
                                    <p>
                                        <span class="badge bg-info">{{ $break->break_type_name_ar }}</span>
                                    </p>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold">وقت بدء الاستراحة:</label>
                                    <p>{{ $break->break_start }}</p>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold">وقت انتهاء الاستراحة:</label>
                                    <p>{{ $break->break_end ?? '-' }}</p>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold">المدة:</label>
                                    <p>{{ $break->duration_minutes }} دقيقة</p>
                                </div>

                                @if($break->notes)
                                <div class="col-md-12">
                                    <label class="form-label fw-bold">ملاحظات:</label>
                                    <p>{{ $break->notes }}</p>
                                </div>
                                @endif

                                <div class="col-md-6">
                                    <label class="form-label fw-bold">تاريخ الإنشاء:</label>
                                    <p>{{ $break->created_at->format('Y-m-d H:i') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

