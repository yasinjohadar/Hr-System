@extends('admin.layouts.master')

@section('page-title')
    قائمة الموظفين
@stop

@section('css')
@stop

@section('content')
    @if (\Session::has('success'))
        <div class="alert alert-success">
            <ul>
                <li>{!! \Session::get('success') !!}</li>
            </ul>
        </div>
    @endif

    @if (\Session::has('error'))
        <div class="alert alert-danger">
            <ul>
                <li>{!! \Session::get('error') !!}</li>
            </ul>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Start::app-content -->
    <div class="main-content app-content">
        <div class="container-fluid">

            <!-- Page Header -->
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">كافة الموظفين</h5>
                </div>
            </div>
            <!-- Page Header Close -->

            <!-- Start::row-1 -->
            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header align-items-center d-flex gap-3">
                            @can('employee-create')
                            <a href="{{ route('admin.employees.create') }}" class="btn btn-primary btn-sm">إضافة موظف جديد</a>
                            @endcan
                            @can('export-data')
                            <a href="{{ route('admin.export.employees') }}" class="btn btn-success btn-sm">
                                <i class="fe fe-download"></i> تصدير Excel
                            </a>
                            @endcan

                            <div class="flex-shrink-0">
                                <div class="form-check form-switch form-switch-right form-switch-md">
                                    <form action="{{ route('admin.employees.index') }}" method="GET"
                                        class="d-flex align-items-center gap-2">
                                        {{-- حقل البحث --}}
                                        <input style="width: 300px" type="text" name="query" class="form-control"
                                            placeholder="بحث بالاسم أو الرقم أو البريد" value="{{ request('query') }}">

                                        {{-- فلتر القسم --}}
                                        <select name="department_id" class="form-select">
                                            <option value="">كل الأقسام</option>
                                            @foreach ($departments as $dept)
                                                <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                                                    {{ $dept->name }}
                                                </option>
                                            @endforeach
                                        </select>

                                        {{-- فلتر المنصب --}}
                                        <select name="position_id" class="form-select">
                                            <option value="">كل المناصب</option>
                                            @foreach ($positions as $pos)
                                                <option value="{{ $pos->id }}" {{ request('position_id') == $pos->id ? 'selected' : '' }}>
                                                    {{ $pos->title }}
                                                </option>
                                            @endforeach
                                        </select>

                                        {{-- فلتر الحالة الوظيفية --}}
                                        <select name="employment_status" class="form-select">
                                            <option value="">كل الحالات</option>
                                            <option value="active" {{ request('employment_status') == 'active' ? 'selected' : '' }}>نشط</option>
                                            <option value="on_leave" {{ request('employment_status') == 'on_leave' ? 'selected' : '' }}>في إجازة</option>
                                            <option value="terminated" {{ request('employment_status') == 'terminated' ? 'selected' : '' }}>منتهي</option>
                                            <option value="resigned" {{ request('employment_status') == 'resigned' ? 'selected' : '' }}>استقال</option>
                                        </select>

                                        {{-- فلتر الحالة النشطة --}}
                                        <select name="is_active" class="form-select">
                                            <option value="">كل الحالات النشطة</option>
                                            <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>نشط</option>
                                            <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>غير نشط</option>
                                        </select>

                                        <button type="submit" class="btn btn-secondary">بحث</button>
                                        <a href="{{ route('admin.employees.index') }}" class="btn btn-danger">مسح</a>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover align-middle table-nowrap mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th scope="col" style="width: 40px;">#</th>
                                            <th scope="col" style="min-width: 120px;">رقم الموظف</th>
                                            <th scope="col" style="min-width: 200px;">الاسم</th>
                                            <th scope="col" style="min-width: 150px;">القسم</th>
                                            <th scope="col" style="min-width: 150px;">المنصب</th>
                                            <th scope="col" style="min-width: 120px;">البريد</th>
                                            <th scope="col" style="min-width: 120px;">الهاتف</th>
                                            <th scope="col" style="min-width: 120px;">تاريخ التوظيف</th>
                                            <th scope="col" style="min-width: 110px;">الحالة</th>
                                            <th scope="col" style="min-width: 120px;">الحالة النشطة</th>
                                            <th scope="col" style="min-width: 200px;">العمليات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($employees as $employee)
                                            <tr>
                                                <th scope="row">{{ $loop->iteration }}</th>
                                                <td>
                                                    <strong>{{ $employee->employee_code }}</strong>
                                                </td>
                                                <td>
                                                    <a href="{{ route('admin.employees.show', $employee->id) }}"
                                                        class="text-decoration-none">
                                                        {{ $employee->full_name }}
                                                    </a>
                                                </td>
                                                <td>
                                                    @if ($employee->department)
                                                        <span class="badge bg-info">{{ $employee->department->name }}</span>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($employee->position)
                                                        <span class="badge bg-primary">{{ $employee->position->title }}</span>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($employee->personal_email)
                                                        <a href="mailto:{{ $employee->personal_email }}"
                                                            class="text-primary text-decoration-none">
                                                            {{ $employee->personal_email }}
                                                        </a>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($employee->personal_phone)
                                                        {{ $employee->personal_phone }}
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    {{ $employee->hire_date ? $employee->hire_date->format('Y-m-d') : '-' }}
                                                </td>
                                                <td>
                                                    @if ($employee->employment_status === 'active')
                                                        <span class="badge bg-success">نشط</span>
                                                    @elseif($employee->employment_status === 'on_leave')
                                                        <span class="badge bg-warning text-dark">في إجازة</span>
                                                    @elseif($employee->employment_status === 'terminated')
                                                        <span class="badge bg-danger">منتهي</span>
                                                    @elseif($employee->employment_status === 'resigned')
                                                        <span class="badge bg-secondary">استقال</span>
                                                    @else
                                                        <span class="badge bg-secondary">غير معروف</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox" 
                                                               {{ $employee->is_active ? 'checked' : '' }}
                                                               disabled>
                                                        <label class="form-check-label">
                                                            {{ $employee->is_active ? 'نشط' : 'غير نشط' }}
                                                        </label>
                                                    </div>
                                                </td>
                                                <td>
                                                    @can('employee-edit')
                                                    <a class="btn btn-info btn-sm me-1"
                                                        href="{{ route('admin.employees.edit', $employee->id) }}"
                                                        title="تعديل الموظف">
                                                        <i class="fa-solid fa-pen-to-square"></i>
                                                    </a>
                                                    @endcan
                                                    @can('employee-show')
                                                    <a class="btn btn-success btn-sm me-1"
                                                        href="{{ route('admin.employees.show', $employee->id) }}"
                                                        title="عرض التفاصيل">
                                                        <i class="fa-solid fa-eye"></i>
                                                    </a>
                                                    @endcan
                                                    @if($employee->user_id && $employee->user && $employee->user->is_active)
                                                        @can('employee-show')
                                                        <form action="{{ route('admin.employees.login-as', $employee) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-outline-secondary btn-sm me-1" title="الدخول كموظف">
                                                                <i class="fa-solid fa-right-to-bracket"></i>
                                                            </button>
                                                        </form>
                                                        @endcan
                                                    @endif
                                                    @can('employee-delete')
                                                    <a class="btn btn-danger btn-sm me-1" data-bs-toggle="modal"
                                                        data-bs-target="#delete{{ $employee->id }}"
                                                        title="حذف الموظف">
                                                        <i class="fa-solid fa-trash-can"></i>
                                                    </a>
                                                    @endcan
                                                </td>
                                            </tr>

                                            @include('admin.pages.employees.delete')
                                        @empty
                                            <tr>
                                                <td colspan="11" class="text-center text-danger fw-bold">لا توجد
                                                    بيانات متاحة
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>

                                <div class="mt-3">
                                    {{ $employees->withQueryString()->links() }}
                                </div>
                            </div>
                        </div><!-- end card-body -->
                    </div><!-- end card -->
                </div>
            </div>
            <!--End::row-1 -->

        </div>
    </div>
    <!-- End::app-content -->
@stop

@section('js')
@stop

