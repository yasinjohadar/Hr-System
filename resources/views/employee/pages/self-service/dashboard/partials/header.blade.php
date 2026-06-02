<div class="card custom-card dashboard-hero bg-primary-gradient mb-4">
    <div class="card-body py-4">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-3">
                @if ($employee->photo)
                    <img src="{{ asset('storage/' . $employee->photo) }}" alt="{{ $employee->full_name }}" class="rounded-circle hero-avatar">
                @else
                    <div class="rounded-circle hero-avatar-placeholder d-inline-flex align-items-center justify-content-center">
                        {{ substr($employee->first_name, 0, 1) }}
                    </div>
                @endif
                <div class="text-white">
                    <h4 class="mb-1 text-white">مرحباً، {{ $employee->first_name }}!</h4>
                    <p class="mb-0 op-8 fs-13">
                        {{ $employee->position->title ?? '' }}
                        @if($employee->department)
                            — {{ $employee->department->name }}
                        @endif
                        @if($yearsOfService > 0)
                            | {{ $yearsOfService }} سنة و {{ $monthsOfService }} شهر
                        @endif
                    </p>
                    <small class="text-white-50" id="dashboard-last-refreshed">آخر تحديث: {{ now()->format('Y/m/d H:i') }}</small>
                </div>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <button type="button" class="btn btn-light btn-sm" id="dashboard-refresh-btn" title="تحديث البيانات">
                    <i class="ri-refresh-line me-1" id="dashboard-refresh-icon"></i>
                    <span class="d-none d-sm-inline">تحديث</span>
                </button>
                <a href="{{ route('employee.leaves') }}" class="btn btn-light btn-sm">
                    <i class="ri-add-line me-1"></i>طلب إجازة
                </a>
                <a href="{{ route('employee.expense-requests.create') }}" class="btn btn-outline-light btn-sm">
                    <i class="ri-money-dollar-circle-line me-1"></i>طلب مصروفات
                </a>
            </div>
        </div>
    </div>
</div>
