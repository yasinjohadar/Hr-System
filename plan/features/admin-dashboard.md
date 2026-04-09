# لوحة تحكم الإدارة والإحصائيات

## الوصف

الصفحة الرئيسية للمسؤول بعد تسجيل الدخول، مع نقطة نهاية لجلب إحصائيات/مؤشرات (مثلاً للودجات أو AJAX).

## المسارات

- `GET admin/` → `admin.dashboard`
- `GET admin/dashboard` → `admin.dashboard.index`
- `GET admin/dashboard/stats` → `admin.dashboard.stats`

الكل تحت: `middleware` `auth`, `check.user.active`, `ensure.admin` — [`routes/admin.php`](../../routes/admin.php)

**Controller**: [`App\Http\Controllers\Admin\DashboardController`](../../app/Http/Controllers/Admin/DashboardController.php)

## النماذج والجداول

لا يوجد نموذج واحد مخصص؛ البيانات تُجمع من عدة كيانات (موظفون، حضور، إلخ).

## الواجهات

- `resources/views/admin/pages/dashboard/*` (أو ما يعادلها في المشروع)

## ملاحظات

- مسار الجذر `/` يوجّه الموظف إلى `employee.dashboard` والباقي إلى لوحة الإدارة — [`routes/web.php`](../../routes/web.php).
