<!-- Modal حذف سجل الحضور -->
<div class="modal fade" id="delete{{ $attendance->id }}" tabindex="-1" aria-labelledby="deleteLabel{{ $attendance->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteLabel{{ $attendance->id }}">تأكيد الحذف</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>هل أنت متأكد من حذف سجل الحضور للموظف <strong>{{ $attendance->employee->full_name ?? $attendance->employee->first_name . ' ' . $attendance->employee->last_name }}</strong> 
                بتاريخ {{ $attendance->attendance_date->format('Y-m-d') }}؟</p>
                <p class="text-danger"><small>تحذير: هذا الإجراء لا يمكن التراجع عنه!</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <form action="{{ route('admin.attendances.destroy', $attendance->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="id" value="{{ $attendance->id }}">
                    <button type="submit" class="btn btn-danger">حذف</button>
                </form>
            </div>
        </div>
    </div>
</div>


