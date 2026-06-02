<div class="modal fade" id="loginCodeModal" tabindex="-1" aria-labelledby="loginCodeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="loginCodeModalLabel">كود دخول لمتصفح آخر</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted small mb-3">
                    صالح لمدة 15 دقيقة ولا يعمل إلا مرة واحدة.
                    <br>المستخدم: <strong id="loginCodeUserName"></strong>
                </p>
                <div class="mb-3">
                    <label class="form-label fw-semibold">الكود</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="loginCodeValue" readonly>
                        <button class="btn btn-outline-secondary" type="button" id="copyCodeBtn" title="نسخ الكود">
                            <i class="ri-file-copy-line"></i>
                        </button>
                    </div>
                </div>
                <div class="mb-0">
                    <label class="form-label fw-semibold">الرابط</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="loginCodeUrl" readonly>
                        <button class="btn btn-outline-secondary" type="button" id="copyUrlBtn" title="نسخ الرابط">
                            <i class="ri-file-copy-line"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
