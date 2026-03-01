<!-- Modal حذف الراتب -->
<div class="modal fade" id="delete{{ $salary->id }}" tabindex="-1" aria-labelledby="deleteLabel{{ $salary->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteLabel{{ $salary->id }}">تأكيد الحذف</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>هل أنت متأكد من حذف راتب الموظف <strong>{{ $salary->employee->full_name ?? $salary->employee->first_name . ' ' . $salary->employee->last_name }}</strong> 
                لشهر {{ $salary->month_name }} {{ $salary->salary_year }}؟</p>
                <p class="text-danger"><small>تحذير: هذا الإجراء لا يمكن التراجع عنه!</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <form action="{{ route('admin.salaries.destroy', $salary->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="id" value="{{ $salary->id }}">
                    <button type="submit" class="btn btn-danger">حذف</button>
                </form>
            </div>
        </div>
    </div>
</div>

