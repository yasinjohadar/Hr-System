# مراجعة شاملة لنظام HR + إدارة المشاريع

**تاريخ المراجعة:** 25 يوليو 2026
**النطاق:** المعمار، الأمان، قاعدة البيانات والأداء، الفجوات الوظيفية، جودة الكود والاختبارات
**المنهجية:** قراءة الكود الفعلي (Laravel 12 / PHP 8.2) — لم يُعتمد على ملفات التوثيق الموجودة كدليل

---

## 0. الحكم التنفيذي

| البند | التقييم |
|---|---|
| حجم المشروع | 111 Controller (‏21,995 سطر)، 101 Model، 112 Migration، 497 Blade (‏67,328 سطر) |
| العمق الحقيقي | **~15% من الوحدات هي برمجيات حقيقية**، الباقي CRUD سطحي |
| الأمان | **غير جاهز للإنتاج** — ثغرة تصعيد صلاحيات كاملة + RCE |
| قاعدة البيانات | تصميم قوي بشكل مفاجئ (decimal في كل المال، unique شامل، onDelete صريح) مع فهارس ناقصة |
| الاختبارات | 43 دالة اختبار، منها ~21 فقط خاصة بالمشروع. **صفر تغطية للرواتب** |
| CI/CD | لا يوجد |
| التقييم العام | **6.5/10** كنموذج أولي متقدم — **3/10** كمنتج جاهز للبيع |

### رأيي بصراحة

النظام يعاني من مشكلة واحدة جوهرية: **الاتساع على حساب العمق**. 101 موديل و112 جدول يعطون انطباعًا بمنتج ضخم، لكن الواقع أن ~49 من أصل 85 Controller إداري لا تحتوي إلا على `index/create/store/show/edit/update/destroy` — لا فعل أعمال واحد، لا حالة، لا حساب.

في المقابل، هناك أجزاء **جيدة فعلاً وليست تافهة**:

- **محرك سير العمل (Workflow/Approval)** — عام، قابل لإعادة الاستخدام، يخدم 8 أنواع كيانات، يتعامل مع التفويض وتعارض المصالح وسلاسل المدراء. هذا أفضل ما في المشروع.
- **محرك الرواتب** — حساب حقيقي: أيام العمل، خصم التأخير والغياب، الإجازات، الأوفرتايم، مكونات الراتب، الضريبة والتأمينات.
- **الحضور بالـ GPS** — haversine حقيقي مع نطاق موقع.
- **بوابة الخدمة الذاتية** (1,623 سطر) — 25+ شاشة فعلية للموظف.
- **تصميم قاعدة البيانات** — لا يوجد `float` واحد في 90+ عمود مالي، 269 مفتاح أجنبي كلها بسلوك حذف صريح، قيود `unique` شاملة على كل ما يهم.

المشكلة أن هذه الجزر الجيدة **غير موصولة ببعضها**، وأن جانب إدارة المشاريع تحديدًا هو قشرة CRUD لا تتصل بأي شيء مالي أو تشغيلي.

**الخلاصة:** لا تُضِف ميزات جديدة. النظام يحتاج مرحلة **تعميق وربط وتأمين** قبل أي توسع.

---

## 1. الأمان — يجب الإصلاح قبل أي نشر

### 🔴 حرج

#### A1. تصعيد صلاحيات كامل — أي مستخدم مسجّل يمكنه أن يصبح Admin

`routes/web.php:37`

```php
Route::middleware(['auth', 'check.user.active'])->group(function () {
    // ...
    Route::post('roles/{id}/apply-template', [RoleController::class, 'applyTemplate']);
});
```

الدالة `applyTemplate` في `RoleController.php:100` **غير مغطاة** بأي middleware صلاحيات — الـ constructor (سطور 15–29) يغطي فقط `index/create/store/edit/update/destroy`. المسار أيضًا خارج مجموعة `ensure.admin`.

**الاستغلال:** أي موظف عادي يرسل `POST /roles/{role_id}/apply-template` مع `template=admin` → `syncPermissions()` يمنح دوره كامل صلاحيات النظام. اختراق كامل.

**مضاعف الخطورة:** التسجيل الذاتي مفتوح على النظام — `routes/auth.php:16-19` يسمح لأي شخص من الإنترنت بإنشاء حساب. الأمران معًا = **سلسلة اختراق من الإنترنت إلى Admin بدون أي بيانات دخول**.

**الإصلاح:**
```php
// RoleController.php
$this->middleware(['permission:role-edit'])->only(['edit', 'update', 'show', 'applyTemplate']);
```
+ نقل المسار إلى مجموعة `ensure.admin` + حذف مسارات `register` من `routes/auth.php`.

---

#### A2. تنفيذ كود عن بُعد (RCE) في حساب الرواتب

`app/Http/Controllers/Admin/PayrollController.php:462`

```php
// تقييم الصيغة (يجب التأكد من الأمان)
try {
    $result = @eval("return $formula;");
    return is_numeric($result) ? (float)$result : 0;
} catch (\Exception $e) { return 0; }
```

`$formula` يأتي من عمود `salary_components.formula` القابل للتعديل من الواجهة. أي مستخدم لديه `salary-component-edit` ينفّذ كودًا عشوائيًا بصلاحيات مستخدم الويب. التعليق نفسه يقول "يجب التأكد من الأمان" — ولم يتم.

إضافة: `catch (\Exception)` لا يلتقط `ParseError` (وهو `Throwable`) → صيغة خاطئة = خطأ فادح في منتصف تشغيل الرواتب.

**الإصلاح:** استخدام `symfony/expression-language` مع خريطة متغيرات صريحة، أو حذف عمود `formula` نهائيًا والاقتصار على `calculation_type` من قائمة مغلقة.

---

#### A3. كل ملفات الموظفين على القرص العام (`public`)

