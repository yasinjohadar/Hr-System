<div class="row mb-4" id="dashboard-attendance-section">
    <div class="col-12 mb-4">
        <div class="card custom-card">
            <div class="card-header justify-content-between flex-wrap gap-2">
                <h6 class="card-title fw-semibold mb-0">
                    <i class="ri-bar-chart-line me-1"></i>
                    ملخص الحضور — <span id="attendance-chart-month">{{ now()->translatedFormat('F Y') }}</span>
                </h6>
                <a href="{{ route('employee.attendance') }}" class="btn btn-sm btn-outline-primary">عرض الكل</a>
            </div>
            <div class="card-body">
                <div class="row align-items-center mb-3">
                    <div class="col-md-4 text-center mb-3 mb-md-0">
                        <h5 class="mb-0 fw-semibold text-success" data-stat="total_attendance">{{ $stats['total_attendance'] }}</h5>
                        <small class="text-muted">أيام حاضر</small>
                    </div>
                    <div class="col-md-4 text-center mb-3 mb-md-0">
                        <h5 class="mb-0 fw-semibold text-danger" data-stat-display="absent">{{ $absentDays }}</h5>
                        <small class="text-muted">أيام غائب</small>
                    </div>
                    <div class="col-md-4 text-center">
                        <h5 class="mb-0 fw-semibold text-warning" data-stat-display="late">{{ $lateDays }}</h5>
                        <small class="text-muted">أيام متأخر</small>
                    </div>
                </div>
                <div class="chart-legend mb-2">
                    <span><span class="dot dot-present"></span> حاضر</span>
                    <span><span class="dot dot-late"></span> متأخر</span>
                    <span><span class="dot dot-absent"></span> غائب</span>
                    <span><span class="dot dot-none"></span> بدون سجل</span>
                </div>
                <div id="attendance-chart"></div>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="card custom-card">
            <div class="card-header justify-content-between">
                <h6 class="card-title fw-semibold mb-0">سجل الحضور الأخير</h6>
                <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#recentAttendanceCollapse">
                    طي / عرض
                </button>
            </div>
            <div class="collapse show" id="recentAttendanceCollapse">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover text-nowrap dashboard-table mb-0">
                            <thead>
                                <tr>
                                    <th>التاريخ</th>
                                    <th>دخول</th>
                                    <th>خروج</th>
                                    <th>الحالة</th>
                                </tr>
                            </thead>
                            <tbody id="recent-attendance-tbody">
                                @forelse($recentAttendance as $attendance)
                                    <tr>
                                        <td>{{ $attendance->attendance_date->format('Y/m/d') }}</td>
                                        <td>{{ $attendance->check_in ? \Carbon\Carbon::parse($attendance->check_in)->format('H:i') : '-' }}</td>
                                        <td>{{ $attendance->check_out ? \Carbon\Carbon::parse($attendance->check_out)->format('H:i') : '-' }}</td>
                                        <td>
                                            <span class="badge bg-{{ $attendance->status === 'present' ? 'success' : ($attendance->status === 'absent' ? 'danger' : 'warning') }}-transparent">
                                                {{ $attendance->status_name_ar }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">لا توجد سجلات حضور</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
