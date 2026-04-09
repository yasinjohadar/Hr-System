# المشاريع والمهام

## الوصف

إدارة مشاريع ومهام مع تعيينات متعددة، تحديث حالة التعيين، تعليقات، ومرفقات (رفع وحذف). عرض المهام والمشاريع للموظف.

## المسارات

- `admin/projects` (resource)
- `admin/tasks` (resource)
- `POST admin/tasks/{id}/assign`
- `POST admin/tasks/{taskId}/assignments/{assignmentId}/update`
- `POST admin/tasks/{id}/add-comment`, `upload-attachment`
- `DELETE admin/tasks/{taskId}/attachments/{attachmentId}`
- **موظف**: `GET employee/tasks`, `GET employee/projects`, `GET employee/project-time`, `GET employee/projects/{project}`, `POST employee/projects/{project}/time`

[`routes/admin.php`](../../routes/admin.php), [`routes/employee.php`](../../routes/employee.php)

## النماذج والجداول

- **Models**: `Project`, `Task`, `TaskAssignment`, `TaskComment`, `TaskAttachment`
- **جداول**: `projects`, `tasks`, `task_assignments`, `task_comments`, `task_attachments`

## الواجهات

- `resources/views/admin/pages/projects/*`, `tasks/*`
- واجهات الموظف تحت `employee/pages/self-service/`

## ملاحظات

- تصدير: `admin/export/projects`, `tasks` — [`plan/features/data-export.md`](data-export.md).

---

## إضافات: فريق المشروع والوقت والتوثيق

### الوصف

توسيع المشروع بفريق صريح (`project_members`)، مرفقات/مستندات (`project_documents`)، وسجلات وقت (`project_time_entries`) مع واجهات إدارة في صفحة تفاصيل المشروع، وبوابة موظف لتسجيل الوقت وعرض السجلات، وتصدير CSV للإدارة.

### الجداول والنماذج

| جدول | نموذج |
|------|--------|
| `project_members` | `ProjectMember` |
| `project_documents` | `ProjectDocument` |
| `project_time_entries` | `ProjectTimeEntry` |

علاقات إضافية على `Project`: `members`, `memberEmployees`, `documents`, `timeEntries`؛ دوال مساعدة `employeeCanParticipate(Employee)`, `allowsTimeLogging()`.

### مسارات الإدارة (تحت بادئة `admin`)

- `GET projects/{project}/time-entries/export` — تصدير سجلات الوقت CSV (`ProjectController@exportTimeEntries`)
- `POST projects/{project}/members` — `ProjectMemberController@store`
- `DELETE projects/{project}/members/{member}` — `ProjectMemberController@destroy`
- `POST projects/{project}/documents` — `ProjectDocumentController@store`
- `DELETE projects/{project}/documents/{document}` — `ProjectDocumentController@destroy`
- `POST projects/{project}/time-entries` — `ProjectTimeEntryController@store`
- `DELETE projects/{project}/time-entries/{timeEntry}` — `ProjectTimeEntryController@destroy`

تفاصيل المشروع: `ProjectController@show` يحمّل الفريق والمستندات وسجلات الوقت ومجموع الساعات؛ الواجهة في `resources/views/admin/pages/projects/show.blade.php` (تبويبات).

### مسارات الموظف

- `GET employee/project-time` — قائمة سجلات وقت الموظف مع فلترة
- `GET employee/projects/{project}` — تفاصيل مشروع يشارك فيه الموظف
- `POST employee/projects/{project}/time` — تسجيل وقت

### الصلاحيات

- تعديل الفريق/المستندات/سجلات الوقت (إداري): `project-edit`
- عرض المشروع وتصدير CSV: `project-show`

### إصلاح الخدمة الذاتية

في [`app/Http/Controllers/Employee/SelfServiceController.php`](../../app/Http/Controllers/Employee/SelfServiceController.php):

- `projects()`: المشاريع الظاهرة = مدير المشروع **أو** عضو في `project_members` **أو** معيّن على مهمة عبر `tasks.assignments` (لا يُستخدم عمود `assigned_to` غير الموجود على `tasks`).
- `tasks()`: المهام عبر `whereHas('assignments', …)` فقط.

### تشغيل

- بعد سحب التغييرات: `php artisan migrate`
- مرفقات المستندات على القرص العام: `php artisan storage:link` إن لزم
