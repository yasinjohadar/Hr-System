# تقييم الأداء

## الوصف

دورة تقييم أداء رسمية للموظفين مع إجراءات موافقة/رفض من الإدارة. عرض سجل التقييمات في بوابة الموظف.

## المسارات

- `admin/performance-reviews` (resource)
- `POST admin/performance-reviews/{id}/approve`, `/reject`
- **موظف**: `GET employee/performance-reviews`

[`routes/admin.php`](../../routes/admin.php), [`routes/employee.php`](../../routes/employee.php)

## النماذج والجداول

- **Model**: `PerformanceReview`
- **جدول**: `performance_reviews`

## الواجهات

- `resources/views/admin/pages/performance-reviews/*`
- واجهات الموظف تحت `employee/pages/self-service/`

## ملاحظات

- تقرير الأداء: `admin/reports/performance` — [`plan/features/reports.md`](reports.md).
