<!-- Modal حذف المنصب -->
<div class="modal fade" id="delete{{ $position->id }}" tabindex="-1" aria-labelledby="deleteLabel{{ $position->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteLabel{{ $position->id }}">تأكيد الحذف</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>هل أنت متأكد من حذف المنصب <strong>{{ $position->title }}</strong>؟</p>
                @if ($position->employees_count > 0)
                    <p class="text-warning">
                        <i class="fas fa-exclamation-triangle"></i> 
                        يوجد {{ $position->employees_count }} موظف مرتبط بهذا المنصب
                    </p>
                @endif
                <p class="text-danger"><small>تحذير: هذا الإجراء لا يمكن التراجع عنه!</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <form action="{{ route('admin.positions.destroy', $position->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="id" value="{{ $position->id }}">
                    <button type="submit" class="btn btn-danger">حذف</button>
                </form>
            </div>
        </div>
    </div>
</div>

