@extends('employee.layouts.master')

@section('page-title')
    الاجتماعات
@stop

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/employee-meetings.css') }}">
@endpush

@section('content')
    <div class="main-content app-content employee-meetings-page">
        <div class="container-fluid pt-4">

            <div class="card page-hero mb-4">
                <div class="card-body py-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="page-hero-icon">
                            <i class="ri-calendar-event-line"></i>
                        </div>
                        <div>
                            <h4 class="mb-1 page-hero-title fw-bold">الاجتماعات</h4>
                            <p class="mb-0 page-hero-subtitle">اجتماعاتك كمنظّم أو كمشارك</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-value">{{ $stats['total'] }}</div>
                                <div class="stat-label">إجمالي الاجتماعات</div>
                            </div>
                            <div class="stat-icon stat-icon--primary"><i class="ri-calendar-line"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-value">{{ $stats['upcoming'] }}</div>
                                <div class="stat-label">قادمة</div>
                            </div>
                            <div class="stat-icon stat-icon--warning"><i class="ri-time-line"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-value">{{ $stats['today'] }}</div>
                                <div class="stat-label">اليوم</div>
                            </div>
                            <div class="stat-icon stat-icon--primary"><i class="ri-sun-line"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-value">{{ $stats['completed'] }}</div>
                                <div class="stat-label">مكتملة</div>
                            </div>
                            <div class="stat-icon stat-icon--success"><i class="ri-checkbox-circle-line"></i></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="content-panel">
                <div class="content-panel-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                    <div>
                        <h5 class="fw-bold mb-1 text-dark">قائمة الاجتماعات</h5>
                        <p class="text-muted fs-13 mb-0">{{ $meetings->total() }} اجتماع</p>
                    </div>
                    @if ($meetings->isNotEmpty())
                        <div class="filter-pills" role="group">
                            <button type="button" class="filter-pill active" data-meeting-filter="all">الكل</button>
                            <button type="button" class="filter-pill" data-meeting-filter="upcoming">قادمة</button>
                            <button type="button" class="filter-pill" data-meeting-filter="today">اليوم</button>
                            <button type="button" class="filter-pill" data-meeting-filter="completed">مكتملة</button>
                        </div>
                    @endif
                </div>

                <div class="p-0">
                    @forelse ($meetings as $meeting)
                        @php
                            $title = $meeting->title_ar ?? $meeting->title;
                            $organizerName = $meeting->organizer
                                ? ($meeting->organizer->full_name ?? trim($meeting->organizer->first_name . ' ' . $meeting->organizer->last_name))
                                : '—';
                            $isOrganizer = $meeting->organizer_id === $employee->id;
                            $isToday = $meeting->start_time && $meeting->start_time->isToday();
                            $isUpcoming = $meeting->start_time
                                && $meeting->start_time->isFuture()
                                && in_array($meeting->status, ['scheduled', 'in_progress']);
                            $filterState = $isUpcoming ? 'upcoming' : ($isToday ? 'today' : $meeting->status);
                            if ($meeting->status === 'completed') {
                                $filterState = 'completed';
                            }
                            $attendeesCount = $meeting->attendees->count();
                        @endphp
                        <article class="meeting-card meeting-card-item {{ $isToday ? 'is-today' : '' }}"
                            data-filter-state="{{ $filterState }}">
                            @if ($meeting->start_time)
                                <div class="meeting-date-box">
                                    <div class="day">{{ $meeting->start_time->format('d') }}</div>
                                    <div class="month">{{ $meeting->start_time->translatedFormat('M') }}</div>
                                </div>
                            @endif
                            <div class="flex-grow-1 min-w-0">
                                <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                                    <div class="meeting-title text-truncate">{{ $title }}</div>
                                    @if ($isOrganizer)
                                        <span class="role-badge">منظّم</span>
                                    @endif
                                </div>
                                <div class="meeting-meta">
                                    <i class="ri-user-line me-1"></i>{{ $organizerName }}
                                    @if ($attendeesCount > 0)
                                        <span class="mx-2">·</span>
                                        <i class="ri-group-line me-1"></i>{{ $attendeesCount }} مشارك
                                    @endif
                                </div>
                            </div>
                            <div class="meeting-time text-end">
                                @if ($meeting->start_time)
                                    {{ $meeting->start_time->format('H:i') }}
                                    @if ($meeting->end_time)
                                        – {{ $meeting->end_time->format('H:i') }}
                                    @endif
                                @else
                                    —
                                @endif
                            </div>
                            <span class="type-pill">{{ $meeting->type_name_ar }}</span>
                            @if ($meeting->location)
                                <span class="meeting-meta text-truncate" style="max-width: 120px" title="{{ $meeting->location }}">
                                    <i class="ri-map-pin-line me-1"></i>{{ $meeting->location }}
                                </span>
                            @endif
                            @if ($meeting->meeting_link)
                                <a href="{{ $meeting->meeting_link }}" target="_blank" rel="noopener" class="meeting-link-btn">
                                    <i class="ri-video-line me-1"></i>انضم
                                </a>
                            @endif
                            <span class="status-pill status-pill--{{ $meeting->status }}">{{ $meeting->status_name_ar }}</span>
                        </article>
                    @empty
                        <div class="empty-state">
                            <div class="empty-icon"><i class="ri-calendar-event-line"></i></div>
                            <h5 class="fw-semibold text-dark mb-2">لا توجد اجتماعات</h5>
                            <p class="text-muted mb-0">ستظهر اجتماعاتك هنا عند جدولتها أو دعوتك إليها</p>
                        </div>
                    @endforelse
                </div>

                @if ($meetings->hasPages())
                    <div class="p-3 border-top">
                        {{ $meetings->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@stop

@push('scripts')
    <script src="{{ asset('assets/js/employee-meetings.js') }}"></script>
@endpush
