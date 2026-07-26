@extends('admin.layouts.master')

@section('page-title')
    تفاصيل المشروع
@stop

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin-project-finance.css') }}?v=1">
@endpush

@section('content')
    @php
        $tab = $activeTab ?? request('tab', 'overview');
        $budget = (float) ($project->budget ?? 0);
        $allocated = (float) ($stagesAllocated ?? 0);
    @endphp

    <div class="main-content app-content">
        <div class="container-fluid admin-page-shell">
            @include('admin.pages.users.partials.alerts')

            <div class="admin-page-banner">
                <div class="admin-page-banner-main">
                    <span class="admin-page-banner-icon"><i class="ri-folder-chart-line"></i></span>
                    <div class="admin-page-banner-text">
                        <h1>{{ $project->name_ar ?? $project->name }}</h1>
                        <p>{{ $project->project_code }} — {{ $project->status_name_ar }} · إنجاز {{ $project->progress }}%</p>
                    </div>
                </div>
                <div class="admin-page-banner-actions d-flex flex-wrap gap-2">
                    <a href="{{ route('admin.projects.index') }}" class="admin-btn admin-btn-light">
                        <i class="ri-arrow-right-line"></i>
                        القائمة
                    </a>
                    @can('project-edit')
                        <a href="{{ route('admin.projects.edit', $project) }}" class="admin-btn admin-btn-primary">
                            <i class="ri-edit-line"></i>
                            تعديل
                        </a>
                    @endcan
                </div>
            </div>

            <div class="admin-report-stats admin-report-stats-4 mb-4">
                <div class="admin-report-stat admin-report-stat-static admin-report-stat-blue">
                    <span class="admin-report-stat-icon"><i class="ri-percent-line"></i></span>
                    <span class="admin-report-stat-label">نسبة الإنجاز</span>
                    <span class="admin-report-stat-value" style="color:#2563eb;">{{ $project->progress }}%</span>
                </div>
                <div class="admin-report-stat admin-report-stat-static admin-report-stat-green">
                    <span class="admin-report-stat-icon"><i class="ri-stack-line"></i></span>
                    <span class="admin-report-stat-label">المراحل</span>
                    <span class="admin-report-stat-value" style="color:#059669;">{{ $project->stages->count() }}</span>
                </div>
                <div class="admin-report-stat admin-report-stat-static admin-report-stat-amber">
                    <span class="admin-report-stat-icon"><i class="ri-wallet-3-line"></i></span>
                    <span class="admin-report-stat-label">ميزانية / مخصّص</span>
                    <span class="admin-report-stat-value" style="color:#d97706;font-size:1.1rem;">
                        {{ $budget > 0 ? number_format($budget, 0) : '—' }}
                        <small class="text-muted fw-normal">/ {{ number_format($allocated, 0) }}</small>
                    </span>
                </div>
                <div class="admin-report-stat admin-report-stat-static admin-report-stat-cyan">
                    <span class="admin-report-stat-icon"><i class="ri-team-line"></i></span>
                    <span class="admin-report-stat-label">أعضاء الفريق</span>
                    <span class="admin-report-stat-value" style="color:#0891b2;">{{ $project->members->count() }}</span>
                </div>
            </div>

            <div class="admin-page-card project-show-card">
                <div class="card-body p-3 p-lg-4">
                            <ul class="nav nav-tabs project-show-tabs mb-3" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link {{ $tab === 'overview' ? 'active' : '' }}" id="tab-overview-btn" data-bs-toggle="tab" data-bs-target="#tab-overview" type="button" role="tab">نظرة عامة</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link {{ $tab === 'stages' ? 'active' : '' }}" id="tab-stages-btn" data-bs-toggle="tab" data-bs-target="#tab-stages" type="button" role="tab">المراحل</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link {{ $tab === 'team' ? 'active' : '' }}" id="tab-team-btn" data-bs-toggle="tab" data-bs-target="#tab-team" type="button" role="tab">فريق المشروع</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link {{ $tab === 'finance' ? 'active' : '' }}" id="tab-finance-btn" data-bs-toggle="tab" data-bs-target="#tab-finance" type="button" role="tab">التمويل</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link {{ $tab === 'docs' ? 'active' : '' }}" id="tab-docs-btn" data-bs-toggle="tab" data-bs-target="#tab-docs" type="button" role="tab">المستندات</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link {{ $tab === 'time' ? 'active' : '' }}" id="tab-time-btn" data-bs-toggle="tab" data-bs-target="#tab-time" type="button" role="tab">سجل الوقت</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link {{ $tab === 'tasks' ? 'active' : '' }}" id="tab-tasks-btn" data-bs-toggle="tab" data-bs-target="#tab-tasks" type="button" role="tab">المهام</button>
                                </li>
                            </ul>

                            <div class="tab-content">
                                <div class="tab-pane fade {{ $tab === 'overview' ? 'show active' : '' }}" id="tab-overview" role="tabpanel">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">رقم المشروع:</label>
                                            <p class="form-control-plaintext">{{ $project->project_code }}</p>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">الاسم:</label>
                                            <p class="form-control-plaintext">{{ $project->name }}</p>
                                        </div>
                                        @if ($project->name_ar)
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-bold">الاسم (عربي):</label>
                                                <p class="form-control-plaintext">{{ $project->name_ar }}</p>
                                            </div>
                                        @endif
                                        @if ($project->department)
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-bold">القسم:</label>
                                                <p class="form-control-plaintext">{{ $project->department->name }}</p>
                                            </div>
                                        @endif
                                        @if ($project->manager)
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-bold">مدير المشروع:</label>
                                                <p class="form-control-plaintext">
                                                    <a href="{{ route('admin.employees.show', $project->manager_id) }}">
                                                        {{ $project->manager->full_name }}
                                                    </a>
                                                </p>
                                            </div>
                                        @endif
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">تاريخ البدء:</label>
                                            <p class="form-control-plaintext">{{ $project->start_date->format('Y-m-d') }}</p>
                                        </div>
                                        @if ($project->end_date)
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-bold">تاريخ الانتهاء:</label>
                                                <p class="form-control-plaintext">{{ $project->end_date->format('Y-m-d') }}</p>
                                            </div>
                                        @endif
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">الحالة:</label>
                                            <p class="form-control-plaintext">
                                                <span class="badge bg-{{ $project->status == 'completed' ? 'success' : ($project->status == 'active' ? 'primary' : ($project->status == 'cancelled' ? 'danger' : 'warning')) }}">
                                                    {{ $project->status_name_ar }}
                                                </span>
                                            </p>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">الأولوية:</label>
                                            <p class="form-control-plaintext">
                                                <span class="badge bg-{{ $project->priority == 'urgent' ? 'danger' : ($project->priority == 'high' ? 'warning' : 'info') }}">
                                                    {{ $project->priority_name_ar }}
                                                </span>
                                            </p>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">نسبة الإنجاز:</label>
                                            <p class="form-control-plaintext">
                                                <div class="progress" style="height: 25px;">
                                                    <div class="progress-bar" role="progressbar" style="width: {{ $project->progress }}%">
                                                        {{ $project->progress }}%
                                                    </div>
                                                </div>
                                            </p>
                                        </div>
                                        @if ($project->budget)
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-bold">الميزانية:</label>
                                                <p class="form-control-plaintext">
                                                    {{ number_format($project->budget, 2) }}
                                                    @if ($project->currency)
                                                        {{ $project->currency->code }}
                                                    @endif
                                                </p>
                                                <small class="text-muted">مخصّص للمراحل: {{ number_format($stagesAllocated ?? 0, 2) }}</small>
                                            </div>
                                        @endif
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">عدد المهام:</label>
                                            <p class="form-control-plaintext">{{ $project->tasks_count }}</p>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">إجمالي الساعات المسجّلة:</label>
                                            <p class="form-control-plaintext">{{ number_format((float) ($project->total_logged_hours ?? 0), 2) }} ساعة</p>
                                        </div>
                                        @if ($project->description)
                                            <div class="col-12 mb-3">
                                                <label class="form-label fw-bold">الوصف:</label>
                                                <p class="form-control-plaintext">{{ $project->description }}</p>
                                            </div>
                                        @endif
                                        @if ($project->notes)
                                            <div class="col-12 mb-3">
                                                <label class="form-label fw-bold">ملاحظات:</label>
                                                <p class="form-control-plaintext">{{ $project->notes }}</p>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="mt-3">
                                        @can('project-edit')
                                            <a href="{{ route('admin.projects.edit', $project->id) }}" class="btn btn-info">
                                                <i class="fas fa-edit me-2"></i>تعديل
                                            </a>
                                        @endcan
                                        @can('task-create')
                                            <a href="{{ route('admin.tasks.create', ['project_id' => $project->id]) }}" class="btn btn-primary">
                                                <i class="fas fa-plus me-2"></i>إضافة مهمة
                                            </a>
                                        @endcan
                                    </div>
                                </div>

                                <div class="tab-pane fade {{ $tab === 'stages' ? 'show active' : '' }}" id="tab-stages" role="tabpanel">
                                    @php
                                        $budget = (float) ($project->budget ?? 0);
                                        $allocated = (float) ($stagesAllocated ?? 0);
                                        $budgetPct = $budget > 0 ? min(100, round(($allocated / $budget) * 100)) : 0;
                                    @endphp
                                    <div class="mb-4 p-3 border rounded">
                                        <div class="d-flex justify-content-between small mb-1">
                                            <span>توزيع ميزانية المراحل</span>
                                            <span>{{ number_format($allocated, 2) }} / {{ $budget > 0 ? number_format($budget, 2) : '—' }}</span>
                                        </div>
                                        <div class="progress" style="height: 10px;">
                                            <div class="progress-bar {{ $allocated > $budget && $budget > 0 ? 'bg-warning' : 'bg-primary' }}" style="width: {{ $budget > 0 ? $budgetPct : 0 }}%"></div>
                                        </div>
                                        @if ($allocated > $budget && $budget > 0)
                                            <small class="text-warning d-block mt-1">مجموع المراحل يتجاوز الميزانية — يتطلب صلاحية تجاوز مع سبب.</small>
                                        @endif
                                    </div>

                                    @can('project-stage-create')
                                        <form action="{{ route('admin.projects.stages.store', $project) }}" method="post" class="row g-2 mb-4 border rounded p-3">
                                            @csrf
                                            <div class="col-md-3">
                                                <label class="form-label">اسم المرحلة</label>
                                                <input type="text" name="name" class="form-control" required value="{{ old('name') }}">
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label">المبلغ المخصص</label>
                                                <input type="number" step="0.01" min="0" name="allocated_amount" class="form-control" required value="{{ old('allocated_amount', 0) }}">
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label">البداية</label>
                                                <input type="date" name="start_date" class="form-control" value="{{ old('start_date') }}">
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label">النهاية</label>
                                                <input type="date" name="end_date" class="form-control" value="{{ old('end_date') }}">
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label">الحالة</label>
                                                <select name="status" class="form-select" required>
                                                    <option value="planned">مخططة</option>
                                                    <option value="active">نشطة</option>
                                                    <option value="completed">مكتملة</option>
                                                    <option value="cancelled">ملغاة</option>
                                                </select>
                                            </div>
                                            @can('project-budget-override')
                                                <div class="col-md-8">
                                                    <label class="form-label">سبب تجاوز الميزانية (إن لزم)</label>
                                                    <input type="text" name="budget_override_reason" class="form-control" value="{{ old('budget_override_reason') }}">
                                                </div>
                                            @endcan
                                            <div class="col-md-4 d-flex align-items-end">
                                                <button type="submit" class="btn btn-primary w-100">إضافة مرحلة</button>
                                            </div>
                                        </form>
                                    @endcan

                                    <div class="table-responsive">
                                        <table class="table table-striped align-middle">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>المرحلة</th>
                                                    <th>التواريخ</th>
                                                    <th>المبلغ</th>
                                                    <th>الحالة</th>
                                                    <th>الأعضاء</th>
                                                    @canAny(['project-stage-edit', 'project-stage-delete'])
                                                        <th></th>
                                                    @endcanAny
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($project->stages as $stage)
                                                    <tr>
                                                        <td>{{ $stage->sort_order }}</td>
                                                        <td>{{ $stage->display_name }}</td>
                                                        <td class="small">
                                                            {{ $stage->start_date?->format('Y-m-d') ?? '—' }}
                                                            →
                                                            {{ $stage->end_date?->format('Y-m-d') ?? '—' }}
                                                        </td>
                                                        <td>{{ number_format((float) $stage->allocated_amount, 2) }}</td>
                                                        <td><span class="badge bg-secondary">{{ $stage->status_name_ar }}</span></td>
                                                        <td>
                                                            @forelse ($stage->members as $sm)
                                                                <span class="badge bg-light text-dark border">{{ $sm->employee->full_name ?? '—' }}</span>
                                                                @can('project-edit')
                                                                    <form action="{{ route('admin.projects.stages.members.destroy', [$project, $stage, $sm]) }}" method="post" class="d-inline" onsubmit="return confirm('إزالة من المرحلة؟');">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit" class="btn btn-link btn-sm text-danger p-0">×</button>
                                                                    </form>
                                                                @endcan
                                                            @empty
                                                                <span class="text-muted small">—</span>
                                                            @endforelse
                                                        </td>
                                                        @canAny(['project-stage-edit', 'project-stage-delete'])
                                                            <td class="text-nowrap">
                                                                @can('project-stage-delete')
                                                                    <form action="{{ route('admin.projects.stages.destroy', [$project, $stage]) }}" method="post" class="d-inline" onsubmit="return confirm('حذف المرحلة؟');">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit" class="btn btn-sm btn-outline-danger">حذف</button>
                                                                    </form>
                                                                @endcan
                                                            </td>
                                                        @endcanAny
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="7" class="text-center text-muted">لا توجد مراحل بعد.</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>

                                    @if ($project->budgetOverrides->isNotEmpty())
                                        <h6 class="mt-4">سجل تجاوزات الميزانية</h6>
                                        <ul class="list-group list-group-flush">
                                            @foreach ($project->budgetOverrides as $override)
                                                <li class="list-group-item small">
                                                    {{ $override->approved_at?->format('Y-m-d H:i') }} —
                                                    إجمالي {{ number_format((float) $override->requested_stages_total, 2) }}
                                                    — {{ $override->reason }}
                                                    ({{ $override->approver->name ?? '—' }})
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </div>

                                <div class="tab-pane fade {{ $tab === 'team' ? 'show active' : '' }}" id="tab-team" role="tabpanel">
                                    @can('project-edit')
                                        <form action="{{ route('admin.projects.members.store', $project) }}" method="post" class="row g-3 mb-4">
                                            @csrf
                                            <div class="col-md-4">
                                                <label class="form-label">الموظف</label>
                                                <select name="employee_id" class="form-select" required>
                                                    <option value="">— اختر —</option>
                                                    @foreach ($employees as $emp)
                                                        <option value="{{ $emp->id }}">{{ $emp->full_name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">دور المشروع</label>
                                                <select name="role" class="form-select" required>
                                                    <option value="member">عضو فريق</option>
                                                    <option value="lead">قائد فريق</option>
                                                    <option value="sponsor">راعي / داعم</option>
                                                </select>
                                            </div>
                                            <div class="col-md-5">
                                                <label class="form-label d-block">نطاق التعيين</label>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="checkbox" name="assign_to_project" value="1" id="assign_project" checked>
                                                    <label class="form-check-label" for="assign_project">المشروع</label>
                                                </div>
                                                @foreach ($project->stages as $stage)
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="checkbox" name="stage_ids[]" value="{{ $stage->id }}" id="stage_{{ $stage->id }}">
                                                        <label class="form-check-label" for="stage_{{ $stage->id }}">{{ $stage->display_name }}</label>
                                                    </div>
                                                @endforeach
                                            </div>
                                            <div class="col-12">
                                                <button type="submit" class="btn btn-primary">حفظ التعيين</button>
                                            </div>
                                        </form>
                                    @endcan

                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>الموظف</th>
                                                    <th>الدور</th>
                                                    <th>منذ</th>
                                                    @can('project-edit')
                                                        <th width="100">إجراءات</th>
                                                    @endcan
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($project->members as $member)
                                                    <tr>
                                                        <td>
                                                            <a href="{{ route('admin.employees.show', $member->employee_id) }}">{{ $member->employee->full_name ?? '—' }}</a>
                                                        </td>
                                                        <td>{{ $member->role_name_ar }}</td>
                                                        <td>{{ $member->created_at?->format('Y-m-d') }}</td>
                                                        @can('project-edit')
                                                            <td>
                                                                <form action="{{ route('admin.projects.members.destroy', [$project, $member]) }}" method="post" class="d-inline" onsubmit="return confirm('إزالة هذا العضو من المشروع؟');">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn btn-sm btn-outline-danger">إزالة</button>
                                                                </form>
                                                            </td>
                                                        @endcan
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="{{ auth()->user()->can('project-edit') ? 4 : 3 }}" class="text-center text-muted">لا يوجد أعضاء مسجّلون بجدول الفريق (عدا مدير المشروع).</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="tab-pane fade {{ $tab === 'finance' ? 'show active' : '' }}" id="tab-finance" role="tabpanel">
                                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                                        <div>
                                            <h6 class="mb-1">تمويل المشروع</h6>
                                            <p class="text-muted small mb-0">آخر التحويلات المرتبطة بهذا المشروع</p>
                                        </div>
                                        @can('fund-transfer-create')
                                            <a href="{{ route('admin.fund-transfers.create', ['project_id' => $project->id]) }}" class="btn btn-primary btn-sm">
                                                <i class="ri-exchange-dollar-line me-1"></i>تحويل جديد
                                            </a>
                                        @endcan
                                        @can('fund-transfer-list')
                                            <a href="{{ route('admin.fund-transfers.index', ['project_id' => $project->id]) }}" class="btn btn-outline-secondary btn-sm">كل التحويلات</a>
                                        @endcan
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>الرمز</th>
                                                    <th>النوع</th>
                                                    <th>المبلغ</th>
                                                    <th>المرحلة</th>
                                                    <th>الحالة</th>
                                                    <th>التاريخ</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($project->fundTransfers as $ft)
                                                    <tr>
                                                        <td>
                                                            @can('fund-transfer-list')
                                                                <a href="{{ route('admin.fund-transfers.show', $ft) }}">{{ $ft->transfer_code }}</a>
                                                            @else
                                                                {{ $ft->transfer_code }}
                                                            @endcan
                                                        </td>
                                                        <td>{{ $ft->type_name_ar }}</td>
                                                        <td>{{ number_format((float) $ft->amount, 2) }}</td>
                                                        <td>{{ $ft->stage?->display_name ?? '—' }}</td>
                                                        <td>{{ $ft->status_name_ar }}</td>
                                                        <td>{{ $ft->created_at?->format('Y-m-d') }}</td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="6" class="text-center text-muted">لا توجد تحويلات مرتبطة بعد.</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="tab-pane fade {{ $tab === 'docs' ? 'show active' : '' }}" id="tab-docs" role="tabpanel">
                                    @can('project-edit')
                                        <form action="{{ route('admin.projects.documents.store', $project) }}" method="post" enctype="multipart/form-data" class="row g-3 mb-4">
                                            @csrf
                                            <div class="col-md-4">
                                                <label class="form-label">عنوان المستند</label>
                                                <input type="text" name="title" class="form-control" required maxlength="255">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">الملف</label>
                                                <input type="file" name="file" class="form-control" required>
                                                <small class="text-muted">حتى 15 ميجابايت — pdf, office, صور, zip</small>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">وصف (اختياري)</label>
                                                <input type="text" name="description" class="form-control">
                                            </div>
                                            <div class="col-md-1 d-flex align-items-end">
                                                <button type="submit" class="btn btn-primary w-100">رفع</button>
                                            </div>
                                        </form>
                                    @endcan

                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>العنوان</th>
                                                    <th>الملف</th>
                                                    <th>الرافع</th>
                                                    <th>التاريخ</th>
                                                    @can('project-edit')
                                                        <th width="100">إجراءات</th>
                                                    @endcan
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($project->documents as $doc)
                                                    <tr>
                                                        <td>{{ $doc->title }}</td>
                                                        <td>
                                                            @if ($doc->disk_url)
                                                                <a href="{{ $doc->disk_url }}" target="_blank" rel="noopener">{{ $doc->original_name ?? 'تحميل' }}</a>
                                                            @else
                                                                —
                                                            @endif
                                                        </td>
                                                        <td>{{ $doc->uploader->name ?? '—' }}</td>
                                                        <td>{{ $doc->created_at?->format('Y-m-d') }}</td>
                                                        @can('project-edit')
                                                            <td>
                                                                <form action="{{ route('admin.projects.documents.destroy', [$project, $doc]) }}" method="post" class="d-inline" onsubmit="return confirm('حذف المستند؟');">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn btn-sm btn-outline-danger">حذف</button>
                                                                </form>
                                                            </td>
                                                        @endcan
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="{{ auth()->user()->can('project-edit') ? 5 : 4 }}" class="text-center text-muted">لا توجد مستندات</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="tab-pane fade {{ $tab === 'time' ? 'show active' : '' }}" id="tab-time" role="tabpanel">
                                    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
                                        <p class="mb-0"><strong>إجمالي الساعات:</strong> {{ number_format((float) ($project->total_logged_hours ?? 0), 2) }} ساعة</p>
                                        @can('project-show')
                                            <a href="{{ route('admin.projects.time-entries.export', $project) }}" class="btn btn-outline-secondary btn-sm">
                                                <i class="fas fa-download me-1"></i>تصدير CSV
                                            </a>
                                        @endcan
                                    </div>

                                    @can('project-edit')
                                        @if ($project->allowsTimeLogging())
                                            <form action="{{ route('admin.projects.time-entries.store', $project) }}" method="post" class="row g-3 mb-4">
                                                @csrf
                                                <div class="col-md-3">
                                                    <label class="form-label">الموظف</label>
                                                    <select name="employee_id" class="form-select" required>
                                                        @foreach ($employees as $emp)
                                                            <option value="{{ $emp->id }}">{{ $emp->full_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label">المهمة (اختياري)</label>
                                                    <select name="task_id" class="form-select">
                                                        <option value="">— بدون —</option>
                                                        @foreach ($project->tasks as $t)
                                                            <option value="{{ $t->id }}">{{ $t->task_code }} — {{ $t->title_ar ?? $t->title }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-2">
                                                    <label class="form-label">تاريخ العمل</label>
                                                    <input type="date" name="worked_date" class="form-control" value="{{ now()->format('Y-m-d') }}" required>
                                                </div>
                                                <div class="col-md-2">
                                                    <label class="form-label">الساعات</label>
                                                    <input type="number" name="hours" class="form-control" step="0.25" min="0.01" max="24" required>
                                                </div>
                                                <div class="col-md-12">
                                                    <label class="form-label">وصف (اختياري)</label>
                                                    <input type="text" name="description" class="form-control" maxlength="2000">
                                                </div>
                                                <div class="col-12">
                                                    <button type="submit" class="btn btn-primary">تسجيل الوقت</button>
                                                </div>
                                            </form>
                                        @else
                                            <div class="alert alert-warning">لا يمكن إضافة سجلات وقت جديدة لمشروع مكتمل أو ملغى.</div>
                                        @endif
                                    @endcan

                                    <div class="table-responsive">
                                        <table class="table table-striped table-sm">
                                            <thead>
                                                <tr>
                                                    <th>التاريخ</th>
                                                    <th>الموظف</th>
                                                    <th>الساعات</th>
                                                    <th>المهمة</th>
                                                    <th>الوصف</th>
                                                    <th>المسجّل</th>
                                                    @can('project-edit')
                                                        <th width="90">إجراءات</th>
                                                    @endcan
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($project->timeEntries as $entry)
                                                    <tr>
                                                        <td>{{ $entry->worked_date->format('Y-m-d') }}</td>
                                                        <td>{{ $entry->employee->full_name ?? '—' }}</td>
                                                        <td>{{ number_format((float) $entry->hours, 2) }}</td>
                                                        <td>
                                                            @if ($entry->task)
                                                                <a href="{{ route('admin.tasks.show', $entry->task_id) }}">{{ $entry->task->task_code }}</a>
                                                            @else
                                                                —
                                                            @endif
                                                        </td>
                                                        <td>{{ \Illuminate\Support\Str::limit($entry->description ?? '', 40) }}</td>
                                                        <td>{{ $entry->creator->name ?? '—' }}</td>
                                                        @can('project-edit')
                                                            <td>
                                                                <form action="{{ route('admin.projects.time-entries.destroy', [$project, $entry]) }}" method="post" class="d-inline" onsubmit="return confirm('حذف السجل؟');">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn btn-sm btn-outline-danger">حذف</button>
                                                                </form>
                                                            </td>
                                                        @endcan
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="{{ auth()->user()->can('project-edit') ? 7 : 6 }}" class="text-center text-muted">لا توجد سجلات وقت</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="tab-pane fade {{ $tab === 'tasks' ? 'show active' : '' }}" id="tab-tasks" role="tabpanel">
                                    @if ($project->tasks->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>رقم المهمة</th>
                                                        <th>العنوان</th>
                                                        <th>الحالة</th>
                                                        <th>نسبة الإنجاز</th>
                                                        <th>الإجراءات</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($project->tasks as $task)
                                                        <tr>
                                                            <td>{{ $task->task_code }}</td>
                                                            <td>{{ $task->title_ar ?? $task->title }}</td>
                                                            <td>
                                                                <span class="badge bg-{{ $task->status == 'completed' ? 'success' : ($task->status == 'in_progress' ? 'primary' : 'warning') }}">
                                                                    {{ $task->status_name_ar }}
                                                                </span>
                                                            </td>
                                                            <td>{{ $task->progress }}%</td>
                                                            <td>
                                                                <a href="{{ route('admin.tasks.show', $task->id) }}" class="btn btn-sm btn-primary">
                                                                    <i class="fas fa-eye"></i>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <p class="text-muted text-center mb-0">لا توجد مهام لهذا المشروع.</p>
                                    @endif
                                </div>
                            </div>
                </div>
            </div>
        </div>
    </div>
@stop
