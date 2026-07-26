<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class FundTransfer extends Model
{
    use SoftDeletes;

    public const ACCOUNT_COMPANY = 'company';
    public const ACCOUNT_EMPLOYEE = 'employee';

    protected $fillable = [
        'transfer_code',
        'type',
        'from_account_type',
        'from_account_id',
        'to_account_type',
        'to_account_id',
        'amount',
        'currency_id',
        'project_id',
        'project_stage_id',
        'status',
        'requested_by',
        'approved_by',
        'approved_at',
        'executed_at',
        'rejection_reason',
        'notes',
        'meta',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'approved_at' => 'datetime',
        'executed_at' => 'datetime',
        'meta' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function (FundTransfer $transfer) {
            if (empty($transfer->transfer_code)) {
                $transfer->transfer_code = 'FT-' . strtoupper(Str::random(10));
            }
        });
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function stage(): BelongsTo
    {
        return $this->belongsTo(ProjectStage::class, 'project_stage_id');
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function getTypeNameArAttribute(): string
    {
        return match ($this->type) {
            'internal' => 'تحويل داخلي',
            'disbursement' => 'صرف لموظف',
            'adjustment' => 'تسوية / إيداع',
            default => $this->type,
        };
    }

    public function getStatusNameArAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'بانتظار الموافقة',
            'completed' => 'منفّذ',
            'rejected' => 'مرفوض',
            'cancelled' => 'ملغي',
            default => $this->status,
        };
    }

    public function resolveFromAccount(): ?Model
    {
        return $this->resolveAccount($this->from_account_type, $this->from_account_id);
    }

    public function resolveToAccount(): ?Model
    {
        return $this->resolveAccount($this->to_account_type, $this->to_account_id);
    }

    protected function resolveAccount(?string $type, ?int $id): ?Model
    {
        if (! $type || ! $id) {
            return null;
        }

        return match ($type) {
            self::ACCOUNT_COMPANY => CompanyBankAccount::find($id),
            self::ACCOUNT_EMPLOYEE => EmployeeBankAccount::find($id),
            default => null,
        };
    }
}
