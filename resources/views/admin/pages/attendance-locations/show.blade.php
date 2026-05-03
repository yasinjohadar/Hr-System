@extends('admin.layouts.master')

@section('page-title')
    تفاصيل موقع الحضور
@stop

@section('css')
    <style>
        .location-show-hero {
            background: linear-gradient(145deg, var(--bs-primary) 0%, rgba(var(--bs-primary-rgb), 0.88) 55%, rgb(15, 76, 129) 100%);
            color: #fff;
            border: none;
        }
        .location-show-hero .text-white-75 { color: rgba(255,255,255,.85) !important; }
    </style>
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">تفاصيل موقع الحضور</h5>
                    <p class="text-muted small mb-0">{{ $location->name_ar ?? $location->name }} — {{ $location->code }}</p>
                </div>
                <div class="d-flex flex-wrap gap-2 mt-2 mt-md-0">
                    <a href="{{ route('admin.attendance-locations.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-right me-1"></i>العودة للقائمة
                    </a>
                    @can('attendance-location-edit')
                        <a href="{{ route('admin.attendance-locations.edit', $location->id) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit me-1"></i>تعديل
                        </a>
                    @endcan
                </div>
            </div>

            <div class="row g-3">
                <div class="col-lg-4">
                    <div class="card location-show-hero shadow-sm h-100">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex align-items-start gap-3 mb-3">
                                <span class="avatar avatar-md bg-white bg-opacity-25 rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width:3rem;height:3rem;">
                                    <i class="fas fa-map-pin fs-4"></i>
                                </span>
                                <div class="min-w-0">
                                    <div class="text-white-75 small mb-1">موقع الحضور</div>
                                    <div class="fw-semibold fs-6 text-truncate">{{ $location->name_ar ?? $location->name }}</div>
                                    <div class="small text-white-75 font-monospace">{{ $location->code }}</div>
                                </div>
                            </div>
                            @if($location->address)
                            <div class="mb-3 pb-3 border-bottom border-white border-opacity-25">
                                <div class="text-white-75 small mb-1"><i class="fas fa-location-dot me-1"></i>العنوان</div>
                                <div class="fw-semibold">{{ $location->address }}</div>
                            </div>
                            @endif
                            <div class="mb-3">
                                <div class="text-white-75 small mb-2">الحالة</div>
                                @if ($location->is_active)
                                    <span class="badge bg-success fs-14 px-3 py-2">نشط</span>
                                @else
                                    <span class="badge bg-secondary fs-14 px-3 py-2">غير نشط</span>
                                @endif
                            </div>
                            <div class="mt-auto pt-3 border-top border-white border-opacity-25">
                                <div class="text-white-75 small mb-1">نصف القطر</div>
                                <div class="fs-3 fw-bold lh-1">{{ $location->radius_meters }}</div>
                                <div class="small text-white-75 mt-1">متر</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-light py-3 border-bottom">
                            <h6 class="mb-0 fw-semibold">
                                <i class="fas fa-circle-info text-primary me-2"></i>بيانات الموقع
                            </h6>
                            <small class="text-muted">الإحداثيات ونطاق التغطية والإعدادات</small>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <tbody>
                                        @if($location->address)
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle" style="width:40%">
                                                <i class="fas fa-map-location-dot text-muted me-2"></i>العنوان
                                            </th>
                                            <td class="pe-4 py-3 align-middle fw-semibold">{{ $location->address }}</td>
                                        </tr>
                                        @endif
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-map-pin text-muted me-2"></i>الإحداثيات
                                            </th>
                                            <td class="pe-4 py-3 align-middle">
                                                <div><span class="text-muted small">Lat:</span> <span class="fw-semibold font-monospace">{{ number_format($location->latitude, 8) }}</span></div>
                                                <div><span class="text-muted small">Lng:</span> <span class="fw-semibold font-monospace">{{ number_format($location->longitude, 8) }}</span></div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-circle-notch text-muted me-2"></i>نصف القطر
                                            </th>
                                            <td class="pe-4 py-3 align-middle fw-semibold">{{ $location->radius_meters }} <span class="text-muted small">متر</span></td>
                                        </tr>
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-shield-check text-muted me-2"></i>التحقق من الموقع
                                            </th>
                                            <td class="pe-4 py-3 align-middle">
                                                @if($location->require_location)
                                                    <span class="badge bg-success-subtle text-success border">مطلوب</span>
                                                @else
                                                    <span class="badge bg-secondary-subtle text-dark border">غير مطلوب</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @if($location->description)
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-align-left text-muted me-2"></i>الوصف
                                            </th>
                                            <td class="pe-4 py-3 align-middle">{{ $location->description }}</td>
                                        </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if(
                ($location->allowed_employees && count($location->allowed_employees) > 0) ||
                ($location->allowed_departments && count($location->allowed_departments) > 0) ||
                ($location->allowed_positions && count($location->allowed_positions) > 0)
            )
            <div class="row g-3 mt-1">
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-light py-3">
                            <h6 class="mb-0 fw-semibold">
                                <i class="fas fa-users text-primary me-2"></i>الصلاحيات والموظفون المسموح لهم
                            </h6>
                            <small class="text-muted">الموظفون والأقسام والمناصب المسموح لها بهذا الموقع</small>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                @if($location->allowed_employees && count($location->allowed_employees) > 0)
                                <div class="col-md-4">
                                    <h6 class="small fw-bold text-muted mb-2"><i class="fas fa-user-check me-1"></i>الموظفون</h6>
                                    <ul class="list-unstyled mb-0">
                                        @foreach($location->allowed_employees as $employeeId)
                                            @php $employee = \App\Models\Employee::find($employeeId); @endphp
                                            @if($employee)
                                                <li class="py-1 small">{{ $employee->full_name }} <span class="text-muted font-monospace">{{ $employee->employee_number }}</span></li>
                                            @endif
                                        @endforeach
                                    </ul>
                                </div>
                                @endif
                                @if($location->allowed_departments && count($location->allowed_departments) > 0)
                                <div class="col-md-4">
                                    <h6 class="small fw-bold text-muted mb-2"><i class="fas fa-building me-1"></i>الأقسام</h6>
                                    <ul class="list-unstyled mb-0">
                                        @foreach($location->allowed_departments as $deptId)
                                            @php $dept = \App\Models\Department::find($deptId); @endphp
                                            @if($dept)
                                                <li class="py-1 small">{{ $dept->name_ar ?? $dept->name }}</li>
                                            @endif
                                        @endforeach
                                    </ul>
                                </div>
                                @endif
                                @if($location->allowed_positions && count($location->allowed_positions) > 0)
                                <div class="col-md-4">
                                    <h6 class="small fw-bold text-muted mb-2"><i class="fas fa-briefcase me-1"></i>المناصب</h6>
                                    <ul class="list-unstyled mb-0">
                                        @foreach($location->allowed_positions as $posId)
                                            @php $pos = \App\Models\Position::find($posId); @endphp
                                            @if($pos)
                                                <li class="py-1 small">{{ $pos->name_ar ?? $pos->name }}</li>
                                            @endif
                                        @endforeach
                                    </ul>
                                </div>
                                @endif
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
                                @if($location->creator)
                                <div class="col border-bottom border-end-md p-3">
                                    <div class="small text-muted mb-1"><i class="fas fa-user-pen me-1"></i>أنشأ بواسطة</div>
                                    <div class="fw-semibold">{{ $location->creator->name }}</div>
                                </div>
                                @endif
                                <div class="col border-bottom p-3">
                                    <div class="small text-muted mb-1"><i class="far fa-clock me-1"></i>تاريخ الإنشاء</div>
                                    <div class="fw-semibold font-monospace small">{{ $location->created_at->format('Y-m-d H:i') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
