@extends('admin.layouts.master')

@section('page-title')
    مخالفات الموظفين
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">مخالفات الموظفين</h5>
                </div>
                <div>
                    @can('employee-violation-create')
                    <a href="{{ route('admin.employee-violations.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>إضافة مخالفة جديدة
                    </a>
                    @endcan
                </div>
            </div>

            <!-- فلترة -->
            <div class="card mb-3">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.employee-violations.index') }}" class="row g-3">
                        <div class="col-md-3">
                            <select name="employee_id" class="form-select">
                                <option value="">كل الموظفين</option>
                                @foreach ($employees as $emp)
                                    <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>
                                        {{ $emp->full_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="violation_type_id" class="form-select">
                                <option value="">كل الأنواع</option>
                                @foreach ($violationTypes as $type)
                                    <option value="{{ $type->id }}" {{ request('violation_type_id') == $type->id ? 'selected' : '' }}>
                                        {{ $type->name_ar ?? $type->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="status" class="form-select">
                                <option value="">كل الحالات</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                                <option value="investigating" {{ request('status') == 'investigating' ? 'selected' : '' }}>قيد التحقيق</option>
                                <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>مؤكد</option>
                                <option value="dismissed" {{ request('status') == 'dismissed' ? 'selected' : '' }}>مرفوض</option>
                                <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>محلول</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="severity" class="form-select">
                                <option value="">كل الخطورات</option>
                                <option value="low" {{ request('severity') == 'low' ? 'selected' : '' }}>منخفض</option>
                                <option value="medium" {{ request('severity') == 'medium' ? 'selected' : '' }}>متوسط</option>
                                <option value="high" {{ request('severity') == 'high' ? 'selected' : '' }}>عالي</option>
                                <option value="critical" {{ request('severity') == 'critical' ? 'selected' : '' }}>حرج</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">تطبيق</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- جدول المخالفات -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">قائمة المخالفات ({{ $violations->total() }})</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>رقم المخالفة</th>
                                    <th>الموظف</th>
                                    <th>نوع المخالفة</th>
                                    <th>تاريخ المخالفة</th>
                                    <th>الخطورة</th>
                                    <th>الحالة</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($violations as $violation)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td><strong>{{ $violation->violation_code }}</strong></td>
                                        <td>
                                            <a href="{{ route('admin.employees.show', $violation->employee_id) }}">
                                                {{ $violation->employee->full_name }}
                                            </a>
                                        </td>
                                        <td>{{ $violation->violationType->name_ar ?? $violation->violationType->name }}</td>
                                        <td>{{ $violation->violation_date->format('Y-m-d') }}</td>
                                        <td>
                                            <span class="badge bg-{{ $violation->severity == 'critical' ? 'danger' : ($violation->severity == 'high' ? 'warning' : 'info') }}">
                                                {{ $violation->severity_name_ar }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $violation->status == 'resolved' ? 'success' : ($violation->status == 'confirmed' ? 'primary' : ($violation->status == 'dismissed' ? 'secondary' : 'warning')) }}">
                                                {{ $violation->status_name_ar }}
                                            </span>
                                        </td>
                                        <td>
                                            @can('employee-violation-show')
                                            <a href="{{ route('admin.employee-violations.show', $violation->id) }}" class="btn btn-sm btn-primary" title="عرض">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @endcan
                                            @if (in_array($violation->status, ['pending', 'dismissed']))
                                            @can('employee-violation-edit')
                                            <a href="{{ route('admin.employee-violations.edit', $violation->id) }}" class="btn btn-sm btn-info" title="تعديل">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @endcan
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">لا توجد مخالفات</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $violations->withQueryString()->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

