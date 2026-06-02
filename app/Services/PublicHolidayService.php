<?php

namespace App\Services;

use App\Models\Country;
use App\Models\Employee;
use App\Models\PublicHoliday;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class PublicHolidayService
{
    public function holidaysForEmployee(Employee $employee, int $year): Collection
    {
        $branchId = $employee->branch_id;
        $countryIds = [];
        if ($employee->branch?->country) {
            $countryIds = Country::query()
                ->where('name', $employee->branch->country)
                ->orWhere('name_ar', $employee->branch->country)
                ->pluck('id')
                ->all();
        }

        return PublicHoliday::query()
            ->where('is_active', true)
            ->whereYear('holiday_date', $year)
            ->where(function ($q) use ($countryIds, $branchId) {
                $q->where(fn ($q2) => $q2->whereNull('country_id')->whereNull('branch_id'));
                if ($countryIds !== []) {
                    $q->orWhereIn('country_id', $countryIds);
                }
                if ($branchId) {
                    $q->orWhere('branch_id', $branchId);
                }
            })
            ->get();
    }

    public function isHoliday(Employee $employee, Carbon $date): bool
    {
        return $this->holidaysForEmployee($employee, (int) $date->year)
            ->contains(fn (PublicHoliday $h) => $h->holiday_date->isSameDay($date)
                || ($h->is_recurring && $h->holiday_date->format('m-d') === $date->format('m-d')));
    }
}
