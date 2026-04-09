<!-- Modal الموافقة على طلب الإجازة -->
<div class="modal fade" id="approve{{ $request->id }}" tabindex="-1" aria-labelledby="approveLabel{{ $request->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <button type="button" class="btn-close ms-0" data-bs-dismiss="modal" aria-label="إغلاق"></button>
            </div>
            <form action="{{ route('admin.leave-requests.approve', $request->id) }}" method="POST">
                @csrf
                <div class="modal-body text-center pt-0 px-4 pb-2">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-success bg-opacity-10 text-success mb-4" style="width: 4.5rem; height: 4.5rem;">
                        <i class="fa-solid fa-circle-check fa-3x"></i>
                    </div>
                    <h5 class="modal-title fw-semibold mb-2" id="approveLabel{{ $request->id }}">تأكيد الموافقة على الطلب</h5>
                    <p class="text-muted mb-1">
                        هل تريد الموافقة على طلب الإجازة للموظف
                        <strong>{{ $request->employee->full_name ?? $request->employee->first_name . ' ' . $request->employee->last_name }}</strong>؟
                    </p>
                    <p class="small text-muted mb-0">
                        <span class="badge bg-info-subtle text-dark border">{{ $request->leaveType->name_ar ?? $request->leaveType->name }}</span>
                        <span class="mx-1">·</span>
                        {{ $request->start_date->format('Y-m-d') }}
                        <i class="fas fa-arrow-left mx-1 small"></i>
                        {{ $request->end_date->format('Y-m-d') }}
                        <span class="mx-1">·</span>
                        {{ $request->days_count }} يوم
                    </p>
                </div>
                <div class="modal-footer border-0 justify-content-center gap-2 pb-4">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-success px-4">
                        <i class="fas fa-check me-2"></i>تأكيد الموافقة
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
