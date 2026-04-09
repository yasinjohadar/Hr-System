# الإجازات (الأنواع، الأرصدة، الطلبات)

## الوصف

تعريف أنواع الإجازات، إدارة أرصدة الإجازة لكل موظف/فترة، ومعالجة طلبات الإجازة (موافقة/رفض). الموظف يقدم طلبًا ويعرض حالته من البوابة.

## المسارات

- `admin/leave-types` (resource)
- `admin/leave-balances` (resource)
- `admin/leave-requests` (resource)
- `POST admin/leave-requests/{id}/approve`, `/reject`
- **موظف**: `GET employee/leaves`, `POST employee/leaves/request`

[`routes/admin.php`](../../routes/admin.php), [`routes/employee.php`](../../routes/employee.php)

## النماذج والجداول

- **Models**: `LeaveType`, `LeaveBalance`, `LeaveRequest`
- **جداول**: `leave_types`, `leave_balances`, `leave_requests`

## الواجهات

- `resources/views/admin/pages/leave-types/*`, `leave-balances/*`, `leave-requests/*`
- `resources/views/employee/pages/self-service/` (إجازات)

## ملاحظات

- تقارير الإجازات: `admin/reports/leaves` — [`plan/features/reports.md`](reports.md).
- تصدير: `admin/export/leave-*` — [`plan/features/data-export.md`](data-export.md).
