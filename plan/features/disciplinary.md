# المخالفات والإجراءات التأديبية

## الوصف

تعريف أنواع المخالفات والإجراءات التأديبية، وتتبع مخالفات الموظفين عبر مراحل: تحقيق، تأكيد، رفض، موافقة، وتطبيق إجراء.

## المسارات

- `admin/violation-types`, `disciplinary-actions` (resources)
- `admin/employee-violations` (resource)
- `POST .../investigate`, `/confirm`, `/dismiss`, `/approve`, `/apply-action`
- **موظف**: `GET employee/violations` (عرض لموظفه)

[`routes/admin.php`](../../routes/admin.php), [`routes/employee.php`](../../routes/employee.php)

## النماذج والجداول

- **Models**: `ViolationType`, `DisciplinaryAction`, `EmployeeViolation`
- **جداول**: `violation_types`, `disciplinary_actions`, `employee_violations`

## الواجهات

- `resources/views/admin/pages/violation-types/*`, `disciplinary-actions/*`, `employee-violations/*`
- واجهات الموظف تحت `employee/pages/self-service/`

## ملاحظات

- بيانات حساسة قانونيًا؛ راجع السجلات والصلاحيات — [`plan/features/audit-logs.md`](audit-logs.md).
