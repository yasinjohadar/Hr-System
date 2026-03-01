<?php

namespace Database\Seeders;

use App\Models\Meeting;
use App\Models\MeetingAttendee;
use App\Models\Employee;
use Illuminate\Database\Seeder;

class MeetingAttendeeSeeder extends Seeder
{
    public function run(): void
    {
        $meetings = Meeting::all();
        $employees = Employee::where('is_active', true)->get();

        if ($meetings->isEmpty() || $employees->isEmpty()) {
            $this->command->warn('لا توجد اجتماعات أو موظفين!');
            return;
        }

        $statuses = ['invited', 'accepted', 'declined', 'tentative', 'attended', 'absent'];
        $statusWeights = [
            'invited' => 10,
            'accepted' => 40,
            'attended' => 30,
            'declined' => 10,
            'tentative' => 5,
            'absent' => 5,
        ];

        foreach ($meetings as $meeting) {
            // عدد الحضور: 3-15 موظف لكل اجتماع
            $numAttendees = rand(3, min(15, $employees->count()));
            $selectedEmployees = $employees->random($numAttendees);

            foreach ($selectedEmployees as $employee) {
                // تجنب التكرار
                $existing = MeetingAttendee::where('meeting_id', $meeting->id)
                    ->where('employee_id', $employee->id)
                    ->first();

                if ($existing) {
                    continue;
                }

                // اختيار الحالة بناءً على الأوزان
                $status = $this->weightedRandom($statusWeights);
                $isRequired = rand(1, 100) <= 30; // 30% من الحضور إلزامي

                MeetingAttendee::create([
                    'meeting_id' => $meeting->id,
                    'employee_id' => $employee->id,
                    'status' => $status,
                    'response_notes' => $status === 'declined' ? 'معذرة، لدي موعد آخر' : null,
                    'is_required' => $isRequired,
                    'notes' => $isRequired ? 'حضور إلزامي' : null,
                ]);
            }
        }

        $totalAttendees = MeetingAttendee::count();
        $this->command->info("✅ تم إنشاء $totalAttendees حضور اجتماع");
    }

    private function weightedRandom(array $weights): string
    {
        $total = array_sum($weights);
        $random = rand(1, $total);
        $current = 0;

        foreach ($weights as $status => $weight) {
            $current += $weight;
            if ($random <= $current) {
                return $status;
            }
        }

        return array_key_first($weights);
    }
}