كل عمليات الرفع في النظام تستخدم `->store(..., 'public')`:
`EmployeeDocumentController.php:74,126` · `ContractController.php:76,119,173` · `EmployeeBenefitController.php:85,129` · `ExpenseRequestController.php:136,241` · `Employee/SelfServiceController.php:1170` · `Public/CareersController.php:55` · `TaskController.php:294` · `ProjectDocumentController.php:28`

والقوالب تربط الملفات مباشرة:
`views/admin/pages/contracts/show.blade.php:71` → `asset('storage/'.$contract->document_path)`
`views/admin/pages/candidates/edit.blade.php:174` → `Storage::url($candidate->cv_path)`

**النتيجة:** بعد `storage:link`، أي شخص يعرف اسم الملف يحمّل عقود العمل والهويات والسجلات الطبية **بدون أي مصادقة**. الحماية الوحيدة هي عشوائية اسم الملف — والذي يُكشف في HTML لأي مستخدم يرى السجل.

**الإصلاح:** نقل كل شيء إلى القرص `local` وتقديم الملفات عبر مسارات محمية بصلاحية + تحقق ملكية.

#### A4. تحميل مستندات أي موظف بدون صلاحية (IDOR)

`EmployeeDocumentController.php:158` — دالة `download` غير مشمولة في `->only()` (تم التحقق: السطور 13–20 تغطي index/show/create/store/edit/update/destroy فقط)، ولا تتحقق من الملكية أو القسم:

```php
public function download(string $id) {
    $document = EmployeeDocument::findOrFail($id);
    return Storage::disk('public')->download($document->file_path, ...);
}
```

أي حساب يمرّ عبر `ensure.admin` — والذي يرفض فقط من دوره الوحيد `employee` (`EnsureUserIsAdmin.php:25`) — يستطيع تعداد `/admin/employee-documents/1..N/download` وسحب كل الملفات.

---

### 🟠 عالي

| # | المشكلة | الموقع | الأثر |
|---|---|---|---|
| A5 | رؤساء الأقسام يرون بيانات كل الموظفين | `ScopesByDepartment` مطبّق في 11 ملف فقط؛ غائب عن `EmployeeDocumentController`، `EmployeeBankAccountController`، `ContractController`، `EmployeeViolationController`، `EmployeeExitController`، `EmployeeAdvanceController` | رئيس قسم يقرأ IBAN كل موظفي الشركة |
| A6 | التصدير يتجاهل نطاق القسم | `ExportController.php:58-72` — صلاحية واحدة `export-data` تفتح 38 عملية تصدير غير مقيّدة، منها الرواتب والحسابات البنكية | تسريب قاعدة بيانات HR كاملة بطلب GET واحد |
| A7 | لا يوجد throttle على التحقق الثنائي | `routes/auth.php:62` | تخمين كود 2FA بلا حدود (تسجيل الدخول نفسه محمي بـ throttle — جيد) |
| A8 | كود الدخول لمرة واحدة بلا throttle | `routes/employee.php:9-10` + `LoginByCodeController.php:29-69` — `Str::random(12)` صالح 15 دقيقة، يشمل حسابات Admin، بلا `session()->regenerate()` | تخمين + تثبيت جلسة |
| A9 | انتحال الهوية بلا تسجيل | `EmployeeController.php:369-384` — مبني على صلاحية **قراءة** `employee-show`، بلا `session()->regenerate()`، **بلا سجل تدقيق** | استيلاء كامل على حساب بدون أثر |

### 🟡 متوسط

- **سجل التدقيق يغطي موديل واحد من 101.** `app/Traits/LogsActivity.php` مستخدم في `Employee.php` فقط. غير مسجّل: إنشاء/اعتماد/دفع الرواتب، تغيير الرواتب، إنشاء المستخدمين، `syncRoles`، إعادة تعيين كلمات المرور، تعطيل الحسابات، التصدير، الانتحال.
- **`EmployeePolicy` كود ميت.** مسجّل في `AppServiceProvider.php:31` لكن `grep 'authorize('` في الـ Controllers = **0 نتيجة**. مستخدم في `@can` واحد في قالب واحد.
- **86 موضع `$request->all()`** يمرّ إلى `create()`/`update()` (مثال: `AssetAssignmentController.php:94,153`). محتوى حاليًا لأن كل الـ 101 موديل تعرّف `$fillable` (لا يوجد `$guarded = []` — جيد)، لكن إضافة عمود واحد للـ `$fillable` مستقبلاً = تجاوز فوري لسير العمل.
- **إعدادات الجلسة/النقل غير مهيّأة.** `.env.example` فيه `APP_DEBUG=true`, `APP_ENV=local`, `SESSION_ENCRYPT=false`، و**لا** `SESSION_SECURE_COOKIE` → كوكي الجلسة يُرسل عبر HTTP. لا `forceScheme('https')` ولا HSTS.
- **2FA اختياري** — `User.php:72-80` يُرجع `false` إن لم يفعّلها المستخدم، فـ `EnsureTwoFactorVerified` بلا أثر لأي Admin لم يفعّلها.
- **كلمات مرور ضعيفة للحسابات المُدارة** — `UserController.php:104,193,270`: `min:8|confirmed` فقط، بلا `Rules\Password::defaults()`، و`updatePassword` لا يطلب كلمة المرور الحالية.
- **رفع ملفات مجهول بلا throttle** — `routes/web.php:16` (تقديم على وظيفة) 5MB لكل طلب على القرص العام.
- **مسار مكرر بلا حماية** — `routes/web.php:48` `toggle-user-status/{id}` بتعليق "مسار بديل للتجربة". يجب حذفه.

### ✅ ما هو جيد فعلاً

