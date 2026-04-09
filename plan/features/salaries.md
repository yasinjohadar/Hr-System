# الرواتب الأساسية

## الوصف

سجلات الراتب الأساسي أو المعلومات التعويضية المرتبطة بالموظف (حسب منطق المشروع) لاستخدامها في التقارير والمسيرات.

## المسارات

- `admin/salaries` (resource) — `SalaryController`
- **موظف**: `GET employee/salaries` — `SelfServiceController`

[`routes/admin.php`](../../routes/admin.php), [`routes/employee.php`](../../routes/employee.php)

## النماذج والجداول

- **Model**: `Salary`
- **جدول**: `salaries`

## الواجهات

- `resources/views/admin/pages/salaries/*`
- `resources/views/employee/pages/self-service/salaries.blade.php`

## ملاحظات

- يُكمّلها نظام المسيرات والمكونات والضرائب — [`plan/features/payroll.md`](payroll.md).
