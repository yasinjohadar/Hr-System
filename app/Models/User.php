<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Symfony\Component\HttpFoundation\Session\Session;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;
      use HasRoles;

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
        ];
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
     * أقسام يديرها المستخدم (كمدير قسم) — للاستخدام في تقييد النطاق
     */
    public function getManagedDepartmentIds(): array
    {
        if (! $this->isDepartmentHead()) {
            return [];
        }

        return \App\Models\Department::where('manager_id', $this->id)->pluck('id')->all();
    }

    /**
     * معرفات الموظفين التابعين لأقسام يديرها المستخدم
     */
    public function getManagedEmployeeIds(): array
    {
        $departmentIds = $this->getManagedDepartmentIds();
        if (empty($departmentIds)) {
            return [];
        }

        return \App\Models\Employee::whereIn('department_id', $departmentIds)->pluck('id')->all();
    }
}