# مستندات الموظفين

## الوصف

رفع وتصنيف مستندات HR للموظفين مع تنزيل آمن من الإدارة. عرض المستندات المتاحة للموظف في البوابة.

## المسارات

- `admin/employee-documents` (resource)
- `GET admin/employee-documents/{id}/download` → `admin.employee-documents.download`
- **موظف**: `GET employee/documents`

[`routes/admin.php`](../../routes/admin.php), [`routes/employee.php`](../../routes/employee.php)

## النماذج والجداول

- **Model**: `EmployeeDocument`
- **جدول**: `employee_documents`

## الواجهات

- `resources/views/admin/pages/employee-documents/*`
- واجهات الموظف تحت `employee/pages/self-service/`

## ملاحظات

- يُفضّل ضبط صلاحيات التخزين والوصول وسجل تدقيق للتنزيلات — [`plan/features/audit-logs.md`](audit-logs.md).