- `.env` **غير مُتَتبَّع في git** (تم التحقق) وموجود في `.gitignore`.
- **لا يوجد SQL Injection.** المدخلات لا تصل إلى SQL خام؛ الترتيب والفلترة عبر Query Builder.
- **لا ثغرة Mass Assignment حاليًا** — كل الموديلات تعرّف `$fillable`.
- **الخدمة الذاتية للموظف مقيّدة بالملكية بشكل صحيح** — `SelfServiceController.php:1100` `Payroll::where('employee_id', $employee->id)->findOrFail($id)`. بحثت تحديدًا عن IDOR في قسائم الرواتب والإجازات فلم أجد.
- التحقق من الملفات المرفوعة متسق (`mimes:` + `max:` في كل مسار).
- `session()->regenerate()` عند الدخول العادي، و`current_password` مطلوب لتعطيل 2FA، وCSRF غير معطّل في أي مكان.

---

## 2. قاعدة البيانات والأداء

### 🔴 حرج

**D1. جدولان متنافسان للرواتب — مصدرا حقيقة متضاربان.**
`salaries` و `payrolls` كلاهما يخزّن أساسي/بدلات/خصومات/صافي لكل موظف/شهر، وكلاهما له `unique(employee_id, month, year)`.
التقارير تقرأ `salaries` (`ReportController.php:180`, `DashboardController.php:377`)، محرك الرواتب يكتب في `payrolls` (`PayrollController.php:117-122`)، و`salary_ledger_lines.salary_id` يشير إلى `salaries`.
**النتيجة:** "إجمالي رواتب هذا الشهر" في اللوحة قد يخالف وحدة الرواتب.
**الإصلاح:** اعتماد `payrolls` كمصدر وحيد، ترحيل البيانات، حذف `salaries`.

**D2. تعدد العملات ديكوري — المجاميع بين عملات مختلفة خاطئة.**
13 جدولاً فيه `currency_id`، و`currencies.exchange_rate` موجود — لكنه **غير مستخدم في أي حساب** (فقط في `CurrencyController` والقوالب). و`ReportController.php:209-214` يجمع `sum('total_salary')` على صفوف بعملات مختلطة. الأسوأ: `employees.salary` **بلا عمود عملة** أصلاً، وهو أساس كل حساب رواتب.
**الإصلاح:** إضافة `employees.currency_id`، وتخزين `exchange_rate_used` + `base_currency_amount` على `payrolls` وقت الحساب، وجمع التقارير على عمود العملة الأساسية.

**D3. أعمدة التصدير تُخرج قيمًا فارغة بصمت.**
`ExportController.php:95` يقرأ `employee_number` والعمود اسمه `employee_code`. السطر 98 يقرأ `phone` والموجود `personal_phone`/`work_phone`. السطر 100 يقرأ `position->name_ar ?? ->name` وجدول `positions` فيه `title` فقط → **عمود المسمى الوظيفي فارغ دائمًا** في كل تصدير للموظفين. نفس الخطأ في `:222-223`, `:730` وفي قوالب `attendance-locations` و`bank-accounts`. Eloquent يُرجع `null` بصمت.

### 🟠 عالي

- **كل تصدير يحمّل الجدول كاملاً في الذاكرة بشكل متزامن** — 25 إغلاق `FromCollection` مع `->get()` غير مقيّد (`ExportController.php:72, 251, 293, 336, ...`). `Attendance::with('employee')->get()` بلا حد: 200 موظف × سنتين ≈ 100 ألف موديل في الطلب. الطوابير مهيّأة (`QUEUE_CONNECTION=database`) لكن غير مستخدمة للتصدير.
  **الإصلاح:** `FromQuery` + `WithChunkReading(1000)` + `Excel::queue()`.
- **صفحات التقارير تحمّل جداول كاملة لحساب مجاميع في PHP** — `ReportController.php:107→111-116`, `:150→154-167`, `:204→208-222`, `:262`, `:315`, `:355`, `:383`. و`employeesReport` **بلا أي حد أو نطاق تاريخ**. (لوحة التحكم تفعل الصواب بـ `selectRaw`+`groupBy` — انسخ نمطها.)
- **`whereYear`/`whereMonth` تُعطّل الفهارس** — 33 `whereDate` + 25 `whereYear` + 23 `whereMonth`. استبدلها بـ `whereBetween`.
- **`leave_requests` بلا أي فهرس صريح** رغم أن `status` أكثر عمود يُفلتر عليه في النظام. نفس الشيء لـ `tasks` و`projects`. **30 جدولاً فيه عمود `status` بلا فهرس.**

```sql
ALTER TABLE attendances     ADD INDEX idx_date_status (attendance_date, status);
ALTER TABLE leave_requests  ADD INDEX idx_emp_status (employee_id, status),
                            ADD INDEX idx_status_start (status, start_date),
                            ADD INDEX idx_dates (start_date, end_date);
ALTER TABLE employees       ADD INDEX idx_active (is_active, employment_status, department_id);
ALTER TABLE tasks           ADD INDEX idx_status_due (status, due_date);
ALTER TABLE projects        ADD INDEX idx_status (status);
ALTER TABLE project_time_entries ADD INDEX idx_status (status);
```

- **المخطط يختلف بين sqlite (الاختبارات) وMySQL (الإنتاج).** `2026_06_03_030000_expand_workflows_type_column.php:11-13` يعود مبكرًا على sqlite → `workflows.type` يبقى ENUM في الاختبارات وVARCHAR في الإنتاج. الاختبارات لا تستطيع كشف مخالفات ENUM. **الحل: توحيد كل الـ 30 عمود `enum()` إلى `string(32)` + تحقق على مستوى التطبيق.**

### 🟡 متوسط

