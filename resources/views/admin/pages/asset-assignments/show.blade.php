@extends('admin.layouts.master')

@section('page-title')
    تفاصيل التوزيع
@stop

@section('css')
    <style>
        .assignment-show-hero {
            background: linear-gradient(145deg, var(--bs-primary) 0%, rgba(var(--bs-primary-rgb), 0.88) 55%, rgb(15, 76, 129) 100%);
            color: #fff;
            border: none;
        }
        .assignment-show-hero .text-white-75 { color: rgba(255,255,255,.85) !important; }
    </style>
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">تفاصيل التوزيع</h5>
                    <p class="text-muted small mb-0">{{ $assignment->asset->asset_code }} — {{ $assignment->employee->full_name }}</p>
                </div>
                <div class="d-flex flex-wrap gap-2 mt-2 mt-md-0">
                    <a href="{{ route('admin.asset-assignments.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-right me-1"></i>العودة للقائمة
                    </a>
                    @if ($assignment->assignment_status == 'active')
                        <a href="{{ route('admin.asset-assignments.return-form', $assignment->id) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-undo me-1"></i>استرجاع الأصل
                        </a>
                    @endif
                    @can('asset-assignment-edit')
                        <a href="{{ route('admin.asset-assignments.edit', $assignment->id) }}" class="btn btn-info btn-sm">
                            <i class="fas fa-edit me-1"></i>تعديل
                        </a>
                    @endcan
                </div>
            </div>

            <div class="row g-3">
                <div class="col-lg-4">
                    <div class="card assignment-show-hero shadow-sm h-100">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex align-items-start gap-3 mb-3">
                                <span class="avatar avatar-md bg-white bg-opacity-25 rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width:3rem;height:3rem;">
                                    <i class="fas fa-exchange-alt fs-4"></i>
                                </span>
                                <div class="min-w-0">
                                    <div class="text-white-75 small mb-1">التوزيع</div>
                                    <div class="fw-semibold fs-6">{{ $assignment->asset->name_ar ?? $assignment->asset->name }}</div>
                                    <div class="small text-white-75 font-monospace">{{ $assignment->asset->asset_code }}</div>
                                </div>
                            </div>
                            <div class="mb-3 pb-3 border-bottom border-white border-opacity-25">
                                <div class="text-white-75 small mb-1"><i class="fas fa-user me-1"></i>الموظف</div>
                                <div class="fw-semibold">{{ $assignment->employee->full_name }}</div>
                            </div>
                            <div class="mb-3">
                                <div class="text-white-75 small mb-2">الحالة</div>
                                <span class="badge bg-{{ $assignment->assignment_status == 'active' ? 'success' : ($assignment->assignment_status == 'returned' ? 'info' : 'danger') }} fs-14 px-3 py-2">
                                    {{ $assignment->assignment_status_name_ar }}
                                </span>
                            </div>
                            <div class="mt-auto pt-3 border-top border-white border-opacity-25">
                                <div class="text-white-75 small mb-1">تاريخ التوزيع</div>
                                <div class="fs-5 fw-semibold">{{ $assignment->assigned_date->format('Y-m-d') }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-light py-3 border-bottom">
                            <h6 class="mb-0 fw-semibold"><i class="fas fa-circle-info text-primary me-2"></i>تفاصيل التوزيع</h6>
                            <small class="text-muted">التواريخ وحالة الأصل والملاحظات</small>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <tbody>
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle" style="width:35%">
                                                <i class="fas fa-calendar-range text-muted me-2"></i>تاريخ التوزيع / الاسترجاع المتوقع
                                            </th>
                                            <td class="pe-4 py-3 align-middle fw-semibold">
                                                {{ $assignment->assigned_date->format('Y-m-d') }}
                                                @if ($assignment->expected_return_date)
                                                    <span class="text-muted mx-1">—</span> {{ $assignment->expected_return_date->format('Y-m-d') }}
                                                @endif
                                            </td>
                                        </tr>
                                        @if ($assignment->actual_return_date)
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-calendar-check text-muted me-2"></i>تاريخ الاسترجاع الفعلي
                                            </th>
                                            <td class="pe-4 py-3 align-middle fw-semibold">{{ $assignment->actual_return_date->format('Y-m-d') }}</td>
                                        </tr>
                                        @endif
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-clipboard-check text-muted me-2"></i>حالة الأصل عند التوزيع
                                            </th>
                                            <td class="pe-4 py-3 align-middle">
                                                <span class="badge bg-info-subtle text-dark border">{{ $assignment->condition_on_assignment_name_ar }}</span>
                                            </td>
                                        </tr>
                                        @if ($assignment->condition_on_return)
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-clipboard-list text-muted me-2"></i>حالة الأصل عند الاسترجاع
                                            </th>
                                            <td class="pe-4 py-3 align-middle">
                                                <span class="badge bg-{{ $assignment->condition_on_return == 'damaged' ? 'danger' : 'info' }} fs-14">
                                                    {{ $assignment->condition_on_return_name_ar }}
                                                </span>
                                            </td>
                                        </tr>
                                        @endif
                                        @if ($assignment->assigner)
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-user-pen text-muted me-2"></i>من وزع
                                            </th>
                                            <td class="pe-4 py-3 align-middle fw-semibold">{{ $assignment->assigner->name }}</td>
                                        </tr>
                                        @endif
                                        @if ($assignment->returner)
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-user-check text-muted me-2"></i>من استرجع
                                            </th>
                                            <td class="pe-4 py-3 align-middle fw-semibold">{{ $assignment->returner->name }}</td>
                                        </tr>
                                        @endif
                                        @if ($assignment->assignment_notes)
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-sticky-note text-muted me-2"></i>ملاحظات التوزيع
                                            </th>
                                            <td class="pe-4 py-3 align-middle">{{ $assignment->assignment_notes }}</td>
                                        </tr>
                                        @endif
                                        @if ($assignment->return_notes)
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-sticky-note text-muted me-2"></i>ملاحظات الاسترجاع
                                            </th>
                                            <td class="pe-4 py-3 align-middle">{{ $assignment->return_notes }}</td>
                                        </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-3 mt-1 mb-4">
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-light py-3"><h6 class="mb-0 fw-semibold"><i class="fas fa-clock-rotate-left text-primary me-2"></i>بيانات السجل</h6></div>
                        <div class="card-body">
                            <div class="row row-cols-1 row-cols-md-2 g-0">
                                <div class="col border-bottom border-end-md p-3">
                                    <div class="small text-muted mb-1"><i class="far fa-clock me-1"></i>تاريخ الإنشاء</div>
                                    <div class="fw-semibold font-monospace small">{{ $assignment->created_at->format('Y-m-d H:i') }}</div>
                                </div>
                                <div class="col border-bottom p-3">
                                    <div class="small text-muted mb-1"><i class="fas fa-pen-to-square me-1"></i>آخر تحديث</div>
                                    <div class="fw-semibold font-monospace small">{{ $assignment->updated_at->format('Y-m-d H:i') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
