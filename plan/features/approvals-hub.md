# مركز الموافقات

## الوصف

صفحة موحّدة لعرض عناصر تحتاج موافقة مع تفاصيل حسب النوع (`type`) والمعرّف (`id`).

## المسارات

- `GET admin/approvals` → `admin.approvals.index`
- `GET admin/approvals/{type}/{id}` → `admin.approvals.show`

[`routes/admin.php`](../../routes/admin.php) — `ApprovalController`

## النماذج والجداول

نماذج متعددة حسب `type` (إجازات، مسيرات، مخالفات، إلخ) — لا يوجد جدول واحد إلزامي.

## الواجهات

- `resources/views/admin/pages/approvals/*`

## ملاحظات

- يرتبط بـ [`routes/channels.php`](../../routes/channels.php) لقناة `approvals` إن وُجدت للبث الفوري.