- **حسابات مالية بأعداد عائمة في PHP** — `PayrollController.php:307` `($late_minutes / 15) * ($salary * 0.01)`، `:349` `$salary / 30`، `:376-378`, `:409-420` بلا `round(…, 2)` قبل الكتابة في `decimal(12,2)` → الإجمالي ≠ مجموع البنود بالقروش.
- **N+1 داخل القوالب** — `attendance-locations/show.blade.php:159,172,185` (`Employee::find()` داخل `@foreach`)، `employee-violations/show.blade.php:327` (استعلام كامل داخل القالب).
- **`with()` حيث المطلوب `withCount()`** — `PolicyController.php:27` يحمّل كل الإقرارات لعدّها فقط: 15 سياسة × 200 موظف = 3000 موديل لكل صفحة. نفس الشيء `TrainingController.php:28`.
- **~20 استعلام `count()` منفصل** في `SelfServiceController.php:226-266` لكل تحميل صفحة، بلا cache. و6-8 استعلامات `Attendance::where('status', X)->count()` في `DepartmentHeadController.php:46-65` تُجمع في واحد بـ `groupBy`.
- **`whereJsonContains` على JSON غير مفهرس** — `PayrollController.php:404-406` مسح كامل لكل موظف في كل تشغيل رواتب.

### ✅ ما هو جيد فعلاً (لا تُصلحه)

- **صفر `float`/`double`** — كل الـ 90+ عمود مالي `decimal`.
- **269 `constrained()` كلها بسلوك حذف صريح** (`cascadeOnDelete`/`nullOnDelete`). حذف قسم لا يُيتّم الموظفين.
- **قيود `unique` شاملة** — حضور واحد/موظف/يوم، رصيد إجازة/موظف/نوع/سنة، راتب/موظف/شهر/سنة، `project_members`، `task_assignments` + ~40 أخرى.
- **Eager loading مطبّق في كل مواضع `paginate()` الـ93** — أفضل من المعتاد بكثير.
- **`DashboardController` نموذجي** — `Cache::remember` 5 دقائق (`:50`)، مجاميع SQL بدل حلقات PHP.
- Soft deletes متسق: 74 موديل / 74 migration.

---

## 3. الفجوات الوظيفية — جوهر المراجعة

### 3.1 عمق الوحدات

| الوحدة | الأسطر | منطق أعمال؟ | الحكم |
|---|---|---|---|
| الرواتب | 671 | ✅ محرك حقيقي كامل | **حقيقية** (الأفضل) |
| الإجازات | 454 | ✅ رصيد، خصم/استرجاع، سير عمل | **حقيقية** |
| محرك سير العمل | 223 + خدمتان (40KB) | ✅ حالات، تفويض، تعارض مصالح | **حقيقية** |
| التغييرات الوظيفية | 381 | ✅ يكتب النتيجة على الموظف | **حقيقية** |
| المصروفات | 426 | ✅ سير عمل | **حقيقية** |
| الحضور | 301 | ✅ GPS haversine، تأخير، قواعد | **حقيقية** |
| الأوفرتايم | 314 | ✅ حساب مضاعفات | **حقيقية** |
| الخدمة الذاتية | 1623 | ✅ 25+ شاشة | **حقيقية** (واسعة جدًا) |
| الأصول | 272 | ✅ دورة حياة | **حقيقية** |
| تقييم الأداء | 355 | جزئي — بلا أثر لاحق | **رقيقة** |
| **المشاريع** | 229 | ❌ الحالة والتقدّم حقول يدوية | **CRUD سطحي** |
| **المهام** | 326 | جزئي — التقدّم يدوي | **CRUD سطحي** |
| **إدخالات الوقت** | **69** (store/destroy فقط) | ❌ بلا سعر، بلا كلفة، بلا اعتماد | **CRUD سطحي** |
| التدريب | 244 | ❌ CRUD خالص | **CRUD سطحي** |
| **تخطيط التعاقب** | 128 | ❌ غير مُشار إليه من أي ملف آخر | **جزيرة ميتة** |
| الأهداف | 123 | ❌ | **CRUD سطحي** |
| التعيين (Onboarding) | 150 | نسخ قالب فقط | **CRUD سطحي** |
| **الاستبيانات** | 138 | ❌ | **معطوبة** — `create`/`edit` تُرجع قوالب غير موجودة → خطأ 500 |
| التوظيف (5 وحدات) | 150-200 | ❌ CRUD خالص | **CRUD سطحي** |
| إنهاء الخدمة | 149 | حد أدنى — 4 مربعات اختيار | **CRUD سطحي** |
| الملاحظات، الاجتماعات، الجزاءات، الورديات، أرصدة الإجازات، الرواتب الأساسية، الضرائب | 121-354 | ❌ | **CRUD سطحي** |

> **49 من أصل ~85 Controller إداري لا تعرض أي فعل خارج CRUD السبعة.**

### 3.2 مصفوفة التكامل — أين ينقطع النظام

