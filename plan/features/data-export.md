# التصدير الجماعي (Export)

## الوصف

صفحة فهرس للتصدير ومسارات `GET` لكل كيان رئيسي (CSV/Excel حسب `ExportController`) لدعم التحليل والنسخ الاحتياطي التشغيلي.

## المسارات

- `GET admin/export` → عرض `admin.pages.export.index`
- بادئة `admin/export/*` تشمل على سبيل المثال: `employees`, `departments`, `branches`, `positions`, `salaries`, `leave-types`, `leave-requests`, `leave-balances`, `attendances`, `payrolls`, `payroll-payments`, `trainings`, `training-records`, `performance-reviews`, `job-vacancies`, `candidates`, `job-applications`, `interviews`, `benefit-types`, `employee-benefits`, `expense-categories`, `expense-requests`, `assets`, `asset-assignments`, `asset-maintenances`, `violation-types`, `disciplinary-actions`, `employee-violations`, `projects`, `tasks`, `tickets`, `meetings`, `calendar-events`, `shifts`, `overtimes`, `bank-accounts`, `tax-settings`, `salary-components`

التفاصيل الكاملة في [`routes/admin.php`](../../routes/admin.php) (مجموعة `export`).

## النماذج والجداول

عابر لجميع الكيانات — [`App\Http\Controllers\Admin\ExportController`](../../app/Http/Controllers/Admin/ExportController.php)

## الواجهات

- `resources/views/admin/pages/export/index.blade.php`

## ملاحظات

- لا يغني عن استراتيجية نسخ احتياطي لقاعدة البيانات والملفات — [`plan/ideal-system-roadmap.md`](../ideal-system-roadmap.md).
