# التذاكر (دعم داخلي)

## الوصف

نظام تذاكر للطلبات أو المشكلات مع تعيين وإغلاق/حل. الموظف يعرض التذاكر وينشئ تذكرة جديدة.

## المسارات

- `admin/tickets` (resource)
- `POST admin/tickets/{id}/assign`, `/resolve`
- **موظف**: `GET employee/tickets`, `create`, `POST store`

[`routes/admin.php`](../../routes/admin.php), [`routes/employee.php`](../../routes/employee.php)

## النماذج والجداول

- **Models**: `Ticket`, `TicketComment`
- **جداول**: `tickets`, `ticket_comments`

## الواجهات

- `resources/views/admin/pages/tickets/*`
- `resources/views/employee/pages/self-service/ticket-create.blade.php` وصفحات القائمة

## ملاحظات

- تصدير: `admin/export/tickets` — [`plan/features/data-export.md`](data-export.md).
