@extends('admin.layouts.master')

@section('page-title')
    مكافآت الموظفين
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">مكافآت الموظفين</h5>
                </div>
                <div>
                    @can('employee-reward-create')
                    <a href="{{ route('admin.employee-rewards.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>إضافة مكافأة جديدة
                    </a>
                    @endcan
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.employee-rewards.index') }}" class="row g-3">
                        <div class="col-md-3">
                            <select name="employee_id" class="form-select">
                                <option value="">كل الموظفين</option>
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}" {{ request('employee_id') == $employee->id ? 'selected' : '' }}>
                                        {{ $employee->full_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="status" class="form-select">
                                <option value="">كل الحالات</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>موافق عليه</option>
                                <option value="awarded" {{ request('status') == 'awarded' ? 'selected' : '' }}>ممنوح</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="reason" class="form-select">
                                <option value="">كل الأسباب</option>
                                <option value="performance" {{ request('reason') == 'performance' ? 'selected' : '' }}>أداء</option>
                                <option value="achievement" {{ request('reason') == 'achievement' ? 'selected' : '' }}>إنجاز</option>
                                <option value="recognition" {{ request('reason') == 'recognition' ? 'selected' : '' }}>اعتراف</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary w-100">تطبيق</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">قائمة المكافآت ({{ $rewards->total() }})</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>كود المكافأة</th>
                                    <th>الموظف</th>
                                    <th>نوع المكافأة</th>
                                    <th>العنوان</th>
                                    <th>القيمة</th>
                                    <th>تاريخ المكافأة</th>
                                    <th>الحالة</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($rewards as $reward)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $reward->reward_code }}</td>
                                        <td>{{ $reward->employee->full_name ?? '-' }}</td>
                                        <td>{{ $reward->rewardType->name_ar ?? $reward->rewardType->name }}</td>
                                        <td>{{ Str::limit($reward->title, 30) }}</td>
                                        <td>
                                            @if($reward->monetary_value)
                                                {{ number_format($reward->monetary_value, 2) }} {{ $reward->currency->code ?? 'ر.س' }}
                                            @elseif($reward->points)
                                                {{ $reward->points }} نقطة
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>{{ $reward->reward_date->format('Y-m-d') }}</td>
                                        <td>
                                            <span class="badge bg-{{ $reward->status == 'awarded' ? 'success' : ($reward->status == 'approved' ? 'info' : 'warning') }}">
                                                {{ $reward->status_name_ar }}
                                            </span>
                                        </td>
                                        <td>
                                            @can('employee-reward-show')
                                            <a href="{{ route('admin.employee-rewards.show', $reward->id) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @endcan
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center">لا توجد مكافآت</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $rewards->withQueryString()->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

