@extends('admin.layouts.master')

@section('page-title')
    تخطيط التعاقب الوظيفي
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">تخطيط التعاقب الوظيفي</h5>
                </div>
                <div>
                    @can('succession-plan-create')
                    <a href="{{ route('admin.succession-plans.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>إضافة خطة جديدة
                    </a>
                    @endcan
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.succession-plans.index') }}" class="row g-3">
                        <div class="col-md-4">
                            <input type="text" name="search" class="form-control" placeholder="بحث..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3">
                            <select name="status" class="form-select">
                                <option value="">كل الحالات</option>
                                <option value="planning" {{ request('status') == 'planning' ? 'selected' : '' }}>قيد التخطيط</option>
                                <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>قيد التنفيذ</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>مكتمل</option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>ملغي</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="urgency" class="form-select">
                                <option value="">كل الأولويات</option>
                                <option value="low" {{ request('urgency') == 'low' ? 'selected' : '' }}>منخفض</option>
                                <option value="medium" {{ request('urgency') == 'medium' ? 'selected' : '' }}>متوسط</option>
                                <option value="high" {{ request('urgency') == 'high' ? 'selected' : '' }}>عالي</option>
                                <option value="critical" {{ request('urgency') == 'critical' ? 'selected' : '' }}>حرج</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">تطبيق</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">قائمة الخطط ({{ $plans->total() }})</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>كود الخطة</th>
                                    <th>المنصب</th>
                                    <th>الموظف الحالي</th>
                                    <th>الأولوية</th>
                                    <th>عدد المرشحين</th>
                                    <th>الحالة</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($plans as $plan)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $plan->plan_code }}</td>
                                        <td>{{ $plan->position->title ?? '-' }}</td>
                                        <td>{{ $plan->currentEmployee->full_name ?? '-' }}</td>
                                        <td><span class="badge bg-{{ $plan->urgency == 'critical' ? 'danger' : ($plan->urgency == 'high' ? 'warning' : 'info') }}">{{ $plan->urgency_name_ar }}</span></td>
                                        <td>{{ $plan->candidates_count }}</td>
                                        <td><span class="badge bg-info">{{ $plan->status_name_ar }}</span></td>
                                        <td>
                                            @can('succession-plan-show')
                                            <a href="{{ route('admin.succession-plans.show', $plan->id) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @endcan
                                            @can('succession-plan-edit')
                                            <a href="{{ route('admin.succession-plans.edit', $plan->id) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @endcan
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">لا توجد خطط</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $plans->withQueryString()->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