| الرابط | موجود؟ | الدليل |
|---|---|---|
| إدخالات الوقت ← الحضور | ❌ | صفر إشارة بين `ProjectTimeEntry` و`Attendance`. **سجلّا ساعات مستقلان** |
| إدخالات الوقت ← اعتماد | ⚠️ **سقالة ميتة** | `ProjectTimeEntry.php:19-22` فيه `status`, `approved_at`, `approved_by`، ومُسجّل في `WorkflowEntityType.php:25` — لكن الـ Controller فيه `store`/`destroy` فقط ولا مسار اعتماد. **الحقول لا تُكتب أبدًا** |
| إدخالات الوقت ← كلفة الرواتب | ❌ | `PayrollController` لا يذكر المشاريع إطلاقًا |
| المشاريع ← الميزانية/الكلفة | ⚠️ عرض فقط | `budget` يُتحقق منه ويُعرض. `grep "billable\|billing_rate\|labor_cost\|actual_cost"` = **0 نتيجة في المشروع كله** |
| المهام ← حِمل العمل/الطاقة | ❌ | `grep "workload\|capacity\|allocat"` = **0 ملف** |
| الإجازات ← موارد المشاريع | ❌ | `LeaveRequestController` بلا أي إشارة لمشروع أو مهمة. اعتماد إجازة لا يمسّ التكليفات |
| الحضور ← خصومات الرواتب | ✅ | `PayrollController.php:290-318` (لكن معادلة التأخير في السطر 307 مكتوبة صلبًا وليست من `AttendanceRule`) |
| التقييم ← الأهداف ← التدريب ← التعاقب ← الترقية | ❌ | مفاتيح أجنبية فقط، بلا منطق. `SuccessionPlan` **معزول تمامًا** |
| التوظيف ← عرض العمل ← التعيين ← ملف الموظف | ❌ **السلسلة تنقطع عند التعيين** | `Employee::create` موجود في مكان **واحد**: `EmployeeController.php:193`. لا `OfferLetterController` ولا `JobApplicationController` ينشئ موظفًا. **إدخال يدوي مزدوج** |
| إنهاء الخدمة ← إرجاع الأصول | ⚠️ مربع اختيار | `EmployeeExit.php:25` `assets_returned` boolean، بلا استعلام على `AssetAssignment`. **يمكن إخلاء طرف موظف وهو يحمل أصولاً** |
| إنهاء الخدمة ← التسوية النهائية | ❌ | `final_settlement_amount` رقم يُكتب يدويًا |
| المصروفات ← المشاريع | ❌ **مفتاح وهمي** | `ExpenseRequest.php:28` `project_code` **نص عادي** لا `project_id`. لا علاقة، لا ربط |
| الموظف ← المشاريع | ⚠️ اتجاه واحد | `Employee.php` فيه 43 علاقة و**لا** `projects()`/`timeEntries()` |
| محرك سير العمل مستخدم فعلاً؟ | ✅ **أقوى جزء** | يُستدعى من `LeaveRequestController:345,420`، `ExpenseRequestController:316,368`، `EmployeeJobChangeController:248,315`، `PerformanceReviewController:300`، `PayrollApprovalController`. عام وليس موازيًا |
| التقارير تغطي المشاريع؟ | ❌ | 12 تقريرًا كلها HR. **صفر تقرير مشاريع أو استغلال أو كلفة** |

### 3.3 التعريب/التدويل — ليس ثنائي اللغة

- `lang/` فيه **ملف واحد**: `lang/ar/permissions.php`.
- `config/app.php:81` → `locale = 'en'`.
- **46 استدعاء `__()` فقط** في 497 قالبًا — كل شيء آخر نصوص عربية مكتوبة صلبًا.
- الحقول ثنائية اللغة غير متسقة: `Project`/`Task` فيهما `name_ar`، لكن `Employee` فيه `full_name` واحد فقط، و`ProjectMember::getRoleNameArAttribute()` يكتب العربية داخل PHP.
- **نشر النظام بالإنجليزية = إعادة كتابة، لا تغيير إعداد.**

### 3.4 توثيق المشروع لا يطابق الواقع

23 ملف `.md` في جذر المشروع (4,786 سطر). `FEATURES_ANALYSIS_REPORT.md:4` يدّعي "**مكتمل 95%+، جاهز للإنتاج، 60+ ميزة**". `SYSTEM_STATUS.md` يعلّم التدريب والتوظيف والأهداف وإنهاء الخدمة والإشعارات الفورية كـ **"كاملة"** — وكلها قشور CRUD أو مربعات اختيار. الاستبيانات "تعمل" لكنها تُسقط خطأ 500.

**التوثيق يعدّ الجداول والمسارات، لا السلوك. اعتبر كل ✅ تعني "يوجد migration".**

---

## 4. جودة الكود والاختبارات

### 4.1 غياب المعاملات (Transactions) على العمليات المالية

**6 من 111 Controller فقط تستخدم `DB::transaction`.** أخطر فجوتين:

- **حساب الرواتب** — `PayrollController.php:205-266` يكتب ~20 عمودًا، و`savePayrollItems():536-555` يحذف بنود الرواتب ثم يعيد إدراجها **بلا معاملة**. فشل بين الحذف والإدراج = راتب بصافي غير صفري و**صفر بنود**. فساد مالي صامت.
- **اعتماد الإجازة** — `LeaveRequestController.php:326-404` يعدّل `workflow_instances` + `workflow_step_actions` + `leave_requests.status` ثم `updateLeaveBalance()` بلا معاملة. فشل الأخير = إجازة معتمدة والرصيد لم يُخصم.

النمط الصحيح موجود في `LeaveRequestController.php:164-184` — لم يُطبّق على مسار الاعتماد فقط.

### 4.2 استحقاق الإجازات يحسب مرتين ويقرّب خطأ

`app/Services/LeaveAccrualService.php:14-65`

- **بلا مفتاح idempotency** — تشغيل الأمر مرتين لنفس الشهر يضيف الأيام مرتين (السطر 51 جمع غير مشروط). لا يوجد `last_accrued_month`. **ولا يوجد أي جدولة في `routes/console.php`** → سيُشغّل يدويًا، ما يجعل التكرار مرجّحًا.
- `(int) round($days)` يبتر الكسور — قاعدة 1.75 يوم/شهر تصبح 2 → 24 يومًا سنويًا بدل 21. و`LeaveBalance` يعرّف `used_days`/`remaining_days` كـ `integer` بينما القاعدة `float`.
- `Country::find()` داخل حلقة الموظفين → N+1، ومطابقة البلد **بمقارنة نصوص الأسماء** لا بمفتاح أجنبي.
- بلا معاملة، بلا اختبار.

### 4.3 الاختبارات — الخطر الأكبر غير مُغطى

17 ملفًا / 43 دالة اختبار. منها **20 سقالة Breeze أصلية** و2 `ExampleTest`. الحقيقي: `LeaveRequestWorkflowTest` (8، كلها عن *من يحق له التصرف*)، `WorkflowStepsManagementTest` (3)، `DepartmentScopeTest` (2)، `PayrollDepartmentScopeTest` (1 — نطاق استعلام فقط)، `ExpenseRequestWorkflowActionTest` (1)، `ApiAuthTest` (2).

`grep "used_days\|net_salary\|calculate" tests/` = **لا شيء**. صفر تغطية لـ: حساب الرواتب، استحقاق وأرصدة الإجازات، قواعد الحضور، صلاحيات الـ 105 Controller الأخرى، الـ 12 خدمة غير المتعلقة بسير العمل.

