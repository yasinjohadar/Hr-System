# المصادقة والملف الشخصي

## الوصف

تسجيل مستخدمين جدد، تسجيل الدخول والخروج، استعادة كلمة المرور، التحقق من البريد الإلكتروني، وتأكيد كلمة المرور للعمليات الحساسة. الملف الشخصي يسمح بتعديل بيانات الحساب وحذفه (ضمن مجموعة middleware محددة).

## المسارات

- **ضيف**: `register`, `login`, `forgot-password`, `reset-password` — [`routes/auth.php`](../../routes/auth.php)
- **مصادق**: `verify-email`, `verification.verify`, إعادة إرسال التحقق، `confirm-password`, `password.update` (PUT), `logout` — [`routes/auth.php`](../../routes/auth.php)
- **ملف شخصي**: `profile.edit`, `profile.update`, `profile.destroy` — [`routes/web.php`](../../routes/web.php) (`middleware`: `auth`, `check.user.active`)

## النماذج والجداول

- **Models**: `User` — [`app/Models/User.php`](../../app/Models/User.php)
- **جداول Laravel الافتراضية**: `users`, `sessions`, `password_reset_tokens` (حسب migrations المشروع)

## الواجهات

- `resources/views/auth/*` (login, register, forgot-password, reset-password, verify-email, confirm-password)
- `resources/views/profile/edit.blade.php`

## ملاحظات

- Breeze-style controllers تحت `App\Http\Controllers\Auth\*`.
- مسار `/` للمستخدم المصادق يوجّه حسب الدور (موظف vs غير ذلك) في [`routes/web.php`](../../routes/web.php).
