@extends('admin.layouts.master')

@section('page-title')
    تفاصيل الأصل
@stop

@section('css')
    <style>
        .asset-show-hero {
            background: linear-gradient(145deg, var(--bs-primary) 0%, rgba(var(--bs-primary-rgb), 0.88) 55%, rgb(15, 76, 129) 100%);
            color: #fff;
            border: none;
        }
        .asset-show-hero .text-white-75 { color: rgba(255,255,255,.85) !important; }
    </style>
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">تفاصيل الأصل</h5>
                    <p class="text-muted small mb-0">{{ $asset->name_ar ?? $asset->name }} — {{ $asset->asset_code }}</p>
                </div>
                <div class="d-flex flex-wrap gap-2 mt-2 mt-md-0">
                    <a href="{{ route('admin.assets.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-right me-1"></i>العودة للقائمة
                    </a>
                    @can('asset-edit')
                        <a href="{{ route('admin.assets.edit', $asset->id) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit me-1"></i>تعديل
                        </a>
                    @endcan
                </div>
            </div>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="row g-3">
                <div class="col-lg-4">
                    <div class="card asset-show-hero shadow-sm h-100">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex align-items-start gap-3 mb-3">
                                <span class="avatar avatar-md bg-white bg-opacity-25 rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width:3rem;height:3rem;">
                                    <i class="fas fa-box fs-4"></i>
                                </span>
                                <div class="min-w-0">
                                    <div class="text-white-75 small mb-1">الأصل</div>
                                    <div class="fw-semibold fs-6 text-truncate">{{ $asset->name_ar ?? $asset->name }}</div>
                                    <div class="small text-white-75 font-monospace">{{ $asset->asset_code }}</div>
                                </div>
                            </div>
                            <div class="mb-3 pb-3 border-bottom border-white border-opacity-25">
                                <div class="text-white-75 small mb-1"><i class="fas fa-folder me-1"></i>الفئة</div>
                                <div class="fw-semibold">{{ $asset->category_name_ar }}</div>
                            </div>
                            <div class="mb-3">
                                <div class="text-white-75 small mb-2">الحالة</div>
                                <span class="badge bg-{{ $asset->status == 'available' ? 'success' : ($asset->status == 'assigned' ? 'primary' : ($asset->status == 'maintenance' ? 'warning' : 'danger')) }} fs-14 px-3 py-2">
                                    {{ $asset->status_name_ar }}
                                </span>
                            </div>
                            <div class="mt-auto pt-3 border-top border-white border-opacity-25">
                                <div class="text-white-75 small mb-1">الموظف الحالي</div>
                                <div class="fs-6 fw-semibold">
                                    @if ($asset->currentEmployee())
                                        <a href="{{ route('admin.employees.show', $asset->currentEmployee()->id) }}" class="text-white">
                                            {{ $asset->currentEmployee()->full_name }}
                                        </a>
                                    @else
                                        <span class="text-white-75">—</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-light py-3 border-bottom">
                            <h6 class="mb-0 fw-semibold">
                                <i class="fas fa-circle-info text-primary me-2"></i>بيانات الأصل
                            </h6>
                            <small class="text-muted">الشركة المصنعة والموديل والتفاصيل</small>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <tbody>
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle" style="width:35%">
                                                <i class="fas fa-industry text-muted me-2"></i>الشركة المصنعة
                                            </th>
                                            <td class="pe-4 py-3 align-middle fw-semibold">{{ $asset->manufacturer ?? '—' }}</td>
                                        </tr>
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-cube text-muted me-2"></i>الموديل
                                            </th>
                                            <td class="pe-4 py-3 align-middle fw-semibold">{{ $asset->model ?? '—' }}</td>
                                        </tr>
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-barcode text-muted me-2"></i>الرقم التسلسلي
                                            </th>
                                            <td class="pe-4 py-3 align-middle font-monospace fw-semibold">{{ $asset->serial_number ?? '—' }}</td>
                                        </tr>
                                        @if ($asset->currentEmployee())
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-user-check text-muted me-2"></i>الموظف الحالي
                                            </th>
                                            <td class="pe-4 py-3 align-middle fw-semibold">
                                                {{ $asset->currentEmployee()->full_name }}
                                            </td>
                                        </tr>
                                        @endif
                                        @if ($asset->description)
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-align-left text-muted me-2"></i>الوصف
                                            </th>
                                            <td class="pe-4 py-3 align-middle">{{ $asset->description }}</td>
                                        </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-3 mt-1">
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-light py-3">
                            <ul class="nav nav-tabs card-header-tabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" type="button" data-bs-toggle="tab" data-bs-target="#tab-overview" role="tab">
                                        <i class="fas fa-circle-info me-1"></i>نظرة عامة
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" type="button" data-bs-toggle="tab" data-bs-target="#tab-timeline" role="tab">
                                        <i class="fas fa-timeline me-1"></i>السجل الزمني
                                        <span class="badge bg-primary ms-1">{{ $lifecycleEvents->count() }}</span>
                                    </button>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="tab-content">
                                <div class="tab-pane fade show active" id="tab-overview" role="tabpanel">
                                    <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-0">
                                        <div class="col border-bottom border-end-md p-3">
                                            <div class="small text-muted mb-1"><i class="fas fa-hashtag me-1"></i>كود الأصل</div>
                                            <div class="fw-semibold font-monospace">{{ $asset->asset_code }}</div>
                                        </div>
                                        <div class="col border-bottom border-end-xl p-3">
                                            <div class="small text-muted mb-1"><i class="fas fa-folder me-1"></i>الفئة</div>
                                            <div class="fw-semibold">{{ $asset->category_name_ar }}</div>
                                        </div>
                                        <div class="col border-bottom p-3">
                                            <div class="small text-muted mb-1"><i class="far fa-clock me-1"></i>تاريخ الإنشاء</div>
                                            <div class="fw-semibold font-monospace small">{{ $asset->created_at->format('Y-m-d H:i') }}</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="tab-timeline" role="tabpanel">
                                    @can('asset-edit')
                                        <div class="card border-0 bg-light mb-4">
                                            <div class="card-body">
                                                <h6 class="mb-3 fw-semibold">إضافة ملاحظة أو مرفقات للسجل</h6>
                                                <form action="{{ route('admin.assets.lifecycle-events.store', $asset) }}" method="post" enctype="multipart/form-data" class="row g-3">
                                                    @csrf
                                                    <div class="col-md-6">
                                                        <label class="form-label small fw-semibold">عنوان الملاحظة <span class="text-danger">*</span></label>
                                                        <input type="text" name="summary" class="form-control" required maxlength="500" value="{{ old('summary') }}">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label small fw-semibold">تفاصيل (اختياري)</label>
                                                        <textarea name="notes" class="form-control" rows="2" maxlength="5000">{{ old('notes') }}</textarea>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label small fw-semibold">مرفقات (صور / PDF)</label>
                                                        <input type="file" name="attachments[]" class="form-control" multiple accept="image/*,.pdf">
                                                        <small class="text-muted">حتى 10 ملفات، 10 ميجا لكل ملف</small>
                                                    </div>
                                                    <div class="col-md-6 d-flex align-items-end">
                                                        <button type="submit" class="btn btn-primary">حفظ في السجل</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    @endcan

                                    <div class="timeline-2">
                                        @forelse ($lifecycleEvents as $event)
                                            <div class="card mb-3 border-0 shadow-sm border-start border-primary border-3">
                                                <div class="card-body py-3">
                                                    <div class="d-flex justify-content-between flex-wrap gap-2">
                                                        <div>
                                                            <span class="badge bg-secondary-subtle text-dark border me-1">{{ $event->event_type_name_ar }}</span>
                                                            @if (! empty($event->meta['backfill']))
                                                                <span class="badge bg-light text-dark border">استيراد</span>
                                                            @endif
                                                            <div class="fw-semibold mt-1">{{ $event->summary }}</div>
                                                            <div class="text-muted small">
                                                                {{ $event->occurred_at->format('Y-m-d H:i') }}
                                                                @if ($event->user) — {{ $event->user->name }} @endif
                                                                @if ($event->employee) — {{ $event->employee->full_name }} @endif
                                                            </div>
                                                        </div>
                                                        <div class="text-nowrap small">
                                                            @if ($event->related_assignment_id)
                                                                <a href="{{ route('admin.asset-assignments.show', $event->related_assignment_id) }}" class="btn btn-sm btn-outline-primary">التوزيع</a>
                                                            @endif
                                                            @if ($event->related_maintenance_id)
                                                                <a href="{{ route('admin.asset-maintenances.show', $event->related_maintenance_id) }}" class="btn btn-sm btn-outline-primary">الصيانة</a>
                                                            @endif
                                                        </div>
                                                    </div>

                                                    @if (! empty($event->meta['notes']))
                                                        <p class="mb-2 mt-2 small">{{ $event->meta['notes'] }}</p>
                                                    @endif

                                                    @if ($event->event_type === 'status_changed' && isset($event->meta['from'], $event->meta['to']))
                                                        <p class="mb-0 small text-muted">من {{ $event->meta['from'] }} إلى {{ $event->meta['to'] }}</p>
                                                    @endif

                                                    @if ($event->attachments->isNotEmpty())
                                                        <div class="d-flex flex-wrap gap-2 mt-2">
                                                            @foreach ($event->attachments as $att)
                                                                @if ($att->disk_url && $att->mime && str_starts_with($att->mime, 'image'))
                                                                    <a href="{{ $att->disk_url }}" target="_blank" rel="noopener">
                                                                        <img src="{{ $att->disk_url }}" alt="" class="rounded border" style="max-height: 80px; max-width: 120px;">
                                                                    </a>
                                                                @else
                                                                    <a href="{{ $att->disk_url }}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-secondary">
                                                                        {{ $att->original_name ?? 'مرفق' }}
                                                                    </a>
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @empty
                                            <p class="text-center text-muted py-4">لا توجد أحداث مسجّلة بعد.</p>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
