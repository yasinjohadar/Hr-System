<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Symfony\Component\HttpFoundation\Session\Session;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, HasRoles, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'phone',
        'password',
        'status',
        'is_active',
        'photo',
        'created_by',
        'last_login_at',
        'last_login_ip',
        'last_login_user_agent',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'two_factor_confirmed_at' => 'datetime',
            'two_factor_secret' => 'encrypted',
            'two_factor_recovery_codes' => 'encrypted:array',
        ];
    }

    public function hasTwoFactorEnabled(): bool
    {
        return $this->two_factor_secret !== null && $this->two_factor_confirmed_at !== null;
    }

    public function requiresTwoFactor(): bool
    {
        if (! $this->hasTwoFactorEnabled()) {
            return false;
        }

        return $this->hasAnyRole(['admin', 'user', 'department_head'])
            || $this->can('payroll-list');
    }

     public function sessions()
    {
        return $this->hasMany(\App\Models\Session::class, 'user_id');
    }

    /**
     * العلاقة مع الموظف
     */
    public function employee()
    {
        return $this->hasOne(Employee::class);
    }

    /**
     * هل المستخدم رئيس قسم فقط (وليس مديراً عاماً)
     */
    public function isDepartmentHead(): bool
    {
        return $this->hasRole('department_head') && ! $this->hasRole('admin');
    }

    /**
     * أقسام يديرها المستخدم (كمدير قسم) — مع الأقسام الفرعية
     */
    public function getManagedDepartmentIds(): array
    {
        if (! $this->isDepartmentHead()) {
            return [];
        }

        $directIds = \App\Models\Department::where('manager_id', $this->id)->pluck('id')->all();

        // إضافة الأقسام الفرعية بشكل متكرر
        $allIds = $directIds;
        $queue = $directIds;

        while (!empty($queue)) {
            $parentId = array_shift($queue);
            $childIds = \App\Models\Department::where('parent_id', $parentId)->pluck('id')->all();
            foreach ($childIds as $childId) {
                if (!in_array($childId, $allIds)) {
                    $allIds[] = $childId;
                    $queue[] = $childId;
                }
            }
        }

        return array_unique($allIds);
    }

    /**
     * معرفات الموظفين التابعين لأقسام يديرها المستخدم (مع المرؤوسين المباشرين)
     */
    public function getManagedEmployeeIds(): array
    {
        $departmentIds = $this->getManagedDepartmentIds();
        $employeeIds = [];

        if (!empty($departmentIds)) {
            $employeeIds = \App\Models\Employee::whereIn('department_id', $departmentIds)
                ->where('is_active', true)
                ->pluck('id')
                ->all();
        }

        // إضافة المرؤوسين المباشرين (عبر employees.manager_id)
        $employee = $this->employee;
        if ($employee) {
            $directSubordinates = \App\Models\Employee::where('manager_id', $employee->id)
                ->where('is_active', true)
                ->pluck('id')
                ->all();
            $employeeIds = array_unique(array_merge($employeeIds, $directSubordinates));
        }

        return $employeeIds;
    }
}