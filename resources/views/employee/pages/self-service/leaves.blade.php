@extends('employee.layouts.master')

@section('page-title')
    الإجازات
@stop

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/employee-leaves.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/workflow-approval-timeline.css') }}">
@endpush

@section('content')
    <div class="main-content app-content employee-leaves-page">
        <div class="container-fluid pt-4">

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
                    <i class="ri-checkbox-circle-line me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
                    <i class="ri-error-warning-line me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- Hero --}}
            <div class="card leaves-hero mb-4">
                <div class="card-body py-4">
                    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="leaves-hero-icon">
                                <i class="ri-calendar-check-line"></i>
                            </div>
                            <div>
                                <h4 class="mb-1 leaves-hero-title fw-bold">إجازاتي</h4>
                                <p class="mb-0 leaves-hero-subtitle">تتبّع طلباتك وأرصدتك لعام {{ $currentYear }}</p>
                            </div>
                        </div>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#requestLeaveModal">
                            <i class="ri-add-circle-line me-1"></i>طلب إجازة جديدة
                        </button>
                    </div>
                </div>
            </div>

            {{-- إحصائيات --}}
            <div class="row g-3 mb-4">
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="d-flex align-items-start justify-content-between">
                            <div>
                                <div class="stat-value">{{ $stats['total'] }}</div>
                                <div class="stat-label">إجمالي الطلبات</div>
                            </div>
                            <div class="stat-icon stat-icon--total"><i class="ri-file-list-3-line"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="d-flex align-items-start justify-content-between">
                            <div>
                                <div class="stat-value">{{ $stats['pending'] }}</div>
                                <div class="stat-label">قيد الانتظار</div>
                            </div>
                            <div class="stat-icon stat-icon--pending"><i class="ri-time-line"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="d-flex align-items-start justify-content-between">
                            <div>
                                <div class="stat-value">{{ $stats['approved'] }}</div>
                                <div class="stat-label">موافق عليها</div>
                            </div>
                            <div class="stat-icon stat-icon--approved"><i class="ri-checkbox-circle-line"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="d-flex align-items-start justify-content-between">
                            <div>
                                <div class="stat-value">{{ $stats['days_approved_year'] }}</div>
                                <div class="stat-label">أيام معتمدة ({{ $currentYear }})</div>
                            </div>
                            <div class="stat-icon stat-icon--days"><i class="ri-calendar-2-line"></i></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- أرصدة الإجازات --}}
            @if ($leaveBalances->isNotEmpty())
                <div class="mb-4">
                    <h6 class="fw-semibold mb-3 text-dark">
                        <i class="ri-pie-chart-2-line text-primary me-1"></i>أرصدة الإجازات — {{ $currentYear }}
                    </h6>
                    <div class="row g-3">
                        @foreach ($leaveBalances as $balance)
                            @php
                                $total = max(1, (int) $balance->total_days + (int) $balance->carried_forward);
                                $used = (int) $balance->used_days;
                                $pct = min(100, round(($used / $total) * 100));
                            @endphp
                            <div class="col-md-6 col-lg-4">
                                <div class="balance-card">
                                    <div class="balance-name">
                                        {{ $balance->leaveType->name_ar ?? $balance->leaveType->name }}
                                    </div>
                                    <div class="d-flex align-items-end justify-content-between mb-2">
                                        <span class="balance-remaining">{{ $balance->remaining_days }}</span>
                                        <small class="text-muted">يوم متبقي</small>
                                    </div>
                                    <div class="balance-progress">
                                        <div class="balance-progress-bar" style="width: {{ $pct }}%"></div>
                                    </div>
                                    <div class="balance-meta">
                                        <span>مستخدم: {{ $used }}</span>
                                        <span>الإجمالي: {{ $total }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- قائمة الطلبات --}}
            <div class="requests-panel mb-4">
                <div class="requests-panel-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                    <div>
                        <h5 class="fw-bold mb-1 text-dark">سجل الطلبات</h5>
                        <p class="text-muted fs-13 mb-0">{{ $leaves->total() }} طلب مسجّل</p>
                    </div>
                    <div class="filter-pills" role="group" aria-label="تصفية الحالة">
                        <button type="button" class="filter-pill active" data-filter="all">الكل</button>
                        <button type="button" class="filter-pill" data-filter="pending">قيد الانتظار</button>
                        <button type="button" class="filter-pill" data-filter="approved">موافق عليه</button>
                        <button type="button" class="filter-pill" data-filter="rejected">مرفوض</button>
                    </div>
                </div>

                <div id="leave-requests-list">
                    @forelse ($leaves as $leave)
                        @php
                            $code = strtoupper($leave->leaveType->code ?? '');
                            $typeClass = match ($code) {
                                'ANNUAL' => ['annual', 'ri-sun-line'],
                                'SICK' => ['sick', 'ri-heart-pulse-line'],
                                'EMERGENCY' => ['emergency', 'ri-alarm-warning-line'],
                                'MATERNITY', 'PATERNITY' => ['paternity', 'ri-parent-line'],
                                default => ['default', 'ri-calendar-event-line'],
                            };
                            $progress = $workflowProgressById[$leave->id] ?? null;
                            $badgeAr = $progress['badge_ar'] ?? $leave->status_name_ar;
                            $badgeVariant = $progress['badge_variant'] ?? $leave->status;
                            $statusClass = match ($badgeVariant) {
                                'approved', 'success' => 'approved',
                                'rejected', 'danger' => 'rejected',
                                'cancelled' => 'cancelled',
                                default => 'pending',
                            };
                        @endphp
                        <div class="leave-request-card-wrap border-bottom">
                            <article class="leave-request-card" data-status="{{ $leave->status }}">
                                <div class="leave-type-badge leave-type-badge--{{ $typeClass[0] }}">
                                    <i class="{{ $typeClass[1] }}"></i>
                                </div>
                                <div class="flex-grow-1 min-w-0">
                                    <h6 class="mb-1 fw-semibold text-dark">
                                        {{ $leave->leaveType->name_ar ?? $leave->leaveType->name }}
                                    </h6>
                                    <div class="leave-dates">
                                        <i class="ri-arrow-left-right-line me-1"></i>
                                        <strong>{{ $leave->start_date->format('d/m/Y') }}</strong>
                                        <span class="mx-1">←</span>
                                        <strong>{{ $leave->end_date->format('d/m/Y') }}</strong>
                                    </div>
                                    @if ($leave->reason)
                                        <p class="text-muted fs-12 mb-0 mt-1 text-truncate">{{ $leave->reason }}</p>
                                    @endif
                                </div>
                                <span class="days-chip">{{ $leave->days_count }} {{ $leave->days_count === 1 ? 'يوم' : 'يوم' }}</span>
                                <span class="status-pill status-pill--{{ $statusClass }}">{{ $badgeAr }}</span>
                            </article>
                            @if ($progress)
                                <div class="px-3 pb-3">
                                    <x-workflow-approval-timeline :workflow-progress="$progress" compact />
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="empty-leaves">
                            <div class="empty-icon"><i class="ri-sun-cloudy-line"></i></div>
                            <h5 class="fw-semibold text-dark mb-2">لا توجد طلبات بعد</h5>
                            <p class="text-muted mb-3">ابدأ بتقديم أول طلب إجازة وسيظهر هنا</p>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#requestLeaveModal">
                                <i class="ri-add-line me-1"></i>طلب إجازة جديدة
                            </button>
                        </div>
                    @endforelse
                </div>

                @if ($leaves->hasPages())
                    <div class="p-3 border-top">
                        {{ $leaves->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Modal طلب إجازة --}}
    <div class="modal fade" id="requestLeaveModal" tabindex="-1" aria-labelledby="requestLeaveModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form method="POST" action="{{ route('employee.leaves.request') }}" id="leaveRequestForm">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold" id="requestLeaveModalLabel">
                            <i class="ri-calendar-todo-line me-2"></i>طلب إجازة جديدة
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-medium">نوع الإجازة <span class="text-danger">*</span></label>
                            <select name="leave_type_id" class="form-select" required id="leave_type_id">
                                <option value="">اختر نوع الإجازة</option>
                                @foreach ($leaveTypes as $type)
                                    <option value="{{ $type->id }}">{{ $type->name_ar ?? $type->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-medium">من تاريخ <span class="text-danger">*</span></label>
                                <input type="date" name="start_date" class="form-control" required id="leave_start_date">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-medium">إلى تاريخ <span class="text-danger">*</span></label>
                                <input type="date" name="end_date" class="form-control" required id="leave_end_date">
                            </div>
                        </div>
                        <div class="leave-duration-preview mt-3" id="leaveDurationPreview" aria-live="polite">
                            المدة: <strong id="leaveDurationDays">—</strong>
                        </div>
                        <div class="mb-0 mt-3">
                            <label class="form-label fw-medium">السبب <span class="text-muted fs-12">(اختياري)</span></label>
                            <textarea name="reason" class="form-control" rows="3" placeholder="اذكر سبب الإجازة إن رغبت…"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0 px-4 pb-4">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="ri-send-plane-line me-1"></i>إرسال الطلب
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@push('scripts')
    <script src="{{ asset('assets/js/employee-leaves.js') }}"></script>
@endpush
