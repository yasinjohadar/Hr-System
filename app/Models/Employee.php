<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Traits\LogsActivity;

class Employee extends Model
{
    use SoftDeletes, LogsActivity;

    protected $fillable = [
        'employee_code',
        'user_id',
        'department_id',
        'position_id',
        'branch_id',
        'manager_id',
        'first_name',
        'last_name',
        'full_name',
        'national_id',
        'date_of_birth',
        'gender',
        'marital_status',
        'address',
        'city',
        'country',
        'postal_code',
        'personal_email',
        'personal_phone',
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_contact_relation',
        'hire_date',
        'probation_end_date',
        'contract_start_date',
        'contract_end_date',
        'employment_type',
        'employment_status',
        'salary',
        'work_location',
        'work_phone',
        'work_email',
        'notes',
        'photo',
        'created_by',
        'is_active',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'hire_date' => 'date',
        'probation_end_date' => 'date',
        'contract_start_date' => 'date',
        'contract_end_date' => 'date',
        'salary' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * العلاقة مع المستخدم
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * العلاقة مع القسم
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * العلاقة مع المنصب
     */
    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    /**
     * العلاقة مع الفرع
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * العلاقة مع المدير
     */
    public function manager(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'manager_id');
    }

    /**
     * العلاقة مع المستندات
     */
    public function documents(): HasMany
    {
        return $this->hasMany(EmployeeDocument::class);
    }

    /**
     * العلاقة مع المهارات
     */
    public function skills(): HasMany
    {
        return $this->hasMany(EmployeeSkill::class);
    }

    /**
     * العلاقة مع الشهادات
     */
    public function certificates(): HasMany
    {
        return $this->hasMany(EmployeeCertificate::class);
    }

    /**
     * العلاقة مع الأهداف
     */
    public function goals(): HasMany
    {
        return $this->hasMany(EmployeeGoal::class);
    }

    /**
     * العلاقة مع إنهاء الخدمة
     */
    public function exits(): HasMany
    {
        return $this->hasMany(EmployeeExit::class);
    }

    /**
     * العلاقة مع طلبات المصروفات
     */
    public function expenseRequests(): HasMany
    {
        return $this->hasMany(ExpenseRequest::class);
    }

    /**
     * العلاقة مع المخالفات
     */
    public function violations(): HasMany
    {
        return $this->hasMany(EmployeeViolation::class);
    }

    /**
     * العلاقة مع تعيينات المهام
     */
    public function taskAssignments(): HasMany
    {
        return $this->hasMany(TaskAssignment::class);
    }

    /**
     * العلاقة مع المهام المعينة (Many-to-Many عبر TaskAssignment)
     */
    public function tasks()
    {
        return $this->belongsToMany(Task::class, 'task_assignments')
                    ->withPivot('assigned_date', 'due_date', 'status', 'progress', 'notes')
                    ->withTimestamps();
    }

    /**
     * العلاقة مع المشاريع (كمدير)
     */
    public function managedProjects(): HasMany
    {
        return $this->hasMany(Project::class, 'manager_id');
    }

    /**
     * العلاقة مع توزيعات الأصول
     */
    public function assetAssignments(): HasMany
    {
        return $this->hasMany(AssetAssignment::class);
    }

    /**
     * العلاقة مع الأصول الموزعة حالياً
     */
    public function currentAssets(): HasMany
    {
        return $this->hasMany(AssetAssignment::class)->where('assignment_status', 'active');
    }

    /**
     * العلاقة مع الرواتب
     */
    public function salaries(): HasMany
    {
        return $this->hasMany(Salary::class);
    }

    /**
     * العلاقة مع طلبات الإجازات
     */
    public function leaveRequests(): HasMany
    {
        return $this->hasMany(LeaveRequest::class);
    }

    /**
     * العلاقة مع أرصدة الإجازات
     */
    public function leaveBalances(): HasMany
    {
        return $this->hasMany(LeaveBalance::class);
    }

    /**
     * العلاقة مع الحضور والانصراف
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * العلاقة مع التقييمات (كموظف مقيّم)
     */
    public function performanceReviews(): HasMany
    {
        return $this->hasMany(PerformanceReview::class, 'employee_id');
    }

    /**
     * العلاقة مع التقييمات (كمقيّم)
     */
    public function reviewsGiven(): HasMany
    {
        return $this->hasMany(PerformanceReview::class, 'reviewer_id');
    }

    /**
     * العلاقة مع سجلات التدريب
     */
    public function trainingRecords(): HasMany
    {
        return $this->hasMany(TrainingRecord::class);
    }

    /**
     * العلاقة مع الدورات التدريبية (من خلال سجلات التدريب)
     */
    public function trainings(): BelongsToMany
    {
        return $this->belongsToMany(Training::class, 'training_records')
            ->withPivot('status', 'registration_date', 'completion_date', 'score', 'feedback', 'evaluation', 'certificate_issued', 'certificate_date', 'notes')
            ->withTimestamps();
    }

    /**
     * العلاقة مع الموظفين التابعين
     */
    public function subordinates(): HasMany
    {
        return $this->hasMany(Employee::class, 'manager_id');
    }

