@extends('admin.layouts.master')

@section('page-title')
    تفاصيل القسم
@stop

@section('css')
    <style>
        .department-show-hero {
            background: linear-gradient(145deg, var(--bs-primary) 0%, rgba(var(--bs-primary-rgb), 0.88) 55%, rgb(15, 76, 129) 100%);
            color: #fff;
            border: none;
        }
        .department-show-hero .text-white-75 { color: rgba(255,255,255,.85) !important; }
    </style>
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">تفاصيل القسم</h5>
                    <p class="text-muted small mb-0">{{ $department->name }}@if ($department->code) — {{ $department->code }}@endif</p>
                </div>
                <div class="d-flex flex-wrap gap-2 mt-2 mt-md-0">
                    <a href="{{ route('admin.departments.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-right me-1"></i>العودة للقائمة
                    </a>
                    @can('department-edit')
                        <a href="{{ route('admin.departments.edit', $department->id) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit me-1"></i>تعديل
                        </a>
                    @endcan
                </div>
            </div>

            <div class="row g-3">
                <div class="col-lg-4">
                    <div class="card department-show-hero shadow-sm h-100">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex align-items-start gap-3 mb-3">
                                <span class="avatar avatar-md bg-white bg-opacity-25 rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width:3rem;height:3rem;">
                                    <i class="fas fa-building fs-4"></i>
                                </span>
                                <div class="min-w-0">
                                    <div class="text-white-75 small mb-1">القسم</div>
                                    <div class="fw-semibold fs-6 text-truncate">{{ $department->name }}</div>
                                    @if ($department->code) <div class="small text-white-75 font-monospace">{{ $department->code }}</div> @endif
                                </div>
                            </div>
                            <div class="mb-3 pb-3 border-bottom border-white border-opacity-25">
                                <div class="text-white-75 small mb-1"><i class="fas fa-sitemap me-1"></i>القسم الأب</div>
                                <div class="fw-semibold">
                                    @if ($department->parent)
                                        <a href="{{ route('admin.departments.show', $department->parent->id) }}" class="text-white">{{ $department->parent->name }}</a>
                                    @else
                                        <span class="text-white-75">قسم رئيسي</span>
                                    @endif
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="text-white-75 small mb-2">الحالة</div>
                                <span class="badge bg-{{ $department->is_active ? 'success' : 'danger' }} fs-14 px-3 py-2">
                                    {{ $department->is_active ? 'نشط' : 'غير نشط' }}
                                </span>
                            </div>
                            <div class="mt-auto pt-3 border-top border-white border-opacity-25">
                                <div class="text-white-75 small mb-1">عدد الموظفين</div>
                                <div class="fs-3 fw-bold lh-1">{{ $department->employees_count }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="card shadow-sm border-0">
                                <div class="card-header bg-light py-3 border-bottom">
                                    <h6 class="mb-0 fw-semibold">
                                        <i class="fas fa-circle-info text-primary me-2"></i>بيانات القسم
                                    </h6>
                                    <small class="text-muted">المدير والموظفين والإعدادات</small>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-hover mb-0">
                                            <tbody>
                                                <tr>
                                                    <th scope="row" class="ps-4 py-3 align-middle" style="width:35%">
                                                        <i class="fas fa-user-tie text-muted me-2"></i>مدير القسم
                                                    </th>
                                                    <td class="pe-4 py-3 align-middle fw-semibold">
                                                        @if ($department->manager)
                                                            {{ $department->manager->name }}
                                                        @else
                                                            <span class="text-muted">غير محدد</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                @if ($department->description)
                                                <tr>
                                                    <th scope="row" class="ps-4 py-3 align-middle">
                                                        <i class="fas fa-align-left text-muted me-2"></i>الوصف
                                                    </th>
                                                    <td class="pe-4 py-3 align-middle">{{ $department->description }}</td>
                                                </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="card shadow-sm border-0">
                                <div class="card-header bg-light py-3 border-bottom">
                                    <h6 class="mb-0 fw-semibold">
                                        <i class="fas fa-chart-simple text-primary me-2"></i>إحصائيات
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row row-cols-1 row-cols-md-3 g-0">
                                        <div class="col border-bottom border-end-md p-3 text-center">
                                            <div class="small text-muted mb-1"><i class="fas fa-users me-1"></i>عدد الموظفين</div>
                                            <div class="fs-3 fw-bold text-primary">{{ $department->employees_count }}</div>
                                        </div>
                                        <div class="col border-bottom border-end-md p-3 text-center">
                                            <div class="small text-muted mb-1"><i class="fas fa-briefcase me-1"></i>عدد المناصب</div>
                                            <div class="fs-3 fw-bold text-info">{{ $department->positions->count() }}</div>
                                        </div>
                                        <div class="col border-bottom p-3 text-center">
                                            <div class="small text-muted mb-1"><i class="fas fa-sitemap me-1"></i>الأقسام الفرعية</div>
                                            <div class="fs-3 fw-bold text-success">{{ $department->children->count() }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if ($department->children->count() > 0)
            <div class="row g-3 mt-1">
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-light py-3 border-bottom">
                            <h6 class="mb-0 fw-semibold">
                                <i class="fas fa-sitemap text-primary me-2"></i>الأقسام الفرعية
                                <span class="badge bg-primary ms-2">{{ $department->children->count() }}</span>
                            </h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="ps-4">القسم</th>
                                            <th>المدير</th>
                                            <th>الموظفين</th>
                                            <th class="pe-4"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($department->children as $child)
                                            <tr>
                                                <td class="ps-4 align-middle fw-semibold">{{ $child->name }}</td>
                                                <td class="align-middle">{{ $child->manager->name ?? '—' }}</td>
                                                <td class="align-middle">
                                                    <span class="badge bg-primary-subtle text-dark border">{{ $child->employees_count ?? $child->employees->count() }}</span>
                                                </td>
                                                <td class="pe-4 align-middle text-end">
                                                    <a href="{{ route('admin.departments.show', $child->id) }}" class="btn btn-sm btn-light">
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

            @if ($department->employees->count() > 0)
            <div class="row g-3 mt-1">
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-light py-3 border-bottom">
                            <h6 class="mb-0 fw-semibold">
                                <i class="fas fa-users text-primary me-2"></i>موظفو القسم
                                <span class="badge bg-info ms-2">{{ $department->employees->count() }}</span>
                            </h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="ps-4">#</th>
                                            <th>الموظف</th>
                                            <th>المنصب</th>
                                            <th>الحالة</th>
                                            <th class="pe-4"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($department->employees as $employee)
                                            <tr>
                                                <td class="ps-4 align-middle text-muted small">{{ $loop->iteration }}</td>
                                                <td class="align-middle">
                                                    <div class="fw-semibold">{{ $employee->full_name }}</div>
                                                    <small class="text-muted font-monospace">{{ $employee->employee_code }}</small>
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
                                    <div class="fw-semibold font-monospace small">{{ $department->created_at->format('Y-m-d H:i') }}</div>
                                </div>
                                <div class="col border-bottom p-3">
                                    <div class="small text-muted mb-1"><i class="fas fa-pen-to-square me-1"></i>آخر تحديث</div>
                                    <div class="fw-semibold font-monospace small">{{ $department->updated_at->format('Y-m-d H:i') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
