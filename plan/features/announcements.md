# إعلانات الشركة

## الوصف

نشر إعلانات للموظفين (شركة/إدارة) وعرضها في بوابة الموظف.

## المسارات

- `admin/announcements` (resource) — `AnnouncementController`
- `GET employee/announcements` — `SelfServiceController@announcements`

[`routes/admin.php`](../../routes/admin.php), [`routes/employee.php`](../../routes/employee.php)

## النماذج والجداول

- **Model**: `Announcement`
- **جدول**: `announcements`

## الواجهات

- `resources/views/admin/pages/announcements/*`
- واجهات الموظف تحت `resources/views/employee/pages/self-service/`

## ملاحظات

- يمكن ربطها لاحقًا بقنوات إشعار (بريد/داخل التطبيق) — [`plan/features/notifications.md`](notifications.md).
