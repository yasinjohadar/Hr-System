@extends('admin.layouts.master')

@section('page-title')
    إدارة العقود
@stop

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">إدارة العقود</h5>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header align-items-center d-flex gap-3 flex-wrap">
                            <a href="{{ route('admin.contracts.create') }}" class="btn btn-primary btn-sm">إضافة عقد جديد</a>
                            <form action="{{ route('admin.contracts.index') }}" method="GET" class="d-flex align-items-center gap-2 flex-wrap">
                                <select name="employee_id" class="form-select" style="width: 180px">
                                    <option value="">كل الموظفين</option>
                                    @foreach ($employees as $emp)
                                        <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>{{ $emp->full_name }}</option>
                                    @endforeach
                                </select>
                                <select name="expiring" class="form-select" style="width: 160px">
                                    <option value="">— قاربت على الانتهاء —</option>
                                    <option value="30" {{ request('expiring') == '30' ? 'selected' : '' }}>خلال 30 يوم</option>
                                    <option value="60" {{ request('expiring') == '60' ? 'selected' : '' }}>خلال 60 يوم</option>
                                    <option value="90" {{ request('expiring') == '90' ? 'selected' : '' }}>خلال 90 يوم</option>
                                </select>
                                <select name="status" class="form-select" style="width: 130px">
                                    <option value="">كل الحالات</option>
                                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>نشط</option>
                                    <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>منتهي</option>
                                    <option value="renewed" {{ request('status') == 'renewed' ? 'selected' : '' }}>تم تجديده</option>
                                    <option value="terminated" {{ request('status') == 'terminated' ? 'selected' : '' }}>منهي</option>
                                </select>
                                <button type="submit" class="btn btn-secondary">بحث</button>
                                <a href="{{ route('admin.contracts.index') }}" class="btn btn-danger">مسح</a>
                            </form>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover align-middle table-nowrap mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>الموظف</th>
                                            <th>نوع العقد</th>
                                            <th>تاريخ البداية</th>
                                            <th>تاريخ النهاية</th>
                                            <th>الأيام المتبقية</th>
                                            <th>الحالة</th>
                                            <th>العمليات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($contracts as $contract)
                                            <tr>
                                                <th>{{ $loop->iteration + ($contracts->currentPage() - 1) * $contracts->perPage() }}</th>
                                                <td>
                                                    <a href="{{ route('admin.employees.show', $contract->employee_id) }}">{{ $contract->employee->full_name ?? $contract->employee->employee_code }}</a>
                                                </td>
                                                <td>{{ $contract->contract_type_label }}</td>
                                                <td>{{ $contract->start_date->format('Y-m-d') }}</td>
                                                <td>{{ $contract->end_date->format('Y-m-d') }}</td>
                                                <td>
                                                    @if($contract->days_remaining !== null)
                                                        @if($contract->days_remaining < 0)
                                                            <span class="text-danger">منتهي</span>
                                                        @elseif($contract->days_remaining <= 30)
                                                            <span class="badge bg-danger">{{ $contract->days_remaining }} يوم</span>
                                                        @elseif($contract->days_remaining <= 90)
                                                            <span class="badge bg-warning text-dark">{{ $contract->days_remaining }} يوم</span>
                                                        @else
                                                            <span class="text-muted">{{ $contract->days_remaining }} يوم</span>
                                                        @endif
                                                    @else
                                                        —
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($contract->status === 'active')
                                                        <span class="badge bg-success">{{ $contract->status_label }}</span>
                                                    @elseif($contract->status === 'expired')
                                                        <span class="badge bg-secondary">{{ $contract->status_label }}</span>
                                                    @elseif($contract->status === 'renewed')
                                                        <span class="badge bg-info">{{ $contract->status_label }}</span>
                                                    @else
                                                        <span class="badge bg-warning text-dark">{{ $contract->status_label }}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <a class="btn btn-info btn-sm me-1" href="{{ route('admin.contracts.show', $contract) }}" title="عرض"><i class="fa-solid fa-eye"></i></a>
                                                    <a class="btn btn-warning btn-sm me-1" href="{{ route('admin.contracts.edit', $contract) }}" title="تعديل"><i class="fa-solid fa-pen-to-square"></i></a>
                                                    @if(in_array($contract->status, ['active', 'expired']))
                                                        <a class="btn btn-success btn-sm me-1" href="{{ route('admin.contracts.renew', $contract) }}" title="تجديد"><i class="fa-solid fa-rotate-right"></i></a>
                                                    @endif
                                                    <a class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#delete{{ $contract->id }}" title="حذف"><i class="fa-solid fa-trash-can"></i></a>
                                                </td>
                                            </tr>
                                            @include('admin.pages.contracts.delete')
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center text-muted py-4">لا توجد عقود.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                                @if ($contracts->hasPages())
                                    <div class="mt-3">{{ $contracts->withQueryString()->links() }}</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
@stop
