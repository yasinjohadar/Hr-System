# الاجتماعات

## الوصف

جدولة اجتماعات وإدارة الحضور (نموذج `MeetingAttendee`). عرض الاجتماعات ذات الصلة للموظف.

## المسارات

- `admin/meetings` (resource) — `MeetingController`
- **موظف**: `GET employee/meetings`

[`routes/admin.php`](../../routes/admin.php), [`routes/employee.php`](../../routes/employee.php)

## النماذج والجداول

- **Models**: `Meeting`, `MeetingAttendee`
- **جداول**: `meetings`, `meeting_attendees`

## الواجهات

- `resources/views/admin/pages/meetings/*`
- واجهات الموظف تحت `employee/pages/self-service/`

## ملاحظات

- تصدير: `admin/export/meetings` — [`plan/features/data-export.md`](data-export.md).
