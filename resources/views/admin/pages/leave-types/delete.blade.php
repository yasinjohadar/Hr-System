<!-- Modal حذف نوع الإجازة -->
<div class="modal fade" id="delete{{ $leaveType->id }}" tabindex="-1" aria-labelledby="deleteLabel{{ $leaveType->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteLabel{{ $leaveType->id }}">تأكيد الحذف</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>هل أنت متأكد من حذف نوع الإجازة <strong>{{ $leaveType->name_ar ?? $leaveType->name }}</strong>؟</p>
                <p class="text-danger"><small>تحذير: هذا الإجراء لا يمكن التراجع عنه!</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <form action="{{ route('admin.leave-types.destroy', $leaveType->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="id" value="{{ $leaveType->id }}">
                    <button type="submit" class="btn btn-danger">حذف</button>
                </form>
            </div>
        </div>
    </div>
</div>


