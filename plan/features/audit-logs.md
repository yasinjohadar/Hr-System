# سجل التدقيق

## الوصف

عرض سجلات أحداث حساسة للامتثال والمراجعة الداخلية مع تصدير.

## المسارات

- `admin/audit-logs` (resource **فقط** index, show)
- `GET admin/audit-logs/export` → `admin.audit-logs.export`

[`routes/admin.php`](../../routes/admin.php) — `AuditLogController`

## النماذج والجداول

- **Model**: `AuditLog`
- **جدول**: `audit_logs`

## الواجهات

- `resources/views/admin/pages/audit-logs/*`

## ملاحظات

- وسّع التغطية لعمليات المسيرات والمستندات والصلاحيات حسب سياسة الأمن — [`plan/ideal-system-roadmap.md`](../ideal-system-roadmap.md).
