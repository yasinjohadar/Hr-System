# نظام الموافقات والتسلسل الهرمي - دليل شامل

## نظرة عامة

تم تطوير نظام شامل للموافقات يعتمد على التسلسل الهرمي للموظفين والأقسام. النظام يعمل تلقائياً ويحدد الموافقين بناءً على:
1. **التسلسل الهرمي للموظفين** (Manager-Subordinate)
2. **مديري الأقسام** (Department Managers)
3. **الأدوار والصلاحيات** (Roles & Permissions)
4. **سير العمل المخصص** (Custom Workflows)

---

## 1. تحديد رؤساء الأقسام

### 1.1 مدير القسم (Department Manager)

**الطريقة:**
- كل `Department` لديه حقل `manager_id` يشير إلى `User`
- يمكن تعيين مدير للقسم عند إنشاء أو تعديل القسم
- يمكن للقسم أن يكون له قسم أب (`parent_id`) - في هذه الحالة، إذا لم يكن للقسم مدير، يتم البحث في القسم الأب

**الكود:**
```php
// في Department Model
$department->manager_id = $user->id; // User ID
$department->save();
```

**الوصول:**
```php
$department = Department::find($id);
$manager = $department->manager; // User
```

---

## 2. تحديد التسلسل الهرمي للموظفين

### 2.1 المدير المباشر (Direct Manager)

**الطريقة:**
- كل `Employee` لديه حقل `manager_id` يشير إلى `Employee` آخر
- هذا يخلق تسلسلاً هرمياً: `Employee -> Manager (Employee) -> Manager's Manager -> ...`

**مثال:**
```
الموظف (A) -> مديره المباشر (B) -> مدير B (C) -> مدير C (D)
```

**الكود:**
```php
// تعيين مدير مباشر
$employee->manager_id = $managerEmployee->id;
$employee->save();
```

**الوصول:**
```php
$employee = Employee::find($id);
$directManager = $employee->getDirectManager(); // Employee
$managerUser = $directManager->user; // User
```

### 2.2 التسلسل الهرمي الكامل

**الطريقة:**
- يمكن الحصول على التسلسل الهرمي الكامل من الموظف حتى المدير العام

**الكود:**
```php
$employee = Employee::find($id);
$hierarchy = $approvalService->getEmployeeHierarchy($employee);

// النتيجة:
[
    'direct_manager' => Employee|null,
    'department_manager' => User|null,
    'chain' => [
        ['type' => 'direct_manager', 'employee' => Employee, 'user' => User],
        ['type' => 'department_manager', 'user' => User],
        ['type' => 'hierarchy_manager', 'level' => 1, 'employee' => Employee, 'user' => User],
        // ... المزيد
    ]
]
```

---

## 3. نظام الموافقات التلقائي

### 3.1 أنواع الموافقين (Approver Types)

النظام يدعم 5 أنواع من الموافقين:

#### 1. **user** - مستخدم محدد
```php
// في WorkflowStep
'approver_type' => 'user',
'approver_id' => $userId, // User ID
```

#### 2. **role** - دور محدد
```php
// في WorkflowStep
'approver_type' => 'role',
'role_id' => $roleId, // Role ID (مثل: HR, Finance, etc.)
// النظام يختار أول مستخدم نشط لهذا الدور
```

#### 3. **employee_manager** - المدير المباشر للموظف
```php
// في WorkflowStep
'approver_type' => 'employee_manager',
// النظام يبحث تلقائياً عن manager_id للموظف
```

#### 4. **department_manager** - مدير القسم
```php
// في WorkflowStep
'approver_type' => 'department_manager',
// النظام يبحث تلقائياً عن manager_id للقسم
```

#### 5. **custom** - مخصص
```php
// في WorkflowStep
'approver_type' => 'custom',
'conditions' => ['amount' => 10000], // شروط مخصصة
// يمكن تطوير منطق مخصص بناءً على conditions
```

---

## 4. سير العمل (Workflow)

### 4.1 إنشاء سير عمل

**مثال: سير عمل طلب إجازة**
```php
Workflow::create([
    'name' => 'Leave Request Workflow',
    'name_ar' => 'سير عمل طلب الإجازة',
    'type' => 'leave_request',
    'is_active' => true,
]);

// الخطوة 1: موافقة المدير المباشر
WorkflowStep::create([
    'workflow_id' => $workflow->id,
    'step_order' => 1,
    'name' => 'Manager Approval',
    'name_ar' => 'موافقة المدير',
    'approver_type' => 'employee_manager',
    'is_required' => true,
]);

// الخطوة 2: موافقة HR
WorkflowStep::create([
    'workflow_id' => $workflow->id,
    'step_order' => 2,
    'name' => 'HR Approval',
    'name_ar' => 'موافقة الموارد البشرية',
    'approver_type' => 'role',
    'role_id' => $hrRoleId,
    'is_required' => true,
]);
```

