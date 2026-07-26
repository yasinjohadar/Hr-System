<?php

namespace App\Support;

use App\Models\EmployeeJobChange;
use App\Models\ExpenseRequest;
use App\Models\LeaveRequest;
use App\Models\OvertimeRecord;
use App\Models\Payroll;
use App\Models\PerformanceReview;
use App\Models\FundTransfer;
use App\Models\ProjectTimeEntry;
use App\Models\Ticket;

class WorkflowEntityType
{
    /** @var array<string, class-string> */
    protected static array $shortToClass = [
        'LeaveRequest' => LeaveRequest::class,
        'ExpenseRequest' => ExpenseRequest::class,
        'EmployeeJobChange' => EmployeeJobChange::class,
        'OvertimeRecord' => OvertimeRecord::class,
        'Payroll' => Payroll::class,
        'PerformanceReview' => PerformanceReview::class,
        'Ticket' => Ticket::class,
        'ProjectTimeEntry' => ProjectTimeEntry::class,
        'FundTransfer' => FundTransfer::class,
    ];

    /**
     * Normalize to FQCN for storage and queries.
     */
    public static function normalize(string $entityType): string
    {
        if (class_exists($entityType) && is_subclass_of($entityType, \Illuminate\Database\Eloquent\Model::class)) {
            return $entityType;
        }

        $trimmed = ltrim($entityType, '\\');
        if (str_contains($trimmed, '\\')) {
            return $trimmed;
        }

        return self::$shortToClass[$trimmed] ?? $entityType;
    }

    /**
     * Resolve model class from stored entity_type (FQCN or legacy short name).
     *
     * @return class-string<\Illuminate\Database\Eloquent\Model>|null
     */
    public static function resolveModelClass(string $entityType): ?string
    {
        $normalized = self::normalize($entityType);

        if (class_exists($normalized) && is_subclass_of($normalized, \Illuminate\Database\Eloquent\Model::class)) {
            return $normalized;
        }

        return null;
    }

    public static function shortName(string $entityType): string
    {
        $class = self::resolveModelClass($entityType);

        return $class ? class_basename($class) : $entityType;
    }

    /**
     * @return array<string, class-string>
     */
    public static function legacyShortNames(): array
    {
        return self::$shortToClass;
    }
}
