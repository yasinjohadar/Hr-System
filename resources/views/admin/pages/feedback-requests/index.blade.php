@extends('admin.layouts.master')

@section('page-title')
    التقييم 360 درجة
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">التقييم 360 درجة</h5>
                </div>
                <div>
                    @can('feedback-request-create')
                    <a href="{{ route('admin.feedback-requests.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>إنشاء طلب تقييم جديد
                    </a>
                    @endcan
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.feedback-requests.index') }}" class="row g-3">
                        <div class="col-md-4">
                            <input type="text" name="search" class="form-control" placeholder="بحث..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3">
                            <select name="status" class="form-select">
                                <option value="">كل الحالات</option>
                                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>مسودة</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>نشط</option>
                                <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>قيد التنفيذ</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>مكتمل</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="feedback_type" class="form-select">
                                <option value="">كل الأنواع</option>
                                <option value="360_degree" {{ request('feedback_type') == '360_degree' ? 'selected' : '' }}>360 درجة</option>
                                <option value="peer" {{ request('feedback_type') == 'peer' ? 'selected' : '' }}>زملاء</option>
                                <option value="subordinate" {{ request('feedback_type') == 'subordinate' ? 'selected' : '' }}>مرؤوسين</option>
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
                    <h5 class="card-title mb-0">قائمة طلبات التقييم ({{ $requests->total() }})</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>كود الطلب</th>
                                    <th>الموظف</th>
                                    <th>نوع التقييم</th>
                                    <th>تاريخ البدء</th>
                                    <th>تاريخ الانتهاء</th>
                                    <th>عدد الردود</th>
                                    <th>الحالة</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($requests as $request)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $request->request_code }}</td>
                                        <td>{{ $request->employee->full_name ?? '-' }}</td>
                                        <td><span class="badge bg-info">{{ $request->feedback_type_name_ar }}</span></td>
                                        <td>{{ $request->start_date->format('Y-m-d') }}</td>
                                        <td>{{ $request->end_date->format('Y-m-d') }}</td>
                                        <td>{{ $request->responses_count }}</td>
                                        <td><span class="badge bg-{{ $request->status == 'completed' ? 'success' : ($request->status == 'active' ? 'primary' : 'secondary') }}">{{ $request->status_name_ar }}</span></td>
                                        <td>
                                            @can('feedback-request-show')
                                            <a href="{{ route('admin.feedback-requests.show', $request->id) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @endcan
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center">لا توجد طلبات تقييم</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $requests->withQueryString()->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

