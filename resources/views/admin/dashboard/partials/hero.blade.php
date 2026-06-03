<div class="card custom-card dashboard-hero bg-primary-gradient mb-4">
    <div class="card-body py-4">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="hero-icon-wrap">
                    <i class="ri-dashboard-3-line"></i>
                </div>
                <div class="text-white">
                    <h4 class="mb-1 text-white">مرحباً، {{ auth()->user()->name }}!</h4>
                    <p class="mb-0 op-8 fs-13">لوحة تحكم شاملة لإدارة الموارد البشرية</p>
                </div>
            </div>
            <div class="d-flex flex-wrap gap-2">
                @if(auth()->user()->hasRole('department_head') && auth()->user()->canAccessEmployeePortal())
                    <a href="{{ route('employee.dashboard') }}" target="_blank" rel="noopener noreferrer"
                        class="btn btn-light btn-sm"
                        title="يفتح بوابة الموظف في تبويب جديد لتبقى لوحة الإدارة مفتوحة">
                        <i class="ri-user-3-line me-1"></i>بوابة الموظف
                        <i class="ri-external-link-line ms-1 opacity-75"></i>
                    </a>
                @endif
                <button type="button" class="btn btn-light btn-sm" onclick="refreshAdminDashboard(event)">
                    <i class="ri-refresh-line me-1"></i>تحديث
                </button>
                <a href="{{ route('admin.employees.create') }}" class="btn btn-outline-light btn-sm">
                    <i class="ri-user-add-line me-1"></i>موظف جديد
                </a>
                <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-light btn-sm">
                    <i class="ri-bar-chart-box-line me-1"></i>التقارير
                </a>
            </div>
        </div>
    </div>
</div>
