# الموظفون ودليل الموظفين

## الوصف

CRUD لسجلات الموظفين وربطها بالمستخدمين والهيكل التنظيمي. أدوات إدارية: توليد كود دخول للموظف، والدخول باسم الموظف (انتحال). دليل موظفين قابل للبحث مع تصدير.

## المسارات

- `admin/employees` (resource)
- `POST admin/employees/{employee}/login-code` → `admin.employees.login-code`
- `POST admin/employees/{employee}/login-as` → `admin.employees.login-as`
- `GET admin/employee-directory` → `admin.employee-directory.index`
- `GET admin/employee-directory/export` → `admin.employee-directory.export`

[`routes/admin.php`](../../routes/admin.php)

**Controllers**: `EmployeeController`, `EmployeeDirectoryController`

## النماذج والجداول

- **Models**: `Employee`, `User` (ارتباط الحساب)
- **جدول**: `employees` (+ migrations مثل `branch_id` إن وُجدت)

## الواجهات

- `resources/views/admin/pages/employees/*`
- `resources/views/admin/pages/employee-directory/*`

## ملاحظات

- بوابة الموظف تعتمد على ربط `User` بدور `employee` — [`plan/features/employee-portal.md`](employee-portal.md).
