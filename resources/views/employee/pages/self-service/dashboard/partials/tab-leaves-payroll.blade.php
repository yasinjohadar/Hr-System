<div class="row mb-4">
    <div class="col-xl-5 col-lg-12 mb-4">
        <div class="card custom-card h-100">
            <div class="card-header justify-content-between">
                <h6 class="card-title fw-semibold mb-0">أرصدة الإجازات</h6>
                <a href="{{ route('employee.leaves') }}" class="btn btn-sm btn-outline-primary">عرض الكل</a>
            </div>
            <div class="card-body">
                @forelse($leaveBalances as $balance)
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="fs-13 fw-medium">{{ $balance->leaveType->name_ar ?? $balance->leaveType->name }}</span>
                            <span class="fs-13 text-muted">{{ $balance->remaining_days }} / {{ $balance->total_days }} يوم</span>
                        </div>
                        @php
                            $percentage = $balance->total_days > 0 ? ($balance->used_days / $balance->total_days) * 100 : 0;
                        @endphp
                        <div class="progress progress-sm" style="height: 8px;">
                            <div class="progress-bar bg-{{ $percentage > 80 ? 'danger' : ($percentage > 50 ? 'warning' : 'success') }}"
                                 role="progressbar" style="width: {{ $percentage }}%"></div>
                        </div>
                        <div class="d-flex justify-content-between mt-1">
                            <small class="text-muted">مستخدم: {{ $balance->used_days }}</small>
                            <small class="text-success">متبقي: {{ $balance->remaining_days }}</small>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-muted py-3">
                        <i class="ri-calendar-line fs-24 d-block mb-2"></i>
                        لا توجد أرصدة
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="col-xl-7 col-lg-12">
        <div class="row">
            <div class="col-12 mb-4">
                <div class="card custom-card">
                    <div class="card-header justify-content-between">
                        <h6 class="card-title fw-semibold mb-0"><i class="ri-sun-line me-1"></i>آخر طلبات الإجازة</h6>
                        <a href="{{ route('employee.leaves') }}" class="btn btn-sm btn-outline-primary">عرض الكل</a>
                    </div>
                    <div class="card-body p-0" id="widget-leaves" data-widget="leaves">
                        @include('employee.pages.self-service.dashboard.partials.widgets.leaves')
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="card custom-card">
                    <div class="card-header justify-content-between">
                        <h6 class="card-title fw-semibold mb-0"><i class="ri-money-dollar-circle-line me-1"></i>سجل الرواتب</h6>
                        <a href="{{ route('employee.salaries') }}" class="btn btn-sm btn-outline-primary">عرض الكل</a>
                    </div>
                    <div class="card-body p-0" id="widget-payroll" data-widget="payroll">
                        @include('employee.pages.self-service.dashboard.partials.widgets.payroll')
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
