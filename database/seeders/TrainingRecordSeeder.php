<?php

namespace Database\Seeders;

use App\Models\TrainingRecord;
use App\Models\Training;
use App\Models\Employee;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class TrainingRecordSeeder extends Seeder
{
    public function run(): void
    {
        $trainings = Training::all();
        $employees = Employee::where('is_active', true)->get();
        $adminUser = \App\Models\User::where('email', 'admin@gmail.com')->first();
        $createdBy = $adminUser ? $adminUser->id : 1;

        if ($trainings->isEmpty() || $employees->isEmpty()) {
            $this->command->warn('لا توجد تدريبات أو موظفين!');
            return;
        }

        $statuses = ['registered', 'attending', 'completed', 'cancelled'];

        foreach ($trainings as $training) {
            // اختيار عدد عشوائي من الموظفين لكل تدريب (2-10)
            $selectedEmployees = $employees->random(rand(2, min(10, $employees->count())));

            foreach ($selectedEmployees as $employee) {
                // تجنب التكرار
                $existing = TrainingRecord::where('training_id', $training->id)
                    ->where('employee_id', $employee->id)
                    ->first();

                if ($existing) {
                    continue;
                }

                $status = $statuses[array_rand($statuses)];
                $registrationDate = Carbon::now()->subDays(rand(1, 60));
                $completionDate = null;
                $score = null;

                if ($status === 'completed') {
                    $completionDate = $registrationDate->copy()->addDays(rand(1, 30));
                    $score = rand(70, 100) + (rand(0, 99) / 100); // درجة من 70.00 إلى 100.99
                }

                TrainingRecord::create([
                    'training_id' => $training->id,
                    'employee_id' => $employee->id,
                    'registration_date' => $registrationDate,
                    'completion_date' => $completionDate,
                    'status' => $status,
                    'score' => $score,
                    'notes' => rand(0, 1) ? 'سجل تدريبي تجريبي' : null,
                    'created_by' => $createdBy,
                ]);
            }
        }

        $this->command->info('✅ تم إنشاء سجلات التدريب بنجاح!');
    }
}
