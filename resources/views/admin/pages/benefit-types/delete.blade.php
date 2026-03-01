<!-- Modal حذف نوع الميزة -->
<div class="modal fade" id="delete{{ $benefitType->id }}" tabindex="-1" aria-labelledby="deleteLabel{{ $benefitType->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteLabel{{ $benefitType->id }}">تأكيد الحذف</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>هل أنت متأكد من حذف نوع الميزة <strong>{{ $benefitType->name_ar ?? $benefitType->name }}</strong>؟</p>
                @if ($benefitType->employee_benefits_count > 0)
                    <p class="text-warning">
                        <i class="fas fa-exclamation-triangle"></i> 
                        يوجد {{ $benefitType->employee_benefits_count }} ميزة مرتبطة بهذا النوع
                    </p>
                @endif
                <p class="text-danger"><small>تحذير: هذا الإجراء لا يمكن التراجع عنه!</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <form action="{{ route('admin.benefit-types.destroy', $benefitType->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="id" value="{{ $benefitType->id }}">
                    <button type="submit" class="btn btn-danger">حذف</button>
                </form>
            </div>
        </div>
    </div>
</div>


