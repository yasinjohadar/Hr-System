@extends('employee.layouts.master')

@section('page-title')
    السياسات واللوائح
@stop

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/employee-policies.css') }}">
@endpush

@section('content')
    <div class="main-content app-content employee-policies-page">
        <div class="container-fluid pt-4">

            <div class="card page-hero mb-4">
                <div class="card-body py-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="page-hero-icon">
                            <i class="ri-file-shield-line"></i>
                        </div>
                        <div>
                            <h4 class="mb-1 page-hero-title fw-bold">السياسات واللوائح</h4>
                            <p class="mb-0 page-hero-subtitle">اطّلع على السياسات وسجّل اعترافك الرسمي</p>
                        </div>
                    </div>
                </div>
            </div>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if (session('info'))
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    {{ session('info') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="row g-3 mb-4">
                <div class="col-sm-4">
                    <div class="stat-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-value">{{ $stats['total'] }}</div>
                                <div class="stat-label">إجمالي السياسات</div>
                            </div>
                            <div class="stat-icon stat-icon--primary"><i class="ri-book-2-line"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="stat-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-value">{{ $stats['pending'] }}</div>
                                <div class="stat-label">بانتظار الاعتراف</div>
                            </div>
                            <div class="stat-icon stat-icon--warning"><i class="ri-error-warning-line"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="stat-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-value">{{ $stats['acknowledged'] }}</div>
                                <div class="stat-label">تم الاعتراف</div>
                            </div>
                            <div class="stat-icon stat-icon--success"><i class="ri-checkbox-circle-line"></i></div>
                        </div>
                    </div>
                </div>
            </div>

            @if ($stats['total'] > 0)
                <div class="filter-pills" role="group">
                    <button type="button" class="filter-pill active" data-policy-section="all">الكل</button>
                    <button type="button" class="filter-pill" data-policy-section="pending">بانتظار الاعتراف</button>
                    <button type="button" class="filter-pill" data-policy-section="acknowledged">تم الاعتراف</button>
                </div>
            @endif

            <div id="policies-pending-section" class="section-panel">
                <div class="section-panel-header">
                    <h5 class="fw-bold mb-1 text-dark">مطلوب الاعتراف</h5>
                    <p class="text-muted fs-13 mb-0">{{ $policiesPending->total() }} سياسة</p>
                </div>
                <div class="section-panel-body">
                    @forelse ($policiesPending as $policy)
                        <div class="policy-card policy-card--pending">
                            <div class="d-flex flex-wrap align-items-start justify-content-between gap-2 mb-2">
                                <div class="policy-title">{{ $policy->title }}</div>
                                @if ($policy->category)
                                    <span class="category-pill">{{ $policy->category }}</span>
                                @endif
                            </div>
                            <div class="policy-meta">
                                @if ($policy->effective_date)
                                    <i class="ri-calendar-line me-1"></i>تاريخ السريان: {{ $policy->effective_date->format('d/m/Y') }}
                                @endif
                                @if ($policy->version)
                                    <span class="mx-2">·</span>الإصدار: {{ $policy->version }}
                                @endif
                            </div>
                            @if ($policy->content)
                                <div class="policy-excerpt">
                                    {{ \Illuminate\Support\Str::limit(strip_tags($policy->content), 280) }}
                                </div>
                            @endif
                            <div class="policy-actions">
                                <form action="{{ route('employee.policies.acknowledge') }}" method="POST" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="policy_id" value="{{ $policy->id }}">
                                    <button type="submit" class="btn btn-primary btn-acknowledge">
                                        <i class="ri-check-line me-1"></i>أقر بأنني اطلعت
                                    </button>
                                </form>
                                @if ($policy->document_path)
                                    <a href="{{ asset('storage/' . $policy->document_path) }}" target="_blank"
                                        class="btn-download">
                                        <i class="ri-download-line me-1"></i>تحميل المستند
                                    </a>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="empty-state">
                            <i class="ri-checkbox-circle-line text-success me-1"></i>
                            لا توجد سياسات بانتظار الاعتراف — أحسنت!
                        </div>
                    @endforelse
                    @if ($policiesPending->hasPages())
                        <div class="mt-3">{{ $policiesPending->links() }}</div>
                    @endif
                </div>
            </div>

            <div id="policies-acknowledged-section" class="section-panel {{ $policiesAcknowledged->isEmpty() ? 'policy-section-hidden' : '' }}">
                <div class="section-panel-header">
                    <h5 class="fw-bold mb-1 text-dark">تم الاعتراف بها</h5>
                    <p class="text-muted fs-13 mb-0">{{ $policiesAcknowledged->count() }} سياسة</p>
                </div>
                <div class="section-panel-body p-0">
                    @forelse ($policiesAcknowledged as $policy)
                        @php
                            $ack = $policy->acknowledgments->first();
                        @endphp
                        <div class="ack-row px-3">
                            <div>
                                <div class="policy-title mb-1">{{ $policy->title }}</div>
                                @if ($policy->category)
                                    <span class="category-pill">{{ $policy->category }}</span>
                                @endif
                            </div>
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                @if ($policy->document_path)
                                    <a href="{{ asset('storage/' . $policy->document_path) }}" target="_blank"
                                        class="btn-download py-1 px-2">
                                        <i class="ri-download-line"></i>
                                    </a>
                                @endif
                                <span class="ack-badge">
                                    <i class="ri-check-line me-1"></i>
                                    {{ $ack?->acknowledged_at?->format('d/m/Y') ?? '—' }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="empty-state p-4">لم تعترف بأي سياسة بعد.</div>
                    @endforelse
                </div>
            </div>

            @if ($stats['total'] === 0)
                <div class="empty-state-block">
                    <div class="empty-icon"><i class="ri-file-shield-line"></i></div>
                    <h5 class="fw-semibold text-dark mb-2">لا توجد سياسات نشطة</h5>
                    <p class="text-muted mb-0">ستظهر السياسات هنا عند نشرها من الإدارة</p>
                </div>
            @endif
        </div>
    </div>
@stop

@push('scripts')
    <script src="{{ asset('assets/js/employee-policies.js') }}"></script>
@endpush
