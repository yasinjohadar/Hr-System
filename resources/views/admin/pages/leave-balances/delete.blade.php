<!-- Modal حذف رصيد الإجازة -->
<div class="modal fade" id="delete{{ $balance->id }}" tabindex="-1" aria-labelledby="deleteLabel{{ $balance->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteLabel{{ $balance->id }}">تأكيد الحذف</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>هل أنت متأكد من حذف رصيد الإجازة للموظف <strong>{{ $balance->employee->full_name ?? $balance->employee->first_name . ' ' . $balance->employee->last_name }}</strong> 
                لنوع {{ $balance->leaveType->name_ar ?? $balance->leaveType->name }} لعام {{ $balance->year }}؟</p>
                <p class="text-danger"><small>تحذير: هذا الإجراء لا يمكن التراجع عنه!</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <form action="{{ route('admin.leave-balances.destroy', $balance->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="id" value="{{ $balance->id }}">
                    <button type="submit" class="btn btn-danger">حذف</button>
                </form>
            </div>
        </div>
    </div>
</div>


