# قوالب البريد والمستندات

## الوصف

قوالب رسائل بريد قابلة لإعادة الاستخدام وقوالب مستندات (عقود، خطابات، إلخ) لتوحيد الاتصالات الرسمية.

## المسارات

- `admin/email-templates` (resource) — `EmailTemplateController`
- `admin/document-templates` (resource) — `DocumentTemplateController`

[`routes/admin.php`](../../routes/admin.php)

## النماذج والجداول

- **Models**: `EmailTemplate`, `DocumentTemplate`
- **جداول**: `email_templates`, `document_templates`

## الواجهات

- `resources/views/admin/pages/email-templates/*`, `document-templates/*`

## ملاحظات

- يرتبط بالتوظيف (`offer-letters`) والعقود والإشعارات — [`plan/features/recruitment.md`](recruitment.md), [`plan/features/contracts.md`](contracts.md).
