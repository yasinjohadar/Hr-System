<!-- Modal رفض طلب الإجازة -->
<div class="modal fade" id="reject{{ $request->id }}" tabindex="-1" aria-labelledby="rejectLabel{{ $request->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rejectLabel{{ $request->id }}">رفض طلب الإجازة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.leave-requests.reject', $request->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p>هل أنت متأكد من رفض طلب الإجازة للموظف <strong>{{ $request->employee->full_name ?? $request->employee->first_name . ' ' . $request->employee->last_name }}</strong>؟</p>
                    <div class="mb-3">
                        <label for="rejection_reason{{ $request->id }}" class="form-label">سبب الرفض <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="rejection_reason" id="rejection_reason{{ $request->id }}" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-danger">رفض</button>
                </div>
            </form>
        </div>
    </div>
</div>


