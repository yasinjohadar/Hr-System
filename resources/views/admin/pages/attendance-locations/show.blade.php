@extends('admin.layouts.master')

@section('page-title')
    تفاصيل موقع الحضور
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="page-header d-flex justify-content-between align-items-center my-4">
                <h5 class="page-title mb-0">تفاصيل موقع الحضور</h5>
                <div>
                    @can('attendance-location-edit')
                    <a href="{{ route('admin.attendance-locations.edit', $location->id) }}" class="btn btn-primary btn-sm">تعديل</a>
                    @endcan
                    <a href="{{ route('admin.attendance-locations.index') }}" class="btn btn-secondary btn-sm">رجوع</a>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">معلومات موقع الحضور</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">الكود:</label>
                                    <p>{{ $location->code }}</p>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold">الاسم:</label>
                                    <p>{{ $location->name_ar ?? $location->name }}</p>
                                </div>

                                @if($location->address)
                                <div class="col-md-12">
                                    <label class="form-label fw-bold">العنوان:</label>
                                    <p>{{ $location->address }}</p>
                                </div>
                                @endif

                                <div class="col-md-4">
                                    <label class="form-label fw-bold">خط العرض:</label>
                                    <p>{{ number_format($location->latitude, 8) }}</p>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-bold">خط الطول:</label>
                                    <p>{{ number_format($location->longitude, 8) }}</p>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-bold">نصف القطر:</label>
                                    <p>{{ $location->radius_meters }} متر</p>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold">يتطلب التحقق من الموقع:</label>
                                    <p>
                                        @if($location->require_location)
                                            <span class="badge bg-success">نعم</span>
                                        @else
                                            <span class="badge bg-secondary">لا</span>
                                        @endif
                                    </p>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold">الحالة:</label>
                                    <p>
                                        <span class="badge bg-{{ $location->is_active ? 'success' : 'secondary' }}">
                                            {{ $location->is_active ? 'نشط' : 'غير نشط' }}
                                        </span>
                                    </p>
                                </div>

                                @if($location->description)
                                <div class="col-md-12">
                                    <label class="form-label fw-bold">الوصف:</label>
                                    <p>{{ $location->description }}</p>
                                </div>
                                @endif

                                @if($location->allowed_employees && count($location->allowed_employees) > 0)
                                <div class="col-md-12">
                                    <label class="form-label fw-bold">الموظفون المسموح لهم:</label>
                                    <ul>
                                        @foreach($location->allowed_employees as $employeeId)
                                            @php $employee = \App\Models\Employee::find($employeeId); @endphp
                                            @if($employee)
                                                <li>{{ $employee->full_name }} - {{ $employee->employee_number }}</li>
                                            @endif
                                        @endforeach
                                    </ul>
                                </div>
                                @endif

                                @if($location->allowed_departments && count($location->allowed_departments) > 0)
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">الأقسام المسموحة:</label>
                                    <ul>
                                        @foreach($location->allowed_departments as $deptId)
                                            @php $dept = \App\Models\Department::find($deptId); @endphp
                                            @if($dept)
                                                <li>{{ $dept->name_ar ?? $dept->name }}</li>
                                            @endif
                                        @endforeach
                                    </ul>
                                </div>
                                @endif

                                @if($location->allowed_positions && count($location->allowed_positions) > 0)
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">المناصب المسموحة:</label>
                                    <ul>
                                        @foreach($location->allowed_positions as $posId)
                                            @php $pos = \App\Models\Position::find($posId); @endphp
                                            @if($pos)
                                                <li>{{ $pos->name_ar ?? $pos->name }}</li>
                                            @endif
                                        @endforeach
                                    </ul>
                                </div>
                                @endif

                                @if($location->creator)
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">أنشئ بواسطة:</label>
                                    <p>{{ $location->creator->name }}</p>
                                </div>
                                @endif

                                <div class="col-md-6">
                                    <label class="form-label fw-bold">تاريخ الإنشاء:</label>
                                    <p>{{ $location->created_at->format('Y-m-d H:i') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

