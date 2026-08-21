@extends('admin.layouts.master')

@section('page-title')
    إعلانات الشركة
@stop

@section('content')
    {{--
        نفس بنية بقية صفحات الإدارة (انظر admin/pages/department-heads/index.blade.php):
        admin-page-shell ← admin-page-banner ← admin-report-stats ← admin-page-card
        ← card-toolbar/admin-filters ← admin-table-wrap/admin-data-table ← admin-table-footer
        كل التنسيق من assets/css/admin-pages.css فلا CSS خاص بهذه الصفحة.
    --}}
    <div class="main-content app-content">
        <div class="container-fluid admin-page-shell">
            @include('admin.pages.users.partials.alerts')

            <div class="admin-page-banner">
                <div class="admin-page-banner-main">
                    <span class="admin-page-banner-icon"><i class="ri-megaphone-line"></i></span>
                    <div class="admin-page-banner-text">
                        <h1>إعلانات الشركة</h1>
                        <p>نشر الإعلانات وإدارة استهدافها ومواعيد نشرها وانتهائها</p>
                    </div>
                </div>
                <div class="admin-page-banner-actions">
                    <a href="{{ route('admin.announcements.create') }}" class="admin-btn admin-btn-light">
                        <i class="ri-add-line"></i>
                        إضافة إعلان جديد
                    </a>
                </div>
            </div>

            <div class="admin-report-stats admin-report-stats-4 mb-4">
                <div class="admin-report-stat admin-report-stat-static admin-report-stat-blue">
                    <span class="admin-report-stat-icon"><i class="ri-megaphone-line"></i></span>
                    <span class="admin-report-stat-label">إجمالي الإعلانات</span>
                    <span class="admin-report-stat-value" style="color:#2563eb;">{{ $stats['total'] }}</span>
                </div>
                <div class="admin-report-stat admin-report-stat-static admin-report-stat-green">
                    <span class="admin-report-stat-icon"><i class="ri-checkbox-circle-line"></i></span>
                    <span class="admin-report-stat-label">منشورة</span>
                    <span class="admin-report-stat-value" style="color:#059669;">{{ $stats['published'] }}</span>
                </div>
                <div class="admin-report-stat admin-report-stat-static admin-report-stat-amber">
                    <span class="admin-report-stat-icon"><i class="ri-draft-line"></i></span>
                    <span class="admin-report-stat-label">مسودات</span>
                    <span class="admin-report-stat-value" style="color:#d97706;">{{ $stats['draft'] }}</span>
                </div>
                <div class="admin-report-stat admin-report-stat-static admin-report-stat-cyan">
                    <span class="admin-report-stat-icon"><i class="ri-archive-line"></i></span>
                    <span class="admin-report-stat-label">مؤرشفة</span>
                    <span class="admin-report-stat-value" style="color:#0891b2;">{{ $stats['archived'] }}</span>
                </div>
            </div>

            <div class="admin-page-card">
                <div class="card-toolbar">
                    <form action="{{ route('admin.announcements.index') }}" method="GET" class="admin-filters w-100">
                        <div class="search-input-wrap">
                            <i class="ri-search-line"></i>
                            <input type="text" name="search" class="form-control"
                                   placeholder="بحث بالعنوان أو المحتوى"
                                   value="{{ request('search') }}" autocomplete="off">
                        </div>
                        <select name="status" class="form-select admin-filter-select">
                            <option value="">كل الحالات</option>
                            <option value="draft" @selected(request('status') === 'draft')>مسودة</option>
                            <option value="published" @selected(request('status') === 'published')>منشور</option>
                            <option value="archived" @selected(request('status') === 'archived')>مؤرشف</option>
                        </select>
                        <button type="submit" class="admin-btn admin-btn-primary">
                            <i class="ri-search-line"></i>
                            بحث
                        </button>
                        <a href="{{ route('admin.announcements.index') }}" class="admin-btn admin-btn-danger">
                            <i class="ri-filter-off-line"></i>
                            مسح
                        </a>
                    </form>
                </div>

                <div class="admin-table-wrap">
                    <div class="table-responsive">
                        <table class="admin-data-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>العنوان</th>
                                    <th>الحالة</th>
                                    <th>الاستهداف</th>
                                    <th>تاريخ النشر</th>
                                    <th>تاريخ الانتهاء</th>
                                    <th>العمليات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($announcements as $announcement)
                                    <tr>
                                        <td>{{ $loop->iteration + ($announcements->currentPage() - 1) * $announcements->perPage() }}</td>
                                        <td>
                                            <div class="fw-semibold">{{ $announcement->title }}</div>
                                            @if ($announcement->content)
                                                <small class="text-muted">{{ Str::limit(strip_tags($announcement->content), 60) }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($announcement->status === 'published')
                                                <span class="admin-badge admin-badge-success">{{ $announcement->status_label }}</span>
                                            @elseif ($announcement->status === 'draft')
                                                <span class="admin-badge admin-badge-muted">{{ $announcement->status_label }}</span>
                                            @else
                                                <span class="admin-badge admin-badge-warning">{{ $announcement->status_label }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="admin-badge admin-badge-role">{{ $announcement->target_type_label }}</span>
                                            @if ($announcement->department)
                                                <span class="admin-badge admin-badge-muted">{{ $announcement->department->name_ar ?? $announcement->department->name }}</span>
                                            @endif
                                            @if ($announcement->branch)
                                                <span class="admin-badge admin-badge-muted">{{ $announcement->branch->name_ar ?? $announcement->branch->name }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $announcement->publish_date?->format('Y-m-d') ?? '—' }}</td>
                                        <td>{{ $announcement->expiry_date?->format('Y-m-d') ?? '—' }}</td>
                                        <td>
                                            <div class="admin-row-actions dropdown">
                                                <button class="admin-kebab-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="ri-more-2-fill"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('admin.announcements.show', $announcement) }}">
                                                            <i class="ri-eye-line text-info me-2"></i>عرض الإعلان
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('admin.announcements.edit', $announcement) }}">
                                                            <i class="ri-pencil-line text-primary me-2"></i>تعديل
                                                        </a>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        {{-- المودال المركزي admin-confirm بدل مودال لكل صف --}}
                                                        <button type="button"
                                                                class="dropdown-item text-danger border-0 bg-transparent w-100 text-start"
                                                                data-delete-url="{{ route('admin.announcements.destroy', $announcement) }}"
                                                                data-delete-message="حذف الإعلان <strong>{{ $announcement->title }}</strong>؟">
                                                            <i class="ri-delete-bin-line me-2"></i>حذف
                                                        </button>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7">
                                            <div class="admin-empty-state">
                                                <i class="ri-megaphone-line"></i>
                                                لا توجد إعلانات.
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
                        @if ($announcements->total() > 0)
                            عرض {{ $announcements->firstItem() }} إلى {{ $announcements->lastItem() }} من {{ $announcements->total() }} نتيجة
                        @else
                            لا توجد نتائج
                        @endif
                    </div>
                    <div class="admin-pagination">
                        {{ $announcements->withQueryString()->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
