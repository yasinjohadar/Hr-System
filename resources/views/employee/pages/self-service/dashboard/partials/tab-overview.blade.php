@include('employee.pages.self-service.dashboard.partials.stats')

<div class="row mb-4">
    <div class="col-xl-4 col-lg-6 mb-4 mb-xl-0">
        <div class="card custom-card h-100">
            <div class="card-body text-center">
                @if ($employee->photo)
                    <img src="{{ asset('storage/' . $employee->photo) }}" alt="صورة الموظف" class="rounded-circle mb-3" style="width: 100px; height: 100px; object-fit: cover; border: 3px solid var(--primary-color);">
                @else
                    <div class="bg-primary-transparent text-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 100px; height: 100px; font-size: 40px;">
                        {{ substr($employee->first_name, 0, 1) }}
                    </div>
                @endif
                <h5 class="mb-1 fw-semibold">{{ $employee->full_name }}</h5>
                <p class="text-muted mb-2 fs-13">{{ $employee->employee_code }}</p>
                <p class="text-muted mb-3 fs-13">{{ $employee->position->title ?? '-' }} — {{ $employee->department->name ?? '-' }}</p>
                <div class="row text-start border-top pt-3">
                    <div class="col-6 mb-2">
                        <small class="text-muted d-block">البريد</small>
                        <span class="fs-13">{{ $employee->work_email ?? $employee->personal_email ?? '-' }}</span>
                    </div>
                    <div class="col-6 mb-2">
                        <small class="text-muted d-block">الهاتف</small>
                        <span class="fs-13">{{ $employee->work_phone ?? $employee->personal_phone ?? '-' }}</span>
                    </div>
                    <div class="col-6 mb-2">
                        <small class="text-muted d-block">تاريخ التوظيف</small>
                        <span class="fs-13">{{ $employee->hire_date->format('Y/m/d') }}</span>
                    </div>
                    <div class="col-6 mb-2">
                        <small class="text-muted d-block">الحالة</small>
                        <span class="badge bg-{{ $employee->is_active ? 'success' : 'danger' }}-transparent">
                            {{ $employee->is_active ? 'نشط' : 'غير نشط' }}
                        </span>
                    </div>
                </div>
                <a href="{{ route('employee.profile') }}" class="btn btn-sm btn-primary mt-2 w-100">
                    <i class="ri-user-settings-line me-1"></i>الملف الشخصي
                </a>
            </div>
        </div>
    </div>

    <div class="col-xl-8 col-lg-6">
        @if($announcements->isNotEmpty())
            <div class="card custom-card border-primary border mb-4">
                <div class="card-header card-header-accent-primary d-flex align-items-center gap-2">
                    <span class="avatar avatar-sm bg-primary-transparent avatar-rounded">
                        <i class="ri-megaphone-line text-primary"></i>
                    </span>
                    <h6 class="card-title fw-semibold mb-0">إعلانات الشركة</h6>
                </div>
                <div class="card-body p-0" id="widget-announcements" data-widget="announcements">
                    @include('employee.pages.self-service.dashboard.partials.widgets.announcements')
                </div>
            </div>
        @endif

        <div class="card custom-card">
            <div class="card-header">
                <h6 class="card-title fw-semibold mb-0"><i class="ri-links-line me-1"></i>روابط سريعة</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    @foreach([
                        ['route' => 'employee.profile', 'icon' => 'ri-user-line', 'label' => 'الملف الشخصي', 'bg' => 'primary'],
                        ['route' => 'employee.leaves', 'icon' => 'ri-sun-line', 'label' => 'الإجازات', 'bg' => 'success'],
                        ['route' => 'employee.attendance', 'icon' => 'ri-calendar-check-line', 'label' => 'الحضور', 'bg' => 'info'],
                        ['route' => 'employee.salaries', 'icon' => 'ri-money-dollar-circle-line', 'label' => 'الرواتب', 'bg' => 'warning'],
                        ['route' => 'employee.tasks', 'icon' => 'ri-task-line', 'label' => 'المهام', 'bg' => 'primary'],
                        ['route' => 'employee.documents', 'icon' => 'ri-file-text-line', 'label' => 'المستندات', 'bg' => 'secondary'],
                        ['route' => 'employee.goals', 'icon' => 'ri-target-line', 'label' => 'الأهداف', 'bg' => 'success'],
                        ['route' => 'employee.tickets', 'icon' => 'ri-customer-service-line', 'label' => 'التذاكر', 'bg' => 'danger'],
                        ['route' => 'employee.assets', 'icon' => 'ri-computer-line', 'label' => 'الأصول', 'bg' => 'teal'],
                    ] as $link)
                        <div class="col-md-4 col-6">
                            <a href="{{ route($link['route']) }}" class="card custom-card h-100 text-center hover-card">
                                <div class="card-body p-3">
                                    <div class="avatar avatar-lg bg-{{ $link['bg'] }}-transparent avatar-rounded mb-2">
                                        <i class="{{ $link['icon'] }} fs-18 text-{{ $link['bg'] }}"></i>
                                    </div>
                                    <p class="mb-0 fs-13 fw-medium">{{ $link['label'] }}</p>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