### 4.2 بدء سير العمل تلقائياً

**عند إنشاء طلب إجازة:**
```php
$leaveRequest = LeaveRequest::create([...]);

// بدء سير العمل تلقائياً
$workflowService = app(WorkflowService::class);
$workflowService->startWorkflow(
    'leave_request',        // نوع سير العمل
    $employee,             // الموظف صاحب الطلب
    'LeaveRequest',        // نوع الكيان
    $leaveRequest->id      // معرف الكيان
);
```

**ما يحدث:**
1. النظام يبحث عن `Workflow` من نوع `leave_request`
2. يبدأ من الخطوة الأولى (`step_order = 1`)
3. يحدد الموافق بناءً على `approver_type`
4. ينشئ `WorkflowInstance`
5. يرسل إشعار للموافق الأول

---

## 5. عملية الموافقة

### 5.1 الموافقة على طلب

**الطريقة:**
```php
// في Controller
$instance = WorkflowInstance::where('entity_type', 'LeaveRequest')
    ->where('entity_id', $leaveRequest->id)
    ->first();

$workflowService->processApproval(
    $instance,
    auth()->user(),  // الموافق
    true,           // approved = true
    'موافقة'        // comments
);
```

**ما يحدث:**
1. التحقق من أن المستخدم يمكنه الموافقة على هذه الخطوة
2. إذا كانت هناك خطوة تالية، الانتقال إليها
3. إذا كانت هذه آخر خطوة، اكتمال سير العمل
4. تحديث حالة الكيان تلقائياً

### 5.2 الرفض

```php
$workflowService->processApproval(
    $instance,
    auth()->user(),
    false,              // approved = false
    'سبب الرفض'         // comments
);
```

**ما يحدث:**
- إنهاء سير العمل فوراً
- تحديث حالة الكيان إلى `rejected`

---

## 6. الصلاحيات

### 6.1 صلاحيات الموافقة

النظام يتحقق من الصلاحيات على مستويات متعددة:

#### 1. **صلاحية الموافق المطلوب**
- إذا كان المستخدم هو الموافق المحدد في `WorkflowStep`، يمكنه الموافقة

#### 2. **صلاحية التسلسل الهرمي**
- المدير المباشر يمكنه الموافقة على طلبات مرؤوسيه
- مدير القسم يمكنه الموافقة على طلبات موظفي القسم

#### 3. **صلاحيات عامة**
- `leave-request-approve-all`: يمكن الموافقة على جميع طلبات الإجازات
- `expense-request-approve-all`: يمكن الموافقة على جميع طلبات المصروفات

### 6.2 التحقق من الصلاحيات

```php
$approvalService = app(ApprovalService::class);

$canApprove = $approvalService->canUserApprove(
    $user,              // المستخدم الحالي
    'leave_request',    // نوع سير العمل
    $employee,          // الموظف صاحب الطلب
    1                   // مستوى الموافقة (step_order)
);
```

---

## 7. أمثلة عملية

### 7.1 مثال: طلب إجازة

**السيناريو:**
- موظف (A) يطلب إجازة
- المدير المباشر (B) يجب أن يوافق أولاً
- ثم HR يجب أن يوافق

**التسلسل:**
1. الموظف (A) يقدم طلب إجازة
2. النظام يبدأ سير العمل تلقائياً
3. الخطوة 1: الموافق = المدير المباشر (B)
4. المدير (B) يوافق
5. الخطوة 2: الموافق = أول مستخدم لدور HR
6. HR يوافق
7. اكتمال سير العمل → طلب الإجازة `approved`

### 7.2 مثال: طلب مصروف

**السيناريو:**
- موظف (A) يطلب مصروف بقيمة 5000 ريال
- المدير المباشر (B) يجب أن يوافق
- ثم المالية يجب أن توافق

**التسلسل:**
1. الموظف (A) يقدم طلب مصروف
2. النظام يبدأ سير العمل
3. الخطوة 1: الموافق = المدير المباشر (B)
4. المدير (B) يوافق
5. الخطوة 2: الموافق = أول مستخدم لدور Finance
6. Finance توافق
7. اكتمال سير العمل → طلب المصروف `approved`

### 7.3 مثال: التقييمات

**السيناريو:**
- عند إنشاء تقييم أداء، إذا لم يتم تحديد `reviewer_id`:
  - النظام يستخدم المدير المباشر تلقائياً
  - إذا لم يكن هناك مدير مباشر، يستخدم مدير القسم

**الكود:**
```php
$employee = Employee::find($employeeId);

// إذا لم يتم تحديد reviewer_id
if (!$request->reviewer_id) {
    $directManager = $employee->getDirectManager();
    if ($directManager) {
        $reviewerId = $directManager->id;
    } else {
        $deptManager = $employee->getDepartmentManagerEmployee();
        if ($deptManager) {
            $reviewerId = $deptManager->id;
        }
    }
}
```

