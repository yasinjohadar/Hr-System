{{-- مودال تأكيد مركزي — AdminConfirm.show() / data-confirm / data-delete-url --}}
<div class="modal fade" id="adminConfirmModal" tabindex="-1" aria-labelledby="adminConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered admin-confirm-dialog">
        <div class="modal-content admin-confirm-modal border-0">
            <div class="modal-body text-center p-0">
                <button type="button" class="admin-confirm-close btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>

                <div class="admin-confirm-icon-ring" id="adminConfirmIconRing" data-type="info">
                    <span class="admin-confirm-icon-inner">
                        <i class="ri-question-line" id="adminConfirmIcon"></i>
                    </span>
                </div>

                <h4 class="admin-confirm-title" id="adminConfirmTitle">تأكيد العملية</h4>
                <div class="admin-confirm-message" id="adminConfirmMessage">هل أنت متأكد من المتابعة؟</div>
                <p class="admin-confirm-hint mb-0" id="adminConfirmHint"></p>

                <div class="admin-confirm-actions">
                    <button type="button" class="admin-confirm-btn admin-confirm-btn-cancel" data-bs-dismiss="modal" id="adminConfirmCancel">
                        <i class="ri-close-line"></i>
                        <span id="adminConfirmCancelText">إلغاء</span>
                    </button>
                    <button type="button" class="admin-confirm-btn admin-confirm-btn-confirm" id="adminConfirmOk">
                        <i class="ri-check-line" id="adminConfirmOkIcon"></i>
                        <span id="adminConfirmOkText">تأكيد</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<form id="adminConfirmDeleteForm" method="POST" class="d-none">
    @csrf
    @method('DELETE')
</form>
