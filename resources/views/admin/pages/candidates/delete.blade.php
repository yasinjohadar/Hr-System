<!-- Modal حذف المرشح -->
<div class="modal fade" id="delete{{ $candidate->id }}" tabindex="-1" aria-labelledby="deleteLabel{{ $candidate->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteLabel{{ $candidate->id }}">تأكيد الحذف</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>هل أنت متأكد من حذف المرشح <strong>{{ $candidate->full_name }}</strong>؟</p>
                @if ($candidate->applications_count > 0)
                    <p class="text-warning">
                        <i class="fas fa-exclamation-triangle"></i> 
                        يوجد {{ $candidate->applications_count }} طلب توظيف مرتبط بهذا المرشح
                    </p>
                @endif
                <p class="text-danger"><small>تحذير: هذا الإجراء لا يمكن التراجع عنه!</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <form action="{{ route('admin.candidates.destroy', $candidate->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="id" value="{{ $candidate->id }}">
                    <button type="submit" class="btn btn-danger">حذف</button>
                </form>
            </div>
        </div>
    </div>
</div>


