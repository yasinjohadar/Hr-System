# التوظيف (طلبات، وظائف، مرشحون، طلبات توظيف، مقابلات، عروض)

## الوصف

مسار التوظيف من طلب التوظيف (requisition) مع موافقة/رفض، نشر شواغر، مرشحون، طلبات التقديم، جدولة مقابلات، وإدارة خطابات العرض مع إرسال وقبول/رفض.

## المسارات

- `admin/requisitions` (+ approve/reject)
- `admin/job-vacancies`, `candidates`, `job-applications`, `interviews` (resources)
- `admin/offer-letters` (resource) + `send`, `accept`, `reject`

[`routes/admin.php`](../../routes/admin.php)

## النماذج والجداول

- **Models**: `Requisition`, `JobVacancy`, `Candidate`, `JobApplication`, `Interview`, `OfferLetter`
- **جداول**: `requisitions`, `job_vacancies`, `candidates`, `job_applications`, `interviews`, `offer_letters`

## الواجهات

- `resources/views/admin/pages/requisitions/*`, `job-vacancies/*`, `candidates/*`, `job-applications/*`, `interviews/*`, `offer-letters/*`

## ملاحظات

- تقرير التوظيف: `admin/reports/recruitment` — [`plan/features/reports.md`](reports.md).
- بوابة توظيف عامة للمتقدمين غير مذكورة في المسارات الحالية — مرشّح في [`plan/ideal-system-roadmap.md`](../ideal-system-roadmap.md).
