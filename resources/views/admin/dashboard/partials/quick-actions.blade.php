<div class="card custom-card">
    <div class="card-header">
        <h6 class="card-title fw-semibold mb-0"><i class="ri-flashlight-line me-1 text-primary"></i>إجراءات سريعة</h6>
    </div>
    <div class="card-body">
        <div class="row g-3">
            @foreach([
                ['route' => 'admin.employees.create', 'icon' => 'ri-user-add-line', 'label' => 'إضافة موظف'],
                ['route' => 'admin.leave-requests.create', 'icon' => 'ri-calendar-event-line', 'label' => 'طلب إجازة'],
                ['route' => 'admin.salaries.create', 'icon' => 'ri-wallet-3-line', 'label' => 'إضافة راتب'],
                ['route' => 'admin.tickets.create', 'icon' => 'ri-customer-service-2-line', 'label' => 'تذكرة جديدة'],
                ['route' => 'admin.meetings.create', 'icon' => 'ri-video-add-line', 'label' => 'اجتماع جديد'],
                ['route' => 'admin.reports.index', 'icon' => 'ri-pie-chart-line', 'label' => 'التقارير'],
            ] as $action)
                <div class="col-xl-2 col-md-4 col-6">
                    <a href="{{ route($action['route']) }}" class="quick-action-btn">
                        <i class="{{ $action['icon'] }} text-primary"></i>
                        <span class="fs-13 fw-medium">{{ $action['label'] }}</span>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</div>
