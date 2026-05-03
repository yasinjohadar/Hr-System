@extends('admin.layouts.master')

@section('page-title')
    تفاصيل الصيانة
@stop

@section('css')
    <style>
        .maintenance-show-hero {
            background: linear-gradient(145deg, var(--bs-primary) 0%, rgba(var(--bs-primary-rgb), 0.88) 55%, rgb(15, 76, 129) 100%);
            color: #fff;
            border: none;
        }
        .maintenance-show-hero .text-white-75 { color: rgba(255,255,255,.85) !important; }
    </style>
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">تفاصيل الصيانة</h5>
                    <p class="text-muted small mb-0">{{ $maintenance->title }} — {{ $maintenance->asset->asset_code ?? '—' }}</p>
                </div>
                <div class="d-flex flex-wrap gap-2 mt-2 mt-md-0">
                    <a href="{{ route('admin.asset-maintenances.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-right me-1"></i>العودة للقائمة
                    </a>
                    @can('asset-maintenance-edit')
                        <a href="{{ route('admin.asset-maintenances.edit', $maintenance->id) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit me-1"></i>تعديل
                        </a>
                    @endcan
                </div>
            </div>

            <div class="row g-3">
                <div class="col-lg-4">
                    <div class="card maintenance-show-hero shadow-sm h-100">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex align-items-start gap-3 mb-3">
                                <span class="avatar avatar-md bg-white bg-opacity-25 rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width:3rem;height:3rem;">
                                    <i class="fas fa-screwdriver-wrench fs-4"></i>
                                </span>
                                <div class="min-w-0">
                                    <div class="text-white-75 small mb-1">الصيانة</div>
                                    <div class="fw-semibold fs-6 text-truncate">{{ $maintenance->title }}</div>
                                    <div class="small text-white-75 font-monospace">{{ $maintenance->asset->asset_code ?? '—' }}</div>
                                </div>
                            </div>
                            <div class="mb-3 pb-3 border-bottom border-white border-opacity-25">
                                <div class="text-white-75 small mb-1"><i class="fas fa-box me-1"></i>الأصل</div>
                                <div class="fw-semibold">
                                    <a href="{{ route('admin.assets.show', $maintenance->asset_id) }}" class="text-white">
                                        {{ $maintenance->asset->name_ar ?? $maintenance->asset->name ?? '—' }}
                                    </a>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="text-white-75 small mb-2">الحالة</div>
                                <span class="badge bg-{{ match($maintenance->status) { 'completed' => 'success', 'in_progress' => 'primary', 'scheduled' => 'warning', 'cancelled' => 'danger', 'postponed' => 'secondary', default => 'secondary' } }} fs-14 px-3 py-2">
                                    {{ $maintenance->status_name_ar }}
                                </span>
                            </div>
                            <div class="mt-auto pt-3 border-top border-white border-opacity-25">
                                <div class="text-white-75 small mb-1">التكلفة</div>
                                <div class="fs-3 fw-bold lh-1">{{ number_format($maintenance->cost, 2) }} <small class="fs-6">ر.س</small></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-light py-3 border-bottom">
                            <h6 class="mb-0 fw-semibold">
                                <i class="fas fa-circle-info text-primary me-2"></i>تفاصيل الصيانة
                            </h6>
                            <small class="text-muted">النوع والتواريخ والمزود</small>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <tbody>
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle" style="width:40%">
                                                <i class="fas fa-shapes text-muted me-2"></i>نوع الصيانة
                                            </th>
                                            <td class="pe-4 py-3 align-middle">
                                                <span class="badge bg-info-subtle text-dark border">{{ $maintenance->maintenance_type_name_ar }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="far fa-calendar me-2 text-muted"></i>تاريخ الصيانة المجدول
                                            </th>
                                            <td class="pe-4 py-3 align-middle fw-semibold">{{ $maintenance->scheduled_date ? $maintenance->scheduled_date->format('Y-m-d') : '—' }}</td>
                                        </tr>
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="far fa-calendar-check me-2 text-muted"></i>تاريخ الصيانة الفعلي
                                            </th>
                                            <td class="pe-4 py-3 align-middle fw-semibold">{{ $maintenance->actual_date ? $maintenance->actual_date->format('Y-m-d') : '—' }}</td>
                                        </tr>
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="far fa-calendar-plus me-2 text-muted"></i>تاريخ الصيانة القادمة
                                            </th>
                                            <td class="pe-4 py-3 align-middle fw-semibold">{{ $maintenance->next_maintenance_date ? $maintenance->next_maintenance_date->format('Y-m-d') : '—' }}</td>
                                        </tr>
                                        @if ($maintenance->service_provider)
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-store text-muted me-2"></i>مزود الخدمة
                                            </th>
                                            <td class="pe-4 py-3 align-middle fw-semibold">{{ $maintenance->service_provider }}</td>
                                        </tr>
                                        @endif
                                        @if ($maintenance->creator)
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-user-pen text-muted me-2"></i>أنشأ بواسطة
                                            </th>
                                            <td class="pe-4 py-3 align-middle fw-semibold">{{ $maintenance->creator->name }}</td>
                                        </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if ($maintenance->description || $maintenance->notes)
            <div class="row g-3 mt-1">
                @if ($maintenance->description)
                <div class="col-md-{{ $maintenance->notes ? '6' : '12' }}">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-light py-3 border-bottom">
                            <h6 class="mb-0 fw-semibold"><i class="fas fa-align-left text-muted me-2"></i>الوصف</h6>
                        </div>
                        <div class="card-body"><p class="mb-0">{{ $maintenance->description }}</p></div>
                    </div>
                </div>
                @endif
                @if ($maintenance->notes)
                <div class="col-md-{{ $maintenance->description ? '6' : '12' }}">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-light py-3 border-bottom">
                            <h6 class="mb-0 fw-semibold"><i class="fas fa-sticky-note text-muted me-2"></i>ملاحظات</h6>
                        </div>
                        <div class="card-body"><p class="mb-0">{{ $maintenance->notes }}</p></div>
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
                                    <div class="fw-semibold font-monospace small">{{ $maintenance->created_at->format('Y-m-d H:i') }}</div>
                                </div>
                                <div class="col border-bottom p-3">
                                    <div class="small text-muted mb-1"><i class="fas fa-pen-to-square me-1"></i>آخر تحديث</div>
                                    <div class="fw-semibold font-monospace small">{{ $maintenance->updated_at->format('Y-m-d H:i') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
