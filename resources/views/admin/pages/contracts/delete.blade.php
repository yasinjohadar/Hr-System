<div class="modal fade" id="delete{{ $contract->id }}" tabindex="-1" aria-labelledby="deleteLabel{{ $contract->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteLabel{{ $contract->id }}">تأكيد الحذف</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>هل أنت متأكد من حذف عقد الموظف <strong>{{ $contract->employee->full_name ?? $contract->employee->employee_code }}</strong> ({{ $contract->start_date->format('Y-m-d') }} — {{ $contract->end_date->format('Y-m-d') }})؟</p>
                <p class="text-danger"><small>هذا الإجراء لا يمكن التراجع عنه.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <form action="{{ route('admin.contracts.destroy', $contract) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">حذف</button>
                </form>
            </div>
        </div>
    </div>
</div>
