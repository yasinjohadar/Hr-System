# تسلسل الموافقات، حالة العرض، والملاحظات الاختيارية

**التاريخ:** 2026-06-03  
**الحالة:** مُنفَّذ

## المشكلة

بعد موافقة وسيطة (رئيس قسم / مدير تنفيذي) تظهر رسالة نجاح لكن الطلب يبقى «قيد الانتظار» لدى الأدمن والموظف ورئيس القسم. لا يوجد سجل بمن وافق ولا ملاحظات اختيارية.

## الحل

### 1) `workflow_step_actions`

سجل موحّد لكل موافقة/رفض: `workflow_instance_id`, `workflow_step_id`, `user_id`, `action`, `comments`, `acted_at`.

يُكتب في [`WorkflowService::processApproval`](app/Services/WorkflowService.php) لجميع أنواع [`approval_workflows.types`](config/approval_workflows.php).

### 2) حالة العرض

[`WorkflowProgressPresenter`](app/Services/WorkflowProgressPresenter.php) يُرجع `badge_ar` مثل «بانتظار موافقة المدير التنفيذي» مع الإبقاء على `entity.status = pending` حتى الاعتماد النهائي.

### 3) الخط الزمني

[`getWorkflowTimeline`](app/Services/WorkflowService.php) + partial [`workflow-approval-timeline`](resources/views/components/workflow-approval-timeline.blade.php) في: بوابة الموظف، admin show، approvals.

### 4) ملاحظات اختيارية

حقول `comments` / `rejection_reason` في نماذج الموافقة والرفض (max 2000).

### 5) رسائل الفلاش

[`approvalFlashMessage`](app/Services/WorkflowService.php): وسيطة vs نهائية vs رفض.

### 6) بيانات قديمة

`php artisan workflow:backfill-step-actions` للخطوات المكتملة بلا سجل.

### 7) المصروفات

إزالة إنشاء مكرر لـ `ExpenseApproval` من الـ controller؛ الاعتماد على `workflow_step_actions`.

## الاختبارات

- [`LeaveRequestWorkflowTest`](tests/Feature/LeaveRequestWorkflowTest.php): سجل + display status + flash
- [`ExpenseRequestWorkflowActionTest`](tests/Feature/ExpenseRequestWorkflowActionTest.php)

## علاقة بمواصفة سابقة

يكمل [تسلسل الرؤية والإجراء](2026-06-03-sequential-approval-visibility-design.md) (`canActOnCurrentStep`).
