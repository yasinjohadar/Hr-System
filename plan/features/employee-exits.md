# إنهاء الخدمة والخروج

## الوصف

إدارة عمليات إنهاء خدمة الموظف مع إكمال مقابلة خروج وموافقة إدارية.

## المسارات

- `admin/employee-exits` (resource)
- `POST admin/employee-exits/{id}/complete-interview`
- `POST admin/employee-exits/{id}/approve`

[`routes/admin.php`](../../routes/admin.php) — `EmployeeExitController`

## النماذج والجداول

- **Model**: `EmployeeExit`
- **جدول**: `employee_exits`

## الواجهات

- `resources/views/admin/pages/employee-exits/*`

## ملاحظات

- تقرير دوران الموظفين: `admin/reports/turnover` — [`plan/features/reports.md`](reports.md).
- يرتبط بأصول وعهدة وإجازات عند الإغلاق — [`plan/features/assets.md`](assets.md).
