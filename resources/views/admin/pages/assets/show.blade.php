@extends('admin.layouts.master')

@section('page-title')
    تفاصيل الأصل
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">تفاصيل الأصل</h5>
                </div>
                <div>
                    <a href="{{ route('admin.assets.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-right me-2"></i>العودة للقائمة
                    </a>
                </div>
            </div>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="row">
                <div class="col-xl-10">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">{{ $asset->name_ar ?? $asset->name }}</h5>
                        </div>
                        <div class="card-body">
                            <ul class="nav nav-tabs mb-3" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" type="button" data-bs-toggle="tab" data-bs-target="#tab-asset-overview" role="tab">نظرة عامة</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" type="button" data-bs-toggle="tab" data-bs-target="#tab-asset-timeline" role="tab">السجل الزمني</button>
                                </li>
                            </ul>

                            <div class="tab-content">
                                <div class="tab-pane fade show active" id="tab-asset-overview" role="tabpanel">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">كود الأصل:</label>
                                            <p class="form-control-plaintext">{{ $asset->asset_code }}</p>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">الفئة:</label>
                                            <p class="form-control-plaintext">
                                                <span class="badge bg-info">{{ $asset->category_name_ar }}</span>
                                            </p>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">الشركة المصنعة:</label>
                                            <p class="form-control-plaintext">{{ $asset->manufacturer ?? '-' }}</p>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">الموديل:</label>
                                            <p class="form-control-plaintext">{{ $asset->model ?? '-' }}</p>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">الرقم التسلسلي:</label>
                                            <p class="form-control-plaintext">{{ $asset->serial_number ?? '-' }}</p>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">الحالة:</label>
                                            <p class="form-control-plaintext">
                                                <span class="badge bg-{{ $asset->status == 'available' ? 'success' : ($asset->status == 'assigned' ? 'primary' : ($asset->status == 'maintenance' ? 'warning' : 'danger')) }}">
                                                    {{ $asset->status_name_ar }}
                                                </span>
                                            </p>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">الموظف الحالي:</label>
                                            <p class="form-control-plaintext">
                                                @if ($asset->currentEmployee())
                                                    <a href="{{ route('admin.employees.show', $asset->currentEmployee()->id) }}">
                                                        {{ $asset->currentEmployee()->full_name }}
                                                    </a>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </p>
                                        </div>
                                        @if ($asset->description)
                                            <div class="col-12 mb-3">
                                                <label class="form-label fw-bold">الوصف:</label>
                                                <p class="form-control-plaintext">{{ $asset->description }}</p>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="mt-3">
                                        @can('asset-edit')
                                            <a href="{{ route('admin.assets.edit', $asset->id) }}" class="btn btn-warning">
                                                <i class="fas fa-edit me-2"></i>تعديل
                                            </a>
                                        @endcan
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="tab-asset-timeline" role="tabpanel">
                                    @can('asset-edit')
                                        <div class="card border mb-4">
                                            <div class="card-header bg-light">
                                                <h6 class="mb-0">إضافة ملاحظة أو مرفقات للسجل</h6>
                                            </div>
                                            <div class="card-body">
                                                <form action="{{ route('admin.assets.lifecycle-events.store', $asset) }}" method="post" enctype="multipart/form-data" class="row g-3">
                                                    @csrf
                                                    <div class="col-12">
                                                        <label class="form-label">عنوان الملاحظة <span class="text-danger">*</span></label>
                                                        <input type="text" name="summary" class="form-control" required maxlength="500" value="{{ old('summary') }}">
                                                    </div>
                                                    <div class="col-12">
                                                        <label class="form-label">تفاصيل (اختياري)</label>
                                                        <textarea name="notes" class="form-control" rows="2" maxlength="5000">{{ old('notes') }}</textarea>
                                                    </div>
                                                    <div class="col-12">
                                                        <label class="form-label">صور أو PDF (حتى 10 ملفات، 10 ميجا لكل ملف)</label>
                                                        <input type="file" name="attachments[]" class="form-control" multiple accept="image/*,.pdf">
                                                    </div>
                                                    <div class="col-12">
                                                        <button type="submit" class="btn btn-primary">حفظ في السجل</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    @endcan

                                    <p class="text-muted small">عرض آخر {{ $lifecycleEvents->count() }} حدثاً كحد أقصى.</p>

                                    <div class="timeline-2">
                                        @forelse ($lifecycleEvents as $event)
                                            <div class="card mb-3 border-start border-primary border-3">
                                                <div class="card-body py-3">
                                                    <div class="d-flex justify-content-between flex-wrap gap-2">
                                                        <div>
                                                            <span class="badge bg-secondary me-1">{{ $event->event_type_name_ar }}</span>
                                                            @if (! empty($event->meta['backfill']))
                                                                <span class="badge bg-light text-dark border">استيراد</span>
                                                            @endif
                                                            <div class="fw-semibold mt-1">{{ $event->summary }}</div>
                                                            <div class="text-muted small">
                                                                {{ $event->occurred_at->format('Y-m-d H:i') }}
                                                                @if ($event->user)
                                                                    — {{ $event->user->name }}
                                                                @endif
                                                                @if ($event->employee)
                                                                    — موظف: {{ $event->employee->full_name }}
                                                                @endif
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
                                            <p class="text-center text-muted py-4">لا توجد أحداث مسجّلة بعد. استخدم «إضافة ملاحظة» أو نفّذ أمر الاستيراد للبيانات القديمة.</p>
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
