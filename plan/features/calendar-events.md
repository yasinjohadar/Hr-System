# أحداث التقويم

## الوصف

أحداث تقويم مشتركة (إجازات، فعاليات، مواعيد) مع نقطة نهاية JSON لعناصر واجهة تقويم (مثل FullCalendar).

## المسارات

- `admin/calendar-events` (resource)
- `GET admin/calendar-events/api/events` → `admin.calendar-events.api.events`

[`routes/admin.php`](../../routes/admin.php) — `CalendarEventController`

## النماذج والجداول

- **Model**: `CalendarEvent`
- **جدول**: `calendar_events`

## الواجهات

- `resources/views/admin/pages/calendar/*` (حسب تسمية المجلد في المشروع)

## ملاحظات

- تصدير: `admin/export/calendar-events` — [`plan/features/data-export.md`](data-export.md).
