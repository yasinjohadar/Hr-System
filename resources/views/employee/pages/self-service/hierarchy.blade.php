@extends('employee.layouts.master')

@section('page-title')
    التسلسل الإداري
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div>
                    <h5 class="page-title fs-21 mb-1">التسلسل الإداري</h5>
                    <p class="text-muted fs-13 mb-0">هيكل الإدارة والتابعية</p>
                </div>
            </div>

            <div class="row">
                <!-- My Info -->
                <div class="col-xl-4 col-lg-12 mb-4">
                    <div class="card custom-card">
                        <div class="card-header bg-primary text-white">
                            <h6 class="card-title fw-semibold mb-0">
                                <i class="ri-user-line me-1"></i>معلوماتي
                            </h6>
                        </div>
                        <div class="card-body text-center">
                            @if ($employee->photo)
                                <img src="{{ asset('storage/' . $employee->photo) }}" alt="صورة الموظف" class="rounded-circle mb-3" style="width: 100px; height: 100px; object-fit: cover; border: 3px solid var(--primary-color);">
                            @else
                                <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 100px; height: 100px; font-size: 40px;">
                                    {{ substr($employee->first_name, 0, 1) }}
                                </div>
                            @endif
                            <h5 class="mb-1 fw-semibold">{{ $employee->full_name }}</h5>
                            <p class="text-muted mb-2">{{ $employee->employee_code }}</p>
                            <p class="mb-3">
                                <span class="badge bg-primary-transparent">{{ $employee->position->title ?? '-' }}</span>
                            </p>
                            <div class="text-start border-top pt-3">
                                <div class="mb-2">
                                    <small class="text-muted d-block">القسم</small>
                                    <span class="fw-medium">{{ $employee->department->name ?? '-' }}</span>
                                </div>
                                <div class="mb-2">
                                    <small class="text-muted d-block">الفرع</small>
                                    <span class="fw-medium">{{ $employee->branch->name ?? '-' }}</span>
                                </div>
                                <div class="mb-2">
                                    <small class="text-muted d-block">تاريخ التوظيف</small>
                                    <span class="fw-medium">{{ $employee->hire_date->format('Y/m/d') }}</span>
                                </div>
                                <div>
                                    <small class="text-muted d-block">سنوات الخدمة</small>
                                    <span class="fw-medium">{{ $employee->hire_date->diffInYears(now()) }} سنة و {{ $employee->hire_date->diffInMonths(now()) % 12 }} شهر</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Management Hierarchy -->
                <div class="col-xl-4 col-lg-12 mb-4">
                    <div class="card custom-card">
                        <div class="card-header bg-success text-white">
                            <h6 class="card-title fw-semibold mb-0">
                                <i class="ri-arrow-up-circle-line me-1"></i>التسلسل الإداري
                            </h6>
                        </div>
                        <div class="card-body p-0">
                            <!-- أنا -->
                            <div class="p-3 border-bottom bg-light">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-md bg-primary avatar-rounded me-3">
                                        {{ substr($employee->first_name, 0, 1) }}
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-semibold">{{ $employee->full_name }}</h6>
                                        <small class="text-muted">{{ $employee->position->title ?? '-' }}</small>
                                    </div>
                                </div>
                            </div>

                            <!-- المدير المباشر -->
                            @if($directManager)
                                <div class="p-3 border-bottom">
                                    <div class="d-flex align-items-center">
                                        <div class="text-center me-3">
                                            <div class="avatar avatar-md bg-success avatar-rounded">
                                                {{ substr($directManager->first_name, 0, 1) }}
                                            </div>
                                            <div class="mt-1">
                                                <i class="ri-arrow-up-line text-success"></i>
                                            </div>
                                        </div>
                                        <div>
                                            <small class="text-muted d-block">المدير المباشر</small>
                                            <h6 class="mb-0 fw-semibold">{{ $directManager->full_name }}</h6>
                                            <small class="text-muted">{{ $directManager->position->title ?? '-' }}</small>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="p-3 border-bottom text-muted text-center">
                                    <small>لا يوجد مدير مباشر محدد</small>
                                </div>
                            @endif

                            <!-- رئيس القسم -->
                            @if($departmentManager)
                                <div class="p-3 border-bottom">
                                    <div class="d-flex align-items-center">
                                        <div class="text-center me-3">
                                            <div class="avatar avatar-md bg-info avatar-rounded">
                                                {{ substr($departmentManager->name, 0, 1) }}
                                            </div>
                                            <div class="mt-1">
                                                <i class="ri-arrow-up-line text-info"></i>
                                            </div>
                                        </div>
                                        <div>
                                            <small class="text-muted d-block">رئيس القسم</small>
                                            <h6 class="mb-0 fw-semibold">{{ $departmentManager->name }}</h6>
                                            <small class="text-muted">{{ $departmentManager->email ?? '-' }}</small>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- باقي السلسلة -->
                            @if(count($managerChain) > 1)
                                @foreach($managerChain as $index => $manager)
                                    @if($manager->id !== $directManager?->id)
                                        <div class="p-3 border-bottom">
                                            <div class="d-flex align-items-center">
                                                <div class="text-center me-3">
                                                    <div class="avatar avatar-md bg-warning avatar-rounded">
                                                        {{ substr($manager->first_name, 0, 1) }}
                                                    </div>
                                                    <div class="mt-1">
                                                        <i class="ri-arrow-up-line text-warning"></i>
                                                    </div>
                                                </div>
                                                <div>
                                                    <small class="text-muted d-block">
                                                        @if($index === 0) المدير المباشر
                                                        @elseif($index === 1) رئيس القسم
                                                        @else المستوى {{ $index + 1 }}
                                                        @endif
                                                    </small>
                                                    <h6 class="mb-0 fw-semibold">{{ $manager->full_name }}</h6>
                                                    <small class="text-muted">{{ $manager->position->title ?? '-' }}</small>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Department Hierarchy -->
                <div class="col-xl-4 col-lg-12 mb-4">
                    <div class="card custom-card">
                        <div class="card-header bg-info text-white">
                            <h6 class="card-title fw-semibold mb-0">
                                <i class="ri-building-line me-1"></i>هيكل القسم
                            </h6>
                        </div>
                        <div class="card-body p-0">
                            @forelse($departmentHierarchy as $index => $dept)
                                <div class="p-3 {{ $index > 0 ? 'border-bottom' : '' }}" style="{{ $index > 0 ? 'padding-left: ' . (20 + $index * 20) . 'px !important;' : '' }}">
                                    <div class="d-flex align-items-center">
                                        @if($index > 0)
                                            <div class="me-2 text-muted">
                                                <i class="ri-corner-down-right-line"></i>
                                            </div>
                                        @endif
                                        <div class="avatar avatar-sm bg-{{ $index === 0 ? 'primary' : ($index === count($departmentHierarchy) - 1 ? 'success' : 'info') }}-transparent avatar-rounded me-3">
                                            <i class="ri-building-line text-{{ $index === 0 ? 'primary' : ($index === count($departmentHierarchy) - 1 ? 'success' : 'info') }}"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 fw-semibold">{{ $dept['name'] }}</h6>
                                            <small class="text-muted">{{ $dept['code'] }}</small>
                                            @if($dept['manager'])
                                                <br><small class="text-primary">رئيس: {{ $dept['manager']->name }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center text-muted py-4">
                                    لا توجد معلومات عن القسم
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Quick Stats -->
                    <div class="card custom-card mt-4">
                        <div class="card-body text-center">
                            <h6 class="fw-semibold mb-3">ملخص سريع</h6>
                            <div class="row">
                                <div class="col-6 mb-3">
                                    <div class="avatar avatar-lg bg-primary-transparent avatar-rounded mb-2">
                                        <i class="ri-user-line text-primary"></i>
                                    </div>
                                    <h5 class="mb-0 fw-semibold">{{ count($managerChain) }}</h5>
                                    <small class="text-muted">مستويات إدارية</small>
                                </div>
                                <div class="col-6 mb-3">
                                    <div class="avatar avatar-lg bg-success-transparent avatar-rounded mb-2">
                                        <i class="ri-building-line text-success"></i>
                                    </div>
                                    <h5 class="mb-0 fw-semibold">{{ count($departmentHierarchy) }}</h5>
                                    <small class="text-muted">أقسام في التسلسل</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
