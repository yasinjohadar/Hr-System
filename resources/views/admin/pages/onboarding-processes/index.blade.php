@extends('admin.layouts.master')

@section('page-title')
    عمليات الاستقبال
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">عمليات الاستقبال</h5>
                </div>
                <div>
                    @can('onboarding-process-create')
                    <a href="{{ route('admin.onboarding-processes.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>إنشاء عملية جديدة
                    </a>
                    @endcan
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.onboarding-processes.index') }}" class="row g-3">
                        <div class="col-md-4">
                            <input type="text" name="search" class="form-control" placeholder="بحث..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3">
                            <select name="status" class="form-select">
                                <option value="">كل الحالات</option>
                                <option value="not_started" {{ request('status') == 'not_started' ? 'selected' : '' }}>لم يبدأ</option>
                                <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>قيد التنفيذ</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>مكتمل</option>
                                <option value="on_hold" {{ request('status') == 'on_hold' ? 'selected' : '' }}>معلق</option>
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
                    <h5 class="card-title mb-0">قائمة العمليات ({{ $processes->total() }})</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>كود العملية</th>
                                    <th>الموظف</th>
                                    <th>القالب</th>
                                    <th>تاريخ البدء</th>
                                    <th>عدد المهام</th>
                                    <th>نسبة الإنجاز</th>
                                    <th>الحالة</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($processes as $process)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $process->process_code }}</td>
                                        <td>{{ $process->employee->full_name ?? '-' }}</td>
                                        <td>{{ $process->template->name_ar ?? $process->template->name ?? '-' }}</td>
                                        <td>{{ $process->start_date->format('Y-m-d') }}</td>
                                        <td>{{ $process->checklists_count }}</td>
                                        <td>
                                            <div class="progress" style="height: 20px;">
                                                <div class="progress-bar" role="progressbar" style="width: {{ $process->completion_percentage }}%">
                                                    {{ $process->completion_percentage }}%
                                                </div>
                                            </div>
                                        </td>
                                        <td><span class="badge bg-info">{{ $process->status_name_ar }}</span></td>
                                        <td>
                                            @can('onboarding-process-show')
                                            <a href="{{ route('admin.onboarding-processes.show', $process->id) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @endcan
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center">لا توجد عمليات</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $processes->withQueryString()->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

