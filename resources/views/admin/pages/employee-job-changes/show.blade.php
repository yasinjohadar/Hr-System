@extends('admin.layouts.master')

@section('page-title')
    عرض طلب تغيير وظيفي
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
                    <h5 class="page-title fs-21 mb-1">عرض طلب تغيير وظيفي</h5>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.employee-job-changes.index') }}" class="btn btn-secondary btn-sm">عودة</a>
                    @if ($employeeJobChange->canBeEdited())
                        <a href="{{ route('admin.employee-job-changes.edit', $employeeJobChange) }}" class="btn btn-warning btn-sm">تعديل</a>
                    @endif
                </div>
            </div>

            <div class="row">
                <!-- معلومات الموظف -->
                <div class="col-xl-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">الموظف</h5>
                        </div>
                        <div class="card-body">
                            <div class="text-center mb-3">
                                @if ($employeeJobChange->employee->photo)
                                    <img src="{{ asset('storage/' . $employeeJobChange->employee->photo) }}" alt="{{ $employeeJobChange->employee->full_name }}" class="rounded-circle" width="80" height="80">
                                @else
                                    <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center text-white" style="width: 80px; height: 80px; margin: 0 auto;">
                                        {{ substr($employeeJobChange->employee->full_name, 0, 1) }}
                                    </div>
                                @endif
                                <h5 class="mt-2 mb-1">{{ $employeeJobChange->employee->full_name }}</h5>
                                <p class="text-muted mb-0">{{ $employeeJobChange->employee->employee_code }}</p>
                            </div>
                            <table class="table table-sm mb-0">
                                <tr>
                                    <td><strong>القسم:</strong></td>
                                    <td>{{ $employeeJobChange->employee->department->name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>المنصب:</strong></td>
                                    <td>{{ $employeeJobChange->employee->position->title ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>الفرع:</strong></td>
                                    <td>{{ $employeeJobChange->employee->branch->name ?? '-' }}</td>
                                </tr>
                            </table>
                            <a href="{{ route('admin.employees.show', $employeeJobChange->employee_id) }}" class="btn btn-primary btn-sm w-100 mt-2">عرض ملف الموظف</a>
                        </div>
                    </div>
                </div>

                <!-- تفاصيل الطلب -->
                <div class="col-xl-8">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">تفاصيل الطلب</h5>
                            <span class="badge bg-{{ $employeeJobChange->status_color }} fs-6">{{ $employeeJobChange->status_label }}</span>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>نوع التغيير:</strong></p>
                                    <p><span class="badge bg-info">{{ $employeeJobChange->change_type_label }}</span></p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>التاريخ الفعال:</strong></p>
                                    <p>{{ $employeeJobChange->effective_date->format('Y-m-d') }}</p>
                                </div>
                            </div>

                            @if ($employeeJobChange->reason)
                                <div class="mb-3">
                                    <p class="mb-1"><strong>الملاحظات:</strong></p>
                                    <p>{{ $employeeJobChange->reason }}</p>
                                </div>
                            @endif

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>تاريخ الطلب:</strong></p>
                                    <p>{{ $employeeJobChange->created_at->format('Y-m-d H:i') }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>طلب بواسطة:</strong></p>
                                    <p>{{ $employeeJobChange->requestedBy->name ?? '-' }}</p>
                                </div>
                            </div>

                            @if ($employeeJobChange->approved_by)
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>تمت الموافقة بواسطة:</strong></p>
                                        <p>{{ $employeeJobChange->approvedBy->name ?? '-' }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>تاريخ الموافقة:</strong></p>
                                        <p>{{ $employeeJobChange->approved_at ? $employeeJobChange->approved_at->format('Y-m-d H:i') : '-' }}</p>
                                    </div>
                                </div>
                            @endif

                            @if ($employeeJobChange->rejection_reason)
                                <div class="alert alert-danger">
                                    <p class="mb-1"><strong>سبب الرفض:</strong></p>
                                    <p>{{ $employeeJobChange->rejection_reason }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- التغييرات -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">التغييرات (قبل / بعد)</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>الحقل</th>
                                        <th>القيمة الحالية</th>
                                        <th>القيمة الجديدة</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($employeeJobChange->old_department_id || $employeeJobChange->new_department_id)
                                        <tr>
                                            <td><strong>القسم</strong></td>
                                            <td>{{ $employeeJobChange->oldDepartment->name ?? '-' }}</td>
                                            <td>{{ $employeeJobChange->newDepartment->name ?? '-' }}</td>
                                        </tr>
                                    @endif
                                    @if ($employeeJobChange->old_position_id || $employeeJobChange->new_position_id)
                                        <tr>
                                            <td><strong>المنصب</strong></td>
                                            <td>{{ $employeeJobChange->oldPosition->title ?? '-' }}</td>
                                            <td>{{ $employeeJobChange->newPosition->title ?? '-' }}</td>
                                        </tr>
                                    @endif
                                    @if ($employeeJobChange->old_branch_id || $employeeJobChange->new_branch_id)
                                        <tr>
                                            <td><strong>الفرع</strong></td>
                                            <td>{{ $employeeJobChange->oldBranch->name ?? '-' }}</td>
                                            <td>{{ $employeeJobChange->newBranch->name ?? '-' }}</td>
                                        </tr>
                                    @endif
                                    @if ($employeeJobChange->old_manager_id || $employeeJobChange->new_manager_id)
                                        <tr>
                                            <td><strong>المدير</strong></td>
                                            <td>{{ $employeeJobChange->oldManager->full_name ?? '-' }}</td>
                                            <td>{{ $employeeJobChange->newManager->full_name ?? '-' }}</td>
                                        </tr>
                                    @endif
                                    @if ($employeeJobChange->old_salary !== null || $employeeJobChange->new_salary !== null)
                                        <tr>
                                            <td><strong>الراتب</strong></td>
                                            <td>{{ $employeeJobChange->old_salary ? number_format($employeeJobChange->old_salary, 2) . ' ريال' : '-' }}</td>
                                            <td>{{ $employeeJobChange->new_salary ? number_format($employeeJobChange->new_salary, 2) . ' ريال' : '-' }}</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- أزرار الموافقة/الرفض -->
                    @if ($employeeJobChange->canBeApproved())
                        <div class="card">
                            <div class="card-body">
                                <h5 class="mb-3">إجراءات الموافقة</h5>
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#approveModal">
                                        <i class="fa fa-check"></i> موافقة
                                    </button>
                                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
                                        <i class="fa fa-times"></i> رفض
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Approve Modal -->
    @if ($employeeJobChange->canBeApproved())
        <div class="modal fade" id="approveModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">موافقة على طلب التغيير الوظيفي</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>هل أنت متأكد من الموافقة على طلب التغيير الوظيفي للموظف <strong>{{ $employeeJobChange->employee->full_name }}</strong>؟</p>
                        <p class="text-muted">سيتم تطبيق التغييرات على بيانات الموظف فوراً.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                        <form action="{{ route('admin.employee-job-changes.approve', $employeeJobChange) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-success">موافقة</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reject Modal -->
        <div class="modal fade" id="rejectModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">رفض طلب التغيير الوظيفي</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('admin.employee-job-changes.reject', $employeeJobChange) }}" method="POST">
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
@endsection
