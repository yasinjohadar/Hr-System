# المزايا والتعويضات غير النقدية

## الوصف

تعريف أنواع المزايا وتسجيل مزايا مرتبطة بموظف محدد. عرض المزايا في بوابة الموظف.

## المسارات

- `admin/benefit-types` (resource)
- `admin/employee-benefits` (resource)
- **موظف**: `GET employee/benefits`

[`routes/admin.php`](../../routes/admin.php), [`routes/employee.php`](../../routes/employee.php)

## النماذج والجداول

- **Models**: `BenefitType`, `EmployeeBenefit`
- **جداول**: `benefit_types`, `employee_benefits`

## الواجهات

- `resources/views/admin/pages/benefit-types/*`, `employee-benefits/*`
- واجهات الموظف تحت `employee/pages/self-service/`

## ملاحظات

- تقرير: `admin/reports/benefits` — [`plan/features/reports.md`](reports.md).
