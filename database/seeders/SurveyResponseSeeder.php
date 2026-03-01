<?php

namespace Database\Seeders;

use App\Models\Survey;
use App\Models\SurveyResponse;
use App\Models\SurveyQuestion;
use App\Models\Employee;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class SurveyResponseSeeder extends Seeder
{
    public function run(): void
    {
        $surveys = Survey::all();
        $employees = Employee::where('is_active', true)->get();

        if ($surveys->isEmpty() || $employees->isEmpty()) {
            $this->command->warn('لا توجد استبيانات أو موظفين!');
            return;
        }

        foreach ($surveys as $survey) {
            $questions = SurveyQuestion::where('survey_id', $survey->id)->get();

            if ($questions->isEmpty()) {
                continue;
            }

            // 40-80% من الموظفين يردون على الاستبيان
            $responseRate = rand(40, 80);
            $numResponses = (int)($employees->count() * $responseRate / 100);
            $respondents = $employees->random(min($numResponses, $employees->count()));

            foreach ($respondents as $employee) {
                // التحقق من عدم وجود رد مسبق
                $existing = SurveyResponse::where('survey_id', $survey->id)
                    ->where('employee_id', $employee->id)
                    ->first();

                if ($existing) {
                    continue;
                }

                // إنشاء إجابات
                $answers = [];
                foreach ($questions as $question) {
                    $questionType = $question->question_type ?? 'text';

                    switch ($questionType) {
                        case 'multiple_choice':
                        case 'single_choice':
                            $options = json_decode($question->options ?? '[]', true);
                            if (!empty($options)) {
                                $answers[$question->id] = $options[array_rand($options)];
                            }
                            break;
                        case 'rating':
                        case 'scale':
                            $answers[$question->id] = rand(1, 5);
                            break;
                        case 'yes_no':
                            $answers[$question->id] = rand(0, 1) ? 'نعم' : 'لا';
                            break;
                        default:
                            $answers[$question->id] = 'إجابة نصية على السؤال';
                            break;
                    }
                }

                SurveyResponse::create([
                    'survey_id' => $survey->id,
                    'employee_id' => $employee->id,
                    'answers' => $answers,
                    'comments' => rand(1, 100) <= 30 ? 'ملاحظات إضافية على الاستبيان' : null,
                    'submitted_at' => Carbon::now()->subDays(rand(1, 30)),
                    'ip_address' => '192.168.1.' . rand(1, 255),
                ]);
            }
        }

        $totalResponses = SurveyResponse::count();
        $this->command->info("✅ تم إنشاء $totalResponses رد استبيان");
    }
}
