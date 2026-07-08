@extends('admin.layouts.master')

@section('page-title')
    تفاصيل رئيس القسم
@stop

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin-department-heads.css') }}?v=1">
@endpush

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid admin-page-shell">
            @include('admin.pages.users.partials.alerts')

            <div class="admin-page-banner">
                <div class="admin-page-banner-main">
                    <span class="admin-page-banner-icon"><i class="ri-user-star-line"></i></span>
                    <div class="admin-page-banner-text">
                        <h1>{{ $head->name }}</h1>
                        <p>{{ $head->email }}</p>
                    </div>
                </div>
                <div class="admin-page-banner-actions d-flex flex-wrap gap-2">
                    <a href="{{ route('admin.department-heads.index') }}" class="admin-btn admin-btn-light">
                        <i class="ri-arrow-right-line"></i>
                        القائمة
                    </a>
                    <a href="{{ route('admin.department-heads.capabilities', $head->id) }}" class="admin-btn admin-btn-light">
                        <i class="ri-shield-user-line"></i>
                        الصلاحيات والقدرات
                    </a>
                    @can('department-head-manage')
                        <a href="{{ route('admin.department-heads.edit', $head->id) }}" class="admin-btn admin-btn-primary">
                            <i class="ri-building-line"></i>
                            تعديل الأقسام
                        </a>
                    @endcan
                </div>
            </div>

            <div class="admin-report-stats admin-report-stats-4 mb-4">
                <div class="admin-report-stat admin-report-stat-static admin-report-stat-blue">
                    <span class="admin-report-stat-icon"><i class="ri-building-4-line"></i></span>
                    <span class="admin-report-stat-label">الأقسام المباشرة</span>
                    <span class="admin-report-stat-value" style="color:#2563eb;">{{ $managedDepartments->count() }}</span>
                </div>
                <div class="admin-report-stat admin-report-stat-static admin-report-stat-green">
                    <span class="admin-report-stat-icon"><i class="ri-team-line"></i></span>
                    <span class="admin-report-stat-label">حجم الفريق</span>
                    <span class="admin-report-stat-value" style="color:#059669;">{{ count($managedEmployeeIds) }}</span>
                </div>
                <div class="admin-report-stat admin-report-stat-static admin-report-stat-amber">
                    <span class="admin-report-stat-icon"><i class="ri-calendar-check-line"></i></span>
                    <span class="admin-report-stat-label">إجازات معلّقة</span>
                    <span class="admin-report-stat-value" style="color:#d97706;">{{ $pendingLeaves }}</span>
                </div>
                <div class="admin-report-stat admin-report-stat-static admin-report-stat-cyan">
                    <span class="admin-report-stat-icon"><i class="ri-key-2-line"></i></span>
                    <span class="admin-report-stat-label">الصلاحيات</span>
                    <span class="admin-report-stat-value" style="color:#0891b2;">{{ $permissions->count() }}</span>
                </div>
            </div>

            @can('department-head-manage')
                <div class="d-flex flex-wrap gap-2 mb-4">
                    <form action="{{ route('admin.department-heads.apply-role-template', $head->id) }}" method="POST" class="d-inline"
                          data-confirm="تطبيق صلاحيات قالب رئيس القسم على هذا المستخدم؟"
                          data-confirm-title="تطبيق قالب الصلاحيات"
                          data-confirm-type="warning"
                          data-confirm-btn="تطبيق القالب">
                        @csrf
                        <button type="submit" class="admin-btn admin-btn-secondary">
                            <i class="ri-refresh-line"></i>
                            تطبيق قالب الصلاحيات
                        </button>
                    </form>
                    <button type="button" class="admin-btn admin-btn-danger"
                            data-delete-url="{{ route('admin.department-heads.destroy', $head->id) }}"
                            data-delete-title="إلغاء تعيين رئيس القسم"
                            data-delete-message="إلغاء تعيين <strong>{{ $head->name }}</strong> من جميع الأقسام؟"
                            data-delete-hint="سيُزال من الأقسام المُدارة وقد يُلغى دور رئيس القسم."
                            data-delete-confirm="إلغاء التعيين">
                        <i class="ri-user-unfollow-line"></i>
                        إلغاء التعيين الكامل
                    </button>
                </div>
            @endcan

            <div class="dh-show-panels mb-4">
                <div class="dh-show-panel">
                    <div class="dh-show-panel__head">
                        <h3><i class="ri-user-line me-1 text-primary"></i> بيانات المستخدم</h3>
                    </div>
                    <div class="dh-show-panel__body">
                        <dl class="dh-dl">
                            <dt>الاسم</dt>
                            <dd>{{ $head->name }}</dd>
                            <dt>البريد</dt>
                            <dd>{{ $head->email }}</dd>
                            <dt>الحالة</dt>
                            <dd>
                                @if ($head->is_active)
                                    <span class="admin-badge admin-badge-success">نشط</span>
                                @else
                                    <span class="admin-badge admin-badge-danger">معطّل</span>
                                @endif
                            </dd>
                            <dt>الأدوار</dt>
                            <dd>
                                @forelse ($head->roles as $role)
                                    <span class="admin-badge admin-badge-role">{{ $role->name }}</span>
                                @empty
                                    <span class="text-muted">—</span>
                                @endforelse
                            </dd>
                            @if ($head->employee)
                                <dt>ملف الموظف</dt>
                                <dd>
                                    <a href="{{ route('admin.employees.show', $head->employee->id) }}">{{ $head->employee->full_name }}</a>
                                </dd>
                            @endif
                        </dl>
                        @if (auth()->id() === $head->id && $head->isDepartmentHead())
                            <div class="mt-3 pt-3 border-top">
                                <a href="{{ route('admin.team.dashboard') }}" class="admin-btn admin-btn-secondary admin-btn-sm">
                                    <i class="ri-dashboard-line"></i>
                                    لوحة إدارة الفريق
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="dh-show-panel">
                    <div class="dh-show-panel__head">
                        <h3><i class="ri-building-4-line me-1 text-success"></i> الأقسام المُدارة</h3>
                        @can('department-edit')
                            <a href="{{ route('admin.departments.index') }}" class="admin-btn admin-btn-secondary admin-btn-sm">كل الأقسام</a>
                        @endcan
                    </div>
                    <div class="dh-show-panel__body p-0">
                        <div class="table-responsive">
                            <table class="admin-data-table mb-0">
                                <thead>
                                    <tr>
                                        <th>القسم</th>
                                        <th>الموظفون</th>
                                        <th>القسم الأب</th>
                                        @can('department-head-manage')
                                            <th></th>
                                        @endcan
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($managedDepartments as $department)
                                        <tr>
                                            <td>
                                                <a href="{{ route('admin.departments.show', $department->id) }}" class="admin-user-link">{{ $department->name }}</a>
                                            </td>
                                            <td><span class="admin-badge admin-badge-muted">{{ $department->employees_count }}</span></td>
                                            <td>{{ $department->parent?->name ?? '—' }}</td>
                                            @can('department-head-manage')
                                                <td>
                                                    <form action="{{ route('admin.department-heads.remove-department', [$head->id, $department->id]) }}"
                                                          method="POST" class="d-inline"
                                                          data-confirm="إزالة {{ $department->name }} من رئيس القسم؟"
                                                          data-confirm-title="إزالة القسم"
                                                          data-confirm-type="warning"
                                                          data-confirm-btn="إزالة">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="admin-btn admin-btn-danger admin-btn-sm">إزالة</button>
                                                    </form>
                                                </td>
                                            @endcan
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4">
                                                <div class="admin-empty-state py-3">
                                                    <i class="ri-building-line"></i>
                                                    لا توجد أقسام معيّنة
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="dh-capabilities-link-card">
                <div>
                    <h6 class="mb-1 fw-semibold">الصلاحيات والقدرات</h6>
                    <p class="text-muted small mb-0">
                        {{ $permissions->count() }} صلاحية مفعّلة
                        @if ($roleTemplate)
                            — القالب الافتراضي {{ count($roleTemplate['permissions'] ?? []) }} صلاحية
                        @endif
                    </p>
                </div>
                <a href="{{ route('admin.department-heads.capabilities', $head->id) }}" class="admin-btn admin-btn-primary">
                    <i class="ri-shield-user-line"></i>
                    عرض التوضيح الكامل
                </a>
            </div>
        </div>
    </div>
@stop
