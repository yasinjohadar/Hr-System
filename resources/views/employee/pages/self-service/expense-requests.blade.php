@extends('employee.layouts.master')

@section('page-title')
    طلبات المصروفات
@stop

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/employee-expenses.css') }}">
@endpush

@section('content')
    <div class="main-content app-content employee-expenses-page" data-expense-list>
        <div class="container-fluid pt-4">

            <div class="card page-hero mb-4">
                <div class="card-body py-4">
                    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="page-hero-icon">
                                <i class="ri-money-dollar-circle-line"></i>
                            </div>
                            <div>
                                <h4 class="mb-1 page-hero-title fw-bold">طلبات المصروفات</h4>
                                <p class="mb-0 page-hero-subtitle">تتبّع طلباتك وحالات الموافقة والدفع</p>
                            </div>
                        </div>
                        <a href="{{ route('employee.expense-requests.create') }}" class="btn btn-primary btn-hero-primary">
                            <i class="ri-add-line me-1"></i>طلب مصروفات جديد
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
                                <div class="stat-label">إجمالي الطلبات</div>
                            </div>
                            <div class="stat-icon stat-icon--primary"><i class="ri-file-list-3-line"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-value">{{ $stats['pending'] }}</div>
                                <div class="stat-label">قيد الانتظار</div>
                            </div>
                            <div class="stat-icon stat-icon--warning"><i class="ri-time-line"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-value">{{ $stats['approved'] }}</div>
                                <div class="stat-label">موافق / مدفوع</div>
                            </div>
                            <div class="stat-icon stat-icon--success"><i class="ri-checkbox-circle-line"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-value">{{ $stats['rejected'] }}</div>
                                <div class="stat-label">مرفوض</div>
                            </div>
                            <div class="stat-icon stat-icon--danger"><i class="ri-close-circle-line"></i></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="content-panel">
                <div class="content-panel-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                    <div>
                        <h5 class="fw-bold mb-1 text-dark">قائمة الطلبات</h5>
                        <p class="text-muted fs-13 mb-0">{{ $expenseRequests->total() }} طلب مسجّل</p>
                    </div>
                    <div class="filter-pills" role="group">
                        <button type="button" class="filter-pill active" data-expense-filter="all">الكل</button>
                        <button type="button" class="filter-pill" data-expense-filter="pending">قيد الانتظار</button>
                        <button type="button" class="filter-pill" data-expense-filter="approved">موافق</button>
                        <button type="button" class="filter-pill" data-expense-filter="rejected">مرفوض</button>
                    </div>
                </div>

                <div class="content-panel-body p-0">
                    @forelse ($expenseRequests as $request)
                        @php
                            $statusClass = match ($request->status) {
                                'approved' => 'approved',
                                'paid' => 'paid',
                                'rejected' => 'rejected',
                                'cancelled' => 'cancelled',
                                default => 'pending',
                            };
                        @endphp
                        <article class="expense-request-card" data-status="{{ $request->status }}">
                            <div class="expense-type-icon">
                                <i class="ri-receipt-line"></i>
                            </div>
                            <div class="flex-grow-1 min-w-0">
                                <h6 class="mb-1 fw-semibold text-dark">
                                    {{ $request->category->name_ar ?? $request->category->name }}
                                </h6>
                                <div class="text-muted fs-12 mb-1">
                                    <i class="ri-calendar-line me-1"></i>{{ $request->expense_date->format('d/m/Y') }}
                                    @if ($request->request_code)
                                        <span class="mx-2">·</span>{{ $request->request_code }}
                                    @endif
                                </div>
                                <p class="text-muted fs-12 mb-0 text-truncate">{{ $request->description }}</p>
                            </div>
                            <div class="expense-amount">
                                {{ number_format($request->amount, 2) }}
                                {{ $request->currency->symbol ?? $request->currency->code ?? '' }}
                            </div>
                            <span class="status-pill status-pill--{{ $statusClass }}">{{ $request->status_name_ar }}</span>
                            <button type="button" class="btn-view-detail" data-bs-toggle="modal"
                                data-bs-target="#expenseModal{{ $request->id }}" title="التفاصيل">
                                <i class="ri-eye-line"></i>
                            </button>
                        </article>

                        <div class="modal fade detail-modal" id="expenseModal{{ $request->id }}" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title fw-bold">تفاصيل طلب المصروف</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="detail-row">
                                            <strong>التاريخ</strong>
                                            <span>{{ $request->expense_date->format('Y-m-d') }}</span>
                                        </div>
                                        <div class="detail-row">
                                            <strong>التصنيف</strong>
                                            <span>{{ $request->category->name_ar ?? $request->category->name }}</span>
                                        </div>
                                        <div class="detail-row">
                                            <strong>المبلغ</strong>
                                            <span class="fw-semibold">
                                                {{ number_format($request->amount, 2) }}
                                                {{ $request->currency->symbol ?? $request->currency->code ?? '' }}
                                            </span>
                                        </div>
                                        <div class="detail-row">
                                            <strong>الحالة</strong>
                                            <span class="status-pill status-pill--{{ $statusClass }}">{{ $request->status_name_ar }}</span>
                                        </div>
                                        @if ($request->payment_method_name_ar)
                                            <div class="detail-row">
                                                <strong>طريقة الدفع</strong>
                                                <span>{{ $request->payment_method_name_ar }}</span>
                                            </div>
                                        @endif
                                        @if ($request->vendor_name)
                                            <div class="detail-row">
                                                <strong>المورد</strong>
                                                <span>{{ $request->vendor_name }}</span>
                                            </div>
                                        @endif
                                        <div class="detail-row flex-column align-items-start">
                                            <strong>الوصف</strong>
                                            <span class="mt-1">{{ $request->description }}</span>
                                        </div>
                                        @if ($request->rejection_reason)
                                            <div class="detail-row">
                                                <strong>سبب الرفض</strong>
                                                <span class="text-danger">{{ $request->rejection_reason }}</span>
                                            </div>
                                        @endif
                                        @if ($request->receipt_path)
                                            <div class="detail-row">
                                                <strong>الإيصال</strong>
                                                <a href="{{ asset('storage/' . $request->receipt_path) }}" target="_blank"
                                                    class="btn btn-sm btn-outline-primary">
                                                    <i class="ri-download-line me-1"></i>تحميل
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="empty-state">
                            <div class="empty-icon"><i class="ri-wallet-line"></i></div>
                            <h5 class="fw-semibold text-dark mb-2">لا توجد طلبات مصروفات</h5>
                            <p class="text-muted mb-3">قدّم أول طلب مصروفات وسيظهر هنا</p>
                            <a href="{{ route('employee.expense-requests.create') }}" class="btn btn-primary">
                                <i class="ri-add-line me-1"></i>طلب مصروفات جديد
                            </a>
                        </div>
                    @endforelse
                </div>

                @if ($expenseRequests->hasPages())
                    <div class="p-3 border-top">
                        {{ $expenseRequests->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@stop

@push('scripts')
    <script src="{{ asset('assets/js/employee-expenses.js') }}"></script>
@endpush
