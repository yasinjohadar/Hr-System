# Spec: صلاحيات الأدمن ورؤساء الأقسام

**التاريخ:** 2026-06-01  
**الحالة:** معتمد للتنفيذ

## أهداف

1. فصل بوابة الموظف (`/employee`) عن الصلاحيات الإدارية (Spatie على `/admin` فقط).
2. توحيد إدارة الفريق لرئيس القسم في `/admin/team/*` (لا `/employee/department-head`).
3. نطاق بيانات مركزي لرئيس القسم عبر `DepartmentScopeService`.
4. قوالب أدوار قابلة لإعادة التطبيق من واجهة الأدوار.

## مصفوفة Personas

| Persona | الأدوار | `/employee` | `/admin` | نطاق البيانات |
|---------|---------|-------------|----------|----------------|
| موظف | `employee` | نعم | لا | سجله الذاتي فقط |
| رئيس قسم | `employee` + `department_head` | نعم (ذاتي) | نعم (حسب Spatie) | `getManagedDepartmentIds()` + `getManagedEmployeeIds()` |
| مدير نظام | `admin` | اختياري | نعم | الكل |

## طبقات التحقق

1. `EnsureUserIsAdmin` — بوابة الإدارة.
2. `permission:*` — Spatie على الكنترولر.
3. `DepartmentScopeService` — تقييد استعلامات الموظفين/الأقسام.
4. `ApprovalService` / Workflow — موافقات السير.

## صلاحيات دور `department_head` (افتراضي)

- `employee-list`, `employee-show`
- `leave-request-list`, `leave-request-show`, `leave-request-approve`
- `attendance-list`, `attendance-show`
- `expense-request-list`, `expense-request-show`, `expense-request-approve`
- `performance-review-list`, `performance-review-show`, `performance-review-approve`
- `approval-list`, `approval-show`
- `employee-job-change-list`, `employee-job-change-show`
- تقارير محدودة + `dashboard-view`, `notification-list`, `notification-mark-read`

## مسارات إدارة الفريق (admin)

| المسار | الوظيفة |
|--------|---------|
| `GET admin/team/dashboard` | لوحة الفريق |
| `GET admin/team/members` | قائمة الموظفين |
| `GET admin/team/approvals` | موافقات معلقة |
| `GET admin/team/structure` | هيكل الأقسام |
| `GET admin/team/delegations` | التفويضات |

مسارات `/employee/department-head/*` → redirect 302 إلى المكافئ في admin.

## قوالب الأدوار

ملف `config/role-templates.php`: `department_head`, `hr_manager`, `payroll_officer` مع مصفوفة أسماء صلاحيات.

## معايير القبول

- رئيس قسم لا يرى موظفاً خارج نطاقه في أي شاشة HR مُحدَّثة.
- موظف بدور `employee` فقط لا يصل `/admin`.
- تغيير `departments.manager_id` يحدّث دور `department_head` (سلوك قائم).
