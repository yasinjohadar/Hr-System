@php
    $isEdit = $isEdit ?? false;
    $user = $user ?? null;
    $selectedRoles = old('roles', $isEdit && $user ? $user->roles->pluck('name')->toArray() : []);
    $photoSrc = $isEdit && $user?->photo
        ? asset('storage/' . $user->photo)
        : asset('assets/images/faces/default-avatar.jpg');
    $hasUsername = $isEdit && $user && filled($user->username);
    $wantUsername = (bool) old('set_username', $hasUsername) || $errors->has('username');
@endphp

<div class="admin-form-body">

    <div class="admin-form-section">
        <div class="admin-form-section-head">
            <div class="admin-section-icon admin-section-icon-blue">
                <i class="ri-user-line"></i>
            </div>
            <div>
                <h3>المعلومات الأساسية</h3>
                <p>الاسم وبيانات الاتصال</p>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-md-6">
                <div class="admin-form-field">
                    <label class="admin-form-label">الاسم الكامل <span class="required">*</span></label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" name="name"
                        placeholder="أدخل الاسم الكامل" value="{{ old('name', $user->name ?? '') }}" required>
                    @error('name')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
            </div>

            @if (! $hasUsername)
                <div class="col-12">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" name="set_username" value="1" id="set_username"
                            {{ $wantUsername ? 'checked' : '' }}>
                        <label class="form-check-label" for="set_username">
                            تعيين اسم مستخدم للدخول (اختياري — الدخول بالبريد كافٍ)
                        </label>
                    </div>
                </div>
            @endif

            <div class="col-md-6 {{ $wantUsername ? '' : 'd-none' }}" id="username-field-col" data-username-optional="1">
                <div class="admin-form-field">
                    <label class="admin-form-label">اسم المستخدم</label>
                    <input type="text" class="form-control @error('username') is-invalid @enderror" name="username" id="username-input"
                        value="{{ $wantUsername ? old('username', $user->username ?? '') : '' }}"
                        autocomplete="off" autocapitalize="off" spellcheck="false"
                        placeholder="مثال: nayef.alobaid"
                        @if (! $wantUsername) disabled @endif>
                    <small class="text-muted d-block mt-1">يجب أن يكون فريداً. لا تستخدم بريد مستخدم آخر.</small>
                    @error('username')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="col-md-6">
                <div class="admin-form-field">
                    <label class="admin-form-label">البريد الإلكتروني <span class="required">*</span></label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" name="email"
                        placeholder="example@domain.com" value="{{ old('email', $user->email ?? '') }}"
                        autocomplete="off" required>
                    @error('email')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="col-md-6">
                <div class="admin-form-field">
                    <label class="admin-form-label">رقم الهاتف</label>
                    <input type="tel" class="form-control @error('phone') is-invalid @enderror" name="phone"
                        placeholder="05xxxxxxxx" value="{{ old('phone', $user->phone ?? '') }}">
                    @error('phone')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>
    </div>

    <div class="admin-form-section">
        <div class="admin-form-section-head">
            <div class="admin-section-icon admin-section-icon-green">
                <i class="ri-lock-password-line"></i>
            </div>
            <div>
                <h3>الأمان</h3>
                <p>
                    @if ($isEdit)
                        اترك الحقول فارغة إن لم تُرد تغيير كلمة المرور
                    @else
                        تعيين كلمة مرور آمنة للحساب
                    @endif
                </p>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-md-6">
                <div class="admin-form-field">
                    <label class="admin-form-label">
                        كلمة المرور @if (!$isEdit)<span class="required">*</span>@endif
                    </label>
                    <div class="admin-password-wrap">
                        <input type="password" id="password" class="form-control @error('password') is-invalid @enderror"
                            name="password" placeholder="••••••••" {{ $isEdit ? '' : 'required' }}>
                        <button type="button" class="admin-password-toggle" data-toggle-password="password" aria-label="إظهار كلمة المرور">
                            <i class="ri-eye-line"></i>
                        </button>
                    </div>
                    @error('password')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="col-md-6">
                <div class="admin-form-field">
                    <label class="admin-form-label">
                        تأكيد كلمة المرور @if (!$isEdit)<span class="required">*</span>@endif
                    </label>
                    <div class="admin-password-wrap">
                        <input type="password" id="password_confirmation"
                            class="form-control @error('password_confirmation') is-invalid @enderror"
                            name="password_confirmation" placeholder="••••••••" {{ $isEdit ? '' : 'required' }}>
                        <button type="button" class="admin-password-toggle" data-toggle-password="password_confirmation" aria-label="إظهار كلمة المرور">
                            <i class="ri-eye-line"></i>
                        </button>
                    </div>
                    @error('password_confirmation')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>
    </div>

    <div class="admin-form-section">
        <div class="admin-form-section-head">
            <div class="admin-section-icon admin-section-icon-amber">
                <i class="ri-settings-3-line"></i>
            </div>
            <div>
                <h3>إعدادات الحساب</h3>
                <p>الصورة والحالة والتفعيل</p>
            </div>
        </div>

        <div class="row g-3 align-items-start">
            <div class="col-md-6">
                <div class="admin-form-field">
                    <label class="admin-form-label">صورة المستخدم</label>
                    <div class="admin-photo-zone">
                        <div class="admin-photo-preview-wrap">
                            <img id="photo-preview" src="{{ $photoSrc }}" alt="صورة المستخدم" class="admin-photo-preview">
                            <input type="file" name="photo" id="photo-input" accept="image/*" data-photo-preview="photo-preview">
                        </div>
                        <div class="admin-photo-actions">
                            <label for="photo-input" class="admin-photo-btn mb-0">
                                <i class="ri-image-add-line"></i>
                                اختر صورة
                            </label>
                            <p class="admin-photo-hint">JPG أو PNG — بحد أقصى 2 ميجابايت</p>
                        </div>
                    </div>
                    @error('photo')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="col-md-6">
                <div class="admin-form-field mb-3">
                    <label class="admin-form-label">حالة الحساب <span class="required">*</span></label>
                    <select class="form-select @error('status') is-invalid @enderror" name="status" required data-admin-choices>
                        <option value="active" {{ old('status', $user->status ?? 'active') == 'active' ? 'selected' : '' }}>مفعل</option>
                        <option value="inactive" {{ old('status', $user->status ?? '') == 'inactive' ? 'selected' : '' }}>موقوف</option>
                        <option value="banned" {{ old('status', $user->status ?? '') == 'banned' ? 'selected' : '' }}>محظور</option>
                    </select>
                    @error('status')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>

                <div class="admin-form-switch-card">
                    <div class="switch-info">
                        <strong>تفعيل الدخول للنظام</strong>
                        <span>السماح بتسجيل الدخول {{ $isEdit ? 'لهذا الحساب' : 'فور الإنشاء' }}</span>
                    </div>
                    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active"
                        {{ old('is_active', $isEdit ? ($user->is_active ?? false) : true) ? 'checked' : '' }}>
                </div>
            </div>
        </div>
    </div>

    <div class="admin-form-section">
        <div class="admin-form-section-head">
            <div class="admin-section-icon admin-section-icon-purple">
                <i class="ri-shield-user-line"></i>
            </div>
            <div>
                <h3>الأدوار والصلاحيات</h3>
                <p>حدد دوراً واحداً أو أكثر للمستخدم</p>
            </div>
        </div>

        <div class="admin-role-grid">
            @foreach ($roles as $role)
                <label class="admin-role-chip">
                    <input type="checkbox" name="roles[]" value="{{ $role->name }}"
                        {{ in_array($role->name, $selectedRoles) ? 'checked' : '' }}>
                    <span>
                        <i class="ri-user-star-line"></i>
                        {{ $role->name }}
                    </span>
                </label>
            @endforeach
        </div>
        @error('roles')<div class="text-danger small mt-2">{{ $message }}</div>@enderror
    </div>

</div>
