@extends('employee.layouts.master')

@section('page-title')
    فريق العمل
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div>
                    <h5 class="page-title fs-21 mb-1">فريق العمل</h5>
                    <p class="text-muted fs-13 mb-0">إدارة موظفي القسم</p>
                </div>
                <a href="{{ route('employee.department-head.dashboard') }}" class="btn btn-outline-primary btn-sm">
                    <i class="ri-arrow-left-line me-1"></i>العودة للوحة التحكم
                </a>
            </div>

            <!-- Team Stats -->
            <div class="row mb-4">
                <div class="col-xl-2 col-md-4 col-6">
                    <div class="card custom-card text-center">
                        <div class="card-body p-3">
                            <h4 class="mb-1 fw-semibold text-primary">{{ $teamStats['total'] }}</h4>
                            <small class="text-muted">إجمالي الموظفين</small>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-md-4 col-6">
                    <div class="card custom-card text-center">
                        <div class="card-body p-3">
                            <h4 class="mb-1 fw-semibold text-success">{{ $teamStats['present'] }}</h4>
                            <small class="text-muted">حاضر اليوم</small>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-md-4 col-6">
                    <div class="card custom-card text-center">
                        <div class="card-body p-3">
                            <h4 class="mb-1 fw-semibold text-danger">{{ $teamStats['absent'] }}</h4>
                            <small class="text-muted">غائب اليوم</small>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-md-4 col-6">
                    <div class="card custom-card text-center">
                        <div class="card-body p-3">
                            <h4 class="mb-1 fw-semibold text-warning">{{ $teamStats['late'] }}</h4>
                            <small class="text-muted">متأخر اليوم</small>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-md-4 col-6">
                    <div class="card custom-card text-center">
                        <div class="card-body p-3">
                            <h4 class="mb-1 fw-semibold text-info">{{ $teamStats['on_leave'] }}</h4>
                            <small class="text-muted">في إجازة</small>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-md-4 col-6">
                    <div class="card custom-card text-center">
                        <div class="card-body p-3">
                            <h4 class="mb-1 fw-semibold text-secondary">{{ count($departments) }}</h4>
                            <small class="text-muted">الأقسام</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Employees Table -->
            <div class="card custom-card">
                <div class="card-header">
                    <h6 class="card-title fw-semibold">قائمة الموظفين</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover text-nowrap mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>الموظف</th>
                                    <th>القسم</th>
                                    <th>المنصب</th>
                                    <th>المدير المباشر</th>
                                    <th>البريد الإلكتروني</th>
                                    <th>الهاتف</th>
                                    <th>تاريخ التوظيف</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($employees as $emp)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-sm bg-primary-transparent avatar-rounded me-2">
                                                    {{ substr($emp->first_name, 0, 1) }}
                                                </div>
                                                <div>
                                                    <span class="fw-medium d-block">{{ $emp->full_name }}</span>
                                                    <small class="text-muted">{{ $emp->employee_code }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $emp->department->name ?? '-' }}</td>
                                        <td>{{ $emp->position->title ?? '-' }}</td>
                                        <td>
                                            @if($emp->manager)
                                                <span class="text-muted">{{ $emp->manager->full_name }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>{{ $emp->work_email ?? $emp->personal_email ?? '-' }}</td>
                                        <td>{{ $emp->work_phone ?? $emp->personal_phone ?? '-' }}</td>
                                        <td>{{ $emp->hire_date->format('Y/m/d') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">
                                            لا يوجد موظفين في القسم
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