**لا يوجد CI** — `.github/` غير موجود.

**العائق الحقيقي: Factory واحد لـ 101 موديل.** `database/factories/` فيه `UserFactory.php` فقط. لهذا يستهلك `LeaveRequestWorkflowTest` 147 سطرًا في `setUp` قبل أول تأكيد. كتابة اختبار رواتب اليوم تكلّف ساعة من السقالات.
→ **توليد Factories لأهم ~20 موديل هو أعلى إصلاح عائدًا في هذه المراجعة بأكملها.**

*(الإعداد نفسه سليم: `phpunit.xml:23-24` sqlite in-memory، Pest مع `RefreshDatabase`.)*

### 4.4 التحقق من المدخلات

**192 كتلة `validate([...])` داخلية في 90 Controller، مقابل 2 FormRequest فقط.**

- **14 ملفًا فيها مجموعات قواعد متطابقة حرفيًا** بين `store` و`update`: `AssetAssignment`, `AssetMaintenance`, `AttendanceBreak`, `AttendanceRule`, `CalendarEvent`, `EmployeeBankAccount`, `EmployeeViolation`, `LeaveRequest`, `OnboardingTemplate`, `PerformanceReview`, `Requisition`, `Salary`, `Shift`, `TrainingRecord`.
- **تحقق مالي ضعيف** — `PayrollController.php:169` يسمح بالكتابة فوق `base_salary` بأي `numeric|min:0` بلا حد أعلى ولا مقارنة بـ `employees.salary`، ثم يعيد الحساب منه. المقابل: `PayrollPaymentController.php:95` **يتحقق** أن `amount <= net_salary` — الغريزة الصحيحة مطبّقة في مكان واحد.

### 4.5 Controllers متضخّمة

- **`ExportController.php` (1,667 سطر) = 38 نسخة من نفس الصنف** — 38 `headings` + 38 `styles` + 38 `new class implements FromCollection`، متطابقة إلا في الموديل والأعمدة.
  → `App\Exports\GenericExport(Builder $query, array $columns, array $headings)` + خريطة config. **1,667 سطر → ~120.**
- **`Employee/SelfServiceController.php` (1,623 سطر) = 12 Controller في معطف واحد** — 43 دالة تغطي الإجازات والحضور والقسائم والمستندات والمهارات والأهداف والتقييمات والمزايا والمهام والمشاريع والتذاكر والاجتماعات والمصروفات والأصول والمخالفات والسياسات والعقود والإعلانات والتدريب والاستبيانات والتعيين، **بالإضافة إلى** نظام بناء لوحة كامل (`:126-322`).

**منطق أعمال يجب نقله:**

| المنطق | المكان الحالي | يجب أن يكون في |
|---|---|---|
| محرك الرواتب كاملاً | `PayrollController.php:271-555` — 285 سطر `private` | `PayrollCalculationService` |
| تعديل أرصدة الإجازات | `LeaveRequestController.php` — 4 مواضع `used_days ±=` منفصلة | `LeaveBalanceService::debit()/credit()` |
| نسب مساهمة صاحب العمل 12%/2% مكتوبة صلبًا | `PayrollController.php:520-521` | جدول `tax_settings` (موجود ويُستعلم في السطر 497!) |
| سلسلة المدراء/المعتمدين | `Employee.php:330-428` — يكرّر `ApprovalService` | `ApprovalService` |

> الـ 285 سطر الخاصة داخل `PayrollController` **غير قابلة للاختبار** لأنها دوال private على Controller يحتاج طلب HTTP. نقلها إلى خدمة يجعلها قابلة للاختبار وحده — وهذا يفتح أهم فجوة تغطية في المشروع.

### 4.6 التكرار البنيوي

التشابه النصي بين Controllers بسيطة: `ViolationType`↔`Shift` **50%**، `Branch`↔`Position` **41%**. الشكل متطابق دائمًا: constructor صلاحيات 6 أسطر → كتلة `filled('search')` + LIKE → `paginate` → `$request->all()` + `created_by`.

الأرقام: **312** `where('is_active', true)` · **157** فلتر LIKE · **51** كتلة بحث متطابقة · **65** `created_by => auth()->id()`.

**الإصلاح الرخيص عالي المردود:** `scopeActive()` + `scopeSearch(array $columns, ?string $term)` على موديل أساس، و`HasCreator` trait مع observer، وtrait للـ constructor. (`Concerns/ScopesByDepartment.php` يثبت أن الفريق قادر على هذا — لم يُوسّع فقط.)

الموديلات معقولة: 97/101 فيها `$casts`. لكن **19 scope في 9 موديلات من 101** — `Employee.php` فيه 48 علاقة و**صفر scope**، وهذا بالضبط سبب الـ312 فلتر `is_active` الداخلي.

### 4.7 الواجهة الأمامية — أدوات البناء حِمل ميت

`package.json` يعلن Vite 6 + Tailwind + Alpine + Axios. `@vite` موجود في **قالب واحد**: `welcome.blade.php`. `resources/js/` = **11 سطرًا**.

التطبيق الفعلي (338 قالبًا) يعمل على **Bootstrap 5 من jsDelivr CDN** + fullcalendar + jquery-orgchart + laravel-echo، كلها من CDN. النتيجة: لا bundling، لا SRI، لا تثبيت إصدارات، تضارب Tailwind 3 مع `@tailwindcss/vite` v4، و**2,552 سطر JS داخلي في 156 وسم `<script>` في 102 قالب**.

**تكرار في العرض:** `app/View/Components/` فيه `AppLayout`/`GuestLayout` من Breeze فقط. `resources/views/components/` 14 ملفًا مستخدمة ~46 مرة في 497 قالبًا. بينما كتلة رسائل التنبيه **منسوخة في 115 قالبًا**، و**40 منها تستخدم `{!! \Session::get('success') !!}`** — إخراج غير مُهرّب = XSS في اللحظة التي يُدخل فيها أي Controller مدخل مستخدم في رسالة flash (وبعضها يفعل).
→ `<x-alerts />` يصلح 40 منفذ XSS و115 تكرارًا بضربة واحدة.

