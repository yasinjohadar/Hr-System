# الحضور والدوام والقواعد والمواقع والاستراحات

## الوصف

تسجيل الحضور والانصراف (يدوي أو عبر check-in/out)، إدارة الورديات وتعيينها للموظفين، قواعد الحضور، مواقع جغرافية (GPS) للتحقق، وسجلات فترات الاستراحة.

## المسارات

- `admin/attendances` (resource)
- `POST admin/attendances/{employeeId}/check-in`, `check-out`
- `admin/shifts`, `shift-assignments`, `attendance-rules` (resources)
- `admin/attendance-locations`, `attendance-breaks` (resources)
- **موظف**: `GET employee/attendance`

[`routes/admin.php`](../../routes/admin.php), [`routes/employee.php`](../../routes/employee.php)

## النماذج والجداول

- **Models**: `Attendance`, `Shift`, `ShiftAssignment`, `AttendanceRule`, `AttendanceLocation`, `AttendanceBreak`
- **جداول**: `attendances`, `shifts`, `shift_assignments`, `attendance_rules`, `attendance_locations`, `attendance_breaks`

## الواجهات

- `resources/views/admin/pages/attendances/*`, `shifts/*`, `shift-assignments/*`, `attendance-rules/*`, `attendance-locations/*`, `attendance-breaks/*`
- `resources/views/employee/pages/self-service/attendance.blade.php`

## ملاحظات

- العمل الإضافي منفصل — [`plan/features/overtime.md`](overtime.md).
- قد توجد حقول GPS على `attendances` عبر migrations.
