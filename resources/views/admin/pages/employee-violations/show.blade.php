@extends('admin.layouts.master')

@section('page-title')
    تفاصيل المخالفة
@stop

@section('css')
    <style>
        .violation-show-hero {
            background: linear-gradient(145deg, var(--bs-primary) 0%, rgba(var(--bs-primary-rgb), 0.88) 55%, rgb(15, 76, 129) 100%);
            color: #fff;
            border: none;
        }
        .violation-show-hero .text-white-75 { color: rgba(255,255,255,.85) !important; }
    </style>
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">تفاصيل المخالفة</h5>
                    <p class="text-muted small mb-0">{{ $violation->violation_code }} — {{ $violation->employee->full_name ?? '—' }}</p>
                </div>
                <div class="d-flex flex-wrap gap-2 mt-2 mt-md-0">
                    <a href="{{ route('admin.employee-violations.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-right me-1"></i>العودة للقائمة
                    </a>
                    @if ($violation->status == 'pending')
                        @can('employee-violation-investigate')
                            <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#investigateModal">
                                <i class="fas fa-search me-1"></i>تحقيق
                            </button>
                            <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#confirmModal">
                                <i class="fas fa-check me-1"></i>تأكيد
                            </button>
                            <button class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#dismissModal">
                                <i class="fas fa-times me-1"></i>رفض
                            </button>
                        @endcan
                    @endif
                    @if ($violation->status == 'confirmed')
                        @can('employee-violation-approve')
                            @if ($violation->disciplinaryAction && $violation->disciplinaryAction->requires_approval)
                                <form action="{{ route('admin.employee-violations.approve', $violation->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button class="btn btn-primary btn-sm"><i class="fas fa-check-circle me-1"></i>موافقة</button>
                                </form>
                            @else
                                <form action="{{ route('admin.employee-violations.apply-action', $violation->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button class="btn btn-success btn-sm"><i class="fas fa-check-double me-1"></i>تطبيق</button>
                                </form>
                            @endif
                        @endcan
                    @endif
                    @if (in_array($violation->status, ['pending', 'dismissed']))
                        @can('employee-violation-edit')
                            <a href="{{ route('admin.employee-violations.edit', $violation->id) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit me-1"></i>تعديل
                            </a>
                        @endcan
                    @endif
                </div>
            </div>

            <div class="row g-3">
                <div class="col-lg-4">
                    <div class="card violation-show-hero shadow-sm h-100">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex align-items-start gap-3 mb-3">
                                <span class="avatar avatar-md bg-white bg-opacity-25 rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width:3rem;height:3rem;">
                                    <i class="fas fa-triangle-exclamation fs-4"></i>
                                </span>
                                <div class="min-w-0">
                                    <div class="text-white-75 small mb-1">المخالفة</div>
                                    <div class="fw-semibold fs-6 text-truncate">{{ $violation->employee->full_name ?? '—' }}</div>
                                    <div class="small text-white-75 font-monospace">{{ $violation->violation_code }}</div>
                                </div>
                            </div>
                            <div class="mb-3 pb-3 border-bottom border-white border-opacity-25">
                                <div class="text-white-75 small mb-1"><i class="fas fa-calendar me-1"></i>تاريخ المخالفة</div>
                                <div class="fw-semibold">{{ $violation->violation_date->format('Y-m-d') }}</div>
                            </div>
                            <div class="mb-3">
                                <div class="text-white-75 small mb-2">الحالة</div>
                                <span class="badge bg-{{ $violation->status == 'resolved' ? 'success' : ($violation->status == 'confirmed' ? 'primary' : ($violation->status == 'dismissed' ? 'secondary' : 'warning')) }} fs-14 px-3 py-2">
                                    {{ $violation->status_name_ar ?? $violation->status }}
                                </span>
                            </div>
                            <div class="mt-auto pt-3 border-top border-white border-opacity-25">
                                <div class="text-white-75 small mb-1">الخطورة</div>
                                <span class="badge bg-{{ $violation->severity == 'critical' ? 'danger' : ($violation->severity == 'high' ? 'warning' : 'info') }} fs-14 px-3 py-2">
                                    {{ $violation->severity_name_ar ?? $violation->severity }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-light py-3 border-bottom">
                            <h6 class="mb-0 fw-semibold">
                                <i class="fas fa-circle-info text-primary me-2"></i>تفاصيل المخالفة
                            </h6>
                            <small class="text-muted">النوع والإجراءات والتحقيقات</small>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <tbody>
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle" style="width:40%">
                                                <i class="fas fa-tag text-muted me-2"></i>نوع المخالفة
                                            </th>
                                            <td class="pe-4 py-3 align-middle fw-semibold">{{ $violation->violationType->name_ar ?? $violation->violationType->name ?? '—' }}</td>
                                        </tr>
                                        @if ($violation->disciplinaryAction)
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-gavel text-muted me-2"></i>الإجراء التأديبي
                                            </th>
                                            <td class="pe-4 py-3 align-middle fw-semibold">{{ $violation->disciplinaryAction->name_ar ?? $violation->disciplinaryAction->name }}</td>
                                        </tr>
                                        @endif
                                        @if ($violation->reporter)
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-bullhorn text-muted me-2"></i>من أبلغ
                                            </th>
                                            <td class="pe-4 py-3 align-middle fw-semibold">{{ $violation->reporter->name }}</td>
                                        </tr>
                                        @endif
                                        @if ($violation->investigator)
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-magnifying-glass text-muted me-2"></i>من قام بالتحقيق
                                            </th>
                                            <td class="pe-4 py-3 align-middle fw-semibold">{{ $violation->investigator->name }}</td>
                                        </tr>
                                        @endif
                                        @if ($violation->investigation_date)
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-calendar-check text-muted me-2"></i>تاريخ التحقيق
                                            </th>
                                            <td class="pe-4 py-3 align-middle fw-semibold">{{ $violation->investigation_date->format('Y-m-d') }}</td>
                                        </tr>
                                        @endif
                                        @if ($violation->approver)
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-user-check text-muted me-2"></i>من وافق
                                            </th>
                                            <td class="pe-4 py-3 align-middle fw-semibold">{{ $violation->approver->name }}</td>
                                        </tr>
                                        @endif
                                        @if ($violation->approval_date)
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-calendar-check text-muted me-2"></i>تاريخ الموافقة
                                            </th>
                                            <td class="pe-4 py-3 align-middle fw-semibold">{{ $violation->approval_date->format('Y-m-d') }}</td>
                                        </tr>
                                        @endif
                                        @if ($violation->action_date)
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-calendar-plus text-muted me-2"></i>تاريخ الإجراء
                                            </th>
                                            <td class="pe-4 py-3 align-middle fw-semibold">{{ $violation->action_date->format('Y-m-d') }}</td>
                                        </tr>
                                        @endif
                                        @if ($violation->attendance)
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-user-clock text-muted me-2"></i>مرتبط بالحضور
                                            </th>
                                            <td class="pe-4 py-3 align-middle">
                                                <a href="{{ route('admin.attendances.show', $violation->attendance_id) }}">{{ $violation->attendance->attendance_date->format('Y-m-d') }}</a>
                                            </td>
                                        </tr>
                                        @endif
                                        @if ($violation->leaveRequest)
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-calendar-week text-muted me-2"></i>مرتبط بالإجازة
                                            </th>
                                            <td class="pe-4 py-3 align-middle">
                                                <a href="{{ route('admin.leave-requests.show', $violation->leave_request_id) }}">{{ $violation->leaveRequest->start_date->format('Y-m-d') }}</a>
                                            </td>
                                        </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if ($violation->description || $violation->witnesses || $violation->employee_response || $violation->investigation_notes || $violation->action_notes || $violation->resolution_notes || $violation->notes)
            <div class="row g-3 mt-1">
                @if ($violation->description)
                <div class="col-md-6">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-light py-3"><h6 class="mb-0 fw-semibold"><i class="fas fa-align-left text-muted me-2"></i>الوصف</h6></div>
                        <div class="card-body"><p class="mb-0">{{ $violation->description }}</p></div>
                    </div>
                </div>
                @endif
                @if ($violation->witnesses)
                <div class="col-md-6">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-light py-3"><h6 class="mb-0 fw-semibold"><i class="fas fa-people-arrows text-muted me-2"></i>الشهود</h6></div>
                        <div class="card-body"><p class="mb-0">{{ $violation->witnesses }}</p></div>
                    </div>
                </div>
                @endif
                @if ($violation->employee_response)
                <div class="col-md-6">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-light py-3"><h6 class="mb-0 fw-semibold"><i class="fas fa-reply text-muted me-2"></i>رد الموظف</h6></div>
                        <div class="card-body"><p class="mb-0">{{ $violation->employee_response }}</p></div>
                    </div>
                </div>
                @endif
                @if ($violation->investigation_notes)
                <div class="col-md-6">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-light py-3"><h6 class="mb-0 fw-semibold"><i class="fas fa-magnifying-glass text-muted me-2"></i>ملاحظات التحقيق</h6></div>
                        <div class="card-body"><p class="mb-0">{{ $violation->investigation_notes }}</p></div>
                    </div>
                </div>
                @endif
                @if ($violation->action_notes)
                <div class="col-md-6">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-light py-3"><h6 class="mb-0 fw-semibold"><i class="fas fa-gavel text-muted me-2"></i>ملاحظات الإجراء</h6></div>
                        <div class="card-body"><p class="mb-0">{{ $violation->action_notes }}</p></div>
                    </div>
                </div>
                @endif
                @if ($violation->resolution_notes)
                <div class="col-md-6">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-light py-3"><h6 class="mb-0 fw-semibold"><i class="fas fa-check-circle text-muted me-2"></i>ملاحظات الحل</h6></div>
                        <div class="card-body"><p class="mb-0">{{ $violation->resolution_notes }}</p></div>
                    </div>
                </div>
                @endif
                @if ($violation->notes)
                <div class="col-md-6">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-light py-3"><h6 class="mb-0 fw-semibold"><i class="fas fa-sticky-note text-muted me-2"></i>ملاحظات</h6></div>
                        <div class="card-body"><p class="mb-0">{{ $violation->notes }}</p></div>
                    </div>
                </div>
                @endif
            </div>
            @endif

            <div class="row g-3 mt-1 mb-4">
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-light py-3">
                            <h6 class="mb-0 fw-semibold"><i class="fas fa-clock-rotate-left text-primary me-2"></i>بيانات السجل</h6>
                        </div>
                        <div class="card-body">
                            <div class="row row-cols-1 row-cols-md-2 g-0">
                                <div class="col border-bottom border-end-md p-3">
                                    <div class="small text-muted mb-1"><i class="far fa-clock me-1"></i>تاريخ الإنشاء</div>
                                    <div class="fw-semibold font-monospace small">{{ $violation->created_at->format('Y-m-d H:i') }}</div>
                                </div>
                                <div class="col border-bottom p-3">
                                    <div class="small text-muted mb-1"><i class="fas fa-pen-to-square me-1"></i>آخر تحديث</div>
                                    <div class="fw-semibold font-monospace small">{{ $violation->updated_at->format('Y-m-d H:i') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="investigateModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.employee-violations.investigate', $violation->id) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">بدء التحقيق</h5>
                        <button class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">ملاحظات التحقيق <span class="text-danger">*</span></label>
                            <textarea name="investigation_notes" class="form-control" rows="4" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                        <button class="btn btn-info">بدء التحقيق</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="confirmModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.employee-violations.confirm', $violation->id) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">تأكيد المخالفة</h5>
                        <button class="btn-close" data-bs-dismiss="modal"></button>
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
                        <button class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                        <button class="btn btn-success">تأكيد</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="dismissModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.employee-violations.dismiss', $violation->id) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">رفض / إلغاء</h5>
                        <button class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">ملاحظات الحل <span class="text-danger">*</span></label>
                            <textarea name="resolution_notes" class="form-control" rows="4" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                        <button class="btn btn-secondary">رفض</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop
