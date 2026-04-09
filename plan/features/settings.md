# الإعدادات العامة

## الوصف

إعدادات مجمّعة حسب مجموعة (`group`) مع تحديث جماعي أو تحديث مفتاح واحد.

## المسارات

- `GET admin/settings` → `admin.settings.index`
- `GET admin/settings/{group}` → `admin.settings.group`
- `PUT admin/settings/{group}` → `admin.settings.update-group`
- `PUT admin/settings/{id}/update` → `admin.settings.update`

[`routes/admin.php`](../../routes/admin.php) — `SettingController`

## النماذج والجداول

- **Model**: `Setting`
- **جدول**: `settings`

## الواجهات

- `resources/views/admin/pages/settings/*`

## ملاحظات

- يفضّل توثيق مفاتيح الإعدادات المدعومة في الكود أو لوحة داخلية لتفادي القيم غير المتوقعة.