### 4.8 نظافة المستودع

- **23 ملف `.md` في الجذر** منها **خمسة** ملفات متداخلة عن نفس السؤال (`MISSING_FEATURES_ANALYSIS`, `MISSING_FEATURES_FINAL`, `HR_SYSTEM_MISSING_FEATURES`, `REMAINING_FEATURES_ANALYSIS`, `FEATURES_ANALYSIS_REPORT`)، وملاحظات إصلاح أخطاء مثبّتة كتوثيق دائم (`TEST_TOGGLE.md`, `TOGGLE_STATUS_FIX_README.md`, `EDIT_USER_UPDATE_README.md`). + ثلاثة مجلدات خطط: `docs/`, `plan/`, `plans/`. **لا شيء يقول أي مستند هو الحالي.**
- `README.md` هو **README لارافيل الافتراضي** — بلا خطوات تشغيل ولا نظرة على النطاق ولا ملاحظات نشر.
- `admin/layouts/master.blade.php:13` لا يزال يحمل وصف القالب المُشترى: *"أفضل موقع للاعلانات المبوبة"* و`Author: claudSoft`.
- **git: 11 commit لـ ~90 ألف سطر.** كامل السجل: `add new project hr`, `new code hr`, `new code hr1`, `new code hr2`, `add new features`, `add new htaccess`, `add new style for all pages`, `add new permossion`, `add new permossion1`, `add new permossion2`, `add new code for new style`. لا فروع، لا PR، **لا إمكانية bisect أو تراجع عن أي تغيير محدد**.
- كود ميت قليل — `DelegationController` فقط غير مرتبط بأي مسار (+ `Http/Middleware/CheckPermission.php` غير مُسجّل في `bootstrap/app.php` وغير مستخدم).

---

## 5. الميزات الناقصة

### 5.1 ضروري لتكامل HR + PM حقيقي

1. **سير عمل اعتماد الساعات (Timesheet)** — وصّل حقول `ProjectTimeEntry.status`/`approved_by` **الموجودة أصلاً** بـ `WorkflowService` **المُسجّل أصلاً**. ~يوم عمل، وهو أعلى إصلاح قيمةً في النظام.
2. **ساعات قابلة للفوترة + سعر كلفة/فوترة للموظف** → تجميع كلفة فعلية للمشروع، ميزانية مقابل فعلي، هامش. حاليًا **صفر**.
3. **التوظيف ← إنشاء الموظف تلقائيًا** — قبول العرض ⇒ `Employee` + `User` + إطلاق `OnboardingProcess`. اليوم إدخال يدوي مزدوج.
4. **إخلاء الطرف مبني على بيانات** — استعلام `AssetAssignment` المفتوحة، المستندات غير المُرجعة، بدل الإجازات، حساب التسوية من الرواتب — لا أربعة مربعات اختيار.
5. **كيان العميل (Client/Customer)** — لا منتج PM قابل للبيع بدونه؛ يحجب الفوترة وربحية المشروع وتقارير المحفظة.
6. **تخطيط الموارد والطاقة الاستيعابية** (نسبة تخصيص، توفّر صافي بعد الإجازات والعطل والورديات). غائب كليًا.
7. **تفاعل الإجازات مع المشاريع** — تنبيه/إعادة تكليف المهام المتقاطعة مع إجازة معتمدة.
8. **تحويل `ExpenseRequest.project_code` إلى `project_id` حقيقي** ليتجمّع صرف المشروع.
9. **إصلاح قوالب `create`/`edit` للاستبيانات** — خطأ 500 في كود منشور.
10. **استيراد البيانات** (موظفون، حضور، أرصدة افتتاحية). **صفر كود استيراد** مقابل 1,667 سطر تصدير → تشغيل عميل جديد مستحيل عمليًا.
11. **توسيع الـ API** — `routes/api.php` **20 سطرًا / 6 نقاط** (دخول، me، قراءة حضور، أرصدة إجازات، إنشاء إجازة، إشعارات). لا مشاريع ولا مهام ولا ساعات ولا رواتب ولا اعتمادات. **لا تطبيق موبايل ممكن على هذا.**

### 5.2 مهم

12. معالم (Milestones)، تبعيات المهام، Gantt/المسار الحرج — لا يوجد موديل `Milestone` ولا `Dependency`.
13. تقارير المشاريع والاستغلال في `ReportController`؛ وحدة التقارير المجدولة فيها قالب واحد.
14. تدويل حقيقي — استخراج النصوص، `lang/en`، مبدّل لغة.
15. تأريخ فعّال (effective dating) لسجل الموظف — لا يوجد إلا `AuditLog`. **لا يمكن إعادة حساب راتب بتاريخ سابق.**
16. تغطية الإشعارات — 5 أصناف فقط كلها عن الاعتمادات. لا شيء لتكليف مهمة، انتهاء عقد، استحقاق تقييم، تذكير ساعات، إرجاع أصل.
17. نقل ثوابت الرواتب المكتوبة صلبًا (`/15`, `* 0.01` في `PayrollController.php:307`) إلى `AttendanceRule` **الموجود والمهمَل**.
18. تفعيل سلسلة التعاقب/الأهداف/التدريب/الأداء — **أو حذف الوحدات**؛ كما هي فهي وزن ميت.

### 5.3 لاحقًا

