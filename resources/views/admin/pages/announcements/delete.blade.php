<div class="modal fade" id="delete{{ $announcement->id }}" tabindex="-1" aria-labelledby="deleteLabel{{ $announcement->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteLabel{{ $announcement->id }}">تأكيد الحذف</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>هل أنت متأكد من حذف الإعلان <strong>{{ $announcement->title }}</strong>؟</p>
                <p class="text-danger"><small>هذا الإجراء لا يمكن التراجع عنه.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <form action="{{ route('admin.announcements.destroy', $announcement) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">حذف</button>
                </form>
            </div>
        </div>
    </div>
</div>
