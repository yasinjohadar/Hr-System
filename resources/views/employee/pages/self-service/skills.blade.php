@extends('employee.layouts.master')

@section('page-title')
    المهارات
@stop

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/employee-skills.css') }}">
@endpush

@section('content')
    <div class="main-content app-content employee-skills-page">
        <div class="container-fluid pt-4">

            <div class="card page-hero mb-4">
                <div class="card-body py-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="page-hero-icon">
                            <i class="ri-star-line"></i>
                        </div>
                        <div>
                            <h4 class="mb-1 page-hero-title fw-bold">مهاراتي</h4>
                            <p class="mb-0 page-hero-subtitle">المهارات المسجّلة في ملفك الوظيفي</p>
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
                                <div class="stat-label">إجمالي المهارات</div>
                            </div>
                            <div class="stat-icon stat-icon--primary"><i class="ri-lightbulb-line"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-value">{{ $stats['verified'] }}</div>
                                <div class="stat-label">تم التحقق</div>
                            </div>
                            <div class="stat-icon stat-icon--success"><i class="ri-shield-check-line"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-value">{{ $stats['pending'] }}</div>
                                <div class="stat-label">قيد التحقق</div>
                            </div>
                            <div class="stat-icon stat-icon--warning"><i class="ri-time-line"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-value">{{ $stats['expert'] }}</div>
                                <div class="stat-label">مستوى خبير</div>
                            </div>
                            <div class="stat-icon stat-icon--success"><i class="ri-award-line"></i></div>
                        </div>
                    </div>
                </div>
            </div>

            @if ($skills->isNotEmpty())
                <div class="filter-pills" role="group">
                    <button type="button" class="filter-pill active" data-skill-filter="all">الكل</button>
                    <button type="button" class="filter-pill" data-skill-filter="verified">تم التحقق</button>
                    <button type="button" class="filter-pill" data-skill-filter="pending">قيد التحقق</button>
                    <button type="button" class="filter-pill" data-skill-filter="expert">خبير</button>
                    <button type="button" class="filter-pill" data-skill-filter="advanced">متقدم</button>
                </div>

                <div class="row g-3" id="skills-grid">
                    @foreach ($skills as $skill)
                        @php
                            $level = $skill->proficiency_level ?? 'beginner';
                            $pct = match ($level) {
                                'expert' => 100,
                                'advanced' => 75,
                                'intermediate' => 50,
                                default => 25,
                            };
                            $displayName = $skill->skill_name_ar ?: $skill->skill_name;
                        @endphp
                        <div class="col-md-6 col-xl-4 skill-card-item"
                            data-level="{{ $level }}"
                            data-verified="{{ $skill->is_verified ? '1' : '0' }}">
                            <div class="skill-card">
                                <div class="skill-card-header">
                                    <div class="d-flex align-items-start gap-2 flex-grow-1 min-w-0">
                                        <div class="skill-icon"><i class="ri-code-box-line"></i></div>
                                        <div class="min-w-0">
                                            <div class="skill-name text-truncate" title="{{ $displayName }}">{{ $displayName }}</div>
                                            @if ($skill->skill_name_ar && $skill->skill_name && $skill->skill_name !== $skill->skill_name_ar)
                                                <small class="text-muted">{{ $skill->skill_name }}</small>
                                            @endif
                                        </div>
                                    </div>
                                    <span class="level-pill level-pill--{{ $level }}">{{ $skill->proficiency_level_name_ar }}</span>
                                </div>
                                <div class="skill-progress">
                                    <div class="skill-progress-bar" style="width: {{ $pct }}%"></div>
                                </div>
                                <div class="skill-meta">
                                    @if ($skill->years_of_experience)
                                        <span><i class="ri-calendar-line me-1"></i>{{ $skill->years_of_experience }} سنوات خبرة</span>
                                    @else
                                        <span>—</span>
                                    @endif
                                    @if ($skill->is_verified)
                                        <span class="verify-pill--yes"><i class="ri-check-line me-1"></i>تم التحقق</span>
                                    @else
                                        <span class="verify-pill--no">قيد التحقق</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state">
                    <div class="empty-icon"><i class="ri-star-smile-line"></i></div>
                    <h5 class="fw-semibold text-dark mb-2">لا توجد مهارات مسجلة</h5>
                    <p class="text-muted mb-0">ستظهر مهاراتك هنا بعد إضافتها من الموارد البشرية</p>
                </div>
            @endif
        </div>
    </div>
@stop

@push('scripts')
    <script src="{{ asset('assets/js/employee-skills.js') }}"></script>
@endpush
