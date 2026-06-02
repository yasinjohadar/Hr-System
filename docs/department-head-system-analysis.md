# تحليل شامل ومفصّل: نظام رؤساء الأقسام والصلاحيات وسير العمل

> **تاريخ التحليل:** 2026-05-09  
> **النظام:** Laravel HR Management System  
> **الإطار:** Laravel 11 + Spatie Permission + Blade Templates  
> **قاعدة البيانات:** MySQL

---

## الفهرس التفصيلي

```
الجزء الأول: البنية التحتية للنظام
├── 1. مخطط قاعدة البيانات التفصيلي
├── 2. نموذج التابعية والإدارة
├── 3. نظام الأدوار والصلاحيات (RBAC)
└── 4. البنية المعمارية للنظام

الجزء الثاني: سير عمل الموافقات
├── 5. نظام Workflow التفصيلي
├── 6. ApprovalService - التحليل السطري
├── 7. WorkflowService - التحليل السطري
├── 8. دورة حياة الطلب (من الإنشاء إلى الاعتماد)
└── 9. نظام الإشعارات

الجزء الثالث: التحليل النقدي
├── 10. المشاكل والثغرات (تفصيلي مع أمثلة)
├── 11. تحليل الأمان
├── 12. تحليل الأداء
└── 13. مقارنة مع أفضل الممارسات

الجزء الرابع: خطة التطوير
├── 14. المتطلبات التفصيلية
├── 15. مخططات قاعدة البيانات الجديدة
├── 16. خطة التنفيذ خطوة بخطوة
├── 17. نماذج الكود المقترحة
└── 18. معايير القبول والاختبار
```

---

# الجزء الأول: البنية التحتية للنظام

## 1. مخطط قاعدة البيانات التفصيلي

### 1.1 جدول الأقسام (departments)

```sql
CREATE TABLE departments (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name            VARCHAR(255) NOT NULL,
    code            VARCHAR(100) UNIQUE,
    description     TEXT,
    manager_id      BIGINT UNSIGNED NULL,        -- FK → users.id
    parent_id       BIGINT UNSIGNED NULL,        -- FK → departments.id (self-referential)
    is_active       TINYINT(1) DEFAULT 1,
    created_at      TIMESTAMP NULL,
    updated_at      TIMESTAMP NULL,
    deleted_at      TIMESTAMP NULL,              -- Soft Deletes

    INDEX idx_manager (manager_id),
    INDEX idx_parent (parent_id),
    INDEX idx_active (is_active),

    CONSTRAINT fk_dept_manager FOREIGN KEY (manager_id) REFERENCES users(id) ON DELETE SET NULL,
    CONSTRAINT fk_dept_parent  FOREIGN KEY (parent_id)  REFERENCES departments(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**ملاحظات هامة:**
- `manager_id` يشير إلى `users.id` وليس `employees.id` ← **مشكلة هيكلية**
- `parent_id` يدعم تسلسل هرمي غير محدود العمق
- `deleted_at` يدعم الحذف الناعم

**مثال على البيانات:**

| id | name | code | manager_id | parent_id | is_active |
|----|------|------|------------|-----------|-----------|
| 1 | الإدارة العامة | ADMIN | 1 | NULL | 1 |
| 2 | الموارد البشرية | HR | 5 | NULL | 1 |
| 3 | التوظيف | RECRUIT | 8 | 2 | 1 |
| 4 | الرواتب | PAYROLL | 12 | 2 | 1 |
| 5 | تقنية المعلومات | IT | 15 | NULL | 1 |
| 6 | الدعم الفني | SUPPORT | 20 | 5 | 1 |

```
الإدارة العامة (manager: user_1)
├── الموارد البشرية (manager: user_5)
│   ├── التوظيف (manager: user_8)
│   └── الرواتب (manager: user_12)
└── تقنية المعلومات (manager: user_15)
    └── الدعم الفني (manager: user_20)
