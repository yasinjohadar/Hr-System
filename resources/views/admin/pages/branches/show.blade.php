@extends('admin.layouts.master')

@section('page-title')
    تفاصيل الفرع
@stop

@section('css')
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="page-header d-flex justify-content-between align-items-center my-4">
                <h5 class="page-title mb-0">تفاصيل الفرع: {{ $branch->name }}</h5>
                <div>
                    @can('branch-edit')
                    <a href="{{ route('admin.branches.edit', $branch->id) }}" class="btn btn-info btn-sm">
                        <i class="fas fa-edit me-1"></i>تعديل
                    </a>
                    @endcan
                    <a href="{{ route('admin.branches.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-right me-1"></i>رجوع
                    </a>
                </div>
            </div>

            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">معلومات الفرع</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="200">اسم الفرع:</th>
                                    <td>
                                        <strong>{{ $branch->name }}</strong>
                                        @if ($branch->is_main)
                                            <span class="badge bg-warning text-dark ms-2">رئيسي</span>
                                        @endif
                                    </td>
                                </tr>
                                @if ($branch->code)
                                <tr>
                                    <th>كود الفرع:</th>
                                    <td><span class="badge bg-info">{{ $branch->code }}</span></td>
                                </tr>
                                @endif
                                @if ($branch->description)
                                <tr>
                                    <th>الوصف:</th>
                                    <td>{{ $branch->description }}</td>
                                </tr>
                                @endif
                                @if ($branch->address)
                                <tr>
                                    <th>العنوان:</th>
                                    <td>{{ $branch->address }}</td>
                                </tr>
                                @endif
                                @if ($branch->city)
                                <tr>
                                    <th>المدينة:</th>
                                    <td>{{ $branch->city }}</td>
                                </tr>
                                @endif
                                @if ($branch->country)
                                <tr>
                                    <th>الدولة:</th>
                                    <td>{{ $branch->country }}</td>
                                </tr>
                                @endif
                                @if ($branch->phone)
                                <tr>
                                    <th>الهاتف:</th>
                                    <td><a href="tel:{{ $branch->phone }}">{{ $branch->phone }}</a></td>
                                </tr>
                                @endif
                                @if ($branch->email)
                                <tr>
                                    <th>البريد الإلكتروني:</th>
                                    <td><a href="mailto:{{ $branch->email }}">{{ $branch->email }}</a></td>
                                </tr>
                                @endif
                                <tr>
                                    <th>المدير:</th>
                                    <td>
                                        @if ($branch->manager)
                                            <span class="text-primary">{{ $branch->manager->name }}</span>
                                        @elseif ($branch->manager_name)
                                            <span class="text-muted">{{ $branch->manager_name }}</span>
                                        @else
                                            <span class="text-muted">غير محدد</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>الحالة:</th>
                                    <td>
                                        @if ($branch->is_active)
                                            <span class="badge bg-success">نشط</span>
                                        @else
                                            <span class="badge bg-danger">غير نشط</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>تاريخ الإنشاء:</th>
                                    <td>{{ $branch->created_at->format('Y-m-d H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">إحصائيات</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <h3 class="text-primary">{{ $branch->employees_count }}</h3>
                                <p class="text-muted mb-0">عدد الموظفين</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if ($branch->employees->count() > 0)
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">موظفو الفرع</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>رقم الموظف</th>
                                    <th>الاسم</th>
                                    <th>القسم</th>
                                    <th>المنصب</th>
                                    <th>الحالة</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($branch->employees as $employee)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $employee->employee_code }}</td>
                                        <td>
                                            <a href="{{ route('admin.employees.show', $employee->id) }}" class="text-decoration-none">
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

