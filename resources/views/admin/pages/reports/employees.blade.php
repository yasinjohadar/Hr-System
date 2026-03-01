@extends('admin.layouts.master')

@section('page-title')
    تقرير الموظفين
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">تقرير الموظفين الشامل</h5>
                </div>
                <div>
                    <a href="{{ route('admin.reports.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-right me-2"></i>العودة للتقارير
                    </a>
                </div>
            </div>

            <!-- فلترة -->
            <div class="card mb-3">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.reports.employees') }}" class="row g-3">
                        <div class="col-md-3">
                            <select name="department_id" class="form-select">
                                <option value="">كل الأقسام</option>
                                @foreach ($departments as $dept)
                                    <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                                        {{ $dept->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="position_id" class="form-select">
                                <option value="">كل المناصب</option>
                                @foreach ($positions as $pos)
                                    <option value="{{ $pos->id }}" {{ request('position_id') == $pos->id ? 'selected' : '' }}>
                                        {{ $pos->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="employment_status" class="form-select">
                                <option value="">كل الحالات</option>
                                <option value="active" {{ request('employment_status') == 'active' ? 'selected' : '' }}>نشط</option>
                                <option value="on_leave" {{ request('employment_status') == 'on_leave' ? 'selected' : '' }}>في إجازة</option>
                                <option value="terminated" {{ request('employment_status') == 'terminated' ? 'selected' : '' }}>منتهي</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary w-100">تطبيق الفلترة</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- الإحصائيات -->
            <div class="row mb-3">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h6 class="card-title">إجمالي الموظفين</h6>
                            <h2 class="mb-0">{{ $stats['total'] }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h6 class="card-title">الموظفين النشطين</h6>
                            <h2 class="mb-0">{{ $stats['active'] }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <h6 class="card-title">عدد الأقسام</h6>
                            <h2 class="mb-0">{{ $stats['by_department']->count() }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <h6 class="card-title">عدد المناصب</h6>
                            <h2 class="mb-0">{{ $stats['by_position']->count() }}</h2>
                        </div>
                    </div>
                </div>
            </div>

            <!-- جدول الموظفين -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">قائمة الموظفين ({{ $employees->count() }})</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>الكود</th>
                                    <th>الاسم</th>
                                    <th>القسم</th>
                                    <th>المنصب</th>
                                    <th>الفرع</th>
                                    <th>تاريخ التوظيف</th>
                                    <th>الحالة</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($employees as $employee)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td><span class="badge bg-info">{{ $employee->employee_code }}</span></td>
                                        <td><strong>{{ $employee->full_name }}</strong></td>
                                        <td>{{ $employee->department->name ?? '-' }}</td>
                                        <td>{{ $employee->position->title ?? '-' }}</td>
                                        <td>{{ $employee->branch->name ?? '-' }}</td>
                                        <td>{{ $employee->hire_date->format('Y-m-d') }}</td>
                                        <td>
                                            <span class="badge bg-{{ $employee->is_active ? 'success' : 'danger' }}">
                                                {{ $employee->is_active ? 'نشط' : 'غير نشط' }}
                                            </span>
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
@stop


