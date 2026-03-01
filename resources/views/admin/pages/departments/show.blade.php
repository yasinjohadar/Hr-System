@extends('admin.layouts.master')

@section('page-title')
    تفاصيل القسم
@stop

@section('css')
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="page-header d-flex justify-content-between align-items-center my-4">
                <h5 class="page-title mb-0">تفاصيل القسم: {{ $department->name }}</h5>
                <div>
                    @can('department-edit')
                    <a href="{{ route('admin.departments.edit', $department->id) }}" class="btn btn-info btn-sm">
                        <i class="fas fa-edit me-1"></i>تعديل
                    </a>
                    @endcan
                    <a href="{{ route('admin.departments.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-right me-1"></i>رجوع
                    </a>
                </div>
            </div>

            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">معلومات القسم</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="200">اسم القسم:</th>
                                    <td><strong>{{ $department->name }}</strong></td>
                                </tr>
                                @if ($department->code)
                                <tr>
                                    <th>كود القسم:</th>
                                    <td><span class="badge bg-info">{{ $department->code }}</span></td>
                                </tr>
                                @endif
                                @if ($department->description)
                                <tr>
                                    <th>الوصف:</th>
                                    <td>{{ $department->description }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <th>المدير:</th>
                                    <td>
                                        @if ($department->manager)
                                            <span class="text-primary">{{ $department->manager->name }}</span>
                                        @else
                                            <span class="text-muted">غير محدد</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>القسم الأب:</th>
                                    <td>
                                        @if ($department->parent)
                                            <span class="badge bg-secondary">{{ $department->parent->name }}</span>
                                        @else
                                            <span class="text-muted">قسم رئيسي</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>الحالة:</th>
                                    <td>
                                        @if ($department->is_active)
                                            <span class="badge bg-success">نشط</span>
                                        @else
                                            <span class="badge bg-danger">غير نشط</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>تاريخ الإنشاء:</th>
                                    <td>{{ $department->created_at->format('Y-m-d H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if ($department->children->count() > 0)
                    <div class="card mt-3">
                        <div class="card-header">
                            <h5 class="card-title mb-0">الأقسام الفرعية</h5>
                        </div>
                        <div class="card-body">
                            <ul class="list-group">
                                @foreach ($department->children as $child)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <a href="{{ route('admin.departments.show', $child->id) }}" class="text-decoration-none">
                                            {{ $child->name }}
                                        </a>
                                        <span class="badge bg-primary">{{ $child->employees_count ?? $child->employees->count() }} موظف</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    @endif
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">إحصائيات</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <h3 class="text-primary">{{ $department->employees_count }}</h3>
                                <p class="text-muted mb-0">عدد الموظفين</p>
                            </div>
                            <div class="mb-3">
                                <h3 class="text-info">{{ $department->positions->count() }}</h3>
                                <p class="text-muted mb-0">عدد المناصب</p>
                            </div>
                            @if ($department->children->count() > 0)
                            <div>
                                <h3 class="text-success">{{ $department->children->count() }}</h3>
                                <p class="text-muted mb-0">عدد الأقسام الفرعية</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            @if ($department->employees->count() > 0)
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">موظفو القسم</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>رقم الموظف</th>
                                    <th>الاسم</th>
                                    <th>المنصب</th>
                                    <th>الحالة</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($department->employees as $employee)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $employee->employee_code }}</td>
                                        <td>
                                            <a href="{{ route('admin.employees.show', $employee->id) }}" class="text-decoration-none">
                                                {{ $employee->full_name }}
                                            </a>
                                        </td>
                                        <td>
                                            @if ($employee->position)
                                                <span class="badge bg-primary">{{ $employee->position->title }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($employee->employment_status === 'active')
                                                <span class="badge bg-success">نشط</span>
                                            @else
                                                <span class="badge bg-warning">{{ $employee->employment_status }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
@stop

@section('js')
@stop

