# التدريب وسجلات التدريب

## الوصف

كتالوج برامج/دورات تدريبية وسجلات إتمام أو حضور لكل موظف. عرض سجل التدريب للموظف في البوابة.

## المسارات

- `admin/trainings` (resource)
- `admin/training-records` (resource)
- **موظف**: `GET employee/training-records`

[`routes/admin.php`](../../routes/admin.php), [`routes/employee.php`](../../routes/employee.php)

## النماذج والجداول

- **Models**: `Training`, `TrainingRecord`
- **جداول**: `trainings`, `training_records`

## الواجهات

- `resources/views/admin/pages/trainings/*`, `training-records/*`
- واجهات الموظف تحت `employee/pages/self-service/`

## ملاحظات

- تقارير: `admin/reports/training`, `training-effectiveness` — [`plan/features/reports.md`](reports.md).
