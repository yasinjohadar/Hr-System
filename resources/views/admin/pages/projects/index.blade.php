@extends('admin.layouts.master')

@section('page-title')
    المشاريع
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid admin-page-shell">
            @include('admin.pages.users.partials.alerts')

            <div class="admin-page-banner">
                <div class="admin-page-banner-main">
                    <span class="admin-page-banner-icon"><i class="ri-folder-chart-line"></i></span>
                    <div class="admin-page-banner-text">
                        <h1>المشاريع</h1>
                        <p>إدارة المشاريع والمراحل والميزانيات والفرق</p>
                    </div>
                </div>
                @can('project-create')
                    <div class="admin-page-banner-actions">
                        <a href="{{ route('admin.projects.create') }}" class="admin-btn admin-btn-light">
                            <i class="ri-add-line"></i>
                            مشروع جديد
                        </a>
                    </div>
                @endcan
            </div>

            <div class="admin-report-stats admin-report-stats-4 mb-4">
                <div class="admin-report-stat admin-report-stat-static admin-report-stat-blue">
                    <span class="admin-report-stat-icon"><i class="ri-folder-2-line"></i></span>
                    <span class="admin-report-stat-label">إجمالي المشاريع</span>
                    <span class="admin-report-stat-value" style="color:#2563eb;">{{ $projectStats['total'] }}</span>
                </div>
                <div class="admin-report-stat admin-report-stat-static admin-report-stat-green">
                    <span class="admin-report-stat-icon"><i class="ri-play-circle-line"></i></span>
                    <span class="admin-report-stat-label">نشطة</span>
                    <span class="admin-report-stat-value" style="color:#059669;">{{ $projectStats['active'] }}</span>
                </div>
                <div class="admin-report-stat admin-report-stat-static admin-report-stat-amber">
                    <span class="admin-report-stat-icon"><i class="ri-draft-line"></i></span>
                    <span class="admin-report-stat-label">قيد التخطيط</span>
                    <span class="admin-report-stat-value" style="color:#d97706;">{{ $projectStats['planning'] }}</span>
                </div>
                <div class="admin-report-stat admin-report-stat-static admin-report-stat-cyan">
                    <span class="admin-report-stat-icon"><i class="ri-checkbox-circle-line"></i></span>
                    <span class="admin-report-stat-label">مكتملة</span>
                    <span class="admin-report-stat-value" style="color:#0891b2;">{{ $projectStats['completed'] }}</span>
                </div>
            </div>

            <div class="admin-page-card">
                <div class="card-toolbar">
                    <form action="{{ route('admin.projects.index') }}" method="GET" class="admin-filters w-100">
                        <div class="search-input-wrap">
                            <i class="ri-search-line"></i>
                            <input type="text" name="search" class="form-control"
                                   placeholder="بحث بالاسم أو رقم المشروع..."
                                   value="{{ request('search') }}" autocomplete="off">
                        </div>
                        <select name="status" class="form-select" style="width:auto;min-width:150px;">
                            <option value="">كل الحالات</option>
                            <option value="planning" @selected(request('status') === 'planning')>قيد التخطيط</option>
                            <option value="active" @selected(request('status') === 'active')>نشط</option>
                            <option value="on_hold" @selected(request('status') === 'on_hold')>معلق</option>
                            <option value="completed" @selected(request('status') === 'completed')>مكتمل</option>
                            <option value="cancelled" @selected(request('status') === 'cancelled')>ملغي</option>
                        </select>
                        <select name="priority" class="form-select" style="width:auto;min-width:140px;">
                            <option value="">كل الأولويات</option>
                            <option value="low" @selected(request('priority') === 'low')>منخفض</option>
                            <option value="medium" @selected(request('priority') === 'medium')>متوسط</option>
                            <option value="high" @selected(request('priority') === 'high')>عالي</option>
                            <option value="urgent" @selected(request('priority') === 'urgent')>عاجل</option>
                        </select>
                        <select name="department_id" class="form-select" style="width:auto;min-width:160px;">
                            <option value="">كل الأقسام</option>
                            @foreach ($departments as $dept)
                                <option value="{{ $dept->id }}" @selected(request('department_id') == $dept->id)>{{ $dept->name }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="admin-btn admin-btn-primary">
                            <i class="ri-search-line"></i> بحث
                        </button>
                        <a href="{{ route('admin.projects.index') }}" class="admin-btn admin-btn-danger">
                            <i class="ri-filter-off-line"></i> مسح
                        </a>
                    </form>
                </div>

                <div class="admin-table-wrap">
                    <div class="table-responsive">
                        <table class="admin-data-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>المشروع</th>
                                    <th>القسم</th>
                                    <th>المدير</th>
                                    <th>الإنجاز</th>
                                    <th>الحالة</th>
                                    <th>الأولوية</th>
                                    <th>المهام</th>
                                    <th>العمليات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($projects as $project)
                                    <tr>
                                        <th class="row-number">{{ $projects->firstItem() + $loop->index }}</th>
                                        <td>
                                            <a href="{{ route('admin.projects.show', $project) }}" class="admin-user-link">
                                                {{ $project->name_ar ?? $project->name }}
                                            </a>
                                            <small class="text-muted d-block">{{ $project->project_code }}</small>
                                        </td>
                                        <td>{{ $project->department->name ?? '—' }}</td>
                                        <td>{{ $project->manager->full_name ?? '—' }}</td>
                                        <td style="min-width:120px;">
                                            <div class="progress" style="height:8px;border-radius:99px;">
                                                <div class="progress-bar bg-primary" style="width:{{ $project->progress }}%"></div>
                                            </div>
                                            <small class="text-muted">{{ $project->progress }}%</small>
                                        </td>
                                        <td>
                                            @php
                                                $statusBadge = match ($project->status) {
                                                    'active' => 'admin-badge-success',
                                                    'completed' => 'admin-badge-role',
                                                    'cancelled' => 'admin-badge-danger',
                                                    default => 'admin-badge-warning',
                                                };
                                            @endphp
                                            <span class="admin-badge {{ $statusBadge }}">{{ $project->status_name_ar }}</span>
                                        </td>
                                        <td>
                                            <span class="admin-badge {{ $project->priority === 'urgent' ? 'admin-badge-danger' : ($project->priority === 'high' ? 'admin-badge-warning' : 'admin-badge-muted') }}">
                                                {{ $project->priority_name_ar }}
                                            </span>
                                        </td>
                                        <td><span class="admin-badge admin-badge-muted">{{ $project->tasks_count }}</span></td>
                                        <td>
                                            <div class="admin-row-actions dropdown">
                                                <button class="admin-kebab-btn" type="button" data-bs-toggle="dropdown">
                                                    <i class="ri-more-2-fill"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    @can('project-show')
                                                        <li>
                                                            <a class="dropdown-item" href="{{ route('admin.projects.show', $project) }}">
                                                                <i class="ri-eye-line text-info me-2"></i>عرض
                                                            </a>
                                                        </li>
                                                    @endcan
                                                    @can('project-edit')
                                                        <li>
                                                            <a class="dropdown-item" href="{{ route('admin.projects.edit', $project) }}">
                                                                <i class="ri-edit-line text-primary me-2"></i>تعديل
                                                            </a>
                                                        </li>
                                                    @endcan
                                                    @can('project-delete')
                                                        <li><hr class="dropdown-divider"></li>
                                                        <li>
                                                            <button type="button" class="dropdown-item text-danger border-0 bg-transparent w-100 text-start"
                                                                    data-delete-url="{{ route('admin.projects.destroy', $project) }}"
                                                                    data-delete-message="هل أنت متأكد من حذف المشروع <strong>{{ $project->name_ar ?? $project->name }}</strong>؟">
                                                                <i class="ri-delete-bin-line me-2"></i>حذف
                                                            </button>
                                                        </li>
                                                    @endcan
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9">
                                            <div class="admin-empty-state">
                                                <i class="ri-folder-open-line"></i>
                                                لا توجد مشاريع مطابقة
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="admin-table-footer">
                    <div class="admin-table-meta">
                        @if ($projects->total() > 0)
                            عرض {{ $projects->firstItem() }} إلى {{ $projects->lastItem() }} من {{ $projects->total() }}
                        @else
                            لا توجد نتائج
                        @endif
                    </div>
                    <div class="admin-pagination">
                        {{ $projects->withQueryString()->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
