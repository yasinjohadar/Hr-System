@php
    $isEdit = $isEdit ?? false;
    $user = $user ?? null;
    $selectedRoles = old('roles', $isEdit && $user ? $user->roles->pluck('name')->toArray() : []);
    $photoSrc = $isEdit && $user?->photo
        ? asset('storage/' . $user->photo)
        : asset('assets/images/faces/default-avatar.jpg');
@endphp

<div class="form-section-title"><i class="ri-user-line me-1"></i>المعلومات الأساسية</div>
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <label class="form-label">الاسم الكامل <span class="text-danger">*</span></label>
        <input type="text" class="form-control @error('name') is-invalid @enderror" name="name"
            value="{{ old('name', $user->name ?? '') }}" required>
        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label class="form-label">اسم المستخدم</label>
        <input type="text" class="form-control @error('username') is-invalid @enderror" name="username"
            value="{{ old('username', $user->username ?? '') }}">
        @error('username')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label class="form-label">البريد الإلكتروني <span class="text-danger">*</span></label>
        <input type="email" class="form-control @error('email') is-invalid @enderror" name="email"
            value="{{ old('email', $user->email ?? '') }}" required>
        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label class="form-label">رقم الهاتف</label>
        <input type="tel" class="form-control @error('phone') is-invalid @enderror" name="phone"
            value="{{ old('phone', $user->phone ?? '') }}">
        @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>

<div class="form-section-title"><i class="ri-lock-password-line me-1"></i>الأمان</div>
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <label class="form-label">
            كلمة المرور @if (!$isEdit)<span class="text-danger">*</span>@else<small class="text-muted">(اتركها فارغة إن لم تُرد التغيير)</small>@endif
        </label>
        <input type="password" class="form-control @error('password') is-invalid @enderror" name="password"
            {{ $isEdit ? '' : 'required' }}>
        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label class="form-label">تأكيد كلمة المرور @if (!$isEdit)<span class="text-danger">*</span>@endif</label>
        <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror"
            name="password_confirmation" {{ $isEdit ? '' : 'required' }}>
        @error('password_confirmation')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>

<div class="form-section-title"><i class="ri-settings-3-line me-1"></i>الحالة والأدوار</div>
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <label class="form-label">حالة الحساب <span class="text-danger">*</span></label>
        <select class="form-select @error('status') is-invalid @enderror" name="status" required>
            <option value="active" {{ old('status', $user->status ?? 'active') == 'active' ? 'selected' : '' }}>مفعل</option>
            <option value="inactive" {{ old('status', $user->status ?? '') == 'inactive' ? 'selected' : '' }}>موقوف</option>
            <option value="banned" {{ old('status', $user->status ?? '') == 'banned' ? 'selected' : '' }}>محظور</option>
        </select>
        @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6 d-flex align-items-end">
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active"
                {{ old('is_active', $isEdit ? ($user->is_active ?? false) : true) ? 'checked' : '' }}>
            <label class="form-check-label" for="is_active">تفعيل الدخول للنظام</label>
        </div>
    </div>
    <div class="col-12">
        <label class="form-label">الأدوار</label>
        <div class="roles-grid">
            @foreach ($roles as $role)
                <label class="role-check-card mb-0">
                    <input type="checkbox" class="form-check-input me-2" name="roles[]" value="{{ $role->name }}"
                        {{ in_array($role->name, $selectedRoles) ? 'checked' : '' }}>
                    {{ $role->name }}
                </label>
            @endforeach
        </div>
        @error('roles')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
    </div>
</div>

<div class="form-section-title"><i class="ri-image-line me-1"></i>الصورة</div>
<div class="row g-3 mb-2">
    <div class="col-12">
        <div class="d-flex align-items-center gap-3">
            <img id="photo-preview" src="{{ $photoSrc }}" alt="صورة المستخدم" class="photo-preview">
            <div>
                <input type="file" name="photo" id="photo-input" class="form-control" accept="image/*"
                    onchange="previewUserPhoto(this)">
                <div class="form-text">JPEG, PNG — بحد أقصى 2 ميجابايت</div>
                @error('photo')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
            </div>
        </div>
    </div>
</div>
