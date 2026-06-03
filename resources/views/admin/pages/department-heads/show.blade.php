@extends('admin.layouts.master')

@section('page-title')
    تفاصيل رئيس القسم
@stop

@section('content')
    @if (session('success'))
        <div class="alert alert-success"><ul class="mb-0"><li>{{ session('success') }}</li></ul></div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger"><ul class="mb-0"><li>{{ session('error') }}</li></ul></div>
    @endif

    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-flex flex-wrap justify-content-between align-items-start my-4 gap-2">
                <div>
                    <h5 class="page-title fs-21 mb-1">{{ $head->name }}</h5>
                    <p class="text-muted mb-0">{{ $head->email }}</p>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('admin.department-heads.index') }}" class="btn btn-outline-secondary btn-sm">القائمة</a>
                    <a href="{{ route('admin.department-heads.capabilities', $head->id) }}" class="btn btn-info btn-sm">
                        <i class="ri-shield-user-line me-1"></i>الصلاحيات والقدرات
                    </a>
                    @can('department-head-manage')
                    <a href="{{ route('admin.department-heads.edit', $head->id) }}" class="btn btn-primary btn-sm">تعديل الأقسام</a>
                    <form action="{{ route('admin.department-heads.apply-role-template', $head->id) }}" method="POST" class="d-inline"
                          onsubmit="return confirm('تطبيق صلاحيات قالب رئيس القسم على هذا المستخدم؟');">
                        @csrf
                        <button type="submit" class="btn btn-warning btn-sm">تطبيق قالب الصلاحيات</button>
                    </form>
                    <form action="{{ route('admin.department-heads.destroy', $head->id) }}" method="POST" class="d-inline"
                          onsubmit="return confirm('إلغاء تعيين هذا المستخدم من جميع الأقسام؟');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">إلغاء التعيين الكامل</button>
                    </form>
                    @endcan
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center">
                            <span class="text-muted small d-block">الأقسام المباشرة</span>
                            <h3 class="mb-0">{{ $managedDepartments->count() }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center">
                            <span class="text-muted small d-block">حجم الفريق (تقريبي)</span>
                            <h3 class="mb-0">{{ count($managedEmployeeIds) }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center">
                            <span class="text-muted small d-block">إجازات معلّقة</span>
                            <h3 class="mb-0 text-warning">{{ $pendingLeaves }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center">
                            <span class="text-muted small d-block">الصلاحيات</span>
                            <h3 class="mb-0">{{ $permissions->count() }}</h3>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header"><h6 class="mb-0">بيانات المستخدم</h6></div>
                        <div class="card-body">
                            <dl class="row mb-0">
                                <dt class="col-sm-4">الاسم</dt>
                                <dd class="col-sm-8">{{ $head->name }}</dd>
                                <dt class="col-sm-4">البريد</dt>
                                <dd class="col-sm-8">{{ $head->email }}</dd>
                                <dt class="col-sm-4">الحالة</dt>
                                <dd class="col-sm-8">
                                    @if ($head->is_active)
                                        <span class="badge bg-success">نشط</span>
                                    @else
                                        <span class="badge bg-danger">معطّل</span>
                                    @endif
                                </dd>
                                <dt class="col-sm-4">الأدوار</dt>
                                <dd class="col-sm-8">
                                    @forelse ($head->roles as $role)
                                        <span class="badge bg-primary me-1">{{ $role->name }}</span>
                                    @empty
                                        <span class="text-muted">—</span>
                                    @endforelse
                                </dd>
                                @if ($head->employee)
                                <dt class="col-sm-4">ملف الموظف</dt>
                                <dd class="col-sm-8">
                                    <a href="{{ route('admin.employees.show', $head->employee->id) }}">{{ $head->employee->full_name }}</a>
                                </dd>
                                @endif
                            </dl>
                            @if (auth()->id() === $head->id && $head->isDepartmentHead())
                            <div class="mt-3 pt-3 border-top">
                                <a href="{{ route('admin.team.dashboard') }}" class="btn btn-outline-primary btn-sm">
                                    لوحة إدارة الفريق
                                </a>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">الأقسام المُدارة</h6>
                            @can('department-edit')
                            <a href="{{ route('admin.departments.index') }}" class="btn btn-link btn-sm p-0">كل الأقسام</a>
                            @endcan
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>القسم</th>
                                            <th>الموظفون</th>
                                            <th>القسم الأب</th>
                                            @can('department-head-manage')
                                            <th></th>
                                            @endcan
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($managedDepartments as $department)
                                            <tr>
                                                <td>
                                                    <a href="{{ route('admin.departments.show', $department->id) }}">{{ $department->name }}</a>
                                                </td>
                                                <td>{{ $department->employees_count }}</td>
                                                <td>{{ $department->parent?->name ?? '—' }}</td>
                                                @can('department-head-manage')
                                                <td>
                                                    <form action="{{ route('admin.department-heads.remove-department', [$head->id, $department->id]) }}"
                                                          method="POST" class="d-inline"
                                                          onsubmit="return confirm('إزالة هذا القسم من رئيس القسم؟');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger">إزالة</button>
                                                    </form>
                                                </td>
                                                @endcan
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center text-muted py-3">لا توجد أقسام معيّنة.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="card border-info border-opacity-25">
                        <div class="card-body d-flex flex-wrap align-items-center justify-content-between gap-2">
                            <div>
                                <h6 class="mb-1">الصلاحيات والقدرات</h6>
                                <p class="text-muted small mb-0">
                                    {{ $permissions->count() }} صلاحية مفعّلة
                                    @if ($roleTemplate)
                                        — القالب الافتراضي {{ count($roleTemplate['permissions'] ?? []) }} صلاحية
                                    @endif
                                </p>
                            </div>
                            <a href="{{ route('admin.department-heads.capabilities', $head->id) }}" class="btn btn-info btn-sm">
                                عرض التوضيح الكامل
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
