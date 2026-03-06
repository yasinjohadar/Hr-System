@extends('admin.layouts.master')

@section('page-title')
    لوحة التحكم
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <!-- Page Header -->
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div>
                    <h4 class="mb-0">مرحباً بك، {{ auth()->user()->name }}!</h4>
                    <p class="mb-0 text-muted">لوحة تحكم شاملة لإدارة الموارد البشرية</p>
                </div>
                <div>
                    <button class="btn btn-primary" onclick="refreshDashboard()">
                        <i class="fas fa-sync-alt me-2"></i>تحديث
                    </button>
                </div>
            </div>
            <!-- End Page Header -->

            <!-- إحصائيات عامة -->
            <div class="row mb-4">
                <div class="col-xl-3 col-lg-6 col-md-6">
                    <div class="card overflow-hidden bg-primary-gradient">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-2 text-white">إجمالي الموظفين</h6>
                                    <h2 class="mb-0 text-white">{{ $stats['total_employees'] ?? 0 }}</h2>
                                    <small class="text-white-50">+{{ $stats['new_employees_this_month'] ?? 0 }} هذا الشهر</small>
                                </div>
                                <div class="fs-40 text-white op-7">
                                    <i class="fas fa-users"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 col-md-6">
                    <div class="card overflow-hidden bg-success-gradient">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-2 text-white">حضور اليوم</h6>
                                    <h2 class="mb-0 text-white">{{ $attendanceStats['today_present'] ?? 0 }}</h2>
                                    <small class="text-white-50">غائب: {{ $attendanceStats['today_absent'] ?? 0 }}</small>
                                </div>
                                <div class="fs-40 text-white op-7">
                                    <i class="fas fa-calendar-check"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 col-md-6">
                    <div class="card overflow-hidden bg-warning-gradient">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-2 text-white">طلبات إجازة قيد الانتظار</h6>
                                    <h2 class="mb-0 text-white">{{ $leaveStats['pending_requests'] ?? 0 }}</h2>
                                    <small class="text-white-50">في إجازة: {{ $leaveStats['approved_today'] ?? 0 }}</small>
                                </div>
                                <div class="fs-40 text-white op-7">
                                    <i class="fas fa-calendar-times"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 col-md-6">
                    <div class="card overflow-hidden bg-info-gradient">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-2 text-white">إجمالي الرواتب (هذا الشهر)</h6>
                                    <h2 class="mb-0 text-white">{{ number_format($salaryStats['total_this_month'] ?? 0, 0) }} ر.س</h2>
                                    <small class="text-white-50">مدفوعة: {{ $salaryStats['paid_count'] ?? 0 }}</small>
                                </div>
                                <div class="fs-40 text-white op-7">
                                    <i class="fas fa-money-bill-wave"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- إحصائيات إضافية -->
            <div class="row mb-4">
                <div class="col-xl-3 col-lg-6 col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="mb-1">الأقسام</h6>
                                    <h3 class="mb-0 text-primary">{{ $stats['total_departments'] ?? 0 }}</h3>
                                </div>
                                <div class="fs-30 text-primary">
                                    <i class="fas fa-building"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="mb-1">المناصب</h6>
                                    <h3 class="mb-0 text-success">{{ $stats['total_positions'] ?? 0 }}</h3>
                                </div>
                                <div class="fs-30 text-success">
                                    <i class="fas fa-briefcase"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="mb-1">الفروع</h6>
                                    <h3 class="mb-0 text-info">{{ $stats['total_branches'] ?? 0 }}</h3>
                                </div>
                                <div class="fs-30 text-info">
                                    <i class="fas fa-map-marker-alt"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="mb-1">معدل الحضور الشهري</h6>
                                    <h3 class="mb-0 text-warning">{{ $attendanceStats['monthly_attendance_rate'] ?? 0 }}%</h3>
                                </div>
                                <div class="fs-30 text-warning">
                                    <i class="fas fa-chart-line"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- المهام العاجلة -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                                المهام العاجلة
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-2">
                                    <a href="{{ route('admin.leave-requests.index', ['status' => 'pending']) }}" class="text-decoration-none">
                                        <div class="alert alert-warning mb-0">
                                            <h6 class="mb-1">طلبات إجازة</h6>
                                            <h4 class="mb-0">{{ $urgentTasks['pending_leaves'] ?? 0 }}</h4>
                                        </div>
                                    </a>
                                </div>
                                <div class="col-md-2">
                                    <a href="{{ route('admin.expense-requests.index', ['status' => 'pending']) }}" class="text-decoration-none">
                                        <div class="alert alert-info mb-0">
                                            <h6 class="mb-1">طلبات مصروفات</h6>
                                            <h4 class="mb-0">{{ $urgentTasks['pending_expenses'] ?? 0 }}</h4>
                                        </div>
                                    </a>
                                </div>
                                <div class="col-md-2">
                                    <a href="{{ route('admin.tickets.index', ['status' => 'open']) }}" class="text-decoration-none">
                                        <div class="alert alert-danger mb-0">
                                            <h6 class="mb-1">تذاكر مفتوحة</h6>
                                            <h4 class="mb-0">{{ $urgentTasks['open_tickets'] ?? 0 }}</h4>
                                        </div>
                                    </a>
                                </div>
                                <div class="col-md-2">
                                    <a href="{{ route('admin.employee-violations.index', ['status' => 'pending']) }}" class="text-decoration-none">
                                        <div class="alert alert-secondary mb-0">
                                            <h6 class="mb-1">مخالفات قيد المراجعة</h6>
                                            <h4 class="mb-0">{{ $urgentTasks['pending_violations'] ?? 0 }}</h4>
                                        </div>
                                    </a>
                                </div>
                                <div class="col-md-2">
                                    <a href="{{ route('admin.meetings.index', ['status' => 'scheduled']) }}" class="text-decoration-none">
                                        <div class="alert alert-primary mb-0">
                                            <h6 class="mb-1">اجتماعات قادمة</h6>
                                            <h4 class="mb-0">{{ $urgentTasks['upcoming_meetings'] ?? 0 }}</h4>
                                        </div>
                                    </a>
                                </div>
                                <div class="col-md-2">
                                    <a href="{{ route('admin.tasks.index', ['status' => 'overdue']) }}" class="text-decoration-none">
                                        <div class="alert alert-dark mb-0">
                                            <h6 class="mb-1">مهام متأخرة</h6>
                                            <h4 class="mb-0">{{ $urgentTasks['overdue_tasks'] ?? 0 }}</h4>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- الإشعارات المهمة -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-bell text-danger me-2"></i>
                                تنبيهات مهمة
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="alert alert-warning">
                                        <i class="fas fa-file-alt me-2"></i>
                                        <strong>مستندات تنتهي قريباً:</strong> {{ $importantNotifications['expiring_documents'] ?? 0 }}
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="alert alert-info">
                                        <i class="fas fa-certificate me-2"></i>
                                        <strong>شهادات تنتهي قريباً:</strong> {{ $importantNotifications['expiring_certificates'] ?? 0 }}
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="alert alert-danger">
                                        <i class="fas fa-file-contract me-2"></i>
                                        <strong>عقود تنتهي قريباً:</strong>
                                        <a href="{{ route('admin.contracts.index', ['expiring' => 90]) }}" class="alert-link">{{ $importantNotifications['contracts_expiring'] ?? 0 }}</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- إعلانات الشركة -->
            @if(isset($announcements) && $announcements->isNotEmpty())
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-bullhorn text-primary me-2"></i>
                                إعلانات الشركة
                            </h5>
                            <a href="{{ route('admin.announcements.index') }}" class="btn btn-sm btn-outline-primary">عرض الكل</a>
                        </div>
                        <div class="card-body">
                            <div class="list-group list-group-flush">
                                @foreach($announcements as $announcement)
                                    <div class="list-group-item border-0 border-bottom px-0">
                                        <h6 class="mb-1">{{ $announcement->title }}</h6>
                                        @if($announcement->content)
                                            <p class="mb-1 text-muted small">{{ Str::limit(strip_tags($announcement->content), 120) }}</p>
                                        @endif
                                        <small class="text-muted">
                                            {{ $announcement->publish_date?->format('Y-m-d') ?? $announcement->created_at->format('Y-m-d') }}
                                            @if($announcement->expiry_date)
                                                — حتى {{ $announcement->expiry_date->format('Y-m-d') }}
                                            @endif
                                        </small>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- آخر الأنشطة -->
            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-chart-line me-2"></i>
                                إحصائيات الحضور (آخر 6 أشهر)
                            </h5>
                        </div>
                        <div class="card-body">
                            <canvas id="attendanceChart" height="100"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-history me-2"></i>
                                آخر الأنشطة
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="list-group list-group-flush">
                                @forelse($recentActivities as $activity)
                                    <div class="list-group-item border-0 px-0">
                                        <div class="d-flex align-items-start">
                                            <div class="me-3">
                                                <span class="badge bg-{{ $activity['color'] ?? 'primary' }} rounded-circle p-2">
                                                    <i class="{{ $activity['icon'] ?? 'fas fa-circle' }}"></i>
                                                </span>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1">{{ $activity['title'] ?? '' }}</h6>
                                                <p class="mb-1 text-muted small">{{ $activity['description'] ?? '' }}</p>
                                                <small class="text-muted">{{ $activity['time']->diffForHumans() ?? '' }}</small>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-muted text-center">لا توجد أنشطة حديثة</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-bolt me-2"></i>
                                إجراءات سريعة
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-2 mb-2">
                                    <a href="{{ route('admin.employees.create') }}" class="btn btn-primary w-100">
                                        <i class="fas fa-user-plus me-2"></i>إضافة موظف
                                    </a>
                                </div>
                                <div class="col-md-2 mb-2">
                                    <a href="{{ route('admin.leave-requests.create') }}" class="btn btn-info w-100">
                                        <i class="fas fa-calendar-plus me-2"></i>طلب إجازة
                                    </a>
                                </div>
                                <div class="col-md-2 mb-2">
                                    <a href="{{ route('admin.salaries.create') }}" class="btn btn-success w-100">
                                        <i class="fas fa-money-bill-wave me-2"></i>إضافة راتب
                                    </a>
                                </div>
                                <div class="col-md-2 mb-2">
                                    <a href="{{ route('admin.tickets.create') }}" class="btn btn-warning w-100">
                                        <i class="fas fa-ticket-alt me-2"></i>تذكرة جديدة
                                    </a>
                                </div>
                                <div class="col-md-2 mb-2">
                                    <a href="{{ route('admin.meetings.create') }}" class="btn btn-danger w-100">
                                        <i class="fas fa-calendar me-2"></i>اجتماع جديد
                                    </a>
                                </div>
                                <div class="col-md-2 mb-2">
                                    <a href="{{ route('admin.reports.index') }}" class="btn btn-secondary w-100">
                                        <i class="fas fa-chart-bar me-2"></i>التقارير
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // رسم بياني للحضور
    const attendanceCtx = document.getElementById('attendanceChart');
    if (attendanceCtx) {
        const attendanceData = @json($chartData['attendance'] ?? []);
        
        new Chart(attendanceCtx, {
            type: 'line',
            data: {
                labels: attendanceData.map(item => item.month),
                datasets: [{
                    label: 'حضور',
                    data: attendanceData.map(item => item.present),
                    borderColor: 'rgb(75, 192, 192)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    tension: 0.1
                }, {
                    label: 'غياب',
                    data: attendanceData.map(item => item.absent),
                    borderColor: 'rgb(255, 99, 132)',
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }

    // تحديث Dashboard
    function refreshDashboard() {
        location.reload();
    }

    // تحديث تلقائي كل 5 دقائق
    setInterval(function() {
        fetch('{{ route("admin.dashboard.stats") }}')
            .then(response => response.json())
            .then(data => {
                // تحديث الإحصائيات
                console.log('Dashboard updated');
            });
    }, 300000); // 5 دقائق
</script>
@stop

@section('css')
<style>
    .bg-primary-gradient {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    .bg-success-gradient {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    }
    .bg-warning-gradient {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    }
    .bg-info-gradient {
        background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
    }
    .text-fixed-white {
        color: #fff !important;
    }
    .op-7 {
        opacity: 0.7;
    }
</style>
@stop
