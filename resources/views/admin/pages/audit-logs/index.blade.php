@extends('admin.layouts.master')

@section('page-title')
    سجلات التدقيق
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">سجلات التدقيق</h5>
                </div>
                <div>
                    @can('audit-log-export')
                    <a href="{{ route('admin.audit-logs.export', request()->query()) }}" class="btn btn-primary">
                        <i class="fas fa-download me-2"></i>تصدير
                    </a>
                    @endcan
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.audit-logs.index') }}" class="row g-3">
                        <div class="col-md-3">
                            <input type="text" name="search" class="form-control" placeholder="بحث..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-2">
                            <select name="action" class="form-select">
                                <option value="">كل الإجراءات</option>
                                <option value="create" {{ request('action') == 'create' ? 'selected' : '' }}>إنشاء</option>
                                <option value="update" {{ request('action') == 'update' ? 'selected' : '' }}>تحديث</option>
                                <option value="delete" {{ request('action') == 'delete' ? 'selected' : '' }}>حذف</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="severity" class="form-select">
                                <option value="">كل المستويات</option>
                                <option value="low" {{ request('severity') == 'low' ? 'selected' : '' }}>منخفض</option>
                                <option value="medium" {{ request('severity') == 'medium' ? 'selected' : '' }}>متوسط</option>
                                <option value="high" {{ request('severity') == 'high' ? 'selected' : '' }}>عالي</option>
                                <option value="critical" {{ request('severity') == 'critical' ? 'selected' : '' }}>حرج</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}" placeholder="من تاريخ">
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary w-100">تطبيق</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">قائمة السجلات ({{ $logs->total() }})</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>التاريخ والوقت</th>
                                    <th>المستخدم</th>
                                    <th>الإجراء</th>
                                    <th>النموذج</th>
                                    <th>الوصف</th>
                                    <th>المستوى</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($logs as $log)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $log->created_at->format('Y-m-d H:i') }}</td>
                                        <td>{{ $log->user->name ?? 'نظام' }}</td>
                                        <td><span class="badge bg-primary">{{ $log->action_name_ar }}</span></td>
                                        <td>{{ $log->model_type }}</td>
                                        <td>{{ Str::limit($log->description, 50) }}</td>
                                        <td>
                                            <span class="badge bg-{{ $log->severity == 'critical' ? 'danger' : ($log->severity == 'high' ? 'warning' : 'info') }}">
                                                {{ $log->severity_name_ar }}
                                            </span>
                                        </td>
                                        <td>
                                            @can('audit-log-show')
                                            <a href="{{ route('admin.audit-logs.show', $log->id) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @endcan
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">لا توجد سجلات</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $logs->withQueryString()->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

