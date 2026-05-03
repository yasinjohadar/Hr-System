<div class="modal fade" id="delete{{ $user->id }}" tabindex="-1" aria-labelledby="deleteLabel{{ $user->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:520px;">
        <div class="modal-content border-0" style="border-radius:5px;box-shadow:0 4px 20px rgba(0,0,0,.08);">
            <form method="POST" action="{{ route('users.destroy', 'test') }}">
                @csrf
                @method('DELETE')
                <input type="hidden" name="id" value="{{ $user->id }}">

                <div class="modal-body text-center px-4 pt-5 pb-4">
                    <div class="mb-4">
                        <span class="d-inline-flex align-items-center justify-content-center bg-danger bg-opacity-10" style="width:64px;height:64px;border-radius:50%;">
                            <i class="fas fa-trash-can text-danger fs-2"></i>
                        </span>
                    </div>

                    <h5 class="fw-bold mb-2">حذف المستخدم</h5>

                    <p class="text-muted mb-0 fs-15 lh-lg">
                        هل أنت متأكد من حذف المستخدم
                        <span class="fw-bold text-dark">{{ $user->name }}</span>
                        ؟
                    </p>
                    <p class="text-muted small mt-1">لا يمكن التراجع عن هذا الإجراء بعد الحذف.</p>
                </div>

                <div class="modal-footer justify-content-center border-0 gap-3 px-4 pb-4 pt-0">
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal" style="min-width:120px;border-radius:5px;">
                        إلغاء
                    </button>
                    <button type="submit" class="btn btn-danger px-4" style="min-width:120px;border-radius:5px;">
                        <i class="fas fa-trash-can me-1"></i>حذف المستخدم
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