    /**
     * الحصول على المدير المباشر (Employee)
     */
    public function getDirectManager(): ?Employee
    {
        return $this->manager_id ? Employee::find($this->manager_id) : null;
    }

    /**
     * الحصول على مدير القسم (User)
     */
    public function getDepartmentManager(): ?User
    {
        if (!$this->department_id) {
            return null;
        }

        $department = $this->department;
        if (!$department) {
            return null;
        }

        // مدير القسم المباشر
        if ($department->manager_id) {
            return User::find($department->manager_id);
        }

        // البحث في القسم الأب
        if ($department->parent_id) {
            $parentDepartment = Department::find($department->parent_id);
            if ($parentDepartment && $parentDepartment->manager_id) {
                return User::find($parentDepartment->manager_id);
            }
        }

        return null;
    }

    /**
     * الحصول على مدير القسم كـ Employee
     */
    public function getDepartmentManagerEmployee(): ?Employee
    {
        $managerUser = $this->getDepartmentManager();
        if (!$managerUser) {
            return null;
        }

        return Employee::where('user_id', $managerUser->id)->first();
    }

    /**
     * الحصول على التسلسل الهرمي الكامل (جميع المديرين)
     */
    public function getManagerChain(): array
    {
        $chain = [];
        $currentManager = $this->getDirectManager();

        while ($currentManager) {
            $chain[] = $currentManager;
            $currentManager = $currentManager->getDirectManager();
        }

        return $chain;
    }

    /**
     * التحقق من أن موظف معين هو مدير مباشر أو غير مباشر
     */
    public function isManagedBy(Employee $manager): bool
    {
        // المدير المباشر
        if ($this->manager_id === $manager->id) {
            return true;
        }

        // البحث في التسلسل الهرمي
        $chain = $this->getManagerChain();
        foreach ($chain as $chainManager) {
            if ($chainManager->id === $manager->id) {
                return true;
            }
        }

        return false;
    }

    /**
     * الحصول على الموافق المطلوب بناءً على نوع الموافق
     */
    public function getApproverForType(string $approverType): ?User
    {
        return match($approverType) {
            'employee_manager' => $this->getDirectManager()?->user,
            'department_manager' => $this->getDepartmentManager(),
            default => null,
        };
    }

    /**
     * العلاقة مع منشئ السجل
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * العلاقة مع التذاكر (منشئ التذكرة)
     */
    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    /**
     * العلاقة مع التذاكر (المكلف بها)
     */
    public function assignedTickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'assigned_to');
    }

    /**
     * العلاقة مع الاجتماعات (منظم)
     */
    public function organizedMeetings(): HasMany
    {
        return $this->hasMany(Meeting::class, 'organizer_id');
    }

    /**
     * العلاقة مع حضور الاجتماعات
     */
    public function meetingAttendances(): HasMany
    {
        return $this->hasMany(MeetingAttendee::class);
    }

    /**
     * العلاقة مع طلبات التقييم 360 درجة (الموظف الذي يتم تقييمه)
     */
    public function feedbackRequests(): HasMany
    {
        return $this->hasMany(FeedbackRequest::class);
    }

    /**
     * العلاقة مع ردود التقييم (المقيم)
     */
    public function feedbackResponses(): HasMany
    {
        return $this->hasMany(FeedbackResponse::class, 'respondent_id');
    }

    /**
     * العلاقة مع المكافآت
     */
    public function rewards(): HasMany
    {
        return $this->hasMany(EmployeeReward::class);
    }

    /**
     * العلاقة مع مرشحي التعاقب
     */
    public function successionCandidates(): HasMany
    {
        return $this->hasMany(SuccessionCandidate::class);
    }

    /**
     * العلاقة مع كشوف الرواتب المتقدمة
     */
    public function payrolls(): HasMany
    {
        return $this->hasMany(Payroll::class);
    }

    /**
     * العلاقة مع تعيينات المناوبات
     */
    public function shiftAssignments(): HasMany
    {
        return $this->hasMany(ShiftAssignment::class);
    }

    /**
     * العلاقة مع الحسابات البنكية
     */
    public function bankAccounts(): HasMany
    {
        return $this->hasMany(EmployeeBankAccount::class);
    }

    /**
     * العلاقة مع الحساب البنكي الأساسي
     */
    public function primaryBankAccount()
    {
        return $this->hasOne(EmployeeBankAccount::class)->where('is_primary', true);
    }

    /**
     * العلاقة مع المناوبة الحالية
     */
    public function currentShiftAssignment()
    {
        return $this->hasOne(ShiftAssignment::class)
            ->where('is_active', true)
            ->where(function($q) {
                $q->whereNull('end_date')
                  ->orWhere('end_date', '>=', now());
            })
            ->where('start_date', '<=', now())
            ->latest();
    }

    /**
     * العلاقة مع سجلات الساعات الإضافية
     */
    public function overtimeRecords(): HasMany
    {
        return $this->hasMany(OvertimeRecord::class);
    }
}