---

## 8. واجهات المستخدم

### 8.1 صفحة طلبات الموافقة المعلقة

**المسار:** `/admin/approvals`

**المحتوى:**
- قائمة بجميع طلبات الإجازات المعلقة التي تحتاج موافقة المستخدم الحالي
- قائمة بجميع طلبات المصروفات المعلقة التي تحتاج موافقة المستخدم الحالي
- عرض سير العمل الحالي لكل طلب

### 8.2 صفحة تفاصيل طلب الموافقة

**المسار:** `/admin/approvals/{type}/{id}`

**المحتوى:**
- تفاصيل الطلب الكاملة
- سير العمل (timeline) يوضح:
  - الخطوات المكتملة
  - الخطوة الحالية
  - الخطوات المعلقة
- أزرار الموافقة/الرفض

---

## 9. الملفات الرئيسية

### 9.1 Services
- `app/Services/ApprovalService.php` - تحديد الموافقين
- `app/Services/WorkflowService.php` - إدارة سير العمل

### 9.2 Models
- `app/Models/Employee.php` - helper methods للتسلسل الهرمي
- `app/Models/Department.php` - مدير القسم
- `app/Models/Workflow.php` - سير العمل
- `app/Models/WorkflowStep.php` - خطوات سير العمل
- `app/Models/WorkflowInstance.php` - حالات سير العمل

### 9.3 Controllers
- `app/Http/Controllers/Admin/ApprovalController.php` - عرض طلبات الموافقة
- `app/Http/Controllers/Admin/LeaveRequestController.php` - محدث لاستخدام النظام الجديد
- `app/Http/Controllers/Admin/ExpenseRequestController.php` - محدث لاستخدام النظام الجديد
- `app/Http/Controllers/Admin/PerformanceReviewController.php` - محدث لاستخدام التسلسل الهرمي

### 9.4 Middleware
- `app/Http/Middleware/CheckApprovalPermission.php` - التحقق من صلاحيات الموافقة

---

## 10. أفضل الممارسات

### 10.1 تعيين المديرين

**للموظفين:**
```php
// عند إنشاء موظف جديد
$employee = Employee::create([
    'manager_id' => $managerEmployee->id, // Employee ID
    // ...
]);
```

**للأقسام:**
```php
// عند إنشاء قسم جديد
$department = Department::create([
    'manager_id' => $managerUser->id, // User ID
    // ...
]);
```

### 10.2 إنشاء سير عمل

**خطوات:**
1. إنشاء `Workflow`
2. إضافة `WorkflowStep` بالترتيب الصحيح
3. تحديد `approver_type` لكل خطوة
4. تفعيل `is_active = true`

### 10.3 معالجة الموافقات

**دائماً استخدم:**
- `WorkflowService::processApproval()` بدلاً من التحديث المباشر
- التحقق من الصلاحيات قبل الموافقة
- معالجة الأخطاء بشكل صحيح

---

## 11. التوسعات المستقبلية

### 11.1 موافقات متعددة المستويات
- يمكن إضافة مستويات إضافية (3، 4، 5...)

### 11.2 شروط مخصصة
- موافقة مختلفة بناءً على المبلغ (مثلاً: > 10000 يحتاج موافقة المدير العام)
- موافقة مختلفة بناءً على نوع الإجازة

### 11.3 إشعارات
- إرسال إشعارات تلقائية للموافقين
- تذكيرات للموافقين المعلقين

### 11.4 التقارير
- تقارير عن طلبات الموافقة
- إحصائيات عن أوقات الموافقة
- تحليل التسلسل الهرمي

---

## 12. الأسئلة الشائعة

### س: كيف يتم تحديد الموافق إذا لم يكن للموظف مدير مباشر؟
**ج:** النظام يبحث تلقائياً عن مدير القسم. إذا لم يكن هناك مدير قسم، يبحث في القسم الأب.

### س: ماذا يحدث إذا كان الموافق غير موجود؟
**ج:** النظام يسجل warning في Log ويترك الطلب معلقاً. يمكن للمسؤول تعيين موافق بديل.

### س: هل يمكن تخطي خطوة في سير العمل؟
**ج:** نعم، إذا كانت `is_required = false` في `WorkflowStep`.

### س: كيف أعرف من هو الموافق التالي؟
**ج:** استخدم `ApprovalService::getNextApprover()` أو عرض صفحة `/admin/approvals/{type}/{id}`.

---

## 13. الدعم الفني

للمزيد من المعلومات أو المساعدة، راجع:
- `app/Services/ApprovalService.php` - الكود الكامل
- `app/Services/WorkflowService.php` - منطق سير العمل
- `app/Models/Employee.php` - helper methods
