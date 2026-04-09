# العمل الإضافي (Overtime)

## الوصف

إدارة سجلات العمل الإضافي للموظفين مع موافقة/رفض، وحساب مرتبط بالحضور (`calculate-from-attendance`).

## المسارات

- `admin/overtimes` (resource)
- `POST admin/overtimes/{id}/approve`, `/reject`
- `POST admin/overtimes/calculate-from-attendance` → `admin.overtimes.calculate-from-attendance`

[`routes/admin.php`](../../routes/admin.php) — `OvertimeController`

## النماذج والجداول

- **Model**: `OvertimeRecord`
- **جدول**: `overtime_records`

## الواجهات

- `resources/views/admin/pages/overtimes/*`

## ملاحظات

- تصدير: `admin/export/overtimes` — [`plan/features/data-export.md`](data-export.md).
- يرتبط بالرواتب/المسيرات عند احتساب المستحقات — [`plan/features/payroll.md`](payroll.md).
