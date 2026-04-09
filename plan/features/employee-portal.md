# بوابة الموظف (Self-Service) والدخول بالرمز

## الوصف

واجهة موظف محدودة الصلاحية (`ensure.employee`) لعرض وطلب خدمات HR: لوحة تحكم، ملف شخصي، إجازات، حضور، رواتب، مستندات، مهارات، شهادات، أهداف، تقييمات، مزايا، مهام، مشاريع، تذاكر، اجتماعات، مصروفات، أصول، مخالفات، سياسات، عقد، إعلانات، سجل تدريب، وقسيمة راتب PDF. **دخول بالرمز** بدون حساب كامل عبر مسار ضيف.

## المسارات

**ضيف** (بدون `auth`):

- `GET|POST employee/login-by-code` — `LoginByCodeController`

**موظف مصادق** (`auth`, `check.user.active`, `ensure.employee`):

- `employee/dashboard`, `profile` (+ `PUT` update)
- `leaves`, `POST leaves/request`
- `attendance`, `salaries`, `documents`, `skills`, `certificates`, `goals`, `performance-reviews`, `benefits`
- `tasks`, `projects`
- `tickets` (+ create/store)
- `meetings`
- `expense-requests` (+ create/store)
- `assets`, `violations`
- `policies` (+ `POST acknowledge`)
- `contract`
- `payrolls/{id}/payslip/pdf`
- `announcements`, `training-records`

[`routes/employee.php`](../../routes/employee.php) — `SelfServiceController` (وبعض الإجراءات في controllers أخرى حسب التوجيه)

## النماذج والجداول

يعتمد على معظم نماذج HR للقراءة أو الكتابة المحدودة — راجع ملفات الميزات الفردية في [`plan/features/`](./).

## الواجهات

- تخطيطات: `resources/views/employee/layouts/*`
- صفحات: `resources/views/employee/pages/self-service/*`
- `resources/views/employee/pages/login-by-code*` (إن وُجدت)

## ملاحظات

- توليد الرمز من الإدارة: [`plan/features/employees-and-directory.md`](employees-and-directory.md).
- الجذر `/` يوجّه دور `employee` إلى `employee.dashboard` — [`routes/web.php`](../../routes/web.php).
- من لديهم دور **`department_head`** يرون في الشريط الجانبي رابط **إدارة الفريق** إلى لوحة الإدارة؛ التشغيل موثّق في [`plan/department-head-runtime.md`](../department-head-runtime.md).
