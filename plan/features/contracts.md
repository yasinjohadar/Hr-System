# عقود العمل

## الوصف

إدارة دورة حياة عقود الموظفين بما فيها تجديد العقد (عرض نموذج + حفظ التجديد).

## المسارات

- `GET|POST admin/contracts/{contract}/renew` و `store-renew`
- `admin/contracts` (resource)

[`routes/admin.php`](../../routes/admin.php) — `ContractController`

**بوابة الموظف**: `GET employee/contract` — [`routes/employee.php`](../../routes/employee.php)

## النماذج والجداول

- **Model**: `Contract`
- **جدول**: `contracts`

## الواجهات

- `resources/views/admin/pages/contracts/*`
- `resources/views/employee/pages/self-service/` (ملف عقد الموظف إن وُجد)

## ملاحظات

- ربط بقوالب المستندات للاتصالات الرسمية — [`plan/features/email-document-templates.md`](email-document-templates.md).
