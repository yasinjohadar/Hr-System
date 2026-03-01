<!-- Modal حذف طلب الإجازة -->
<div class="modal fade" id="delete{{ $request->id }}" tabindex="-1" aria-labelledby="deleteLabel{{ $request->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteLabel{{ $request->id }}">تأكيد الحذف</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>هل أنت متأكد من حذف طلب الإجازة للموظف <strong>{{ $request->employee->full_name ?? $request->employee->first_name . ' ' . $request->employee->last_name }}</strong>؟</p>
                @if ($request->status == 'approved')
                    <p class="text-warning">
                        <i class="fas fa-exclamation-triangle"></i> 
                        هذا الطلب موافق عليه، سيتم إعادة الرصيد تلقائياً
                    </p>
                @endif
                <p class="text-danger"><small>تحذير: هذا الإجراء لا يمكن التراجع عنه!</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <form action="{{ route('admin.leave-requests.destroy', $request->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="id" value="{{ $request->id }}">
                    <button type="submit" class="btn btn-danger">حذف</button>
                </form>
            </div>
        </div>
    </div>
</div>


