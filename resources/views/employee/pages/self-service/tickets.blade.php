@extends('employee.layouts.master')

@section('page-title')
    التذاكر
@stop

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/employee-tickets.css') }}">
@endpush

@section('content')
    <div class="main-content app-content employee-tickets-page">
        <div class="container-fluid pt-4">

            <div class="card page-hero mb-4">
                <div class="card-body py-4">
                    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="page-hero-icon">
                                <i class="ri-customer-service-2-line"></i>
                            </div>
                            <div>
                                <h4 class="mb-1 page-hero-title fw-bold">التذاكر</h4>
                                <p class="mb-0 page-hero-subtitle">طلبات الدعم والمتابعة مع الفريق المختص</p>
                            </div>
                        </div>
                        <a href="{{ route('employee.tickets.create') }}" class="btn btn-primary btn-hero-primary">
                            <i class="ri-add-line me-1"></i>تذكرة جديدة
                        </a>
                    </div>
                </div>
            </div>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="row g-3 mb-4">
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-value">{{ $stats['total'] }}</div>
                                <div class="stat-label">إجمالي التذاكر</div>
                            </div>
                            <div class="stat-icon stat-icon--primary"><i class="ri-ticket-line"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-value">{{ $stats['open'] }}</div>
                                <div class="stat-label">مفتوحة / قيد المعالجة</div>
                            </div>
                            <div class="stat-icon stat-icon--warning"><i class="ri-loader-4-line"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-value">{{ $stats['resolved'] }}</div>
                                <div class="stat-label">محلولة</div>
                            </div>
                            <div class="stat-icon stat-icon--success"><i class="ri-checkbox-circle-line"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-value">{{ $stats['closed'] }}</div>
                                <div class="stat-label">مغلقة / ملغاة</div>
                            </div>
                            <div class="stat-icon stat-icon--muted"><i class="ri-archive-line"></i></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="content-panel">
                <div class="content-panel-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                    <div>
                        <h5 class="fw-bold mb-1 text-dark">قائمة التذاكر</h5>
                        <p class="text-muted fs-13 mb-0">{{ $tickets->total() }} تذكرة</p>
                    </div>
                    @if ($tickets->isNotEmpty())
                        <div class="filter-pills" role="group">
                            <button type="button" class="filter-pill active" data-ticket-filter="all">الكل</button>
                            <button type="button" class="filter-pill" data-ticket-filter="open">مفتوحة</button>
                            <button type="button" class="filter-pill" data-ticket-filter="resolved">محلولة</button>
                            <button type="button" class="filter-pill" data-ticket-filter="closed">مغلقة</button>
                        </div>
                    @endif
                </div>

                <div class="p-0">
                    @forelse ($tickets as $ticket)
                        @php
                            $assigneeName = $ticket->assignedTo
                                ? ($ticket->assignedTo->full_name ?? trim($ticket->assignedTo->first_name . ' ' . $ticket->assignedTo->last_name))
                                : null;
                            $isAssignedToMe = $ticket->assigned_to === $employee->id && $ticket->employee_id !== $employee->id;
                        @endphp
                        <article class="ticket-card ticket-card-item" data-status="{{ $ticket->status }}">
                            <div class="ticket-icon">
                                <i class="ri-ticket-2-line"></i>
                            </div>
                            <div class="flex-grow-1 min-w-0">
                                <div class="ticket-code mb-1">{{ $ticket->ticket_code }}</div>
                                <div class="ticket-title text-truncate">{{ $ticket->title }}</div>
                                @if ($isAssignedToMe)
                                    <span class="role-badge mt-1 d-inline-block">مكلّف إليّ</span>
                                @endif
                            </div>
                            <span class="category-pill">{{ $ticket->category_name_ar }}</span>
                            <span class="priority-pill priority-pill--{{ $ticket->priority }}">{{ $ticket->priority_name_ar }}</span>
                            <div class="assignee-text">
                                <i class="ri-user-line me-1"></i>{{ $assigneeName ?: 'غير مكلّف' }}
                            </div>
                            <span class="status-pill status-pill--{{ $ticket->status }}">{{ $ticket->status_name_ar }}</span>
                        </article>
                    @empty
                        <div class="empty-state">
                            <div class="empty-icon"><i class="ri-customer-service-2-line"></i></div>
                            <h5 class="fw-semibold text-dark mb-2">لا توجد تذاكر</h5>
                            <p class="text-muted mb-3">افتح تذكرة جديدة للحصول على الدعم</p>
                            <a href="{{ route('employee.tickets.create') }}" class="btn btn-primary">
                                <i class="ri-add-line me-1"></i>تذكرة جديدة
                            </a>
                        </div>
                    @endforelse
                </div>

                @if ($tickets->hasPages())
                    <div class="p-3 border-top">
                        {{ $tickets->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@stop

@push('scripts')
    <script src="{{ asset('assets/js/employee-tickets.js') }}"></script>
@endpush
