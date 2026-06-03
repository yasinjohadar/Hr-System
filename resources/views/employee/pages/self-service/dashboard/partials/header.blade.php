<div class="card dashboard-hero mb-4">
    <div class="card-body py-4">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-3">
                @if ($employee->photo)
                    <img src="{{ asset('storage/' . $employee->photo) }}" alt="{{ $employee->full_name }}" class="rounded-circle hero-avatar">
                @else
                    <div class="rounded-circle hero-avatar-placeholder d-inline-flex align-items-center justify-content-center fw-bold">
                        {{ substr($employee->first_name, 0, 1) }}
                    </div>
                @endif
                <div>
                    <h4 class="mb-1 dashboard-hero-title fw-bold">مرحباً، {{ $employee->first_name }}!</h4>
                    <p class="mb-0 dashboard-hero-subtitle">
                        {{ $employee->position->title ?? '' }}
                        @if ($employee->department)
                            — {{ $employee->department->name }}
                        @endif
                        @if ($yearsOfService > 0 || $monthsOfService > 0)
                            <span class="dashboard-hero-sep">|</span>
                            {{ $yearsOfService }} {{ $yearsOfService === 1 ? 'سنة' : 'سنوات' }}
                            @if ($monthsOfService > 0)
                                و {{ $monthsOfService }} {{ $monthsOfService === 1 ? 'شهر' : 'أشهر' }}
                            @endif
                        @endif
                    </p>
                    <small class="dashboard-hero-meta" id="dashboard-last-refreshed">آخر تحديث: {{ now()->format('Y/m/d H:i') }}</small>
                </div>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <button type="button" class="btn btn-outline-secondary btn-sm" id="dashboard-refresh-btn" title="تحديث البيانات">
                    <i class="ri-refresh-line me-1" id="dashboard-refresh-icon"></i>
                    <span class="d-none d-sm-inline">تحديث</span>
                </button>
                @if (auth()->user()->hasRole('department_head'))
                    <a href="{{ route('admin.dashboard') }}" target="_blank" rel="noopener noreferrer"
                        class="btn btn-outline-primary btn-sm" title="لوحة الإدارة في تبويب جديد">
                        <i class="ri-admin-line me-1"></i>لوحة الإدارة
                        <i class="ri-external-link-line ms-1 opacity-75"></i>
                    </a>
                @endif
                <a href="{{ route('employee.leaves') }}" class="btn btn-primary btn-sm">
                    <i class="ri-add-line me-1"></i>طلب إجازة
                </a>
                <a href="{{ route('employee.expense-requests.create') }}" class="btn btn-outline-primary btn-sm">
                    <i class="ri-money-dollar-circle-line me-1"></i>طلب مصروفات
                </a>
            </div>
        </div>
    </div>
</div>
