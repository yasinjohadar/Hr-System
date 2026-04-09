# الإشعارات داخل التطبيق

## الوصف

مركز إشعارات مخصص (إنشاء، عرض، حذف، تعليم كمقروء، عد غير المقروء، آخر الإشعارات) بالإضافة إلى مسار مصادقة البث `POST /broadcasting/auth` في [`routes/web.php`](../../routes/web.php).

## المسارات

تحت `admin/notifications/*` — [`routes/admin.php`](../../routes/admin.php):

- index, create, store, show, destroy
- `mark-read`, `mark-all-read`
- `api/unread-count`, `api/latest`

**Controller**: `NotificationController` — النموذج `CustomNotification`

## النماذج والجداول

- **Model**: `CustomNotification`
- **جدول**: `custom_notifications`

## الواجهات

- `resources/views/admin/pages/notifications/*`

## ملاحظات

- قنوات Laravel في [`routes/channels.php`](../../routes/channels.php) (`user.{userId}`, `notifications`, `approvals`).