```

### 1.2 جدول الموظفين (employees)

```sql
CREATE TABLE employees (
    id                        BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    employee_code             VARCHAR(100) UNIQUE,
    user_id                   BIGINT UNSIGNED NULL,        -- FK → users.id
    department_id             BIGINT UNSIGNED NULL,        -- FK → departments.id
    position_id               BIGINT UNSIGNED NULL,        -- FK → positions.id
    branch_id                 BIGINT UNSIGNED NULL,        -- FK → branches.id
    manager_id                BIGINT UNSIGNED NULL,        -- FK → employees.id (self-referential)
    first_name                VARCHAR(255) NOT NULL,
    last_name                 VARCHAR(255) NOT NULL,
    full_name                 VARCHAR(255),
    national_id               VARCHAR(50),
    date_of_birth             DATE,
    gender                    ENUM('male', 'female'),
    marital_status            ENUM('single', 'married', 'divorced', 'widowed'),
    address                   TEXT,
    city                      VARCHAR(100),
    country                   VARCHAR(100),
    postal_code               VARCHAR(20),
    personal_email            VARCHAR(255),
    personal_phone            VARCHAR(50),
    emergency_contact_name    VARCHAR(255),
    emergency_contact_phone   VARCHAR(50),
    emergency_contact_relation VARCHAR(100),
    hire_date                 DATE,
    probation_end_date        DATE,
    contract_start_date       DATE,
    contract_end_date         DATE,
    employment_type           ENUM('full_time', 'part_time', 'contract', 'intern', 'temporary'),
    employment_status         ENUM('active', 'on_leave', 'terminated', 'resigned', 'retired'),
    salary                    DECIMAL(15,2),
    work_location             VARCHAR(255),
    work_phone                VARCHAR(50),
    work_email                VARCHAR(255),
    notes                     TEXT,
    photo                     VARCHAR(255),
    created_by                BIGINT UNSIGNED,
    is_active                 TINYINT(1) DEFAULT 1,
    created_at                TIMESTAMP NULL,
    updated_at                TIMESTAMP NULL,
    deleted_at                TIMESTAMP NULL,

    INDEX idx_user (user_id),
    INDEX idx_department (department_id),
    INDEX idx_manager (manager_id),
    INDEX idx_position (position_id),
    INDEX idx_branch (branch_id),
    INDEX idx_active (is_active),
    INDEX idx_code (employee_code),

    CONSTRAINT fk_emp_user       FOREIGN KEY (user_id)       REFERENCES users(id) ON DELETE SET NULL,
    CONSTRAINT fk_emp_department FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE SET NULL,
    CONSTRAINT fk_emp_position   FOREIGN KEY (position_id)   REFERENCES positions(id) ON DELETE SET NULL,
    CONSTRAINT fk_emp_branch     FOREIGN KEY (branch_id)     REFERENCES branches(id) ON DELETE SET NULL,
    CONSTRAINT fk_emp_manager    FOREIGN KEY (manager_id)    REFERENCES employees(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**ملاحظات هامة:**
- `manager_id` يشير إلى `employees.id` ← **نظام إدارة مزدوج**
- `user_id` يربط الموظف بحساب النظام
- `department_id` يربط الموظف بالقسم

### 1.3 جدول المستخدمين (users)

```sql
CREATE TABLE users (
    id                  BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name                VARCHAR(255) NOT NULL,
    username            VARCHAR(255) UNIQUE,
    email               VARCHAR(255) UNIQUE,
    phone               VARCHAR(50),
    password            VARCHAR(255) NOT NULL,
    status              VARCHAR(50) DEFAULT 'active',
    is_active           TINYINT(1) DEFAULT 1,
    photo               VARCHAR(255),
    created_by          BIGINT UNSIGNED,
    last_login_at       TIMESTAMP NULL,
    last_login_ip       VARCHAR(45),
    last_login_user_agent TEXT,
    email_verified_at   TIMESTAMP NULL,
    remember_token      VARCHAR(100),
    created_at          TIMESTAMP NULL,
    updated_at          TIMESTAMP NULL,

    INDEX idx_email (email),
    INDEX idx_username (username),
    INDEX idx_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- جداول Spatie Permission
CREATE TABLE roles (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name            VARCHAR(255) NOT NULL,
    guard_name      VARCHAR(255) NOT NULL,
    created_at      TIMESTAMP NULL,
    updated_at      TIMESTAMP NULL,
    UNIQUE INDEX idx_name_guard (name, guard_name)
);

CREATE TABLE model_has_roles (
    role_id     BIGINT UNSIGNED NOT NULL,
    model_type  VARCHAR(255) NOT NULL,
    model_id    BIGINT UNSIGNED NOT NULL,
    PRIMARY KEY (role_id, model_id, model_type),
    INDEX idx_model (model_id, model_type),
    CONSTRAINT fk_mhr_role FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE
);

CREATE TABLE permissions (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name            VARCHAR(255) NOT NULL,
    guard_name      VARCHAR(255) NOT NULL,
    created_at      TIMESTAMP NULL,
    updated_at      TIMESTAMP NULL,
    UNIQUE INDEX idx_name_guard (name, guard_name)
);

CREATE TABLE role_has_permissions (
    permission_id BIGINT UNSIGNED NOT NULL,
    role_id       BIGINT UNSIGNED NOT NULL,
    PRIMARY KEY (permission_id, role_id),
    CONSTRAINT fk_rhp_permission FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE,
    CONSTRAINT fk_rhp_role       FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE
);
```

### 1.4 جدول سير العمل (workflows + workflow_steps + workflow_instances)

```sql
CREATE TABLE workflows (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name            VARCHAR(255) NOT NULL,
    name_ar         VARCHAR(255),
    code            VARCHAR(100) UNIQUE,
    description     TEXT,
    type            ENUM('leave_request', 'expense_request', 'task_approval', 'performance_review', 'custom'),
    is_active       TINYINT(1) DEFAULT 1,
    created_by      BIGINT UNSIGNED,
    created_at      TIMESTAMP NULL,
    updated_at      TIMESTAMP NULL,
    deleted_at      TIMESTAMP NULL,

    INDEX idx_type (type),
    INDEX idx_active (is_active)
);

CREATE TABLE workflow_steps (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    workflow_id     BIGINT UNSIGNED NOT NULL,
    name            VARCHAR(255),
    name_ar         VARCHAR(255),
    step_order      INT NOT NULL,
    approver_type   ENUM('user', 'role', 'department_manager', 'employee_manager', 'custom'),
    approver_id     BIGINT UNSIGNED NULL,        -- FK → users.id
    role_id         BIGINT UNSIGNED NULL,        -- FK → roles.id
    is_required     TINYINT(1) DEFAULT 1,
    can_reject      TINYINT(1) DEFAULT 1,
    timeout_hours   INT NULL,
    conditions      JSON NULL,                   -- شروط ديناميكية
    created_at      TIMESTAMP NULL,
    updated_at      TIMESTAMP NULL,
    deleted_at      TIMESTAMP NULL,

    INDEX idx_workflow (workflow_id),
    INDEX idx_order (step_order),
    INDEX idx_approver_type (approver_type),

    CONSTRAINT fk_ws_workflow FOREIGN KEY (workflow_id) REFERENCES workflows(id) ON DELETE CASCADE
);

CREATE TABLE workflow_instances (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    workflow_id     BIGINT UNSIGNED NOT NULL,
    workflow_step_id BIGINT UNSIGNED NOT NULL,
    entity_type     VARCHAR(255) NOT NULL,       -- morph: 'App\Models\LeaveRequest'
    entity_id       BIGINT UNSIGNED NOT NULL,
    status          ENUM('pending', 'in_progress', 'approved', 'rejected', 'cancelled'),
    initiated_by    BIGINT UNSIGNED,             -- FK → users.id
    started_at      TIMESTAMP NULL,
    completed_at    TIMESTAMP NULL,
    notes           TEXT,
    created_at      TIMESTAMP NULL,
    updated_at      TIMESTAMP NULL,
    deleted_at      TIMESTAMP NULL,

    INDEX idx_entity (entity_type, entity_id),
    INDEX idx_status (status),
    INDEX idx_step (workflow_step_id),

    CONSTRAINT fk_wi_workflow FOREIGN KEY (workflow_id) REFERENCES workflows(id) ON DELETE CASCADE,
    CONSTRAINT fk_wi_step     FOREIGN KEY (workflow_step_id) REFERENCES workflow_steps(id) ON DELETE CASCADE
);
```

### 1.5 جدول طلبات الإجازة (leave_requests)

```sql
CREATE TABLE leave_requests (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    employee_id     BIGINT UNSIGNED NOT NULL,
    leave_type_id   BIGINT UNSIGNED NOT NULL,
    start_date      DATE NOT NULL,
    end_date        DATE NOT NULL,
    days_count      INT NOT NULL,
    reason          TEXT,
    notes           TEXT,
    status          ENUM('pending', 'approved', 'rejected', 'cancelled'),
    approved_by     BIGINT UNSIGNED NULL,        -- FK → users.id
    approved_at     TIMESTAMP NULL,
    rejection_reason TEXT,
    created_by      BIGINT UNSIGNED,
    created_at      TIMESTAMP NULL,
    updated_at      TIMESTAMP NULL,
    deleted_at      TIMESTAMP NULL,

    INDEX idx_employee (employee_id),
    INDEX idx_status (status),
    INDEX idx_dates (start_date, end_date),

    CONSTRAINT fk_lr_employee FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE,
    CONSTRAINT fk_lr_type     FOREIGN KEY (leave_type_id) REFERENCES leave_types(id) ON DELETE RESTRICT
);
```

### 1.6 جدول أرصدة الإجازات (leave_balances)

```sql
CREATE TABLE leave_balances (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    employee_id     BIGINT UNSIGNED NOT NULL,
    leave_type_id   BIGINT UNSIGNED NOT NULL,
    year            INT NOT NULL,
    total_days      INT NOT NULL,
    used_days       INT DEFAULT 0,
    remaining_days  INT,
    carried_forward INT DEFAULT 0,
    created_at      TIMESTAMP NULL,
    updated_at      TIMESTAMP NULL,
    deleted_at      TIMESTAMP NULL,

    UNIQUE INDEX idx_employee_type_year (employee_id, leave_type_id, year),

    CONSTRAINT fk_lb_employee FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE,
    CONSTRAINT fk_lb_type     FOREIGN KEY (leave_type_id) REFERENCES leave_types(id) ON DELETE RESTRICT
);
```

---

## 2. نموذج التابعية والإدارة

### 2.1 النظام المزدوج للإدارة

يوجد **نظامان متوازيان ومختلفان** لإدارة التابعية في النظام:

```
┌─────────────────────────────────────────────────────────────────────────┐
│                    النظام الأول: إدارة الأقسام                          │
│                    (Department-Level Management)                         │
│                                                                         │
│  departments.manager_id → users.id                                      │
│                                                                         │
│  • رئيس القسم = User (ليس بالضرورة Employee)                            │
│  • يتم تعيين دور 'department_head' تلقائياً                             │
│  • الصلاحيات مبنية على الدور (RBAC)                                     │
│  • النطاق: جميع موظفي القسم + الأقسام الفرعية                           │
│                                                                         │
│  User (id=5) ──manager──→ Department (id=2, HR)                         │
│                              │                                          │
│                              ├── Employee (id=10, أحمد)                  │
│                              ├── Employee (id=11, سارة)                  │
│                              └── Employee (id=12, محمد)                  │
└─────────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────────────┐
│                   النظام الثاني: الإدارة المباشرة                        │
│                   (Employee-Level Management)                            │
│                                                                         │
│  employees.manager_id → employees.id                                    │
│                                                                         │
│  • المدير المباشر = Employee آخر                                        │
│  • لا دور RBAC مطلوب                                                    │
│  • التسلسل: manager_id → manager_id → ... (سلسلة)                       │
│  • يُستخدم في: getDirectManager(), getManagerChain()                    │
│                                                                         │
│  Employee (id=1, المدير)                                                │
│      │ manager_id=1                                                     │
│      ├── Employee (id=5, رئيس فريق)                                     │
│      │       │ manager_id=5                                             │
│      │       ├── Employee (id=10, مطور)                                 │
│      │       └── Employee (id=11, مصمم)                                 │
│      └── Employee (id=6, رئيس فريق آخر)                                 │
└─────────────────────────────────────────────────────────────────────────┘
```

### 2.2 التحليل التفصيلي للعلاقات

**في Employee Model (الأسطر 330-425):**

```php
// السطر 330-333: المدير المباشر
public function getDirectManager(): ?Employee
{
    if (! $this->manager_id) return null;
    return Employee::with('user')->find($this->manager_id);
}
// ← يُرجع Employee الذي يملكه هذا الموظف كمدير مباشر
// ← manager_id يشير إلى employees.id

// السطر 338-363: رئيس القسم
public function getDepartmentManager(): ?User
{
    $department = $this->department;
    while ($department) {
        if ($department->manager_id) {
            return User::find($department->manager_id);
        }
        // يتسلل للأب إذا لم يكن هناك مدير
        $department = $department->parent;
    }
    return null;
}
// ← يُرجع User الذي يدير قسم هذا الموظف
// ← يتسلل عبر parent_id حتى يجد مدير
// ← departments.manager_id يشير إلى users.id

// السطر 368-376: رئيس القسم كـ Employee
public function getDepartmentManagerEmployee(): ?Employee
{
    $deptManager = $this->getDepartmentManager();
    if (! $deptManager) return null;
    return Employee::where('user_id', $deptManager->id)->first();
}
// ← يحوّل User المدير إلى Employee
// ← قد يُرجع null إذا لم يكن للمدير سجل Employee!

// السطر 381-392: سلسلة المدراء
public function getManagerChain(): array
{
    $chain = [];
    $current = $this;
    $visited = [];

    while ($current->manager_id) {
        if (in_array($current->manager_id, $visited)) break; // حلقة لا نهائية
        $visited[] = $current->manager_id;
        $manager = $current->getDirectManager();
        if (! $manager) break;
        $chain[] = $manager;
        $current = $manager;
    }
    return $chain;
}
// ← يُرجع مصفوفة من Employee من الأدنى للأعلى
// ← يحمي من الحلقات اللانهائية

// السطر 397-413: هل هذا الموظف تابع لمدير؟
public function isManagedBy(Employee $manager): bool
{
    $chain = $this->getManagerChain();
    foreach ($chain as $m) {
        if ($m->id === $manager->id) return true;
    }
    return false;
}

// السطر 418-425: الحصول على الموافِق حسب النوع
public function getApproverForType(string $approverType): ?User
{
    return match($approverType) {
        'employee_manager' => $this->getDirectManager()?->user,
        'department_manager' => $this->getDepartmentManager(),
        default => null,
    };
}
```

### 2.3 في User Model (الأسطر 40-56):

```php
// السطر 40-43: هل المستخدم رئيس قسم؟
public function isDepartmentHead(): bool
{
    return $this->hasRole('department_head') && ! $this->hasRole('admin');
}
// ← يشترط وجود دور department_head
// ← يستبعد admins (حتى لو لديهم الدور)

// السطر 45-49: الأقسام المُدارة
public function getManagedDepartmentIds(): array
{
    if (! $this->isDepartmentHead()) return [];
    return Department::where('manager_id', $this->id)->pluck('id')->all();
}
// ← يُرجع معرفات الأقسام التي manager_id = هذا المستخدم
// ← لا يشمل الأقسام الفرعية (children)!

// السطر 51-56: الموظفون المُدارون
public function getManagedEmployeeIds(): array
{
    $departmentIds = $this->getManagedDepartmentIds();
    if (empty($departmentIds)) return [];
    return Employee::whereIn('department_id', $departmentIds)->pluck('id')->all();
}
// ← يُرجع موظفي الأقسام المُدارة فقط
// ← لا يشمل المرؤوسين المباشرين في أقسام أخرى!
// ← لا يشمل موظفي الأقسام الفرعية!
```

### 2.4 سيناريو تعارض النظامين

```
السيناريو:
- أحمد (employee_id=10) في قسم التوظيف (department_id=3)
- رئيس قسم التوظيف: سارة (user_id=8, department.manager_id=8)
- المدير المباشر لأحمد: خالد (employee_id=7, employees.manager_id=7)
- خالد في قسم التطوير (department_id=5)

عند تقديم أحمد طلب إجازة:
┌──────────────────────────────────────────────────────────────┐
│ الخطوة 1 في Workflow: approver_type = 'department_manager'   │
│ → getDepartmentManager() على أحمد                            │
│ → قسم التوظيف → manager_id = 8 (سارة)                        │
│ → سارة هي الموافِقة في الخطوة 1                              │
│                                                              │
│ السؤال: أين خالد (المدير المباشر)؟                           │
│ الجواب: لا يظهر في سير الموافقة الافتراضي!                   │
└──────────────────────────────────────────────────────────────┘

المشكلة: المدير المباشر (خالد) لا يشارك في الموافقة
         لأن Workflow يستخدم department_manager فقط
```

---

## 3. نظام الأدوار والصلاحيات (RBAC)

### 3.1 الأدوار المعرفة في النظام

| الدور | الوصف | عدد الصلاحيات |
|-------|-------|---------------|
| `admin` | مدير النظام - صلاحيات كاملة | جميع الصلاحيات |
| `user` | مستخدم عادي - وصول محدود | 4 |
| `employee` | موظف - لوحة الموظف فقط | 1 |
| `department_head` | رئيس قسم - إدارة قسمه | 28 |
| `executive_director` | مدير تنفيذي - موافقة هرمية | 13 |
| `general_manager` | مدير عام - موافقة نهائية | 13 |

### 3.2 صلاحيات department_head بالتفصيل

```
department_head permissions (28 صلاحية):

┌─────────────────────────────────────────────────────────────────┐
│ إدارة الموظفين (2)                                              │
│ ├── employee-list          → عرض قائمة الموظفين                │
│ └── employee-show          → عرض تفاصيل موظف                    │
├─────────────────────────────────────────────────────────────────┤
│ إدارة الإجازات (3)                                               │
│ ├── leave-request-list     → عرض طلبات الإجازة                  │
│ ├── leave-request-show     → عرض تفاصيل طلب                     │
│ └── leave-request-approve  → الموافقة على طلب إجازة             │
├─────────────────────────────────────────────────────────────────┤
│ إدارة الحضور (2)                                                 │
│ ├── attendance-list        → عرض سجلات الحضور                   │
│ └── attendance-show        → عرض تفاصيل سجل                     │
├─────────────────────────────────────────────────────────────────┤
│ إدارة المصروفات (3)                                              │
│ ├── expense-request-list   → عرض طلبات المصروفات               │
│ ├── expense-request-show   → عرض تفاصيل طلب                     │
│ └── expense-request-approve→ الموافقة على طلب مصروفات           │
├─────────────────────────────────────────────────────────────────┤
│ إدارة التقييمات (3)                                              │
│ ├── performance-review-list→ عرض التقييمات                      │
│ ├── performance-review-show→ عرض تفاصيل تقييم                   │
│ └── performance-review-approve → الموافقة على تقييم             │
├─────────────────────────────────────────────────────────────────┤
│ إدارة الموافقات (2)                                              │
│ ├── approval-list          → عرض طلبات الموافقة                 │
│ └── approval-show          → عرض تفاصيل موافقة                  │
├─────────────────────────────────────────────────────────────────┤
│ التقارير (10)                                                    │
│ ├── report-view            → عرض التقارير                       │
│ ├── report-employees       → تقرير الموظفين                     │
│ ├── report-attendance      → تقرير الحضور                       │
│ ├── report-salaries        → تقرير الرواتب                      │
│ ├── report-leaves          → تقرير الإجازات                     │
│ ├── report-performance     → تقرير التقييمات                    │
│ ├── report-training        → تقرير التدريب                      │
│ ├── report-recruitment     → تقرير التوظيف                     │
│ ├── report-benefits        → تقرير المزايا                      │
│ ├── report-dashboard       → لوحة التقارير                      │
│ ├── report-turnover        → تقرير دوران الموظفين               │
│ ├── report-training-effectiveness → فعالية التدريب              │
│ └── report-kpis            → مؤشرات الأداء                      │
├─────────────────────────────────────────────────────────────────┤
│ الإشعارات (2)                                                    │
│ ├── notification-list      → عرض الإشعارات                      │
│ └── notification-mark-read → تحديد كمقروء                       │
├─────────────────────────────────────────────────────────────────┤
│ لوحة التحكم (1)                                                  │
│ └── dashboard-view         → عرض لوحة التحكم                    │
└─────────────────────────────────────────────────────────────────┘
```

### 3.3 آلية تعيين الدور تلقائياً

**في DepartmentController (الأسطر 177-200):**

```php
// عند تعيين مدير لقسم:
private function ensureUserHasDepartmentHeadRole(?User $user): void
{
    if (! $user || $user->hasRole('admin')) return;

    $role = Role::firstOrCreate(['name' => 'department_head']);
    if (! $user->hasRole('department_head')) {
        $user->assignRole('department_head');
    }
}

// عند إزالة مدير من قسم:
private function revokeDepartmentHeadRoleIfNotManager(?User $user): void
{
    if (! $user) return;

    // هل يدير أي قسم آخر؟
    $stillManages = Department::where('manager_id', $user->id)->exists();
    if (! $stillManages && $user->hasRole('department_head')) {
        $user->removeRole('department_head');
    }
}
```

**متى يتم الاستدعاء:**

| العملية | الاستدعاء |
|---------|-----------|
| إنشاء قسم جديد | `ensureUserHasDepartmentHeadRole($request->manager_id)` |
| تحديث قسم | `ensureUserHasDepartmentHeadRole($newManager)` + `revokeDepartmentHeadRoleIfNotManager($oldManager)` |
| حذف قسم | `revokeDepartmentHeadRoleIfNotManager($department->manager)` |

### 3.4 آلية التصفية في المتحكمات

**النمط الموحد في 6 متحكمات:**

```php
// مثال من LeaveRequestController (index method):
public function index(Request $request)
{
    $query = LeaveRequest::with(['employee', 'leaveType', 'approver']);

    // تصفية رئيس القسم
    if (Auth::user()->isDepartmentHead()) {
        $employeeIds = Auth::user()->getManagedEmployeeIds();
        if (!empty($employeeIds)) {
            $query->whereIn('employee_id', $employeeIds);
        } else {
            $query->whereRaw('1 = 0'); // لا نتائج
        }
    }

    // فلاتر إضافية
    if ($request->filled('employee_id')) {
        $query->where('employee_id', $request->employee_id);
    }
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    $leaveRequests = $query->latest()->paginate(15);
    // ...
}
```

**هذا النمط يتكرر في:**

| المتحكم | السطر | الكيان المصفّى |
|---------|-------|-----------------|
| `LeaveRequestController` | 73 | `employee_id` في leave_requests |
| `ExpenseRequestController` | 70 | `employee_id` في expense_requests |
| `AttendanceController` | 68 | `employee_id` في attendances |
| `EmployeeController` | 205 | الأقسام في employees |
| `PerformanceReviewController` | 67 | `employee_id` في performance_reviews |
| `ReportController` | متعدد | جميع التقارير |

---

## 4. البنية المعمارية للنظام

### 4.1 المزدوجة في الواجهات

```
┌─────────────────────────────────────────────────────────────────┐
│                        Middleware Routing                        │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  routes/web.php                                                 │
│  ├── middleware: web                                            │
│  │   └── / → يوجه حسب الدور:                                   │
│  │       ├── hasRole('employee') → employee.dashboard           │
│  │       └── else → admin.dashboard                             │
│  │                                                              │
│  ├── routes/admin.php                                           │
│  │   └── middleware: auth, check.user.active, ensure.admin      │
│  │       └── ensure.admin يسمح لـ: admin, user, department_head│
│  │                                                              │
│  └── routes/employee.php                                        │
│      └── middleware: auth, check.user.active, ensure.employee   │
│          └── ensure.employee يشترط: hasRole('employee')         │
│                                                                 │
│  ملاحظة: المستخدم يمكنه أن يكون employee + department_head       │
│  في نفس الوقت!                                                  │
└─────────────────────────────────────────────────────────────────┘
```

### 4.2 Middleware التحليل

**EnsureUserIsAdmin (الأسطر 1-32):**

```php
public function handle(Request $request, Closure $next): Response
{
    if (!Auth::check()) return $next($request);

    $user = Auth::user();

    // إذا كان لديه دور employee فقط (بدون admin/user/department_head)
    // ← ارجعه للوحة الموظف
    if ($user->hasRole('employee') &&
        !$user->hasAnyRole(['admin', 'user', 'department_head'])) {
        return redirect()->route('employee.dashboard')
            ->with('error', 'ليس لديك صلاحية الدخول إلى لوحة الإدارة.');
    }

    return $next($request);
}
```

**EnsureUserIsEmployee (الأسطر 1-26):**

```php
public function handle(Request $request, Closure $next): Response
{
    // إذا كان مسجلاً وليس لديه دور employee
    // ← ارجعه للوحة الأدمن
    if (Auth::check() && !Auth::user()->hasRole('employee')) {
        return redirect()->route('admin.dashboard')
            ->with('error', 'لوحة الموظف مخصصة للموظفين فقط.');
    }

    return $next($request);
}
```

**النتيجة:** مستخدم بأدوار `['employee', 'department_head']`:
- ✅ يمكنه دخول لوحة الأدمن (لديه department_head)
- ✅ يمكنه دخول لوحة الموظف (لديه employee)

---

# الجزء الثاني: سير عمل الموافقات

## 5. نظام Workflow التفصيلي

### 5.1 سير العمل الافتراضي (من WorkflowSeeder)

```
┌──────────────────────────────────────────────────────────────────────┐
│                    Leave Request Workflow                             │
│                    (workflow.type = 'leave_request')                  │
├──────────────────────────────────────────────────────────────────────┤
│                                                                      │
│  الخطوة 1                    الخطوة 2              الخطوة 3          │
│  ┌──────────────────────┐  ┌──────────────────┐  ┌───────────────┐ │
│  │ name: Department Mgr │  │ name: Exec Dir   │  │ name: Gen Mgr │ │
│  │ step_order: 1        │  │ step_order: 2    │  │ step_order: 3 │ │
│  │ approver_type:       │  │ approver_type:   │  │ approver_type:│ │
│  │   department_manager │  │   role           │  │   role        │ │
│  │ approver_id: NULL    │  │ role_id:         │  │ role_id:      │ │
│  │ role_id: NULL        │  │   exec_director  │  │   gen_manager │ │
│  │ is_required: true    │  │ is_required: true│  │ is_required:  │ │
│  │ can_reject: true     │  │ can_reject: true │  │   true        │ │
│  │ timeout_hours: NULL  │  │ timeout_hours:   │  │ can_reject:   │ │
│  │ conditions: NULL     │  │   NULL           │  │   true        │ │
│  └──────────────────────┘  │ timeout_hours:   │  │ timeout_hours:│ │
│                            │   NULL           │  │   NULL        │ │
│                            │ conditions: NULL │  │ conditions:   │ │
│                            └──────────────────┘  │   NULL        │ │
│                                                  └───────────────┘ │
│                                                                      │
│  نفس الهيكل لـ: expense_request, employee_job_change                 │
└──────────────────────────────────────────────────────────────────────┘
```

### 5.2 WorkflowStep - أنواع الموافِقين

| approver_type | كيف يتم التحديد | مثال |
|---------------|-----------------|------|
| `user` | مباشر من `approver_id` | المستخدم رقم 5 |
| `role` | أول مستخدم نشط بهذا الدور | أول admin |
| `department_manager` | `departments.manager_id` مع التسلل للأب | رئيس قسم الموظف |
| `employee_manager` | `employees.manager_id` مع العودة لرئيس القسم | المدير المباشر |
| `custom` | fallback إلى `approver_id` أو منطق مخصص | حسب الكيان |

### 5.3 حقل conditions (JSON)

```json
{
    "min_days": 5,
    "max_amount": 10000,
    "leave_types": ["annual", "unpaid"],
    "department_ids": [1, 2, 3]
}
```

**ملاحظة:** هذا الحقل موجود في قاعدة البيانات لكن **لا يُستخدم حالياً** في الكود!

---

## 6. ApprovalService - التحليل السطري

**الملف:** `app/Services/ApprovalService.php` (338 سطر)

### 6.1 الدوال الرئيسية

```php
// السطر 15-25: تحويل نوع Workflow إلى صلاحية Spatie
private function approveAllPermissionForWorkflow(string $workflowType): string
{
    return match($workflowType) {
        'leave_request'     => 'leave-request-approve-all',
        'expense_request'   => 'expense-request-approve-all',
        'employee_job_change' => 'employee-job-change-approve-all',
        default => 'approval-approve-all',
    };
}
// ← يُستخدم لمنح صلاحية تجاوز كل الخطوات

// السطر 33-43: تحديد الموافِق للخطوة
public function getApproverForStep(WorkflowStep $step, Employee $employee, $entity = null): ?User
{
    return match($step->approver_type) {
        'user'               => $this->getUserApprover($step),
        'role'               => $this->getRoleApprover($step),
        'employee_manager'   => $this->getEmployeeManager($employee),
        'department_manager' => $this->getDepartmentManager($employee),
        'custom'             => $this->getCustomApprover($step, $employee, $entity),
        default => null,
    };
}

// السطر 50-57: موافِق مستخدم محدد
private function getUserApprover(WorkflowStep $step): ?User
{
    if (! $step->approver_id) return null;
    return User::find($step->approver_id);
}

// السطر 62-72: موافِق بالدور
private function getRoleApprover(WorkflowStep $step): ?User
{
    if (! $step->role_id) return null;
    $role = Role::find($step->role_id);
    return User::role($role->name)
        ->where('is_active', true)
        ->first();
}
// ← يُرجع أول مستخدم نشط بالدور المحدد
// ← مشكلة: إذا كان هناك عدة مستخدمين بالدور، يختار الأول فقط!

// السطر 77-87: المدير المباشر
private function getEmployeeManager(Employee $employee): ?User
{
    $directManager = $employee->getDirectManager();
    if ($directManager && $directManager->user_id) {
        return User::find($directManager->user_id);
    }
    // Fallback: رئيس القسم
    return $employee->getDepartmentManager();
}

// السطر 92-108: رئيس القسم
private function getDepartmentManager(Employee $employee): ?User
{
    return $employee->getDepartmentManager();
    // يتسلل عبر department.parent حتى يجد manager_id
}

// السطر 113-135: جميع الموافِقين المطلوبين
public function getAllRequiredApprovers(string $workflowType, Employee $employee): array
{
    $workflow = Workflow::where('type', $workflowType)
        ->where('is_active', true)
        ->with('steps')
        ->first();

    if (! $workflow) return [];

    $approvers = [];
    foreach ($workflow->steps as $step) {
        if ($step->is_required) {
            $approver = $this->getApproverForStep($step, $employee);
            if ($approver) {
                $approvers[$step->step_order] = [
                    'step' => $step,
                    'approver' => $approver,
                ];
            }
        }
    }
    return $approvers;
}

// السطر 170-205: هل يمكن للمستخدم الموافقة؟
public function canUserApprove(
    User $user,
    string $workflowType,
    Employee $employee,
    int $approvalLevel
): bool {
    // 1. إيجاد Workflow
    $workflow = Workflow::where('type', $workflowType)
        ->where('is_active', true)
        ->first();
    if (! $workflow) return false;

    // 2. إيجاد الخطوة
    $step = $workflow->steps->firstWhere('step_order', $approvalLevel);
    if (! $step) return false;

    // 3. تحديد الموافِق المطلوب
    $requiredApprover = $this->getApproverForStep($step, $employee);
    if (! $requiredApprover) return false;

    // 4. مطابقة المستخدم
    if ($user->id === $requiredApprover->id) return true;

    // 5. صلاحية تجاوز
    $permission = $this->approveAllPermissionForWorkflow($workflowType);
    if ($user->can($permission)) return true;

    return false;
}

// السطر 215-247: إيجاد الموافِق التالي
public function getNextApprover(
    string $workflowType,
    Employee $employee,
    array $completedLevels = []
): ?array {
    $approvers = $this->getAllRequiredApprovers($workflowType, $employee);

    foreach ($approvers as $stepOrder => $data) {
        if (!in_array($stepOrder, $completedLevels)) {
            return [
                'step_order' => $stepOrder,
                'step' => $data['step'],
                'approver' => $data['approver'],
            ];
        }
    }
    return null; // جميع الخطوات مكتملة
}

// السطر 288-337: التسلسل الإداري
public function getEmployeeHierarchy(Employee $employee): array
{
    $directManager = $employee->getDirectManager();
    $deptManager = $employee->getDepartmentManager();
    $deptManagerEmp = $employee->getDepartmentManagerEmployee();
    $chain = $employee->getManagerChain();

    return [
        'direct_manager' => $directManager,
        'department_manager' => $deptManager,
        'department_manager_employee' => $deptManagerEmp,
        'chain' => $chain,
        'all_managers' => collect($chain)
            ->pluck('user_id')
            ->filter()
            ->unique()
            ->values()
            ->all(),
    ];
}
```

---

## 7. WorkflowService - التحليل السطري

**الملف:** `app/Services/WorkflowService.php` (354 سطر)

### 7.1 بدء سير العمل

```php
// السطر 34-71: startWorkflow
public function startWorkflow(
    string $workflowType,
    Employee $employee,
    string $entityType,
    int $entityId
): ?WorkflowInstance {
    // 1. إيجاد Workflow نشط
    $workflow = Workflow::where('type', $workflowType)
        ->where('is_active', true)
        ->with('steps')
        ->first();
    if (! $workflow) return null;

    // 2. إيجاد أول خطوة مطلوبة
    $firstStep = $workflow->steps
        ->where('is_required', true)
        ->sortBy('step_order')
        ->first();
    if (! $firstStep) return null;

    // 3. إنشاء WorkflowInstance
    $instance = WorkflowInstance::create([
        'workflow_id'     => $workflow->id,
        'workflow_step_id' => $firstStep->id,
        'entity_type'     => $entityType,
        'entity_id'       => $entityId,
        'status'          => 'in_progress',
        'initiated_by'    => auth()->id(),
        'started_at'      => now(),
    ]);

    // 4. إشعار أول موافِق
    $this->notifyApprover($firstStep, $employee, $instance);

    return $instance;
}
```

### 7.2 معالجة الموافقة

```php
// السطر 82-155: processApproval
public function processApproval(
    WorkflowInstance $instance,
    User $approver,
    bool $approved,
    ?string $comments = null
): array {
    return DB::transaction(function () use ($instance, $approver, $approved, $comments) {
        // 1. التحقق من الخطوة الحالية
        $currentStep = $instance->currentStep;
        if (! $currentStep) {
            throw new \Exception('لا توجد خطوة حالية');
        }

        // 2. الحصول على الكيان والموظف
        $entity = $instance->entity;
        $employee = $this->getEmployeeFromEntity($entity);

        // 3. التحقق من صلاحية الموافقة
        if (! app(ApprovalService::class)->canUserApprove(
            $approver,
            $instance->workflow->type,
            $employee,
            $currentStep->step_order
        )) {
            throw new \Exception('ليس لديك صلاحية الموافقة');
        }

        // 4. معالجة الموافقة
        if ($approved) {
            // إيجاد الخطوة التالية
            $completedLevels = [$currentStep->step_order];
            $nextApprover = app(ApprovalService::class)->getNextApprover(
                $instance->workflow->type,
                $employee,
                $completedLevels
            );

            if ($nextApprover) {
                // هناك خطوة تالية
                $instance->update([
                    'workflow_step_id' => $nextApprover['step']->id,
                ]);
                $this->notifyApprover(
                    $nextApprover['step'],
                    $employee,
                    $instance
                );
                return ['status' => 'in_progress', 'next_step' => $nextApprover];
            } else {
                // جميع الخطوات مكتملة
                $instance->update([
                    'status' => 'approved',
                    'completed_at' => now(),
                ]);
                $this->updateEntityStatus($entity, 'approved');
                return ['status' => 'approved'];
            }
        } else {
            // رفض
            $instance->update([
                'status' => 'rejected',
                'completed_at' => now(),
                'notes' => $comments,
            ]);
            $this->updateEntityStatus($entity, 'rejected', $comments);
            return ['status' => 'rejected'];
        }
    });
}
```

### 7.3 إشعار الموافِق

```php
// السطر 245-287: notifyApprover
public function notifyApprover(
    WorkflowStep $step,
    Employee $employee,
    WorkflowInstance $instance
): void {
    // 1. تحديد الموافِق
    $approver = app(ApprovalService::class)
        ->getApproverForStep($step, $employee, $instance->entity);

    if (! $approver) return;

    // 2. إنشاء الإشعار
    $entityName = $this->getEntityName($instance->entity, $instance->entity_type);

    $approver->notify(new ApprovalRequestNotification(
        $instance,
        $instance->entity_type,
        $entityName,
        $employee->full_name
    ));

    // 3. إطلاق الحدث
    event(new ApprovalRequestSent(
        $approver,
        $instance,
        $entityName
    ));
}
```

---

## 8. دورة حياة الطلب (من الإنشاء إلى الاعتماد)

### 8.1 طلب إجازة - التدفق الكامل

```
┌─────────────────────────────────────────────────────────────────────────┐
│                   دورة حياة طلب الإجازة                                   │
└─────────────────────────────────────────────────────────────────────────┘

1. الموظف يقدم الطلب
   │
   ├─ SelfServiceController::requestLeave()
   │   ├─ التحقق من التواريخ
   │   ├─ حساب days_count
   │   ├─ التحقق من LeaveBalance (remaining_days >= days_count)
   │   └─ إنشاء LeaveRequest (status='pending')
   │
   ▼
2. بدء سير العمل
   │
   ├─ WorkflowService::startWorkflow('leave_request', employee, ...)
   │   ├─ إيجاد Workflow نشط من نوع 'leave_request'
   │   ├─ إيجاد أول خطوة (step_order=1, department_manager)
   │   ├─ إنشاء WorkflowInstance (status='in_progress')
   │   └─ notifyApprover() → إشعار رئيس القسم
   │
   ▼
3. رئيس القسم يراجع الطلب
   │
   ├─ ApprovalController::index()
   │   ├─ بحث LeaveRequest (status='pending')
   │   ├─ لكل طلب: التحقق من WorkflowInstance
   │   ├─ canUserApprove() → هل هذا المستخدم هو department_manager؟
   │   └─ عرض الطلبات المؤهلة
   │
   ▼
4. رئيس القسم يوافق
   │
   ├─ LeaveRequestController::approve($id)
   │   ├─ إيجاد WorkflowInstance
   │   ├─ WorkflowService::processApproval(instance, user, true)
   │   │   ├─ canUserApprove() → تأكيد الصلاحية
   │   │   ├─ getNextApprover() → الخطوة التالية (executive_director)
   │   │   ├─ تحديث workflow_step_id
   │   │   └─ notifyApprover() → إشعار المدير التنفيذي
   │   └─ [إذا كانت آخر خطوة] → status='approved', updateLeaveBalance()
   │
   ▼
5. المدير التنفيذي يوافق
   │
   ├─ نفس العملية → getNextApprover() → general_manager
   │
   ▼
6. المدير العام يوافق
   │
   ├─ نفس العملية → لا خطوات متبقية
   ├─ WorkflowInstance.status = 'approved'
   ├─ LeaveRequest.status = 'approved'
   ├─ LeaveRequest.approved_by = user_id
   ├─ LeaveRequest.approved_at = now()
   └─ updateLeaveBalance() → used_days += days_count
   │
   ▼
7. إشعار الموظف بالموافقة النهائية
   └─ (غير مُنفَّذ حالياً!)
```

### 8.2 حالة الرفض

```
في أي خطوة → رفض:
├─ WorkflowInstance.status = 'rejected'
├─ WorkflowInstance.notes = rejection_reason
├─ WorkflowInstance.completed_at = now()
├─ LeaveRequest.status = 'rejected'
├─ LeaveRequest.rejection_reason = comments
└─ إشعار الموظف بالرفض
   └─ (غير مُنفَّذ حالياً!)
```

---

## 9. نظام الإشعارات

### 9.1 ApprovalRequestNotification

**الملف:** `app/Notifications/ApprovalRequestNotification.php`

```php
class ApprovalRequestNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
        // ← لا يشمل 'mail' رغم وجود toMail()!
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'approval_request',
            'title' => $this->getTitle(),
            'message' => $this->getMessage(),
            'message_ar' => $this->getMessageAr(),
            'action_url' => $this->getActionUrl(),
            'icon' => $this->getIcon(),
            'color' => 'warning',
            'data' => [
                'workflow_instance_id' => $this->instance->id,
                'entity_type' => $this->entityType,
                'entity_id' => $this->instance->entity_id,
                'workflow_type' => $this->instance->workflow->type,
            ],
        ];
    }
}
```

**أنواع الإشعارات حسب الكيان:**

| الكيان | العنوان | الأيقونة | اللون |
|--------|---------|----------|-------|
| LeaveRequest | "طلب إجازة جديد" | fa-calendar | warning |
| ExpenseRequest | "طلب مصروف جديد" | fa-money-bill | warning |
| OvertimeRecord | "طلب عمل إضافي" | fa-clock | info |
| PerformanceReview | "تقييم أداء" | fa-star | primary |

### 9.2 CustomNotification

**الملف:** `app/Models/CustomNotification.php`

```php
// أنواع الإشعارات المخصصة:
'type' => [
    'leave_request',          // طلب إجازة جديد
    'leave_approved',         // إجازة مقبولة
    'leave_rejected',         // إجازة مرفوضة
    'attendance',             // تنبيه حضور
    'salary',                 // إشعار راتب
    'performance_review',     // تقييم أداء
    'training',               // تدريب
    'recruitment',            // توظيف
    'benefit',                // ميزة
    'system',                 // نظام
    'reminder',               // تذكير
    'contract_expiry_reminder', // تذكير انتهاء عقد
]
```

**ملاحظة:** هذه الأنواع معرفة لكن **لا يتم استخدامها بشكل منهجي**!

---

# الجزء الثالث: التحليل النقدي

## 10. المشاكل والثغرات (تفصيلي)

### 10.1 مشاكل هيكلية

#### المشكلة #1: تعارض نظامي الإدارة

**الوصف:** يوجد نظامان منفصلان للإدارة لا يتزامنان دائماً.

```
السيناريو:
┌────────────────────────────────────────────────────────────┐
│  Department: تقنية المعلومات                                │
│  manager_id: user_15 (سارة)                                │
│                                                            │
│  Employees في القسم:                                        │
│  ├── employee_20 (أحمد) - manager_id: employee_25 (خالد)   │
│  ├── employee_21 (فاطمة) - manager_id: employee_25 (خالد)  │
│  └── employee_22 (محمد) - manager_id: NULL                 │
│                                                            │
│  خالد (employee_25) في قسم: التطوير (manager_id: user_30)  │
└────────────────────────────────────────────────────────────┘

عند طلب إجازة من أحمد:
- رئيس القسم (سارة) ← الخطوة 1 في Workflow
- المدير المباشر (خالد) ← لا يظهر في Workflow!

المشكلة: خالد هو المدير المباشر اليومي لأحمد
         لكنه لا يشارك في الموافقة لأنه في قسم مختلف
```

**التأثير:**
- المدير المباشر لا يرى طلبات مرؤوسيه
- رئيس القسم قد يوافق على طلبات لا يعرف عنها شيئاً
- فقدان التسلسل الإداري الطبيعي

**الملفات المتأثرة:**
- `app/Models/Employee.php:330-425`
- `app/Services/ApprovalService.php:77-108`
- `database/seeders/WorkflowSeeder.php`

---

#### المشكلة #2: رئيس القسم قد لا يكون Employee

**الوصف:** `departments.manager_id` → `users.id` وليس `employees.id`

```php
// في Department Model:
public function manager(): BelongsTo
{
    return $this->belongsTo(User::class, 'manager_id');
    // ← User وليس Employee!
}

// المشكلة:
$user = User::find(15); // سارة
$user->employee; // ← قد يكون NULL!

// في Employee::getDepartmentManagerEmployee():
public function getDepartmentManagerEmployee(): ?Employee
{
    $deptManager = $this->getDepartmentManager(); // User
    if (! $deptManager) return null;
    return Employee::where('user_id', $deptManager->id)->first();
    // ← قد يُرجع NULL!
}
```

**التأثير:**
- رئيس القسم بدون سجل Employee لا يظهر في التقارير
- لا يمكن تعيينه كمدير مباشر لآخرين
- مشاكل في `getManagerChain()`

---

#### المشكلة #3: لا لوحة تحكم مخصصة لرئيس القسم

**الوصف:** رئيس القسم يرى رابط واحد فقط "إدارة الفريق" في لوحة الموظف.

```blade
<!-- employee/layouts/main-sidebar.blade.php:36-43 -->
@if(auth()->check() && auth()->user()->hasRole('department_head'))
<li class="slide">
    <a href="{{ route('admin.dashboard') }}" class="side-menu__item">
        <span class="side-menu__label">إدارة الفريق</span>
    </a>
</li>
@endif
```

**ما يفتقده رئيس القسم في لوحة الموظف:**

| الميزة | الحالة |
|--------|--------|
| عرض الطلبات المعلقة | ❌ غير موجود |
| إحصائيات القسم | ❌ غير موجود |
| قائمة موظفي القسم | ❌ غير موجود |
| الموافقة السريعة | ❌ غير موجود |
| عرض التسلسل الهرمي | ❌ غير موجود |
| الحضور اليومي للقسم | ❌ غير موجود |
| إشعارات مخصصة | ❌ غير موجود |

**النتيجة:** رئيس القسم مضطر للانتقال للوحة الأدمن الكاملة لإدارة فريقه.

---

#### المشكلة #4: getManagedEmployeeIds لا يشمل الأقسام الفرعية

**الوصف:** الدالة تُرجع موظفي القسم المباشر فقط.

```php
public function getManagedEmployeeIds(): array
{
    $departmentIds = $this->getManagedDepartmentIds();
    // ← [2] فقط (الموارد البشرية)
    // ← لا يشمل [3, 4] (التوظيف، الرواتب)

    return Employee::whereIn('department_id', $departmentIds)->pluck('id')->all();
    // ← موظفو department_id=2 فقط
}
```

```
Department: الموارد البشرية (id=2, manager=user_5)
├── Employee (id=10) ← department_id=2 ✅ يُرجع
├── Employee (id=11) ← department_id=2 ✅ يُرجع
└── قسم التوظيف (id=3, parent_id=2)
    ├── Employee (id=15) ← department_id=3 ❌ لا يُرجع!
    └── Employee (id=16) ← department_id=3 ❌ لا يُرجع!
```

**التأثير:** رئيس "الموارد البشرية" لا يرى موظفي "التوظيف" رغم أنه القسم الأب.

---

#### المشكلة #5: لا عرض للتسلسل الإداري للموظف

**الوصف:** الموظف لا يمكنه معرفة من يديره.

```
ما يراه الموظف حالياً في لوحة التحكم:
┌─────────────────────────────────────┐
│ معلومات سريعة                       │
├─────────────────────────────────────┤
│ البريد: ahmed@company.com           │
│ الهاتف: 0501234567                  │
│ تاريخ التوظيف: 2023/01/15          │
│ الحالة: نشط                         │
└─────────────────────────────────────┘

ما يجب أن يراه:
┌─────────────────────────────────────┐
│ التسلسل الإداري                      │
├─────────────────────────────────────┤
│ أنا: أحمد - قسم التطوير              │
│ │                                   │
│ ▼ المدير المباشر                     │
│ خالد علي - قائد فريق                 │
│ │                                   │
│ ▼ رئيس القسم                        │
│ سارة أحمد - مديرة تقنية المعلومات    │
│ │                                   │
│ ▼ المدير التنفيذي                   │
│ محمد حسن                             │
│ │                                   │
│ ▼ المدير العام                      │
│ فاطمة عبدالله                       │
└─────────────────────────────────────┘
```

---

### 10.2 مشاكل في سير الموافقات

#### المشكلة #6: سير موافقة ثابت غير مشروط

**الوصف:** نفس 3 خطوات لكل الطلبات بغض النظر عن النوع أو الحجم.

```
الواقع المطلوب:
┌──────────────────────────────────────────────────────────────────┐
│ نوع الإجازة    │ عدد الأيام  │ مسار الموافقة                      │
├──────────────────────────────────────────────────────────────────┤
│ سنوية         │ 1-3 أيام   │ رئيس القسم فقط                      │
│ سنوية         │ 4-10 أيام  │ رئيس القسم → المدير التنفيذي        │
│ سنوية         │ > 10 أيام  │ رئيس القسم → المدير التنفيذي → العام│
│ مرضية         │ 1-2 أيام   │ رئيس القسم فقط                      │
│ مرضية         │ > 2 أيام   │ رئيس القسم → الموارد البشرية        │
│ بدون راتب     │ أي عدد     │ رئيس القسم → التنفيذي → العام       │
│ طارئة         │ 1-3 أيام   │ رئيس القسم فقط                      │
│ طارئة         │ > 3 أيام   │ رئيس القسم → المدير التنفيذي        │
└──────────────────────────────────────────────────────────────────┘

الواقع الحالي:
┌──────────────────────────────────────────────────────────────────┐
│ كل الطلبات → رئيس القسم → المدير التنفيذي → المدير العام         │
└──────────────────────────────────────────────────────────────────┘
```

**حقل `conditions` غير مُستخدم:**

```json
// workflow_steps.conditions موجود لكن لا يُقرأ في ApprovalService!
{
    "min_days": 5,
    "max_days": 10
}
```

---

#### المشكلة #7: لا نظام تفويض

**الوصف:** رئيس القسم لا يمكنه تفويض صلاحياته أثناء غيابه.

```
السيناريو:
- سارة (رئيسة قسم الموارد البشرية) في إجازة لمدة أسبوع
- لا يمكنها تفويض صلاحيات الموافقة لأحد
- الطلبات تتراكم بدون معالجة
- لا يوجد fallback تلقائي

الحل المطلوب:
┌────────────────────────────────────────────────────────────┐
│ ApprovalDelegation                                         │
├────────────────────────────────────────────────────────────┤
│ - delegator_id (User)     ← سارة (user_5)                  │
│ - delegate_id (User)      ← أحمد (user_10)                 │
│ - start_date              ← 2026-05-10                     │
│ - end_date                ← 2026-05-20                     │
│ - delegation_types        ← ['leave_request', 'expense']   │
│ - status                  ← active                         │
└────────────────────────────────────────────────────────────┘

عند وصول طلب موافقة:
1. هل سارة في إجازة؟ نعم
2. هل هناك تفويض نشط؟ نعم
3. تحويل الطلب لأحمد تلقائياً
```

---

#### المشكلة #8: timeout_hours غير مُفعّل

**الوصف:** حقل `timeout_hours` موجود في `workflow_steps` لكن لا يُستخدم.

```php
// WorkflowStep:
protected $casts = [
    'timeout_hours' => 'integer',
    // ← موجود في قاعدة البيانات
];

// لكن في ApprovalService و WorkflowService:
// ← لا يوجد أي كود يتحقق من timeout_hours!
// ← لا يوجد Job مجدول يفحص الطلبات المتأخرة!
```

**النتيجة:** الطلبات يمكن أن تبقى معلقة إلى الأبد.

---

#### المشكلة #9: لا تذكير تلقائي

**الوصف:** لا يوجد نظام تذكير للموافِقين.

```
المطلوب:
┌────────────────────────────────────────────────────────────┐
│ Scheduled Job: SendApprovalReminders                       │
├────────────────────────────────────────────────────────────┤
│ كل 4 ساعات:                                                 │
│   └─ فحص WorkflowInstance (in_progress, started_at < 24h)  │
│      └─ إرسال تذكير أول للموافِق                            │
│                                                            │
│ كل 24 ساعة:                                                 │
│   └─ فحص WorkflowInstance (in_progress, started_at < 48h)  │
│      └─ إرسال تذكير ثاني + إشعار للمدير                    │
│                                                            │
│ كل 48 ساعة:                                                 │
│   └─ فحص WorkflowInstance (in_progress, started_at < 72h)  │
│      └─ تصعيد للمدير التالي                                │
└────────────────────────────────────────────────────────────┘
```

---

#### المشكلة #10: لا إشعار للموظف بالنتيجة

**الوصف:** الموظف لا يُبلغ عند الموافقة أو الرفض.

```
التدفق الحالي:
1. موظف يقدم طلب → إشعار لرئيس القسم ✅
2. رئيس القسم يوافق → إشعار للمدير التنفيذي ✅
3. المدير التنفيذي يوافق → إشعار للمدير العام ✅
4. المدير العام يوافق → ❌ لا إشعار للموظف!

5. رئيس القسم يرفض → ❌ لا إشعار للموظف!

المطلوب:
- عند الموافقة النهائية: إشعار للموظف + email
- عند الرفض: إشعار للموظف + email + سبب الرفض
- عند الانتقال لخطوة تالية: إشعار اختياري للموظف
```

---

### 10.3 مشاكل الأمان

#### المشكلة #11: تفويض صلاحيات عبر الأدوار المتعددة

```php
// User يمكنه أن يكون:
$user->roles = ['employee', 'department_head', 'executive_director'];

// المشكلة:
// - كموظف: يقدم طلب إجازة
// - كرئيس قسم: يوافق على طلبات مرؤوسيه
// - كمدير تنفيذي: يوافق على طلبات رؤساء أقسام آخرين

// هل يمكنه الموافقة على طلبه الخاص؟
// ApprovalService::canUserApprover() يتحقق من المطابقة
// لكن لا يتحقق من تعارض المصالح!
```

#### المشكلة #12: لا سجل تدقيق للموافقات

```
المطلوب:
┌────────────────────────────────────────────────────────────┐
│ ApprovalAuditLog                                           │
├────────────────────────────────────────────────────────────┤
│ - id                                                       │
│ - workflow_instance_id                                     │
│ - action (approved, rejected, delegated, escalated)        │
│ - user_id (من نفّذ الإجراء)                                │
│ - previous_status                                          │
│ - new_status                                               │
│ - comments                                                 │
│ - ip_address                                               │
│ - user_agent                                               │
│ - created_at                                               │
└────────────────────────────────────────────────────────────┘
```

---

### 10.4 مشاكل الأداء

#### المشكلة #13: استعلامات N+1 في ApprovalController

```php
// ApprovalController::index()
$leaveRequests = LeaveRequest::where('status', 'pending')->get();

foreach ($leaveRequests as $request) {
    // استعلام لكل طلب!
    $instance = WorkflowInstance::where('entity_type', ...)
        ->where('entity_id', $request->id)
        ->first();

    // استعلام إضافي!
    $canApprove = $approvalService->canUserApprove(...);
}
// ← 2N استعلام إضافي لـ N طلب
```

**الحل:** استخدام eager loading + batch checking.

---

## 11. تحليل الأمان

### 11.1 مصفوفة الصلاحيات الحالية

| الدور | عرض | إنشاء | تعديل | حذف | موافقة |
|-------|-----|-------|-------|-----|--------|
| `admin` | ✅ | ✅ | ✅ | ✅ | ✅ |
| `department_head` | ✅ (قسمه فقط) | ✅ | ✅ | ❌ | ✅ (قسمه فقط) |
| `executive_director` | ✅ | ❌ | ❌ | ❌ | ✅ (الخطوة 2) |
| `general_manager` | ✅ | ❌ | ❌ | ❌ | ✅ (الخطوة 3) |
| `employee` | ✅ (طلباته فقط) | ✅ (طلباته) | ✅ (المعلقة) | ❌ | ❌ |

### 11.2 نقاط الضعف

| # | النقطة | الوصف | الخطورة |
|---|--------|-------|---------|
| 1 | تعارض المصالح | المستخدم يمكنه الموافقة على طلبه إذا كان له أدوار متعددة | عالية |
| 2 | لا تحقق من IP | لا تسجيل IP عند الموافقة | متوسطة |
| 3 | لا مصادقة ثنائية | لا 2FA للموافقات المهمة | متوسطة |
| 4 | حذف ناعم فقط | soft deletes يمكن عكسها بصلاحية admin | منخفضة |

---

## 12. تحليل الأداء

### 12.1 الاستعلامات الأكثر تكلفة

| الاستعلام | التكرار | التكلفة | الحل |
|-----------|---------|---------|------|
| `getManagedEmployeeIds()` | كل صفحة أدمن | عالية (JOIN) | caching |
| `getDepartmentManager()` | كل طلب | متوسطة (recursive) | caching |
| `getManagerChain()` | كل موافقة | عالية (loop) | caching |
| `canUserApprove()` | كل عرض طلب | متوسطة | caching |

### 12.2 الفهارس المفقودة

```sql
-- مفقود في workflow_instances:
-- INDEX idx_entity_type_status (entity_type, status)
-- INDEX idx_workflow_step_status (workflow_step_id, status)

-- مفقود في leave_requests:
-- INDEX idx_employee_status (employee_id, status)

-- مفقود في workflow_steps:
-- INDEX idx_workflow_required (workflow_id, is_required, step_order)
```

---

## 13. مقارنة مع أفضل الممارسات

| المعيار | الوضع الحالي | الأفضل | الفجوة |
|---------|--------------|--------|--------|
| تسلسل موافقة ديناميكي | ❌ ثابت | ✅ حسب الشروط | كبيرة |
| تفويض الصلاحيات | ❌ غير موجود | ✅ موجود | كبيرة |
| تذكيرات تلقائية | ❌ غير موجودة | ✅ موجودة | كبيرة |
| إشعارات النتيجة | ❌ جزئية | ✅ كاملة | متوسطة |
| سجل تدقيق | ❌ غير موجود | ✅ موجود | متوسطة |
| تعارض المصالح | ❌ لا تحقق | ✅ تحقق | كبيرة |
| لوحة رئيس القسم | ❌ بدائية | ✅ شاملة | كبيرة |
| عرض التسلسل الإداري | ❌ غير موجود | ✅ موجود | كبيرة |

---

# الجزء الرابع: خطة التطوير

## 14. المتطلبات التفصيلية

### 14.1 المتطلبات الوظيفية

#### RF-001: لوحة تحكم رئيس القسم

**الوصف:** لوحة شاملة لرئيس القسم تعرض:

| العنصر | الوصف | المصدر |
|--------|-------|--------|
| إحصائيات سريعة | عدد الموظفين، الطلبات المعلقة، الحضور اليوم | employees, leave_requests, attendances |
| الطلبات المعلقة | قائمة طلبات الإجازة والمصروفات المعلقة | workflow_instances + leave_requests |
| هيكل القسم | عرض شجري للقسم والأقسام الفرعية | departments (parent_id) |
| موظفو القسم | قائمة الموظفين مع حالتهم | employees |
| الحضور اليومي | من حضر ومن غاب اليوم | attendances (today) |
| الإشعارات | إشعارات مخصصة لرئيس القسم | notifications |

**الملفات الجديدة:**
```
app/Http/Controllers/Employee/DepartmentHeadController.php
resources/views/employee/pages/department-head/
├── dashboard.blade.php
├── team.blade.php
├── approvals.blade.php
└── structure.blade.php
```

---

#### RF-002: صفحة التسلسل الإداري

**الوصف:** صفحة تعرض للموظف تسلسله الإداري الكامل.

**البيانات المطلوبة:**
```php
[
    'employee' => $employee,
    'direct_manager' => $employee->getDirectManager(),
    'department_manager' => $employee->getDepartmentManager(),
    'manager_chain' => $employee->getManagerChain(),
    'department_hierarchy' => $this->getDepartmentHierarchy($employee->department),
]
```

---

#### RF-003: سير موافقة ديناميكي

**الوصف:** مسار الموافقة يتحدد بناءً على شروط.

**مصفوفة الشروط:**

| Workflow Type | Condition Field | Condition Value | Result |
|---------------|-----------------|-----------------|--------|
| leave_request | days_count | <= 3 | Step 1 only |
| leave_request | days_count | 4-10 | Steps 1-2 |
| leave_request | days_count | > 10 | Steps 1-3 |
| leave_request | leave_type | sick | Step 1 only (if <= 2 days) |
| leave_request | leave_type | unpaid | Steps 1-3 always |
| expense_request | amount | <= 1000 | Step 1 only |
| expense_request | amount | 1001-5000 | Steps 1-2 |
| expense_request | amount | > 5000 | Steps 1-3 |

---

#### RF-004: نظام التفويض

**الوصف:** تمكين رؤساء الأقسام من تفويض صلاحياتهم.

**نموذج ApprovalDelegation:**

```sql
CREATE TABLE approval_delegations (
    id                  BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    delegator_id        BIGINT UNSIGNED NOT NULL,    -- User who delegates
    delegate_id         BIGINT UNSIGNED NOT NULL,    -- User who receives delegation
    workflow_types      JSON,                        -- ['leave_request', 'expense_request']
    start_date          DATETIME NOT NULL,
    end_date            DATETIME NOT NULL,
    status              ENUM('active', 'expired', 'cancelled') DEFAULT 'active',
    notes               TEXT,
    created_by          BIGINT UNSIGNED,
    created_at          TIMESTAMP NULL,
    updated_at          TIMESTAMP NULL,
    deleted_at          TIMESTAMP NULL,

    INDEX idx_delegator (delegator_id),
    INDEX idx_delegate (delegate_id),
    INDEX idx_status_dates (status, start_date, end_date),

    CONSTRAINT fk_ad_delegator FOREIGN KEY (delegator_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_ad_delegate  FOREIGN KEY (delegate_id)  REFERENCES users(id) ON DELETE CASCADE
);
```

---

#### RF-005: نظام التذكيرات والتصعيد

**الوصف:** Jobs مجدولة ترسل تذكيرات وتصعد الطلبات المتأخرة.

**جدول التنفيذ:**

| Job | التكرار | الشرط | الإجراء |
|-----|---------|-------|---------|
| SendFirstReminder | كل 4 ساعات | started_at < 24h | إشعار database |
| SendSecondReminder | كل 24 ساعة | started_at < 48h | إشعار + email |
| EscalatePending | كل 48 ساعة | started_at < 72h | تصعيد للمدير التالي |

---

#### RF-006: إشعارات النتيجة

**الوصف:** إشعار الموظف عند الموافقة النهائية أو الرفض.

**أحداث جديدة:**
```php
// Events:
ApprovalCompleted   → إشعار للموظف + email
ApprovalRejected    → إشعار للموظف + email + السبب
ApprovalDelegated   → إشعار للمفوَّض + المفوِّض
ApprovalEscalated   → إشعار للمدير الجديد
```

---

### 14.2 المتطلبات غير الوظيفية

| المتطلب | الوصف |
|---------|-------|
| الأداء | استجابة < 2 ثانية للصفحات |
| الأمان | منع تعارض المصالح، تسجيل IP |
| التوفر | 99.9% uptime |
| التوسع | دعم 10,000+ موظف |
| التوثيق | توثيق API + كود comments |

---

## 15. مخططات قاعدة البيانات الجديدة

### 15.1 جدول التفويض (approval_delegations)

```sql
CREATE TABLE approval_delegations (
    id                  BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    delegator_id        BIGINT UNSIGNED NOT NULL,
    delegate_id         BIGINT UNSIGNED NOT NULL,
    workflow_types      JSON,
    start_date          DATETIME NOT NULL,
    end_date            DATETIME NOT NULL,
    status              ENUM('active', 'expired', 'cancelled') DEFAULT 'active',
    notes               TEXT,
    created_by          BIGINT UNSIGNED,
    created_at          TIMESTAMP NULL,
    updated_at          TIMESTAMP NULL,
    deleted_at          TIMESTAMP NULL,

    INDEX idx_delegator_status (delegator_id, status),
    INDEX idx_delegate_status (delegate_id, status),
    INDEX idx_active_dates (status, start_date, end_date),

    CONSTRAINT fk_ad_delegator FOREIGN KEY (delegator_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_ad_delegate  FOREIGN KEY (delegate_id)  REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT chk_dates CHECK (end_date > start_date)
);
```

### 15.2 جدول سجل التدقيق (approval_audit_logs)

```sql
CREATE TABLE approval_audit_logs (
    id                  BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    workflow_instance_id BIGINT UNSIGNED NOT NULL,
    action              ENUM('approved', 'rejected', 'delegated', 'escalated', 'timeout'),
    user_id             BIGINT UNSIGNED NOT NULL,
    previous_status     VARCHAR(50),
    new_status          VARCHAR(50),
    comments            TEXT,
    ip_address          VARCHAR(45),
    user_agent          TEXT,
    created_at          TIMESTAMP NULL,

    INDEX idx_instance (workflow_instance_id),
    INDEX idx_user (user_id),
    INDEX idx_action (action),
    INDEX idx_created (created_at),

    CONSTRAINT fk_aal_instance FOREIGN KEY (workflow_instance_id) REFERENCES workflow_instances(id) ON DELETE CASCADE,
    CONSTRAINT fk_aal_user     FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE RESTRICT
);
```

### 15.3 جدول التذكيرات (approval_reminders)

```sql
CREATE TABLE approval_reminders (
    id                  BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    workflow_instance_id BIGINT UNSIGNED NOT NULL,
    reminder_level      TINYINT,            -- 1, 2, 3
    sent_at             TIMESTAMP NULL,
    sent_to             BIGINT UNSIGNED,    -- user_id
    channel             ENUM('database', 'email', 'sms'),
    status              ENUM('sent', 'failed') DEFAULT 'sent',

    INDEX idx_instance_level (workflow_instance_id, reminder_level),
    INDEX idx_sent_at (sent_at),

    CONSTRAINT fk_ar_instance FOREIGN KEY (workflow_instance_id) REFERENCES workflow_instances(id) ON DELETE CASCADE
);
```

---

## 16. خطة التنفيذ خطوة بخطوة

### المرحلة 1: الأساسيات (الأسبوع 1)

#### المهمة 1.1: إنشاء لوحة رئيس القسم

**الخطوات:**
1. إنشاء `DepartmentHeadController`
2. إنشاء routes في `routes/employee.php`
3. إنشاء views في `employee/pages/department-head/`
4. تحديث sidebar لإضافة روابط جديدة

**الملفات:**
```
+ app/Http/Controllers/Employee/DepartmentHeadController.php
+ routes/employee.php (تعديل)
+ resources/views/employee/pages/department-head/dashboard.blade.php
+ resources/views/employee/pages/department-head/team.blade.php
+ resources/views/employee/pages/department-head/approvals.blade.php
+ resources/views/employee/layouts/main-sidebar.blade.php (تعديل)
```

**تقدير الوقت:** 4 ساعات

---

#### المهمة 1.2: صفحة التسلسل الإداري

**الخطوات:**
1. إضافة route `employee.hierarchy`
2. إضافة method في `SelfServiceController`
3. إنشاء view

**الملفات:**
```
+ routes/employee.php (تعديل)
~ app/Http/Controllers/Employee/SelfServiceController.php (تعديل)
+ resources/views/employee/pages/self-service/hierarchy.blade.php
```

**تقدير الوقت:** 2 ساعات

---

#### المهمة 1.3: تحسين getManagedEmployeeIds

**الخطوات:**
1. تعديل الدالة لتشمل الأقسام الفرعية
2. إضافة المرؤوسين المباشرين
3. caching

**الملفات:**
```
~ app/Models/User.php (تعديل)
```

**تقدير الوقت:** 1 ساعة

---

#### المهمة 1.4: إشعارات النتيجة

**الخطوات:**
1. إنشاء Events جديدة
2. إنشاء Notifications جديدة
3. تعديل WorkflowService

**الملفات:**
```
+ app/Events/ApprovalCompleted.php
+ app/Events/ApprovalRejected.php
+ app/Notifications/ApprovalCompletedNotification.php
+ app/Notifications/ApprovalRejectedNotification.php
~ app/Services/WorkflowService.php (تعديل)
```

**تقدير الوقت:** 3 ساعات

---

### المرحلة 2: سير الموافقات (الأسبوع 2)

#### المهمة 2.1: سير موافقة ديناميكي

**الخطوات:**
1. تعديل ApprovalService لقراءة conditions
2. إضافة منطق الشروط
3. تحديث WorkflowSeeder

**الملفات:**
```
~ app/Services/ApprovalService.php (تعديل كبير)
~ database/seeders/WorkflowSeeder.php (تعديل)
```

**تقدير الوقت:** 6 ساعات

---

#### المهمة 2.2: صفحة طلبات الموافقة للموظف

**الخطوات:**
1. إضافة route
2. إضافة method في SelfServiceController
3. إنشاء view

**الملفات:**
```
+ routes/employee.php (تعديل)
~ app/Http/Controllers/Employee/SelfServiceController.php (تعديل)
+ resources/views/employee/pages/self-service/my-approvals.blade.php
```

**تقدير الوقت:** 3 ساعات

---

### المرحلة 3: التفويض والتذكيرات (الأسبوع 3)

#### المهمة 3.1: نظام التفويض

**الخطوات:**
1. إنشاء migration
2. إنشاء Model
3. إنشاء Controller + CRUD views
4. تعديل ApprovalService للتحقق من التفويض

**الملفات:**
```
+ database/migrations/xxxx_create_approval_delegations_table.php
+ app/Models/ApprovalDelegation.php
+ app/Http/Controllers/Employee/DelegationController.php
+ resources/views/employee/pages/delegations/*.blade.php
~ app/Services/ApprovalService.php (تعديل)
```

**تقدير الوقت:** 5 ساعات

---

#### المهمة 3.2: Job التذكيرات

**الخطوات:**
1. إنشاء 3 Jobs
2. إضافة schedule
3. إنشاء migration لـ approval_reminders

**الملفات:**
```
+ database/migrations/xxxx_create_approval_reminders_table.php
+ app/Models/ApprovalReminder.php
+ app/Jobs/SendFirstReminder.php
+ app/Jobs/SendSecondReminder.php
+ app/Jobs/EscalatePending.php
~ app/Console/Kernel.php أو routes/console.php
```

**تقدير الوقت:** 4 ساعات

---

#### المهمة 3.3: سجل التدقيق

**الخطوات:**
1. إنشاء migration
2. إنشاء Model
3. تعديل WorkflowService لتسجيل الأحداث

**الملفات:**
```
+ database/migrations/xxxx_create_approval_audit_logs_table.php
+ app/Models/ApprovalAuditLog.php
~ app/Services/WorkflowService.php (تعديل)
```

**تقدير الوقت:** 2 ساعات

---

### المرحلة 4: التحسينات (الأسبوع 4)

#### المهمة 4.1: عرض هيكل القسم

**الخطوات:**
1. API endpoint للهيكل الشجري
2. view شجري تفاعلي

**الملفات:**
```
+ app/Http/Controllers/Api/DepartmentStructureController.php
+ resources/views/employee/pages/department-head/structure.blade.php
```

**تقدير الوقت:** 4 ساعات

---

#### المهمة 4.2: منع تعارض المصالح

**الخطوات:**
1. إضافة تحقق في ApprovalService
2. إضافة approval_audit_logs

**الملفات:**
```
~ app/Services/ApprovalService.php (تعديل)
```

**تقدير الوقت:** 2 ساعات

---

#### المهمة 4.3: تحسين الأداء

**الخطوات:**
1. إضافة caching
2. تحسين الاستعلامات
3. إضافة فهارس

**الملفات:**
```
~ app/Models/User.php (تعديل - caching)
~ app/Services/ApprovalService.php (تعديل - caching)
+ database/migrations/xxxx_add_indexes.php
```

**تقدير الوقت:** 3 ساعات

---

## 17. نماذج الكود المقترحة

### 17.1 DepartmentHeadController

```php
<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\ExpenseRequest;
use App\Models\Attendance;
use App\Models\WorkflowInstance;
use App\Services\ApprovalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DepartmentHeadController extends Controller
{
    public function __construct(
        protected ApprovalService $approvalService
    ) {}

    public function dashboard()
    {
        $user = Auth::user();
        if (! $user->isDepartmentHead()) {
            abort(403);
        }

        $departmentIds = $this->getAllManagedDepartmentIds($user);
        $employeeIds = Employee::whereIn('department_id', $departmentIds)
            ->pluck('id')
            ->all();

        // إحصائيات
        $stats = [
            'total_employees' => count($employeeIds),
            'present_today' => Attendance::whereIn('employee_id', $employeeIds)
                ->whereDate('attendance_date', today())
                ->where('status', 'present')->count(),
            'absent_today' => Attendance::whereIn('employee_id', $employeeIds)
                ->whereDate('attendance_date', today())
                ->where('status', 'absent')->count(),
            'pending_leaves' => LeaveRequest::whereIn('employee_id', $employeeIds)
                ->where('status', 'pending')->count(),
            'pending_expenses' => ExpenseRequest::whereIn('employee_id', $employeeIds)
                ->where('status', 'pending')->count(),
            'my_pending_approvals' => $this->getMyPendingApprovalsCount($user),
        ];

        // الأقسام المُدارة
        $departments = Department::whereIn('id', $departmentIds)
            ->withCount('employees')
            ->get();

        // الطلبات المعلقة للموافقة
        $pendingApprovals = $this->getPendingApprovals($user);

        return view('employee.pages.department-head.dashboard', compact(
            'stats', 'departments', 'pendingApprovals'
        ));
    }

    public function team()
    {
        $user = Auth::user();
        if (! $user->isDepartmentHead()) {
            abort(403);
        }

        $departmentIds = $this->getAllManagedDepartmentIds($user);

        $employees = Employee::whereIn('department_id', $departmentIds)
            ->with(['department', 'position', 'user', 'manager'])
            ->where('is_active', true)
            ->get();

        return view('employee.pages.department-head.team', compact('employees'));
    }

    public function approvals()
    {
        $user = Auth::user();
        if (! $user->isDepartmentHead()) {
            abort(403);
        }

        $pendingApprovals = $this->getPendingApprovals($user);

        return view('employee.pages.department-head.approvals', compact('pendingApprovals'));
    }

    protected function getAllManagedDepartmentIds($user): array
    {
        $directIds = Department::where('manager_id', $user->id)->pluck('id')->all();
        $childIds = Department::whereIn('parent_id', $directIds)->pluck('id')->all();

        return array_unique(array_merge($directIds, $childIds));
    }

    protected function getMyPendingApprovalsCount($user): int
    {
        $employeeIds = $user->getManagedEmployeeIds();
        $count = 0;

        $leaveRequests = LeaveRequest::whereIn('employee_id', $employeeIds)
            ->where('status', 'pending')
            ->get();

        foreach ($leaveRequests as $request) {
            $instance = WorkflowInstance::where('entity_type', LeaveRequest::class)
                ->where('entity_id', $request->id)
                ->where('status', 'in_progress')
                ->first();

            if ($instance && $this->approvalService->canUserApprove(
                $user, 'leave_request', $request->employee, $instance->currentStep->step_order
            )) {
                $count++;
            }
        }

        return $count;
    }

    protected function getPendingApprovals($user)
    {
        // تطبيق مشابه مع eager loading محسّن
        // ...
    }
}
```

### 17.2 ApprovalService - التحقق من التفويض

```php
// إضافة في ApprovalService:

public function canUserApprove(
    User $user,
    string $workflowType,
    Employee $employee,
    int $approvalLevel
): bool {
    // 1. التحقق من تعارض المصالح
    if ($this->hasConflictOfInterest($user, $employee)) {
        return false;
    }

    // 2. التحقق من التفويض
    $delegatedApprover = $this->getDelegatedApprover($user, $workflowType);
    if ($delegatedApprover) {
        // المستخدم هو مفوَّض نشط
        return true;
    }

    // 3. التحقق العادي
    $workflow = Workflow::where('type', $workflowType)
        ->where('is_active', true)
        ->first();
    if (! $workflow) return false;

    $step = $workflow->steps->firstWhere('step_order', $approvalLevel);
    if (! $step) return false;

    $requiredApprover = $this->getApproverForStep($step, $employee);
    if (! $requiredApprover) return false;

    if ($user->id === $requiredApprover->id) return true;

    $permission = $this->approveAllPermissionForWorkflow($workflowType);
    return $user->can($permission);
}

protected function hasConflictOfInterest(User $user, Employee $employee): bool
{
    // هل المستخدم هو صاحب الطلب؟
    return $employee->user_id === $user->id;
}

protected function getDelegatedApprover(User $user, string $workflowType): ?ApprovalDelegation
{
    return ApprovalDelegation::where('delegate_id', $user->id)
        ->where('status', 'active')
        ->where('start_date', '<=', now())
        ->where('end_date', '>=', now())
        ->whereJsonContains('workflow_types', $workflowType)
        ->first();
}
```

### 17.3 Job التذكيرات

```php
<?php

namespace App\Jobs;

use App\Models\WorkflowInstance;
use App\Models\ApprovalReminder;
use App\Notifications\ApprovalReminderNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendFirstReminder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        // طلبات معلقة منذ أكثر من 24 ساعة
        $instances = WorkflowInstance::where('status', 'in_progress')
            ->where('started_at', '<', now()->subHours(24))
            ->get();

        foreach ($instances as $instance) {
            // التحقق من عدم إرسال تذكير سابق
            $alreadySent = ApprovalReminder::where('workflow_instance_id', $instance->id)
                ->where('reminder_level', 1)
                ->exists();

            if ($alreadySent) continue;

            // إرسال التذكير
            $approver = $instance->currentStep->approver; // أو ApprovalService
            if ($approver) {
                $approver->notify(new ApprovalReminderNotification($instance, 1));

                ApprovalReminder::create([
                    'workflow_instance_id' => $instance->id,
                    'reminder_level' => 1,
                    'sent_at' => now(),
                    'sent_to' => $approver->id,
                    'channel' => 'database',
                ]);
            }
        }
    }
}
```

### 17.4 Schedule

```php
// في routes/console.php أو App\Console\Kernel:

use App\Jobs\SendFirstReminder;
use App\Jobs\SendSecondReminder;
use App\Jobs\EscalatePending;

Schedule::job(new SendFirstReminder)->everyFourHours();
Schedule::job(new SendSecondReminder)->daily();
Schedule::job(new EscalatePending)->everyTwoDays();

// تحديث الأرصدة اليومية
Schedule::command('leave-balances:recalculate')->dailyAt('00:00');

// تنظيف التفويضات المنتهية
Schedule::command('delegations:expire')->dailyAt('01:00');
```

---

## 18. معايير القبول والاختبار

### 18.1 معايير القبول لكل متطلب

#### RF-001: لوحة رئيس القسم

| # | المعيار | طريقة التحقق |
|---|---------|--------------|
| 1 | عرض الإحصائيات الصحيحة | مقارنة الأعداد مع قاعدة البيانات |
| 2 | عرض الطلبات المعلقة للموافقة | تقديم طلب والتحقق من ظهوره |
| 3 | عرض موظفي القسم | مقارنة القائمة مع employees |
| 4 | عرض هيكل الأقسام الفرعية | التحقق من parent_id |
| 5 | صلاحيات الوصول | محاولة الدخول بغير رئيس قسم → 403 |

#### RF-002: التسلسل الإداري

| # | المعيار | طريقة التحقق |
|---|---------|--------------|
| 1 | عرض المدير المباشر | مقارنة مع employees.manager_id |
| 2 | عرض رئيس القسم | مقارنة مع departments.manager_id |
| 3 | عرض السلسلة كاملة | مقارنة مع getManagerChain() |
| 4 | التعامل مع الحلقات اللانهائية | إنشاء حلقة والتحقق من الحماية |

#### RF-003: سير موافقة ديناميكي

| # | المعيار | طريقة التحقق |
|---|---------|--------------|
| 1 | إجازة 1-3 أيام → خطوة واحدة | تقديم طلب 3 أيام والتحقق |
| 2 | إجازة 4-10 أيام → خطوتين | تقديم طلب 5 أيام والتحقق |
| 3 | إجازة > 10 أيام → 3 خطوات | تقديم طلب 15 يوم والتحقق |
| 4 | مصروف < 1000 → خطوة واحدة | تقديم طلب 500 والتحقق |

#### RF-004: نظام التفويض

| # | المعيار | طريقة التحقق |
|---|---------|--------------|
| 1 | إنشاء تفويض | إنشاء وتفقد في قاعدة البيانات |
| 2 | التفويض نشط في الفترة | التحقق من start_date/end_date |
| 3 | المفوَّض يمكنه الموافقة | تقديم طلب والتحقق |
| 4 | انتهاء التفويض تلقائياً | انتظار end_date والتحقق |

#### RF-005: التذكيرات

| # | المعيار | طريقة التحقق |
|---|---------|--------------|
| 1 | تذكير أول بعد 24 ساعة | تشغيل Job يدوياً والتحقق |
| 2 | تذكير ثاني بعد 48 ساعة | تشغيل Job يدوياً والتحقق |
| 3 | تصعيد بعد 72 ساعة | تشغيل Job يدوياً والتحقق |

#### RF-006: إشعارات النتيجة

| # | المعيار | طريقة التحقق |
|---|---------|--------------|
| 1 | إشعار عند الموافقة النهائية | إكمال سير العمل والتحقق |
| 2 | إشعار عند الرفض | رفض طلب والتحقق |
| 3 | إشعار يتضمن السبب | رفض مع سبب والتحقق |

---

### 18.2 حالات الاختبار

#### Test Case 1: طلب إجازة عادي

```
Given: موظف أحمد في قسم الموارد البشرية
When: يقدم طلب إجازة سنوية 5 أيام
Then:
  - LeaveRequest created (status=pending)
  - WorkflowInstance created (step_order=1)
  - إشعار لرئيس القسم (سارة)
  - سارة توافق → step_order=2
  - إشعار للمدير التنفيذي
  - المدير التنفيذي يوافق → status=approved
  - إشعار لأحمد بالموافقة ✅
  - LeaveBalance updated (used_days += 5)
```

#### Test Case 2: تفويض الموافقة

```
Given: سارة (رئيسة قسم) في إجازة
And: تفويض نشط لأحمد (user_10)
When: يصل طلب إجازة لموظف في قسم سارة
Then:
  - أحمد يظهر كموافِق بدلاً من سارة
  - أحمد يوافق → الطلب ينتقل للخطوة التالية
  - سجل تفويض في approval_audit_logs
```

#### Test Case 3: تعارض المصالح

```
Given: أحمد رئيس قسم + لديه دور department_head
When: يقدم طلب إجازة
Then:
  - لا يمكنه الموافقة على طلبه
  - الطلب ينتقل تلقائياً للمدير التنفيذي
  - سجل في approval_audit_logs (conflict_of_interest)
```

#### Test Case 4: تصعيد الطلب

```
Given: طلب إجازة معلق منذ 72 ساعة
When: تشغيل Job EscalatePending
Then:
  - إشعار تصعيد للمدير التالي
  - سجل في approval_reminders (level=3)
  - إشعار للموظف بالتأخير
```

---

### 18.3 اختبار الأداء

| الاختبار | المعيار | النتيجة المتوقعة |
|----------|---------|-------------------|
| تحميل لوحة رئيس القسم | < 2 ثانية | ✅ |
| عرض 1000 موظف | < 3 ثانية | ✅ |
| معالجة 100 طلب موافقة | < 10 ثانية | ✅ |
| Job التذكيرات (1000 instance) | < 30 ثانية | ✅ |

---

## الملخص التنفيذي

### الوضع الحالي

| الجانب | التقييم |
|--------|---------|
| البنية التحتية | ⭐⭐⭐☆☆ جيد لكن يحتاج توحيد |
| سير الموافقات | ⭐⭐☆☆☆ ثابت وغير مرن |
| الصلاحيات | ⭐⭐⭐⭐☆ شامل لكن يحتاج تحسين |
| الإشعارات | ⭐⭐☆☆☆ محدود جداً |
| واجهة رئيس القسم | ⭐☆☆☆☆ غير موجودة فعلياً |
| الأمان | ⭐⭐⭐☆☆ جيد لكن يحتاج تدقيق |
| الأداء | ⭐⭐⭐☆☆ مقبول مع تحسينات |

### الأولويات

| الأولوية | المهمة | الأثر | الجهد |
|----------|--------|-------|-------|
| 🔴 عالية | لوحة رئيس القسم | كبير | متوسط |
| 🔴 عالية | إشعارات النتيجة | كبير | صغير |
| 🔴 عالية | التسلسل الإداري | متوسط | صغير |
| 🟡 متوسطة | سير ديناميكي | كبير | كبير |
| 🟡 متوسطة | نظام التفويض | متوسط | متوسط |
| 🟢 منخفضة | التذكيرات | متوسط | متوسط |
| 🟢 منخفضة | سجل التدقيق | متوسط | صغير |

### الجدول الزمني المقترح

```
الأسبوع 1: الأساسيات (لوحة رئيس القسم + التسلسل + إشعارات)
الأسبوع 2: سير الموافقات (ديناميكي + صفحة موافقات)
الأسبوع 3: التفويض والتذكيرات
الأسبوع 4: التحسينات والأداء
```

---

> **ملاحظة ختامية:** هذا التحليل شامل ومفصّل. يُنصح بالبدء بالمرحلة 1 (الأساسيات) لأنها تقدم أكبر قيمة بأقل جهد، ثم التدرج للمراحل التالية حسب الأولوية والموارد المتاحة.
