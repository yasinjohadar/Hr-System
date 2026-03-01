@extends('admin.layouts.master')

@section('page-title')
    موافقات الرواتب
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
                    <h5 class="page-title fs-21 mb-1">موافقات الرواتب</h5>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header align-items-center d-flex gap-3">
                            @can('payroll-approval-create')
                            <a href="{{ route('admin.payroll-approvals.create') }}" class="btn btn-primary btn-sm">إضافة موافقة جديدة</a>
                            @endcan

                            <div class="flex-shrink-0">
                                <form action="{{ route('admin.payroll-approvals.index') }}" method="GET" class="d-flex align-items-center gap-2">
                                    <input type="text" name="search" class="form-control" placeholder="بحث..." value="{{ request('search') }}" style="width: 200px;">
                                    
                                    <select name="payroll_id" class="form-select" style="width: 200px;">
                                        <option value="">كل كشوف الرواتب</option>
                                        @foreach ($payrolls as $payroll)
                                            <option value="{{ $payroll->id }}" {{ request('payroll_id') == $payroll->id ? 'selected' : '' }}>
                                                {{ $payroll->payroll_code }} - {{ $payroll->employee->full_name }}
                                            </option>
                                        @endforeach
                                    </select>

                                    <select name="status" class="form-select" style="width: 150px;">
                                        <option value="">كل الحالات</option>
                                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>موافق عليه</option>
                                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>مرفوض</option>
                                    </select>

                                    <select name="approval_level" class="form-select" style="width: 150px;">
                                        <option value="">كل المستويات</option>
                                        @for($i = 1; $i <= 5; $i++)
                                            <option value="{{ $i }}" {{ request('approval_level') == $i ? 'selected' : '' }}>المستوى {{ $i }}</option>
                                        @endfor
                                    </select>

                                    <button type="submit" class="btn btn-primary btn-sm">بحث</button>
                                    <a href="{{ route('admin.payroll-approvals.index') }}" class="btn btn-secondary btn-sm">إعادة تعيين</a>
                                </form>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered text-nowrap border-bottom">
                                    <thead>
                                        <tr>
                                            <th>كشف الراتب</th>
                                            <th>الموظف</th>
                                            <th>مستوى الموافقة</th>
                                            <th>الموافق</th>
                                            <th>الحالة</th>
                                            <th>تاريخ الموافقة/الرفض</th>
                                            <th>الإجراءات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($approvals as $approval)
                                            <tr>
                                                <td>{{ $approval->payroll->payroll_code }}</td>
                                                <td>{{ $approval->payroll->employee->full_name }}</td>
                                                <td>
                                                    <span class="badge bg-secondary">المستوى {{ $approval->approval_level }}</span>
                                                </td>
                                                <td>{{ $approval->approver->name }}</td>
                                                <td>
                                                    <span class="badge bg-{{ match($approval->status) {
                                                        'approved' => 'success',
                                                        'rejected' => 'danger',
                                                        'pending' => 'warning',
                                                        default => 'secondary'
                                                    } }}">
                                                        {{ $approval->status_name_ar }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if($approval->approved_at)
                                                        {{ $approval->approved_at->format('Y-m-d H:i') }}
                                                    @elseif($approval->rejected_at)
                                                        {{ $approval->rejected_at->format('Y-m-d H:i') }}
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="d-flex gap-2">
                                                        @can('payroll-approval-show')
                                                        <a href="{{ route('admin.payroll-approvals.show', $approval->id) }}" class="btn btn-sm btn-info">عرض</a>
                                                        @endcan
                                                        @can('payroll-approval-edit')
                                                        @if($approval->status === 'pending')
                                                        <a href="{{ route('admin.payroll-approvals.edit', $approval->id) }}" class="btn btn-sm btn-primary">تعديل</a>
                                                        @endif
                                                        @endcan
                                                        @can('payroll-approval-delete')
                                                        @if($approval->status === 'pending')
                                                        <form action="{{ route('admin.payroll-approvals.destroy', $approval->id) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من الحذف؟');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger">حذف</button>
                                                        </form>
                                                        @endif
                                                        @endcan
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center">لا توجد موافقات</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <div class="d-flex justify-content-center mt-3">
                                {{ $approvals->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

