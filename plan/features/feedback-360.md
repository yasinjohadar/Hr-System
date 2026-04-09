# طلبات الملاحظات (360°)

## الوصف

إنشاء طلبات جمع ملاحظات متعددة المصادر وتخزين الاستجابات المرتبطة بها (تقييم 360 درجة).

## المسارات

- `admin/feedback-requests` (resource) — `FeedbackRequestController`

[`routes/admin.php`](../../routes/admin.php)

## النماذج والجداول

- **Models**: `FeedbackRequest`, `FeedbackResponse`
- **جداول**: `feedback_requests`, `feedback_responses`

## الواجهات

- `resources/views/admin/pages/feedback-requests/*`

## ملاحظات

- لا يوجد مسار موظف مباشر في [`routes/employee.php`](../../routes/employee.php) لهذه الوحدة؛ التفاعل قد يكون عبر روابط/واجهات أخرى حسب التنفيذ في الـ Controller.
