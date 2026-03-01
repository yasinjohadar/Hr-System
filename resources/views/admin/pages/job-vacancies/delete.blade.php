<!-- Modal حذف الوظيفة الشاغرة -->
<div class="modal fade" id="delete{{ $vacancy->id }}" tabindex="-1" aria-labelledby="deleteLabel{{ $vacancy->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteLabel{{ $vacancy->id }}">تأكيد الحذف</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>هل أنت متأكد من حذف الوظيفة الشاغرة <strong>{{ $vacancy->title_ar ?? $vacancy->title }}</strong>؟</p>
                @if ($vacancy->applications_count > 0)
                    <p class="text-warning">
                        <i class="fas fa-exclamation-triangle"></i> 
                        يوجد {{ $vacancy->applications_count }} طلب توظيف مرتبط بهذه الوظيفة
                    </p>
                @endif
                <p class="text-danger"><small>تحذير: هذا الإجراء لا يمكن التراجع عنه!</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <form action="{{ route('admin.job-vacancies.destroy', $vacancy->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="id" value="{{ $vacancy->id }}">
                    <button type="submit" class="btn btn-danger">حذف</button>
                </form>
            </div>
        </div>
    </div>
</div>


