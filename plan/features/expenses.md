# المصروفات (الفئات والطلبات)

## الوصف

فئات مصروفات وطلبات تعويض من الموظفين مع مسار موافقة (نموذج + POST) ورفض وتحديد كمدفوع. الموظف ينشئ ويعرض طلباته.

## المسارات

- `admin/expense-categories` (resource)
- `admin/expense-requests` (resource)
- `GET|POST admin/expense-requests/{id}/approve`, `reject`, `pay`
- **موظف**: `GET employee/expense-requests`, `create`, `POST store`

[`routes/admin.php`](../../routes/admin.php), [`routes/employee.php`](../../routes/employee.php)

## النماذج والجداول

- **Models**: `ExpenseCategory`, `ExpenseRequest`, `ExpenseApproval`
- **جداول**: `expense_categories`, `expense_requests`, `expense_approvals`

## الواجهات

- `resources/views/admin/pages/expense-categories/*`, `expense-requests/*`
- واجهات الموظف تحت `employee/pages/self-service/`

## ملاحظات

- تصدير: `admin/export/expense-*` — [`plan/features/data-export.md`](data-export.md).
