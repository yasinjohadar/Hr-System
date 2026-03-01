@extends('admin.layouts.master')

@section('page-title')
    قائمة مزايا الموظفين
@stop

@section('content')
    @if (\Session::has('success'))
        <div class="alert alert-success">
            <ul>
                <li>{!! \Session::get('success') !!}</li>
            </ul>
        </div>
    @endif

    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">كافة مزايا الموظفين</h5>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header align-items-center d-flex gap-3">
                            @can('employee-benefit-create')
                            <a href="{{ route('admin.employee-benefits.create') }}" class="btn btn-primary btn-sm">إضافة ميزة موظف جديدة</a>
                            @endcan

                            <div class="flex-shrink-0">
                                <form action="{{ route('admin.employee-benefits.index') }}" method="GET" class="d-flex align-items-center gap-2">
                                    <input style="width: 300px" type="text" name="search" class="form-control"
                                        placeholder="بحث" value="{{ request('search') }}">
                                    <select name="status" class="form-select">
                                        <option value="">كل الحالات</option>
                                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>نشط</option>
                                        <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>معلق</option>
                                        <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>منتهي</option>
                                    </select>
                                    <select name="employee_id" class="form-select">
                                        <option value="">كل الموظفين</option>
                                        @foreach ($employees as $employee)
                                            <option value="{{ $employee->id }}" {{ request('employee_id') == $employee->id ? 'selected' : '' }}>
                                                {{ $employee->full_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="btn btn-secondary">بحث</button>
                                    <a href="{{ route('admin.employee-benefits.index') }}" class="btn btn-danger">مسح</a>
                                </form>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover align-middle table-nowrap mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>الموظف</th>
                                            <th>نوع الميزة</th>
                                            <th>القيمة</th>
                                            <th>تاريخ البدء</th>
                                            <th>تاريخ الانتهاء</th>
                                            <th>الحالة</th>
                                            <th>العمليات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($employeeBenefits as $benefit)
                                            <tr>
                                                <th>{{ $loop->iteration }}</th>
                                                <td>
                                                    <strong>{{ $benefit->employee->full_name }}</strong>
                                                    <br><small class="text-muted">{{ $benefit->employee->employee_code }}</small>
                                                </td>
                                                <td>{{ $benefit->benefitType->name_ar ?? $benefit->benefitType->name }}</td>
                                                <td>
                                                    @if ($benefit->value)
                                                        {{ number_format($benefit->value, 2) }}
                                                        @if ($benefit->currency)
                                                            {{ $benefit->currency->symbol_ar ?? $benefit->currency->symbol }}
                                                        @endif
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>{{ $benefit->start_date->format('Y-m-d') }}</td>
                                                <td>{{ $benefit->end_date ? $benefit->end_date->format('Y-m-d') : 'دائم' }}</td>
                                                <td>
                                                    <span class="badge bg-{{ $benefit->status == 'active' ? 'success' : ($benefit->status == 'expired' ? 'danger' : 'warning') }}">
                                                        {{ $benefit->status_name_ar }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @can('employee-benefit-show')
                                                    <a class="btn btn-info btn-sm me-1" href="{{ route('admin.employee-benefits.show', $benefit->id) }}" title="عرض">
                                                        <i class="fa-solid fa-eye"></i>
                                                    </a>
                                                    @endcan
                                                    @can('employee-benefit-edit')
                                                    <a class="btn btn-warning btn-sm me-1" href="{{ route('admin.employee-benefits.edit', $benefit->id) }}" title="تعديل">
                                                        <i class="fa-solid fa-pen-to-square"></i>
                                                    </a>
                                                    @endcan
                                                    @can('employee-benefit-delete')
                                                    <a class="btn btn-danger btn-sm me-1" data-bs-toggle="modal" data-bs-target="#delete{{ $benefit->id }}" title="حذف">
                                                        <i class="fa-solid fa-trash-can"></i>
                                                    </a>
                                                    @endcan
                                                </td>
                                            </tr>
                                            @include('admin.pages.employee-benefits.delete')
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center text-danger fw-bold">لا توجد بيانات متاحة</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>

                                <div class="mt-3">
                                    {{ $employeeBenefits->withQueryString()->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop


