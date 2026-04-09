# البيانات المرجعية والهيكل التنظيمي

## الوصف

إدارة الأقسام، الفروع، الدول، العملات، والمناصب الوظيفية كبيانات أساسية تُربط بسجلات الموظفين والرواتب والحضور وغيرها.

## المسارات

تحت `admin/*` (resource لكل كيان) — [`routes/admin.php`](../../routes/admin.php):

- `departments`
- `branches`
- `countries`
- `currencies`
- `positions`

## النماذج والجداول

| Model | جدول تقريبي |
|--------|-------------|
| `Department` | `departments` |
| `Branch` | `branches` |
| `Country` | `countries` |
| `Currency` | `currencies` |
| `Position` | `positions` |

الملفات: [`app/Models/`](../../app/Models/)

## الواجهات

- `resources/views/admin/pages/departments/*`
- `resources/views/admin/pages/branches/*`
- `resources/views/admin/pages/countries/*`
- `resources/views/admin/pages/currencies/*`
- `resources/views/admin/pages/positions/*`

## ملاحظات

- التصدير الجماعي متاح لعدة هذه الكيانات عبر `admin/export/*` — انظر [`plan/features/data-export.md`](data-export.md).
