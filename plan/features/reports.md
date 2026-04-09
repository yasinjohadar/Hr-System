# التقارير والتحليلات

## الوصف

مجموعة تقارير جاهزة للقوى العاملة: موظفون، حضور، رواتب، إجازات، أداء، تدريب، توظيف، مزايا، لوحة تحليلات، دوران، فعالية تدريب، ومؤشرات أداء.

## المسارات

تحت `admin/reports/*` — [`routes/admin.php`](../../routes/admin.php):

- `/` index
- `employees`, `attendance`, `salaries`, `leaves`, `performance`, `training`, `recruitment`, `benefits`, `dashboard`, `turnover`, `training-effectiveness`, `kpis`

**Controller**: `ReportController`

## النماذج والجداول

- قد يُستخدم نموذج `Report` لتقارير محفوظة/مجدولة إن وُجد في الكود — [`app/Models/Report.php`](../../app/Models/Report.php)

## الواجهات

- `resources/views/admin/pages/reports/*`

## ملاحظات

- للتصدير الخام انظر [`plan/features/data-export.md`](data-export.md).
