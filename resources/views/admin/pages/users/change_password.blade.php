<div class="modal fade" id="change_password{{ $user->id }}" tabindex="-1" aria-labelledby="changePasswordLabel{{ $user->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:500px;">
        <div class="modal-content border-0" style="border-radius:5px;box-shadow:0 4px 20px rgba(0,0,0,.15);">
            <form method="POST" action="{{ route('users.update-password', $user->id) }}" id="changePasswordForm{{ $user->id }}">
                @csrf
                @method('PUT')

                <div class="modal-body text-center px-4 pt-5 pb-3">
                    <div class="mb-3">
                        <span class="d-inline-flex align-items-center justify-content-center bg-primary bg-opacity-10" style="width:56px;height:56px;border-radius:50%;">
                            <i class="fas fa-lock text-primary fs-4"></i>
                        </span>
                    </div>

                    <h5 class="fw-bold mb-1">تعديل كلمة المرور</h5>
                    <p class="text-muted small mb-4">{{ $user->name }}</p>

                    <div class="text-start mb-3">
                        <label for="password{{ $user->id }}" class="form-label fw-semibold small mb-1">كلمة المرور الجديدة</label>
                        <div class="input-group">
                            <input type="password" name="password" id="password{{ $user->id }}" class="form-control" required minlength="8"
                                style="border-radius:5px 0 0 5px;background-color:#f8f9fa;">
                            <button type="button" class="btn btn-outline-secondary toggle-password" data-target="password{{ $user->id }}" style="border-radius:0 5px 5px 0;border-left:0;">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <small class="text-muted">8 أحرف على الأقل</small>
                    </div>

                    <div class="text-start mb-0">
                        <label for="password_confirmation{{ $user->id }}" class="form-label fw-semibold small mb-1">تأكيد كلمة المرور</label>
                        <div class="input-group">
                            <input type="password" name="password_confirmation" id="password_confirmation{{ $user->id }}" class="form-control" required minlength="8"
                                style="border-radius:5px 0 0 5px;background-color:#f8f9fa;">
                            <button type="button" class="btn btn-outline-secondary toggle-password" data-target="password_confirmation{{ $user->id }}" style="border-radius:0 5px 5px 0;border-left:0;">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="modal-footer justify-content-center border-0 gap-3 px-4 pb-4 pt-0">
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal" style="min-width:120px;border-radius:5px;">
                        إلغاء
                    </button>
                    <button type="submit" class="btn btn-primary px-4" style="min-width:120px;border-radius:5px;">
                        <i class="fas fa-check me-1"></i>حفظ التغيير
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
