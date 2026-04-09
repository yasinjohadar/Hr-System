@extends('admin.layouts.master')

@section('page-title')
    سلف الموظفين
@stop

@section('content')
    @if (\Session::has('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {!! \Session::get('success') !!}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if (\Session::has('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {!! \Session::get('error') !!}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">سلف الموظفين</h5>
                </div>
                @can('employee-advance-create')
                    <a href="{{ route('admin.employee-advances.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus me-1"></i>تسجيل سلفة جديدة
                    </a>
                @endcan
            </div>

            <div class="card mb-3">
                <div class="card-body py-3">
                    <form method="GET" action="{{ route('admin.employee-advances.index') }}" class="row g-2 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label small text-muted mb-0">الموظف</label>
                            <select name="employee_id" class="form-select form-select-sm">
                                <option value="">كل الموظفين</option>
                                @foreach ($employees as $emp)
                                    <option value="{{ $emp->id }}" @selected((string) request('employee_id') === (string) $emp->id)>
                                        {{ $emp->full_name ?? $emp->first_name . ' ' . $emp->last_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-sm btn-secondary">تصفية</button>
                            <a href="{{ route('admin.employee-advances.index') }}" class="btn btn-sm btn-outline-secondary">الكل</a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>الموظف</th>
                                    <th>المبلغ الأصلي</th>
                                    <th>المتبقي</th>
                                    <th>تاريخ المنح</th>
                                    <th>الحالة</th>
                                    <th>العمليات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($advances as $advance)
                                    <tr>
                                        <th>{{ $advances->firstItem() + $loop->index }}</th>
                                        <td>
                                            <strong>{{ $advance->employee->full_name ?? $advance->employee->first_name . ' ' . $advance->employee->last_name }}</strong>
                                            <br><small class="text-muted">{{ $advance->employee->employee_code ?? '' }}</small>
                                        </td>
                                        <td>{{ number_format($advance->principal_amount, 2) }}</td>
                                        <td>{{ number_format($advance->remaining_balance, 2) }}</td>
                                        <td>{{ $advance->granted_at ? $advance->granted_at->format('Y-m-d') : '—' }}</td>
                                        <td>
                                            @if ($advance->status === 'active')
                                                <span class="badge bg-success">نشطة</span>
                                            @else
                                                <span class="badge bg-secondary">مغلقة</span>
                                            @endif
                                        </td>
                                        <td>
                                            @can('employee-advance-edit')
                                                <a href="{{ route('admin.employee-advances.edit', $advance) }}" class="btn btn-warning btn-sm">تعديل</a>
                                            @endcan
                                            @can('employee-advance-delete')
                                                <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteAdvance{{ $advance->id }}">حذف</button>
                                            @endcan
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">لا توجد سلف مسجلة</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="p-3">
                        {{ $advances->withQueryString()->links() }}
                    </div>
                </div>
            </div>

            @can('employee-advance-delete')
                @foreach ($advances as $advance)
                    <div class="modal fade" id="deleteAdvance{{ $advance->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">تأكيد الحذف</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    حذف سلفة الموظف {{ $advance->employee->full_name ?? '' }}؟
                                </div>
                                <div class="modal-footer">
                                    <form action="{{ route('admin.employee-advances.destroy', $advance) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                                        <button type="submit" class="btn btn-danger">حذف</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endcan
        </div>
    </div>
@stop
