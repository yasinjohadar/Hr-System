<!-- Modal حذف رصيد الإجازة -->
<div class="modal fade" id="delete{{ $balance->id }}" tabindex="-1" aria-labelledby="deleteLabel{{ $balance->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <button type="button" class="btn-close ms-0" data-bs-dismiss="modal" aria-label="إغلاق"></button>
            </div>
            <form action="{{ route('admin.leave-balances.destroy', $balance->id) }}" method="POST">
                @csrf
                @method('DELETE')
                <input type="hidden" name="id" value="{{ $balance->id }}">
                <div class="modal-body text-center pt-0 px-4 pb-2">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-danger bg-opacity-10 text-danger mb-4" style="width: 4.5rem; height: 4.5rem;">
                        <i class="fa-solid fa-triangle-exclamation fa-3x"></i>
                    </div>
                    <h5 class="modal-title fw-semibold mb-2" id="deleteLabel{{ $balance->id }}">تأكيد حذف رصيد الإجازة</h5>
                    <p class="text-muted mb-2">
                        هل أنت متأكد من حذف رصيد الإجازة للموظف
                        <strong>{{ $balance->employee->full_name ?? $balance->employee->first_name . ' ' . $balance->employee->last_name }}</strong>؟
                    </p>
                    <p class="small text-muted mb-3">
                        <span class="badge bg-info-subtle text-dark border">{{ $balance->leaveType->name_ar ?? $balance->leaveType->name }}</span>
                        <span class="mx-1">·</span>
                        عام {{ $balance->year }}
                    </p>
                    <p class="small text-danger mb-0 fw-medium">
                        <i class="fas fa-exclamation-triangle me-1"></i>
                        تحذير: هذا الإجراء لا يمكن التراجع عنه.
                    </p>
                </div>
                <div class="modal-footer border-0 justify-content-center gap-2 pb-4">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-danger px-4">
                        <i class="fa-solid fa-trash-can me-2"></i>تأكيد الحذف
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
