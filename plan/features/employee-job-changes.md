# التغييرات الوظيفية (نقل / ترقية)

## الوصف

تسجيل طلبات أو قرارات بتغيير المنصب/القسم/الهيكل للموظف مع مسار موافقة أو رفض من الإدارة. لا يوجد حذف عبر المورد (ما عدا الاستثناءات في المسار).

## المسارات

- `admin/employee-job-changes` (resource **بدون** `destroy`)
- `POST admin/employee-job-changes/{employee_job_change}/approve`
- `POST admin/employee-job-changes/{employee_job_change}/reject`

[`routes/admin.php`](../../routes/admin.php) — `EmployeeJobChangeController`

## النماذج والجداول

- **Model**: `EmployeeJobChange`
- **جدول**: `employee_job_changes`

## الواجهات

- `resources/views/admin/pages/employee-job-changes/*`

## ملاحظات

- قد يتداخل مع مركز الموافقات — [`plan/features/approvals-hub.md`](approvals-hub.md).
