# تسلسل ظهور وموافقة طلبات الإجازة والمصروفات

**التاريخ:** 2026-06-03  
**الحالة:** مُنفَّذ

## المشكلة

عند تقديم إجازة من بوابة الموظف، كان الطلب يظهر لرئيس القسم والمدير التنفيذي والمدير العام (والأدمن) مع أزرار موافقة في نفس الوقت، رغم أن سير العمل يحدد خطوة نشطة واحدة.

## السبب الجذري

1. واجهة الإدارة (`leave-requests` / `expense-requests`) تعرض أزرار الموافقة لأي مستخدم يملك `*-approve` دون التحقق من `WorkflowInstance.currentStep`.
2. `ApprovalService::canUserApprove` كان يسمح بـ `leave-request-approve-all` / `expense-request-approve-all` على أي خطوة.
3. `ApprovalController` يستخدم fallback هرمي عند غياب instance فيعرض الطلب لعدة أدوار دفعة واحدة.

## القرار المعتمد (view_only لـ approve-all)

| السلوك | التفاصيل |
|--------|----------|
| الرؤية | من يملك `*-list` أو `approve-all` يرى الطلبات المعلقة في القوائم العامة للمراقبة |
| الإجراء | الموافقة/الرفض فقط لمن يطابق موافق **الخطوة الحالية** (أو مفوّضه) |
| approve-all | لا يتجاوز التسلسل؛ لا يمنح `canActOnCurrentStep` |

## التصميم التقني

### ApprovalService

- `canActOnCurrentStep()` — مطابقة موافق الخطوة + تفويض، **بدون** approve-all.
- `canActOnEntity()` — يحل instance `in_progress` ويستدعي `canActOnCurrentStep` على `currentStep.step_order`.
- `canActOnEntitiesMap()` — خريطة `entity_id => bool` لقوائم الجداول.
- `canUserApprove()` — alias لـ `canActOnCurrentStep` (توافق مع `WorkflowService`).

### Controllers

- `LeaveRequestController` / `ExpenseRequestController`: تمرير `canApproveNow` و`canApproveNowById`.
- `ApprovalController`: فلترة القائمة بـ `canActOnEntity` فقط؛ إزالة `isUserApprover`.
- `approve` / `reject`: استخدام `canActOnCurrentStep` مع تمرير الكيان لتقييم الشروط.

### الواجهة

- أزرار الموافقة/الرفض مشروطة بـ `$canApproveNow` أو `$canApproveNowById[$id]`.
- modals الموافقة في index تُضمَّن فقط عندما `canApproveNow` true.

### Middleware

- `CheckApprovalPermission`: 403 إذا `!canActOnEntity` (بما في ذلك غياب instance).

## تدفق مستهدف

1. موظف يقدم طلباً → `WorkflowInstance` على الخطوة 1 → إشعار رئيس القسم.
2. رئيس القسم فقط يرى أزرار الموافقة في `/admin/leave-requests` و`/admin/approvals`.
3. بعد الموافقة → الخطوة 2 → إشعار المدير التنفيذي → أزراره تظهر له فقط.
4. الأدمن بـ `approve-all` يرى الطلب في القائمة دون أزرار حتى تصبح خطوته الحالية (نادراً إلا إن عُيِّن موافقاً صريحاً).

## الاختبارات

`tests/Feature/LeaveRequestWorkflowTest.php`:

- رئيس القسم فقط في الخطوة 1.
- المدير التنفيذي بعد موافقة رئيس القسم.
- `leave-request-approve-all` لا يمر في الخطوة 1.

## بيانات قديمة

تشغيل `php artisan workflow:backfill-instances` للطلبات `pending` بلا `WorkflowInstance` حتى تظهر في `/admin/approvals`.

## خارج النطاق

- تجاوز طوارئ (override) لـ approve-all.
- تغيير `getRoleApprover` لإرجاع كل مستخدمي الدور.
- فلترة إحصائيات Dashboard.
