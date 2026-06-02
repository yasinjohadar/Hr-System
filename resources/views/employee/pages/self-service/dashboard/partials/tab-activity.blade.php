<div class="row mb-4">
    <div class="col-xl-6 mb-4">
        <div class="card custom-card h-100">
            <div class="card-header justify-content-between">
                <h6 class="card-title fw-semibold mb-0"><i class="ri-calendar-event-line me-1"></i>الاجتماعات القادمة</h6>
                <a href="{{ route('employee.meetings') }}" class="btn btn-sm btn-outline-primary">عرض الكل</a>
            </div>
            <div class="card-body p-0" id="widget-meetings" data-widget="meetings">
                @include('employee.pages.self-service.dashboard.partials.widgets.meetings')
            </div>
        </div>
    </div>
    <div class="col-xl-6 mb-4">
        <div class="card custom-card h-100">
            <div class="card-header justify-content-between">
                <h6 class="card-title fw-semibold mb-0"><i class="ri-task-line me-1"></i>المهام النشطة</h6>
                <a href="{{ route('employee.tasks') }}" class="btn btn-sm btn-outline-primary">عرض الكل</a>
            </div>
            <div class="card-body p-0" id="widget-tasks" data-widget="tasks">
                @include('employee.pages.self-service.dashboard.partials.widgets.tasks')
            </div>
        </div>
    </div>
</div>

@if($recentViolations->isNotEmpty() || $assignedAssets->isNotEmpty())
    <div class="row mb-4">
        @if($recentViolations->isNotEmpty())
            <div class="col-xl-6 mb-4 mb-xl-0">
                <div class="card custom-card border-danger border">
                    <div class="card-header card-header-accent-danger d-flex align-items-center gap-2">
                        <span class="avatar avatar-sm bg-danger-transparent avatar-rounded">
                            <i class="ri-alert-line text-danger"></i>
                        </span>
                        <h6 class="card-title fw-semibold mb-0">آخر المخالفات</h6>
                    </div>
                    <div class="card-body p-0" id="widget-violations" data-widget="violations">
                        @include('employee.pages.self-service.dashboard.partials.widgets.violations')
                    </div>
                </div>
            </div>
        @endif
        @if($assignedAssets->isNotEmpty())
            <div class="col-xl-6">
                <div class="card custom-card h-100">
                    <div class="card-header justify-content-between">
                        <h6 class="card-title fw-semibold mb-0"><i class="ri-computer-line me-1"></i>الأصول المعينة</h6>
                        <a href="{{ route('employee.assets') }}" class="btn btn-sm btn-outline-primary">عرض الكل</a>
                    </div>
                    <div class="card-body p-0" id="widget-assets" data-widget="assets">
                        @include('employee.pages.self-service.dashboard.partials.widgets.assets')
                    </div>
                </div>
            </div>
        @endif
    </div>
@endif
