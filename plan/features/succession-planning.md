# تخطيط التعاقب الوظيفي

## الوصف

تخطيط مناصب رئيسية ومرشحي تعاقب مع تقييم الجاهزية (حسب حقول النموذج).

## المسارات

- `admin/succession-plans` (resource) — `SuccessionPlanController`

[`routes/admin.php`](../../routes/admin.php)

## النماذج والجداول

- **Models**: `SuccessionPlan`, `SuccessionCandidate`
- **جداول**: `succession_plans`, `succession_candidates`

## الواجهات

- `resources/views/admin/pages/succession-plans/*`

## ملاحظات

- يرتبط بالمهارات والتدريب والتقييم — [`plan/features/skills-and-certificates.md`](skills-and-certificates.md), [`plan/features/training.md`](training.md).
