@extends('admin.layouts.master')

@section('page-title')
    صلاحيات وقدرات رئيس القسم
@stop

@php
    $templateTotal = max(1, $capabilities['template_permissions']->count());
    $templatePct = (int) round(($capabilities['template_granted_count'] / $templateTotal) * 100);
    $circumference = 2 * 3.14159 * 36;
    $strokeOffset = $circumference - ($templatePct / 100) * $circumference;
    $initial = mb_substr($head->name, 0, 1);
@endphp

@section('content')
    <div class="main-content app-content dh-capabilities">
        <div class="container-fluid admin-page-shell pb-4">

            {{-- Hero --}}
            <div class="card dh-hero mb-4">
                <div class="card-body">
                    <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-4">
                        <div class="flex-grow-1">
                            <nav aria-label="breadcrumb" class="mb-2">
                                <ol class="breadcrumb mb-0 small">
                                    <li class="breadcrumb-item"><a href="{{ route('admin.department-heads.index') }}">رؤساء الأقسام</a></li>
                                    <li class="breadcrumb-item active">الصلاحيات والقدرات</li>
                                </ol>
                            </nav>
                            <div class="d-flex align-items-center gap-3 flex-wrap">
                                <div class="dh-hero-avatar">
                                    @if ($head->photo)
                                        <img src="{{ asset('storage/' . $head->photo) }}" alt="">
                                    @else
                                        {{ $initial }}
                                    @endif
                                </div>
                                <div>
                                    <h4 class="mb-1">{{ $head->name }}</h4>
                                    <p class="dh-subtitle mb-2">{{ $head->email }}</p>
                                    <div class="d-flex flex-wrap gap-1">
                                        @foreach ($head->roles as $role)
                                            <span class="badge bg-white bg-opacity-25 text-white">{{ $role->name }}</span>
                                        @endforeach
                                        @if ($capabilities['has_department_head_role'])
                                            <span class="badge bg-success bg-opacity-75">رئيس قسم نشط</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex align-items-center gap-4">
                            <div class="position-relative dh-progress-ring-wrap d-none d-md-block">
                                <svg class="dh-progress-ring" viewBox="0 0 88 88">
                                    <circle class="ring-bg" cx="44" cy="44" r="36"></circle>
                                    <circle class="ring-fill" cx="44" cy="44" r="36"
                                        stroke-dasharray="{{ $circumference }}"
                                        stroke-dashoffset="{{ $strokeOffset }}"></circle>
                                </svg>
                                <div class="dh-progress-label">
                                    {{ $templatePct }}%
                                    <small>القالب</small>
                                </div>
                            </div>
                            <div class="dh-hero-actions d-flex flex-wrap gap-2">
                                <a href="{{ route('admin.department-heads.index') }}" class="btn btn-outline-light btn-sm">القائمة</a>
                                <a href="{{ route('admin.department-heads.show', $head->id) }}" class="btn btn-light btn-sm">ملف رئيس القسم</a>
                                @can('department-head-manage')
                                <form action="{{ route('admin.department-heads.apply-role-template', $head->id) }}" method="POST" class="d-inline"
                                      data-confirm="تطبيق قالب صلاحيات رئيس القسم على هذا المستخدم؟"
                                      data-confirm-title="مزامنة القالب"
                                      data-confirm-type="warning"
                                      data-confirm-btn="مزامنة">
                                    @csrf
                                    <button type="submit" class="btn btn-warning btn-sm">
                                        <i class="ri-refresh-line me-1"></i>مزامنة القالب
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if (!$capabilities['has_department_head_role'])
                <div class="alert dh-alert-banner mb-4 d-flex align-items-start gap-2">
                    <i class="ri-alert-line fs-20 text-warning mt-1"></i>
                    <div>
                        <strong class="d-block mb-1">تنبيه: الدور غير مكتمل</strong>
                        <span class="small text-muted">مدير أقسام بدون دور <code>department_head</code> — قد لا تظهر مسارات «إدارة الفريق» حتى تُطبَّق الصلاحيات.</span>
                    </div>
                </div>
            @endif

            {{-- Stats --}}
            <div class="row g-3 mb-4">
                <div class="col-sm-6 col-xl-3">
                    <div class="dh-stat-card">
                        <div class="dh-stat-icon indigo"><i class="ri-building-4-line"></i></div>
                        <div>
                            <div class="dh-stat-value">{{ $capabilities['direct_managed_departments']->count() }}</div>
                            <div class="dh-stat-label">أقسام مُدارة مباشرة</div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="dh-stat-card">
                        <div class="dh-stat-icon sky"><i class="ri-node-tree"></i></div>
                        <div>
                            <div class="dh-stat-value">{{ count($capabilities['managed_department_ids']) }}</div>
                            <div class="dh-stat-label">نطاق كامل (مع الفرعية)</div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="dh-stat-card">
                        <div class="dh-stat-icon emerald"><i class="ri-team-line"></i></div>
                        <div>
                            <div class="dh-stat-value">{{ $capabilities['managed_team_count'] }}</div>
                            <div class="dh-stat-label">حجم الفريق</div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="dh-stat-card">
                        <div class="dh-stat-icon amber"><i class="ri-shield-check-line"></i></div>
                        <div>
                            <div class="dh-stat-value">
                                {{ $capabilities['template_granted_count'] }}<span class="fs-16 text-muted fw-normal">/{{ $capabilities['template_permissions']->count() }}</span>
                            </div>
                            <div class="dh-stat-label">صلاحيات القالب</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Scope & limits --}}
            <div class="row g-3 mb-4">
                <div class="col-lg-6">
                    <div class="dh-panel">
                        <div class="dh-panel-header scope">
                            <i class="ri-focus-3-line"></i>
                            {{ $capabilities['scope']['title'] ?? 'نطاق البيانات' }}
                        </div>
                        <div class="dh-panel-body">
                            <p class="text-muted mb-3">{{ $capabilities['scope']['description'] ?? '' }}</p>
                            @if ($capabilities['direct_managed_departments']->isNotEmpty())
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach ($capabilities['direct_managed_departments'] as $dept)
                                        <span class="dh-dept-chip">
                                            <i class="ri-building-line"></i>{{ $dept->name }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif
                            @if ($capabilities['managed_departments']->count() > $capabilities['direct_managed_departments']->count())
                                <p class="text-muted small mt-3 mb-0">
                                    <i class="ri-information-line me-1"></i>يشمل الأقسام الفرعية التابعة.
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="dh-panel">
                        <div class="dh-panel-header limits">
                            <i class="ri-shield-cross-line"></i>قيود عامة
                        </div>
                        <div class="dh-panel-body">
                            @foreach ($capabilities['limitations'] as $limitation)
                                <div class="dh-limit-item">
                                    <i class="ri-close-circle-line"></i>
                                    <span>{{ $limitation }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- Portal --}}
            <div class="mb-2 dh-section-title">
                <i class="ri-apps-2-line text-primary"></i>بوابات ومسارات متاحة
            </div>
            <div class="row g-3 mb-4">
                @foreach ($capabilities['portal'] as $feature)
                    <div class="col-md-6 col-lg-4">
                        <div class="dh-portal-card {{ $feature['available'] ? 'is-active' : 'is-inactive' }}">
                            <div class="dh-portal-icon">
                                <i class="{{ $feature['available'] ? 'ri-checkbox-circle-fill' : 'ri-indeterminate-circle-line' }}"></i>
                            </div>
                            <div class="fw-semibold mb-1">{{ $feature['label'] }}</div>
                            <p class="text-muted small mb-2">{{ $feature['description'] }}</p>
                            @if ($feature['available'] && $feature['url'] && (auth()->id() === $head->id || auth()->user()->hasRole('admin')))
                                <a href="{{ $feature['url'] }}" class="btn btn-sm btn-primary" @if(auth()->id() !== $head->id) target="_blank" @endif>
                                    <i class="ri-external-link-line me-1"></i>فتح
                                </a>
                            @elseif ($feature['available'] && $feature['url'])
                                <span class="badge bg-success-transparent text-success">متاح عند تسجيل دخوله</span>
                            @else
                                <span class="badge bg-secondary-transparent">غير متاح</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Capability groups --}}
            <div class="mb-2 dh-section-title">
                <i class="ri-list-check-2 text-primary"></i>ما يمكنه فعله — حسب الصلاحيات
            </div>
            <div class="row g-3 mb-4">
                @foreach ($capabilities['groups'] as $group)
                    @php
                        $groupPct = $group['total_count'] > 0
                            ? (int) round(($group['granted_count'] / $group['total_count']) * 100)
                            : 0;
                    @endphp
                    <div class="col-lg-6">
                        <div class="dh-capability-card {{ $group['fully_granted'] ? 'is-complete' : '' }}">
                            <div class="dh-capability-head">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="dh-group-icon"><i class="{{ $group['icon'] }}"></i></div>
                                    <div>
                                        <div class="fw-semibold">{{ $group['title'] }}</div>
                                        <div class="text-muted small">{{ $group['summary'] }}</div>
                                        <div class="dh-capability-progress">
                                            <div class="bar {{ $group['fully_granted'] ? 'is-full' : '' }}" style="width: {{ $groupPct }}%"></div>
                                        </div>
                                    </div>
                                </div>
                                <span class="badge {{ $group['fully_granted'] ? 'bg-success' : 'bg-warning-transparent text-warning' }} rounded-pill">
                                    {{ $group['granted_count'] }}/{{ $group['total_count'] }}
                                </span>
                            </div>
                            <div class="p-3">
                                @foreach ($group['actions'] as $action)
                                    <div class="dh-action-row">
                                        <span class="dh-action-status {{ $action['granted'] ? 'granted' : 'denied' }}">
                                            <i class="{{ $action['granted'] ? 'ri-check-line' : 'ri-close-line' }}"></i>
                                        </span>
                                        <div class="flex-grow-1">
                                            <div class="{{ $action['granted'] ? 'fw-medium' : 'text-muted' }}">{{ $action['label'] }}</div>
                                            @if ($action['permission'])
                                                <small class="text-muted">{{ $action['permission_label'] }}</small>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            @if ($capabilities['template_missing']->isNotEmpty())
                <div class="dh-panel mb-4 border-warning">
                    <div class="dh-panel-header" style="background: rgba(245, 158, 11, 0.1); color: #d97706;">
                        <i class="ri-error-warning-line"></i>صلاحيات ناقصة عن قالب «رئيس قسم»
                    </div>
                    <div class="dh-panel-body">
                        <p class="text-muted small mb-3">مُعرَّفة في القالب وغير ممنوحة حالياً:</p>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach ($capabilities['template_missing'] as $perm)
                                <span class="badge bg-warning-transparent text-warning rounded-pill px-3 py-2">
                                    {{ __('permissions.' . $perm, [], 'ar') !== 'permissions.' . $perm ? __('permissions.' . $perm, [], 'ar') : $perm }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            @if (!empty($capabilities['extra_permissions']))
                <div class="dh-panel mb-4">
                    <div class="dh-panel-header">
                        <i class="ri-add-circle-line text-info"></i>صلاحيات إضافية
                    </div>
                    <div class="dh-panel-body">
                        @foreach ($capabilities['extra_permissions'] as $category => $perms)
                            <p class="fw-semibold small text-primary mb-2">{{ $category }}</p>
                            <div class="d-flex flex-wrap gap-1 mb-3">
                                @foreach ($perms as $perm)
                                    <span class="badge bg-info-transparent text-info rounded-pill">{{ $perm['label'] }}</span>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Technical summary (collapsible) --}}
            <div class="dh-panel">
                <div class="dh-panel-header dh-collapse-toggle d-flex justify-content-between align-items-center"
                     data-bs-toggle="collapse" data-bs-target="#dhPermCollapse" aria-expanded="false">
                    <span>
                        <i class="ri-key-2-line text-primary me-1"></i>
                        ملخص الصلاحيات الفنية ({{ $capabilities['granted_names']->count() }})
                    </span>
                    <span class="small text-muted">
                        من الدور: {{ $capabilities['role_permission_names']->count() }}
                        @if ($capabilities['direct_permission_names']->isNotEmpty())
                            · مباشرة: {{ $capabilities['direct_permission_names']->count() }}
                        @endif
                        <i class="ri-arrow-down-s-line ms-1"></i>
                    </span>
                </div>
                <div class="collapse" id="dhPermCollapse">
                    <div class="dh-panel-body dh-perm-cloud">
                        @foreach ($capabilities['granted_names'] as $perm)
                            <span class="perm-tag" title="{{ $perm }}">
                                {{ __('permissions.' . $perm, [], 'ar') !== 'permissions.' . $perm ? __('permissions.' . $perm, [], 'ar') : $perm }}
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>

        </div>
    </div>
@stop

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/department-head-capabilities.css') }}">
@endpush
