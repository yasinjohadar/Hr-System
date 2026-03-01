@extends('admin.layouts.master')

@section('page-title')
    تفاصيل المخالفة
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">تفاصيل المخالفة</h5>
                </div>
                <div>
                    <a href="{{ route('admin.employee-violations.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-right me-2"></i>العودة للقائمة
                    </a>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">مخالفة رقم: {{ $violation->violation_code }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">الموظف:</label>
                                    <p class="form-control-plaintext">
                                        <a href="{{ route('admin.employees.show', $violation->employee_id) }}">
                                            {{ $violation->employee->full_name }}
                                        </a>
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">نوع المخالفة:</label>
                                    <p class="form-control-plaintext">{{ $violation->violationType->name_ar ?? $violation->violationType->name }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">تاريخ المخالفة:</label>
                                    <p class="form-control-plaintext">{{ $violation->violation_date->format('Y-m-d') }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">الخطورة:</label>
                                    <p class="form-control-plaintext">
                                        <span class="badge bg-{{ $violation->severity == 'critical' ? 'danger' : ($violation->severity == 'high' ? 'warning' : 'info') }}">
                                            {{ $violation->severity_name_ar }}
                                        </span>
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">الحالة:</label>
                                    <p class="form-control-plaintext">
                                        <span class="badge bg-{{ $violation->status == 'resolved' ? 'success' : ($violation->status == 'confirmed' ? 'primary' : ($violation->status == 'dismissed' ? 'secondary' : 'warning')) }}">
                                            {{ $violation->status_name_ar }}
                                        </span>
                                    </p>
                                </div>
                                @if ($violation->disciplinaryAction)
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">الإجراء التأديبي:</label>
                                    <p class="form-control-plaintext">{{ $violation->disciplinaryAction->name_ar ?? $violation->disciplinaryAction->name }}</p>
                                </div>
                                @endif
                                @if ($violation->reporter)
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">من أبلغ:</label>
                                    <p class="form-control-plaintext">{{ $violation->reporter->name }}</p>
                                </div>
                                @endif
                                @if ($violation->investigator)
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">من قام بالتحقيق:</label>
                                    <p class="form-control-plaintext">{{ $violation->investigator->name }}</p>
                                </div>
                                @endif
                                @if ($violation->investigation_date)
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">تاريخ التحقيق:</label>
                                    <p class="form-control-plaintext">{{ $violation->investigation_date->format('Y-m-d') }}</p>
                                </div>
                                @endif
                                @if ($violation->approver)
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">من وافق:</label>
                                    <p class="form-control-plaintext">{{ $violation->approver->name }}</p>
                                </div>
                                @endif
                                @if ($violation->approval_date)
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">تاريخ الموافقة:</label>
                                    <p class="form-control-plaintext">{{ $violation->approval_date->format('Y-m-d') }}</p>
                                </div>
                                @endif
                                @if ($violation->action_date)
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">تاريخ تطبيق الإجراء:</label>
                                    <p class="form-control-plaintext">{{ $violation->action_date->format('Y-m-d') }}</p>
                                </div>
                                @endif
                                @if ($violation->attendance)
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">مرتبط بالحضور:</label>
                                    <p class="form-control-plaintext">
                                        <a href="{{ route('admin.attendances.show', $violation->attendance_id) }}">
                                            {{ $violation->attendance->attendance_date->format('Y-m-d') }}
                                        </a>
                                    </p>
                                </div>
                                @endif
                                @if ($violation->leaveRequest)
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">مرتبط بالإجازة:</label>
                                    <p class="form-control-plaintext">
                                        <a href="{{ route('admin.leave-requests.show', $violation->leave_request_id) }}">
                                            {{ $violation->leaveRequest->start_date->format('Y-m-d') }}
                                        </a>
                                    </p>
                                </div>
                                @endif
                                <div class="col-12 mb-3">
                                    <label class="form-label fw-bold">وصف المخالفة:</label>
                                    <p class="form-control-plaintext">{{ $violation->description }}</p>
                                </div>
                                @if ($violation->witnesses)
                                <div class="col-12 mb-3">
                                    <label class="form-label fw-bold">الشهود:</label>
                                    <p class="form-control-plaintext">{{ $violation->witnesses }}</p>
                                </div>
                                @endif
                                @if ($violation->employee_response)
                                <div class="col-12 mb-3">
                                    <label class="form-label fw-bold">رد الموظف:</label>
                                    <p class="form-control-plaintext">{{ $violation->employee_response }}</p>
                                </div>
                                @endif
                                @if ($violation->investigation_notes)
                                <div class="col-12 mb-3">
                                    <label class="form-label fw-bold">ملاحظات التحقيق:</label>
                                    <p class="form-control-plaintext">{{ $violation->investigation_notes }}</p>
                                </div>
                                @endif
                                @if ($violation->action_notes)
                                <div class="col-12 mb-3">
                                    <label class="form-label fw-bold">ملاحظات الإجراء:</label>
                                    <p class="form-control-plaintext">{{ $violation->action_notes }}</p>
                                </div>
                                @endif
                                @if ($violation->resolution_notes)
                                <div class="col-12 mb-3">
                                    <label class="form-label fw-bold">ملاحظات الحل:</label>
                                    <p class="form-control-plaintext">{{ $violation->resolution_notes }}</p>
                                </div>
                                @endif
                                @if ($violation->notes)
                                <div class="col-12 mb-3">
                                    <label class="form-label fw-bold">ملاحظات:</label>
                                    <p class="form-control-plaintext">{{ $violation->notes }}</p>
                                </div>
                                @endif
                            </div>

                            <!-- أزرار الإجراءات -->
                            <div class="mt-4">
                                @if ($violation->status == 'pending')
                                    @can('employee-violation-investigate')
                                    <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#investigateModal">
                                        <i class="fas fa-search me-2"></i>بدء التحقيق
                                    </button>
                                    @endcan
                                    @can('employee-violation-investigate')
                                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#confirmModal">
                                        <i class="fas fa-check me-2"></i>تأكيد المخالفة
                                    </button>
                                    @endcan
                                    @can('employee-violation-investigate')
                                    <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#dismissModal">
                                        <i class="fas fa-times me-2"></i>رفض/إلغاء
                                    </button>
                                    @endcan
                                @endif

                                @if ($violation->status == 'confirmed' && $violation->disciplinaryAction && $violation->disciplinaryAction->requires_approval)
                                    @can('employee-violation-approve')
                                    <form method="POST" action="{{ route('admin.employee-violations.approve', $violation->id) }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-check-circle me-2"></i>الموافقة على الإجراء
                                        </button>
                                    </form>
                                    @endcan
                                @endif

                                @if ($violation->status == 'confirmed' && (!$violation->disciplinaryAction || !$violation->disciplinaryAction->requires_approval))
                                    @can('employee-violation-approve')
                                    <form method="POST" action="{{ route('admin.employee-violations.apply-action', $violation->id) }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-check-double me-2"></i>تطبيق الإجراء
                                        </button>
                                    </form>
                                    @endcan
                                @endif

                                @if (in_array($violation->status, ['pending', 'dismissed']))
                                    @can('employee-violation-edit')
                                    <a href="{{ route('admin.employee-violations.edit', $violation->id) }}" class="btn btn-warning">
                                        <i class="fas fa-edit me-2"></i>تعديل
                                    </a>
                                    @endcan
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal بدء التحقيق -->
    <div class="modal fade" id="investigateModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ route('admin.employee-violations.investigate', $violation->id) }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">بدء التحقيق</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">ملاحظات التحقيق <span class="text-danger">*</span></label>
                            <textarea name="investigation_notes" class="form-control" rows="4" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-info">بدء التحقيق</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal تأكيد المخالفة -->
    <div class="modal fade" id="confirmModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ route('admin.employee-violations.confirm', $violation->id) }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">تأكيد المخالفة</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">الإجراء التأديبي <span class="text-danger">*</span></label>
                            <select name="disciplinary_action_id" class="form-select" required>
                                <option value="">اختر الإجراء</option>
                                @foreach (\App\Models\DisciplinaryAction::where('is_active', true)->get() as $action)
                                    <option value="{{ $action->id }}">{{ $action->name_ar ?? $action->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">ملاحظات الإجراء</label>
                            <textarea name="action_notes" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-success">تأكيد</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal رفض/إلغاء -->
    <div class="modal fade" id="dismissModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ route('admin.employee-violations.dismiss', $violation->id) }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">رفض/إلغاء المخالفة</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">ملاحظات الحل <span class="text-danger">*</span></label>
                            <textarea name="resolution_notes" class="form-control" rows="4" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-secondary">رفض</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