19. الفوترة والمدينون (يعتمد على #5).
20. تعدد الشركات/Tenancy — لا `Company` ولا `Tenant`؛ الفروع تجميلية.
21. التوقيع الإلكتروني (عروض العمل، السياسات) — صفر كود.
22. GDPR / الاحتفاظ بالبيانات / إخفاء الهوية — صفر نتائج.
23. الهيكل التنظيمي: `OrganizationChartController` 174 سطرًا وقالب واحد؛ بلا إعادة تنظيم بالسحب ولا إظهار الشواغر.

---

## 6. خطة عمل مرتّبة

### المرحلة 0 — أوقف النزيف (2–3 أيام)

| # | العمل | الملف |
|---|---|---|
| 1 | إضافة `permission:role-edit` لـ `applyTemplate` + نقل المسار إلى `ensure.admin` | `RoleController.php:15-29`, `routes/web.php:37` |
| 2 | حذف مسارات `register` | `routes/auth.php:16-19` |
| 3 | حذف `eval()` واستبداله بمُقيّم آمن | `PayrollController.php:462` |
| 4 | إضافة `permission` + تحقق ملكية لـ `download` | `EmployeeDocumentController.php:158` |
| 5 | نقل كل الملفات إلى القرص `local` + مسارات تحميل محمية | 8 Controllers + القوالب |
| 6 | `throttle:5,1` على 2FA وكود الدخول | `routes/auth.php:62`, `routes/employee.php:9-10` |
| 7 | `SESSION_SECURE_COOKIE=true`, `SESSION_ENCRYPT=true`, `APP_DEBUG=false` + `forceScheme('https')` | `.env.example`, Provider |
| 8 | حذف `routes/web.php:48` (المسار "التجريبي") | |
| 9 | `DB::transaction` حول حساب الرواتب واعتماد الإجازات/المصروفات | `PayrollController.php:205-266`, `LeaveRequestController.php:326-404` |

### المرحلة 1 — أسِّس للأمان الهيكلي (1–2 أسبوع)

10. **توليد Factories لأهم ~20 موديل** ← يفتح كل ما بعده.
11. `.github/workflows/tests.yml` يشغّل `php artisan test` — ساعة عمل، قبل الخطوة 12.
12. استخراج `PayrollCalculationService` و`LeaveBalanceService` + اختبارات وحدة لهما.
13. إصلاح `LeaveAccrualService`: idempotency + كسور عشرية + معاملة + إزالة N+1 + جدولة.
14. تطبيق `ScopesByDepartment` على كل Controllers أبناء الموظف (A5) وتقييد التصدير (A6).
15. Migration للفهارس (SQL في القسم 2) — مكسب صافٍ بلا تغيير كود.
16. إضافة `LogsActivity` إلى `Payroll`, `PayrollPayment`, `Salary`, `User`, `EmployeeBankAccount`, `EmployeeDocument`, `Contract` + تسجيل التصدير والانتحال.

### المرحلة 2 — نظّف وعمّق (3–4 أسابيع)

17. حلّ تعارض `salaries` ↔ `payrolls` (D1).
18. `GenericExport` + `FromQuery`/`WithChunkReading` (‏1,667 → ~120 سطر).
19. تقسيم `SelfServiceController` إلى Controllers بحسب السياق.
20. FormRequests للـ14 ملفًا المكرّرة أولاً، وإزالة `$request->all()` في نفس المرور.
21. `<x-alerts />` (‏40 منفذ XSS + 115 تكرار).
22. `scopeActive`/`scopeSearch`/`HasCreator` — يحذف مئات الأسطر.
23. تصحيح أخطاء أعمدة التصدير (D3) وتوحيد أعمدة `enum` إلى `string`.
24. دمج ملفات `.md` الـ23 في `docs/` + README حقيقي + تنظيف `master.blade.php:13`.
25. تبنّي انضباط git: فروع، commits وصفية، PRs.

### المرحلة 3 — اجعله منتجًا (2–3 أشهر)

26. الساعات ← الاعتماد ← الكلفة ← ميزانية المشروع (الميزات 1، 2).
27. سلسلة التوظيف ← التعيين، وسلسلة إخلاء الطرف (3، 4).
28. كيان العميل + تخطيط الموارد (5، 6).
29. تدويل حقيقي + توسيع API + استيراد بيانات (10، 11، 14).
30. القرار الصعب: **فعّل أو احذف** التعاقب والأهداف والتدريب والاستبيانات.

---

## 7. رأيي النهائي

الفريق قادر على كتابة برمجيات جيدة — الدليل موجود: محرك سير العمل، حساب الرواتب، `ScopesByDepartment`، `DashboardController`، وتصميم المخطط. ما حدث هو أن هذه القدرة استُهلكت في **التوسّع الأفقي** (101 موديل) بدل **التعميق والربط**.

**المخاطر الثلاثة الفعلية:**

1. **أمني** — سلسلة (تسجيل مفتوح ← `applyTemplate` غير محمي) قابلة للاستغلال من الإنترنت وتنتهي بصلاحيات Admin كاملة. هذا وحده يمنع النشر.
2. **مالي** — أخطر كود في المستودع (285 سطر حساب رواتب) **غير قابل للاختبار وغير مُختبر وغير محمي بمعاملة**. مع تعارض `salaries`/`payrolls` وتعدد العملات الديكوري، أول خطأ في الرواتب سيكون خطأ ثقة لا خطأ كود.
3. **مصداقية** — التوثيق يقول 95%، والكود يقول ~15% عمق. أي التزام أمام إدارة أو عميل مبني على تلك الملفات سيسقط في أول عرض تفصيلي.

**التوصية:** توقّف عن إضافة الوحدات. نفّذ المرحلة 0 هذا الأسبوع، والمرحلة 1 هذا الشهر. ثم اختر **5 وحدات فقط** (الرواتب، الإجازات، الحضور، المشاريع+الساعات، التوظيف→التعيين) وأوصلها بعمق حقيقي من طرف إلى طرف. خمس وحدات متكاملة تُبنى عليها ثقة أكثر من خمسين قشرة CRUD.

النظام ليس فاشلاً — هو **نموذج أولي واسع جدًا في منتصف الطريق**، ويحتاج أن يُعرَّف كذلك بصراحة قبل أي وعد بموعد إطلاق.
