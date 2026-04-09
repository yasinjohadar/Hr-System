# المهارات والشهادات

## الوصف

سجل مهارات الموظفين مع إمكانية التحقق الإداري (`verify`)، وإدارة الشهادات المهنية. عرض المهارات والشهادات في بوابة الموظف.

## المسارات

- `admin/employee-skills` (resource)
- `POST admin/employee-skills/{id}/verify`
- `admin/employee-certificates` (resource)
- **موظف**: `GET employee/skills`, `GET employee/certificates`

[`routes/admin.php`](../../routes/admin.php), [`routes/employee.php`](../../routes/employee.php)

## النماذج والجداول

- **Models**: `EmployeeSkill`, `EmployeeCertificate`
- **جداول**: `employee_skills`, `employee_certificates`

## الواجهات

- `resources/views/admin/pages/employee-skills/*`, `employee-certificates/*`
- واجهات الموظف تحت `employee/pages/self-service/`

## ملاحظات

- تدعم تخطيط التعاقب والتوظيف الداخلي — [`plan/features/succession-planning.md`](succession-planning.md).
