@extends('admin.layouts.master')

@section('page-title')
    هيكل القسم
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid admin-page-shell">
            @include('admin.pages.users.partials.alerts')

            <div class="admin-page-banner">
                <div class="admin-page-banner-main">
                    <span class="admin-page-banner-icon"><i class="ri-node-tree"></i></span>
                    <div class="admin-page-banner-text">
                        <h1>هيكل القسم</h1>
                        <p>الهيكل التنظيمي للأقسام المُدارة وأقسامها الفرعية وموظفيها</p>
                    </div>
                </div>
                <div class="admin-page-banner-actions">
                    <a href="{{ route('admin.team.dashboard') }}" class="admin-btn admin-btn-light">
                        <i class="ri-arrow-right-line"></i>
                        العودة للوحة التحكم
                    </a>
                </div>
            </div>

            @if (! empty($tree))
                <div class="admin-report-stats admin-report-stats-4 mb-4">
                    <div class="admin-report-stat admin-report-stat-static admin-report-stat-blue">
                        <span class="admin-report-stat-icon"><i class="ri-building-line"></i></span>
                        <span class="admin-report-stat-label">أقسام مُدارة</span>
                        <span class="admin-report-stat-value" style="color:#2563eb;">{{ $stats['departments'] }}</span>
                    </div>
                    <div class="admin-report-stat admin-report-stat-static admin-report-stat-cyan">
                        <span class="admin-report-stat-icon"><i class="ri-git-branch-line"></i></span>
                        <span class="admin-report-stat-label">أقسام فرعية</span>
                        <span class="admin-report-stat-value" style="color:#0891b2;">{{ $stats['sub_departments'] }}</span>
                    </div>
                    <div class="admin-report-stat admin-report-stat-static admin-report-stat-green">
                        <span class="admin-report-stat-icon"><i class="ri-team-line"></i></span>
                        <span class="admin-report-stat-label">إجمالي الموظفين</span>
                        <span class="admin-report-stat-value" style="color:#059669;">{{ $stats['employees'] }}</span>
                    </div>
                </div>
            @endif

            <div class="row g-3">
                @forelse ($tree as $node)
                    <div class="col-xl-4 col-lg-6">
                        @include('admin.pages.team.partials.department-node', ['node' => $node, 'level' => 0])
                    </div>
                @empty
                    <div class="col-12">
                        <div class="admin-page-card">
                            <div class="admin-empty-state">
                                <i class="ri-building-line"></i>
                                لا توجد أقسام مُدارة
                            </div>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@stop

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin-team-structure.css') }}">
@endpush
