@extends('admin.layouts.master')

@section('page-title')
    التغييرات الوظيفية (النقل والترقية)
@stop

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">التغييرات الوظيفية</h5>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header align-items-center d-flex gap-3 flex-wrap">
                            <a href="{{ route('admin.employee-job-changes.create') }}" class="btn btn-primary btn-sm">إضافة طلب تغيير وظيفي</a>
                            <form action="{{ route('admin.employee-job-changes.index') }}" method="GET" class="d-flex align-items-center gap-2 flex-wrap">
                                <select name="employee_id" class="form-select" style="width: 180px">
                                    <option value="">كل الموظفين</option>
                                    @foreach ($employees as $emp)
                                        <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>{{ $emp->full_name }}</option>
                                    @endforeach
                                </select>
                                <select name="status" class="form-select" style="width: 130px">
                                    <option value="">كل الحالات</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>تمت الموافقة</option>
                                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>مرفوض</option>
                                </select>
                                <select name="change_type" class="form-select" style="width: 130px">
                                    <option value="">كل الأنواع</option>
                                    <option value="transfer" {{ request('change_type') == 'transfer' ? 'selected' : '' }}>نقل</option>
                                    <option value="promotion" {{ request('change_type') == 'promotion' ? 'selected' : '' }}>ترقية</option>
                                    <option value="salary_change" {{ request('change_type') == 'salary_change' ? 'selected' : '' }}>تعديل راتب</option>
                                    <option value="demotion" {{ request('change_type') == 'demotion' ? 'selected' : '' }}>تنزيل</option>
                                </select>
                                <input type="date" name="date_from" class="form-control" style="width: 150px" value="{{ request('date_from') }}" placeholder="من تاريخ">
                                <input type="date" name="date_to" class="form-control" style="width: 150px" value="{{ request('date_to') }}" placeholder="إلى تاريخ">
                                <button type="submit" class="btn btn-secondary">بحث</button>
                                <a href="{{ route('admin.employee-job-changes.index') }}" class="btn btn-danger">مسح</a>
                            </form>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover align-middle table-nowrap mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>الموظف</th>
                                            <th>نوع التغيير</th>
                                            <th>التاريخ الفعال</th>
                                            <th>الحالة</th>
                                            <th>تاريخ الطلب</th>
                                            <th>العمليات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($jobChanges as $jobChange)
                                            <tr>
                                                <th>{{ $loop->iteration + ($jobChanges->currentPage() - 1) * $jobChanges->perPage() }}</th>
                                                <td>
                                                    <a href="{{ route('admin.employees.show', $jobChange->employee_id) }}">
                                                        {{ $jobChange->employee->full_name ?? $jobChange->employee->employee_code }}
                                                    </a>
                                                </td>
                                                <td>
                                                    <span class="badge bg-info">{{ $jobChange->change_type_label }}</span>
                                                </td>
                                                <td>{{ $jobChange->effective_date->format('Y-m-d') }}</td>
                                                <td>
                                                    <span class="badge bg-{{ $jobChange->status_color }}">
                                                        {{ $jobChange->status_label }}
                                                    </span>
                                                </td>
                                                <td>{{ $jobChange->created_at->format('Y-m-d H:i') }}</td>
                                                <td>
                                                    <div class="btn-group">
                                                        <a href="{{ route('admin.employee-job-changes.show', $jobChange) }}" class="btn btn-sm btn-info" title="عرض">
                                                            <i class="fa fa-eye"></i>
                                                        </a>
                                                        @if ($jobChange->canBeEdited())
                                                            <a href="{{ route('admin.employee-job-changes.edit', $jobChange) }}" class="btn btn-sm btn-warning" title="تعديل">
                                                                <i class="fa fa-edit"></i>
                                                            </a>
                                                        @endif
                                                        @if ($jobChange->canBeApproved())
                                                            <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#approveModal{{ $jobChange->id }}" title="موافقة">
                                                                <i class="fa fa-check"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $jobChange->id }}" title="رفض">
                                                                <i class="fa fa-times"></i>
                                                            </button>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center py-4">لا توجد طلبات تغيير وظيفي</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div class="d-flex justify-content-center mt-3">
                                {{ $jobChanges->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modals for Approve/Reject -->
    @foreach ($jobChanges as $jobChange)
        @if ($jobChange->canBeApproved())
            <!-- Approve Modal -->
            <div class="modal fade" id="approveModal{{ $jobChange->id }}" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">موافقة على طلب التغيير الوظيفي</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <p>هل أنت متأكد من الموافقة على طلب التغيير الوظيفي للموظف <strong>{{ $jobChange->employee->full_name }}</strong>؟</p>
                            <p class="text-muted">سيتم تطبيق التغييرات على بيانات الموظف فوراً.</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                            <form action="{{ route('admin.employee-job-changes.approve', $jobChange) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-success">موافقة</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Reject Modal -->
            <div class="modal fade" id="rejectModal{{ $jobChange->id }}" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">رفض طلب التغيير الوظيفي</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <form action="{{ route('admin.employee-job-changes.reject', $jobChange) }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label">سبب الرفض <span class="text-danger">*</span></label>
                                    <textarea name="rejection_reason" class="form-control" rows="3" required></textarea>
                                </div>
                                <div class="modal-footer px-0 pb-0">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                                    <button type="submit" class="btn btn-danger">رفض</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endforeach
@endsection
