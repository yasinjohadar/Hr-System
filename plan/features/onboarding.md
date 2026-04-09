# الاستقبال والتأهيل (Onboarding)

## الوصف

قوالب استقبال وعمليات مرتبطة بموظف جديد مع مهام وقوائم تحقق (`OnboardingTask`, `OnboardingChecklist`).

## المسارات

- `admin/onboarding-templates` (resource)
- `admin/onboarding-processes` (resource)

[`routes/admin.php`](../../routes/admin.php)

## النماذج والجداول

- **Models**: `OnboardingTemplate`, `OnboardingProcess`, `OnboardingTask`, `OnboardingChecklist`
- **جداول**: `onboarding_templates`, `onboarding_processes`, `onboarding_tasks`, `onboarding_checklists`

## الواجهات

- `resources/views/admin/pages/onboarding-templates/*`, `onboarding-processes/*`

## ملاحظات

- واجهة موظف لمهام الاستقبال غير مدرجة صراحة في مسارات الموظف — مرشّح للتوسعة.
