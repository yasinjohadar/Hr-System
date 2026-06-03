@extends('admin.layouts.master')

@section('page-title')
    التغييرات الوظيفية (النقل والترقية)
@stop

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin-employee-job-changes.css') }}">
@endpush

@section('content')
    <div class="main-content app-content admin-job-changes-page">
        <div class="container-fluid pt-4">

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

            <div class="card page-hero mb-4">
                <div class="card-body py-4">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="page-hero-icon">
                                <i class="ri-exchange-line"></i>
                            </div>
                            <div>
                                <h4 class="mb-1 page-hero-title fw-bold">التغييرات الوظيفية</h4>
                                <p class="mb-0 page-hero-subtitle">النقل والترقية وتعديل الراتب</p>
                            </div>
                        </div>
                        <a href="{{ route('admin.employee-job-changes.create', request()->only('employee_id')) }}" class="btn btn-hero-primary btn-sm">
                            <i class="ri-add-line me-1"></i>إضافة طلب تغيير وظيفي
                        </a>
                    </div>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-value">{{ $stats['total'] }}</div>
                                <div class="stat-label">إجمالي الطلبات</div>
                            </div>
                            <div class="stat-icon stat-icon--primary"><i class="ri-file-list-3-line"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-value">{{ $stats['pending'] }}</div>
                                <div class="stat-label">قيد الانتظار</div>
                            </div>
                            <div class="stat-icon stat-icon--warning"><i class="ri-time-line"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-value">{{ $stats['approved'] }}</div>
                                <div class="stat-label">تمت الموافقة</div>
                            </div>
                            <div class="stat-icon stat-icon--success"><i class="ri-checkbox-circle-line"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-value">{{ $stats['rejected'] }}</div>
                                <div class="stat-label">مرفوض</div>
                            </div>
                            <div class="stat-icon stat-icon--danger"><i class="ri-close-circle-line"></i></div>
                        </div>
                    </div>
                </div>
            </div>

            <form action="{{ route('admin.employee-job-changes.index') }}" method="GET" class="filters-panel">
                <div class="row g-3 align-items-end">
                    <div class="col-md-6 col-lg-3">
                        <label class="form-label">الموظف</label>
                        <select name="employee_id" class="form-select">
                            <option value="">كل الموظفين</option>
                            @foreach ($employees as $emp)
                                <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>{{ $emp->full_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6 col-md-3 col-lg-2">
                        <label class="form-label">الحالة</label>
                        <select name="status" class="form-select">
                            <option value="">كل الحالات</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>تمت الموافقة</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>مرفوض</option>
                        </select>
                    </div>
                    <div class="col-6 col-md-3 col-lg-2">
                        <label class="form-label">نوع التغيير</label>
                        <select name="change_type" class="form-select">
                            <option value="">كل الأنواع</option>
                            <option value="transfer" {{ request('change_type') == 'transfer' ? 'selected' : '' }}>نقل</option>
                            <option value="promotion" {{ request('change_type') == 'promotion' ? 'selected' : '' }}>ترقية</option>
                            <option value="salary_change" {{ request('change_type') == 'salary_change' ? 'selected' : '' }}>تعديل راتب</option>
                            <option value="demotion" {{ request('change_type') == 'demotion' ? 'selected' : '' }}>تنزيل</option>
                        </select>
                    </div>
                    <div class="col-6 col-md-3 col-lg-2">
                        <label class="form-label">من تاريخ</label>
                        <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-6 col-md-3 col-lg-2">
                        <label class="form-label">إلى تاريخ</label>
                        <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                    </div>
                    <div class="col-12 col-lg-auto d-flex flex-wrap gap-2">
                        <button type="submit" class="btn btn-filter-submit">
                            <i class="ri-search-line me-1"></i>بحث
                        </button>
                        <a href="{{ route('admin.employee-job-changes.index') }}" class="btn btn-filter-clear">مسح</a>
                    </div>
                </div>
            </form>

            <div class="content-panel">
                <div class="content-panel-header">
                    <h5 class="fw-bold mb-0">طلبات التغيير الوظيفي</h5>
                </div>

                @if ($jobChanges->isNotEmpty())
                    <div class="changes-table-scroll">
                        <div class="changes-table-header">
                            <span>#</span>
                            <span>الموظف</span>
                            <span>نوع التغيير</span>
                            <span>التاريخ الفعال</span>
                            <span>الحالة</span>
                            <span>تاريخ الطلب</span>
                            <span class="text-end">العمليات</span>
                        </div>

                        @foreach ($jobChanges as $jobChange)
                            <div class="changes-table-row">
                                <span class="row-index">{{ $loop->iteration + ($jobChanges->currentPage() - 1) * $jobChanges->perPage() }}</span>

                                <div class="changes-mobile-field">
                                    <span class="changes-mobile-label">الموظف</span>
                                    <a href="{{ route('admin.employees.show', $jobChange->employee_id) }}" class="employee-link">
                                        {{ $jobChange->employee->full_name ?? $jobChange->employee->employee_code }}
                                    </a>
                                </div>

                                <div class="changes-mobile-field">
                                    <span class="changes-mobile-label">نوع التغيير</span>
                                    <span class="type-pill type-pill--{{ $jobChange->change_type }}">{{ $jobChange->change_type_label }}</span>
                                </div>

                                <div class="changes-mobile-field">
                                    <span class="changes-mobile-label">التاريخ الفعال</span>
                                    <span>{{ $jobChange->effective_date->format('Y/m/d') }}</span>
                                </div>

                                <div class="changes-mobile-field">
                                    <span class="changes-mobile-label">الحالة</span>
                                    <span class="status-pill status-pill--{{ $jobChange->status }}">{{ $jobChange->status_label }}</span>
                                </div>

                                <div class="changes-mobile-field">
                                    <span class="changes-mobile-label">تاريخ الطلب</span>
                                    <span class="cell-muted">{{ $jobChange->created_at->format('Y/m/d H:i') }}</span>
                                </div>

                                <div class="row-actions">
                                    <a href="{{ route('admin.employee-job-changes.show', $jobChange) }}" class="btn-action btn-action--view" title="عرض">
                                        <i class="ri-eye-line"></i>
                                    </a>
                                    @if ($jobChange->canBeEdited())
                                        <a href="{{ route('admin.employee-job-changes.edit', $jobChange) }}" class="btn-action btn-action--edit" title="تعديل">
                                            <i class="ri-pencil-line"></i>
                                        </a>
                                    @endif
                                    @if ($jobChange->canBeApproved())
                                        <button type="button" class="btn-action btn-action--approve" data-bs-toggle="modal" data-bs-target="#approveModal{{ $jobChange->id }}" title="موافقة">
                                            <i class="ri-check-line"></i>
                                        </button>
                                        <button type="button" class="btn-action btn-action--reject" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $jobChange->id }}" title="رفض">
                                            <i class="ri-close-line"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="pagination-wrap d-flex justify-content-center">
                        {{ $jobChanges->withQueryString()->links() }}
                    </div>
                @else
                    <div class="empty-state">
                        <i class="ri-file-list-3-line fs-2 d-block mb-2"></i>
                        لا توجد طلبات تغيير وظيفي
                    </div>
                @endif
            </div>

        </div>
    </div>

    @foreach ($jobChanges as $jobChange)
        @if ($jobChange->canBeApproved())
            <div class="modal fade" id="approveModal{{ $jobChange->id }}" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">موافقة على طلب التغيير الوظيفي</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <p>هل أنت متأكد من الموافقة على طلب التغيير الوظيفي للموظف <strong>{{ $jobChange->employee->full_name }}</strong>؟</p>
                            <p class="text-muted mb-0">سيتم تطبيق التغييرات على بيانات الموظف فوراً.</p>
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

            <div class="modal fade" id="rejectModal{{ $jobChange->id }}" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">رفض طلب التغيير الوظيفي</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <form action="{{ route('admin.employee-job-changes.reject', $jobChange) }}" method="POST" id="rejectForm{{ $jobChange->id }}">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label">سبب الرفض <span class="text-danger">*</span></label>
                                    <textarea name="rejection_reason" class="form-control" rows="3" required></textarea>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                            <button type="submit" form="rejectForm{{ $jobChange->id }}" class="btn btn-danger">رفض</button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endforeach
@endsection
