@include('employee.pages.self-service.dashboard.partials.stats')

<div class="row g-4 mb-4">
    <div class="col-xl-4 col-lg-5">
        <div class="card profile-summary-card h-100">
            <div class="card-body text-center p-4">
                @if ($employee->photo)
                    <img src="{{ asset('storage/' . $employee->photo) }}" alt="صورة الموظف" class="profile-summary-avatar mb-3">
                @else
                    <div class="profile-summary-avatar profile-summary-avatar--placeholder mb-3">
                        {{ substr($employee->first_name, 0, 1) }}
                    </div>
                @endif
                <h5 class="mb-1 fw-bold">{{ $employee->full_name }}</h5>
                <p class="text-muted mb-1 fs-13 font-monospace">{{ $employee->employee_code }}</p>
                <p class="text-muted mb-3 fs-13">{{ $employee->position->title ?? '-' }} — {{ $employee->department->name ?? '-' }}</p>
                <div class="profile-summary-details text-start">
                    <div class="profile-detail-row">
                        <span class="profile-detail-label"><i class="ri-mail-line me-1"></i>البريد</span>
                        <span class="profile-detail-value">{{ $employee->work_email ?? $employee->personal_email ?? '—' }}</span>
                    </div>
                    <div class="profile-detail-row">
                        <span class="profile-detail-label"><i class="ri-phone-line me-1"></i>الهاتف</span>
                        <span class="profile-detail-value">{{ $employee->work_phone ?? $employee->personal_phone ?? '—' }}</span>
                    </div>
                    <div class="profile-detail-row">
                        <span class="profile-detail-label"><i class="ri-calendar-line me-1"></i>التوظيف</span>
                        <span class="profile-detail-value">{{ $employee->hire_date->format('Y/m/d') }}</span>
                    </div>
                    <div class="profile-detail-row mb-0">
                        <span class="profile-detail-label"><i class="ri-shield-check-line me-1"></i>الحالة</span>
                        <span class="badge bg-{{ $employee->is_active ? 'success' : 'danger' }}-transparent">
                            {{ $employee->is_active ? 'نشط' : 'غير نشط' }}
                        </span>
                    </div>
                </div>
                <a href="{{ route('employee.profile') }}" class="btn btn-primary w-100 mt-4">
                    <i class="ri-user-settings-line me-1"></i>الملف الشخصي
                </a>
            </div>
        </div>
    </div>

    <div class="col-xl-8 col-lg-7">
        @if ($announcements->isNotEmpty())
            <div class="card announcements-card mb-4">
                <div class="card-header announcements-card-header d-flex align-items-center gap-2">
                    <span class="announcements-card-icon"><i class="ri-megaphone-line"></i></span>
                    <h6 class="card-title fw-semibold mb-0">إعلانات الشركة</h6>
                </div>
                <div class="card-body p-0" id="widget-announcements" data-widget="announcements">
                    @include('employee.pages.self-service.dashboard.partials.widgets.announcements')
                </div>
            </div>
        @endif

        <div class="card quick-links-card">
            <div class="card-header quick-links-card-header">
                <h6 class="card-title fw-semibold mb-0"><i class="ri-apps-2-line me-1 text-primary"></i>روابط سريعة</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    @foreach ([
                        ['route' => 'employee.profile', 'icon' => 'ri-user-line', 'label' => 'الملف الشخصي', 'tone' => 'primary'],
                        ['route' => 'employee.leaves', 'icon' => 'ri-sun-line', 'label' => 'الإجازات', 'tone' => 'success'],
                        ['route' => 'employee.attendance', 'icon' => 'ri-calendar-check-line', 'label' => 'الحضور', 'tone' => 'info'],
                        ['route' => 'employee.salaries', 'icon' => 'ri-money-dollar-circle-line', 'label' => 'الرواتب', 'tone' => 'warning'],
                        ['route' => 'employee.tasks', 'icon' => 'ri-task-line', 'label' => 'المهام', 'tone' => 'primary'],
                        ['route' => 'employee.documents', 'icon' => 'ri-file-text-line', 'label' => 'المستندات', 'tone' => 'secondary'],
                        ['route' => 'employee.goals', 'icon' => 'ri-target-line', 'label' => 'الأهداف', 'tone' => 'success'],
                        ['route' => 'employee.tickets', 'icon' => 'ri-customer-service-line', 'label' => 'التذاكر', 'tone' => 'danger'],
                        ['route' => 'employee.assets', 'icon' => 'ri-computer-line', 'label' => 'الأصول', 'tone' => 'info'],
                    ] as $link)
                        <div class="col-md-4 col-6">
                            <a href="{{ route($link['route']) }}" class="quick-link-tile quick-link-tile--{{ $link['tone'] }}">
                                <span class="quick-link-tile__icon"><i class="{{ $link['icon'] }}"></i></span>
                                <span class="quick-link-tile__label">{{ $link['label'] }}</span>
                                <i class="ri-arrow-left-s-line quick-link-tile__arrow"></i>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
