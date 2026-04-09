# السياسات والإقرار بها

## الوصف

نشر سياسات/لوائح داخلية وتتبع إقرار الموظفين بقراءتها والموافقة عليها.

## المسارات

- `admin/policies` (resource)
- `POST admin/policies/{policy}/acknowledge` — إقرار من واجهة الإدارة إن وُجد
- **موظف**: `GET employee/policies`, `POST employee/policies/acknowledge`

[`routes/admin.php`](../../routes/admin.php), [`routes/employee.php`](../../routes/employee.php)

## النماذج والجداول

- **Models**: `Policy`, `PolicyAcknowledgment`
- **جداول**: `policies`, `policy_acknowledgments`

## الواجهات

- `resources/views/admin/pages/policies/*`
- `resources/views/employee/pages/self-service/` (سياسات الموظف)

## ملاحظات

- مفيد للامتثال وتدقيق التدريب على السياسات — يرتبط بـ [`plan/features/audit-logs.md`](audit-logs.md) لأحداث حساسة.
