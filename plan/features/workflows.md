# سير العمل (تعريف عام)

## الوصف

تعريف مسارات عمل متعددة الخطوات وتتبع حالات التنفيذ (طبقة عامة يمكن ربطها بعمليات HR).

## المسارات

- `admin/workflows` (resource) — `WorkflowController`

[`routes/admin.php`](../../routes/admin.php)

## النماذج والجداول

- **Models**: `Workflow`, `WorkflowStep`, `WorkflowInstance`
- **جداول**: `workflows`, `workflow_steps`, `workflow_instances`

## الواجهات

- `resources/views/admin/pages/workflows/*`

## ملاحظات

- التكامل العميق مع كل الوحدات (إجازات، مسيرات، توظيف) يحدد مدى نضج النظام — انظر [`plan/ideal-system-roadmap.md`](../ideal-system-roadmap.md).
