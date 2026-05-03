@extends('admin.layouts.master')

@section('page-title')
    تفاصيل الفرع
@stop

@section('css')
    <style>
        .branch-show-hero {
            background: linear-gradient(145deg, var(--bs-primary) 0%, rgba(var(--bs-primary-rgb), 0.88) 55%, rgb(15, 76, 129) 100%);
            color: #fff;
            border: none;
        }
        .branch-show-hero .text-white-75 { color: rgba(255,255,255,.85) !important; }
    </style>
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">تفاصيل الفرع</h5>
                    <p class="text-muted small mb-0">{{ $branch->name }}@if ($branch->code) — {{ $branch->code }}@endif</p>
                </div>
                <div class="d-flex flex-wrap gap-2 mt-2 mt-md-0">
                    <a href="{{ route('admin.branches.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-right me-1"></i>العودة للقائمة
                    </a>
                    @can('branch-edit')
                        <a href="{{ route('admin.branches.edit', $branch->id) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit me-1"></i>تعديل
                        </a>
                    @endcan
                </div>
            </div>

            <div class="row g-3">
                <div class="col-lg-4">
                    <div class="card branch-show-hero shadow-sm h-100">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex align-items-start gap-3 mb-3">
                                <span class="avatar avatar-md bg-white bg-opacity-25 rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width:3rem;height:3rem;">
                                    <i class="fas fa-code-branch fs-4"></i>
                                </span>
                                <div class="min-w-0">
                                    <div class="text-white-75 small mb-1">الفرع</div>
                                    <div class="fw-semibold fs-6 text-truncate">
                                        {{ $branch->name }}
                                        @if ($branch->is_main)
                                            <span class="badge bg-warning text-dark ms-1 align-middle">رئيسي</span>
                                        @endif
                                    </div>
                                    @if ($branch->code) <div class="small text-white-75 font-monospace">{{ $branch->code }}</div> @endif
                                </div>
                            </div>
                            <div class="mb-3 pb-3 border-bottom border-white border-opacity-25">
                                <div class="text-white-75 small mb-1"><i class="fas fa-user-tie me-1"></i>المدير</div>
                                <div class="fw-semibold">
                                    @if ($branch->manager)
                                        {{ $branch->manager->name }}
                                    @elseif ($branch->manager_name)
                                        {{ $branch->manager_name }}
                                    @else
                                        <span class="text-white-75">غير محدد</span>
                                    @endif
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="text-white-75 small mb-2">الحالة</div>
                                <span class="badge bg-{{ $branch->is_active ? 'success' : 'danger' }} fs-14 px-3 py-2">
                                    {{ $branch->is_active ? 'نشط' : 'غير نشط' }}
                                </span>
                            </div>
                            <div class="mt-auto pt-3 border-top border-white border-opacity-25">
                                <div class="text-white-75 small mb-1">عدد الموظفين</div>
                                <div class="fs-3 fw-bold lh-1">{{ $branch->employees_count }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-light py-3 border-bottom">
                            <h6 class="mb-0 fw-semibold">
                                <i class="fas fa-circle-info text-primary me-2"></i>بيانات الفرع
                            </h6>
                            <small class="text-muted">الموقع ومعلومات الاتصال</small>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <tbody>
                                        @if ($branch->address)
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle" style="width:35%">
                                                <i class="fas fa-map-marker-alt text-muted me-2"></i>العنوان
                                            </th>
                                            <td class="pe-4 py-3 align-middle fw-semibold">{{ $branch->address }}</td>
                                        </tr>
                                        @endif
                                        @if ($branch->city)
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-city text-muted me-2"></i>المدينة
                                            </th>
                                            <td class="pe-4 py-3 align-middle fw-semibold">{{ $branch->city }}</td>
                                        </tr>
                                        @endif
                                        @if ($branch->country)
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-globe text-muted me-2"></i>الدولة
                                            </th>
                                            <td class="pe-4 py-3 align-middle fw-semibold">{{ $branch->country }}</td>
                                        </tr>
                                        @endif
                                        @if ($branch->phone)
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-phone text-muted me-2"></i>الهاتف
                                            </th>
                                            <td class="pe-4 py-3 align-middle fw-semibold">
                                                <a href="tel:{{ $branch->phone }}">{{ $branch->phone }}</a>
                                            </td>
                                        </tr>
                                        @endif
                                        @if ($branch->email)
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-envelope text-muted me-2"></i>البريد الإلكتروني
                                            </th>
                                            <td class="pe-4 py-3 align-middle fw-semibold">
                                                <a href="mailto:{{ $branch->email }}">{{ $branch->email }}</a>
                                            </td>
                                        </tr>
                                        @endif
                                        @if ($branch->description)
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-align-left text-muted me-2"></i>الوصف
                                            </th>
                                            <td class="pe-4 py-3 align-middle">{{ $branch->description }}</td>
                                        </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if ($branch->employees->count() > 0)
            <div class="row g-3 mt-1">
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-light py-3 border-bottom">
                            <h6 class="mb-0 fw-semibold">
                                <i class="fas fa-users text-primary me-2"></i>موظفو الفرع
                                <span class="badge bg-info ms-2">{{ $branch->employees->count() }}</span>
                            </h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="ps-4">#</th>
                                            <th>الموظف</th>
                                            <th>القسم</th>
                                            <th>المنصب</th>
                                            <th>الحالة</th>
                                            <th class="pe-4"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($branch->employees as $employee)
                                            <tr>
                                                <td class="ps-4 align-middle text-muted small">{{ $loop->iteration }}</td>
                                                <td class="align-middle">
                                                    <div class="fw-semibold">{{ $employee->full_name }}</div>
                                                    <small class="text-muted font-monospace">{{ $employee->employee_code }}</small>
                                                </td>
                                                <td class="align-middle">
                                                    @if ($employee->department)
                                                        <span class="badge bg-info-subtle text-dark border">{{ $employee->department->name }}</span>
                                                    @else
                                                        <span class="text-muted">—</span>
                                                    @endif
                                                </td>
                                                <td class="align-middle">
                                                    @if ($employee->position)
                                                        <span class="badge bg-primary-subtle text-dark border">{{ $employee->position->title }}</span>
                                                    @else
                                                        <span class="text-muted">—</span>
                                                    @endif
                                                </td>
                                                <td class="align-middle">
                                                    @if ($employee->employment_status === 'active')
                                                        <span class="badge bg-success-subtle text-success border">نشط</span>
                                                    @else
                                                        <span class="badge bg-warning-subtle text-dark border">{{ $employee->employment_status }}</span>
                                                    @endif
                                                </td>
                                                <td class="pe-4 align-middle text-end">
                                                    <a href="{{ route('admin.employees.show', $employee->id) }}" class="btn btn-sm btn-light">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
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
                            <h6 class="mb-0 fw-semibold"><i class="fas fa-clock-rotate-left text-primary me-2"></i>بيانات السجل</h6>
                        </div>
                        <div class="card-body">
                            <div class="row row-cols-1 row-cols-md-2 g-0">
                                <div class="col border-bottom border-end-md p-3">
                                    <div class="small text-muted mb-1"><i class="far fa-clock me-1"></i>تاريخ الإنشاء</div>
                                    <div class="fw-semibold font-monospace small">{{ $branch->created_at->format('Y-m-d H:i') }}</div>
                                </div>
                                <div class="col border-bottom p-3">
                                    <div class="small text-muted mb-1"><i class="fas fa-pen-to-square me-1"></i>آخر تحديث</div>
                                    <div class="fw-semibold font-monospace small">{{ $branch->updated_at->format('Y-m-d H:i') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
