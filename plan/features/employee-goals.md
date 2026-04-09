# أهداف الموظفين

## الوصف

تعريف ومتابعة أهداف فردية للموظفين (أهداف أداء/تطوير). عرض الأهداف في بوابة الموظف.

## المسارات

- `admin/employee-goals` (resource) — `EmployeeGoalController`
- **موظف**: `GET employee/goals`

[`routes/admin.php`](../../routes/admin.php), [`routes/employee.php`](../../routes/employee.php)

## النماذج والجداول

- **Model**: `EmployeeGoal`
- **جدول**: `employee_goals`

## الواجهات

- `resources/views/admin/pages/employee-goals/*`
- واجهات الموظف تحت `employee/pages/self-service/`

## ملاحظات

- يكمّل وحدة تقييم الأداء — [`plan/features/performance-reviews.md`](performance-reviews.md).
