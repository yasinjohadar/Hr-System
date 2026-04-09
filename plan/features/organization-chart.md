# المخطط التنظيمي

## الوصف

عرض تسلسل هرمي للهيكل (موظفون/أقسام/مناصب حسب منطق الـ Controller) مع واجهة تقرأ بيانات JSON.

## المسارات

- `GET admin/organization-chart` → `admin.organization-chart.index`
- `GET admin/organization-chart/get-data` → `admin.organization-chart.get-data`

[`routes/admin.php`](../../routes/admin.php) — `OrganizationChartController`

## النماذج والجداول

يعتمد على `Employee`, `Department`, `Position` (بدون جدول منفصل للمخطط).

## الواجهات

- `resources/views/admin/pages/organization-chart/*`

## ملاحظات

- مكمّل لـ [`plan/features/master-data-org.md`](master-data-org.md) و[`plan/features/employees-and-directory.md`](employees-and-directory.md).
