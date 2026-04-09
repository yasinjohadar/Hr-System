# الأصول والعهدة والصيانة

## الوصف

تسجيل أصول الشركة، تعيينها للموظفين مع إرجاع رسمي، وتسجيل أعمال الصيانة. عرض الأصول المعيّنة للموظف في البوابة. **سجل حياة زمني موحّد** للأصل في لوحة الإدارة (أحداث تلقائية + ملاحظات ومرفقات يدوية).

## المسارات

- `admin/assets` (resource)
- `POST admin/assets/{asset}/lifecycle-events` — إضافة ملاحظة/مرفقات للسجل الزمني (`AssetController@storeLifecycleEvent`)، صلاحية `asset-edit`
- `admin/asset-assignments` (resource)
- `GET|POST admin/asset-assignments/{id}/return` (نموذج + تنفيذ)
- `admin/asset-maintenances` (resource)
- **موظف**: `GET employee/assets` (بدون عرض السجل الزمني الكامل حسب التصميم الحالي)

[`routes/admin.php`](../../routes/admin.php), [`routes/employee.php`](../../routes/employee.php)

## النماذج والجداول

- **Models**: `Asset`, `AssetAssignment`, `AssetMaintenance`, `AssetLifecycleEvent`, `AssetLifecycleAttachment`
- **جداول**: `assets`, `asset_assignments`, `asset_maintenances`, `asset_lifecycle_events`, `asset_lifecycle_attachments`

### السجل الزمني (`asset_lifecycle_events`)

- أنواع أحداث شائعة: `created`, `status_changed`, `branch_changed`, `department_changed`, `photo_updated`, `assignment_started`, `assignment_returned`, `maintenance_recorded`, `maintenance_status_changed`, `manual_note`
- حقول: `occurred_at`, `user_id`, `employee_id` (اختياري), `related_assignment_id`, `related_maintenance_id`, `summary`, `meta` (JSON)
- المرفقات مرتبطة بالحدث في `asset_lifecycle_attachments` (تخزين `public` تحت `asset_lifecycle/{asset_id}/`)

التسجيل التلقائي عبر [`App\Services\AssetLifecycleRecorder`](../../app/Services/AssetLifecycleRecorder.php) من:

- إنشاء/تحديث الأصل ([`AssetController`](../../app/Http/Controllers/Admin/AssetController.php))
- التوزيع والاسترجاع ([`AssetAssignmentController`](../../app/Http/Controllers/Admin/AssetAssignmentController.php))
- إنشاء/تحديث حالة الصيانة ([`AssetMaintenanceController`](../../app/Http/Controllers/Admin/AssetMaintenanceController.php))

### استيراد البيانات القديمة

```bash
php artisan assets:backfill-lifecycle-events
```

- يضيف أحداث `created` للأصول بلا حدث إنشاء، وأحداث توزيع/استرجاع وصيانة من السجلات الحالية عند عدم وجود حدث مطابق.
- الخيار `--force` يحذف الأحداث التي وُسِمت في `meta` بـ `backfill: true` ثم يعيد التوليد.

## الواجهات

- `resources/views/admin/pages/assets/*` — صفحة العرض تتضمن تبويب **السجل الزمني** ونموذج ملاحظة/مرفقات
- `asset-assignments/*`, `asset-maintenances/*`
- `resources/views/employee/pages/self-service/assets.blade.php`

## ملاحظات

- تصدير: `admin/export/assets*`, `asset-assignments`, `asset-maintenances` — [`plan/features/data-export.md`](data-export.md).
- مرفقات السجل: `php artisan storage:link` إن لزم.
- عرض السجل الزمني الكامل **للإدارة فقط**؛ البوابة الذاتية تعرض قائمة الأصول المعيّنة دون الخط الزمني.
