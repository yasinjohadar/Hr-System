<!-- Modal حذف القسم -->
<div class="modal fade" id="delete{{ $department->id }}" tabindex="-1" aria-labelledby="deleteLabel{{ $department->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteLabel{{ $department->id }}">تأكيد الحذف</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>هل أنت متأكد من حذف القسم <strong>{{ $department->name }}</strong>؟</p>
                @if ($department->employees->count() > 0)
                    <p class="text-warning">
                        <i class="fas fa-exclamation-triangle"></i> 
                        تحذير: يوجد {{ $department->employees->count() }} موظف في هذا القسم!
                    </p>
                @endif
                <p class="text-danger"><small>تحذير: هذا الإجراء لا يمكن التراجع عنه!</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <form action="{{ route('admin.departments.destroy', $department->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="id" value="{{ $department->id }}">
                    <button type="submit" class="btn btn-danger">حذف</button>
                </form>
            </div>
        </div>
    </div>
</div>

