# المستخدمون والأدوار وانتحال الشخصية

## الوصف

إدارة مستخدمي النظام (CRUD)، تغيير كلمة مرور المستخدم، تفعيل/تعطيل الحساب، إدارة الأدوار والصلاحيات عبر Spatie Permission، والخروج من وضع انتحال شخصية الموظف.

## المسارات

- **مستخدمون**: `users` (resource), `users.update-password`, `users.toggle-status` — [`routes/web.php`](../../routes/web.php)
- **مسار بديل**: `toggle-user-status/{id}` (بدون `check.user.active` على المسار الأساسي لـ toggle — راجع الملف)
- **أدوار**: `roles` (resource) — [`routes/web.php`](../../routes/web.php)
- **انتحال**: `leave-impersonation` (POST) — [`routes/web.php`](../../routes/web.php)

## النماذج والجداول

- **Models**: `User` — [`app/Models/User.php`](../../app/Models/User.php)
- **Spatie**: جداول `roles`, `permissions`, `model_has_roles`, `model_has_permissions`, `role_has_permissions`

## الواجهات

- `resources/views/admin/pages/users/*`
- `resources/views/admin/pages/roles/*`

## ملاحظات

- Middleware: `role`, `permission`, `ensure.admin` للوحة الإدارة؛ `ImpersonationController` للخروج من الانتحال.
- إدارة المستخدمين على مسارات الويب العامة وليس تحت بادئة `admin/` فقط — راجع التصميم الحالي عند الصلاحيات.
