<?php

namespace Database\Seeders;

use App\Models\Survey;
use App\Models\SurveyQuestion;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class SurveySeeder extends Seeder
{
    public function run(): void
    {
        $adminUser = \App\Models\User::where('email', 'admin@gmail.com')->first();
        $createdBy = $adminUser ? $adminUser->id : 1;

        $surveys = [
            [
                'title' => 'Employee Satisfaction Survey',
                'title_ar' => 'استبيان رضا الموظفين',
                'type' => 'satisfaction',
                'description' => 'استبيان شامل لقياس رضا الموظفين',
                'start_date' => Carbon::now()->subDays(30),
                'end_date' => Carbon::now()->addDays(30),
                'status' => 'active',
                'is_anonymous' => true,
                'target_audience' => 'all',
                'questions' => [
                    [
                        'text' => 'How satisfied are you with your current role?',
                        'text_ar' => 'ما مدى رضاك عن دورك الحالي؟',
                        'type' => 'rating',
                        'required' => true,
                    ],
                    [
                        'text' => 'How would you rate your work-life balance?',
                        'text_ar' => 'كيف تقيم توازنك بين العمل والحياة؟',
                        'type' => 'rating',
                        'required' => true,
                    ],
                    [
                        'text' => 'What improvements would you suggest?',
                        'text_ar' => 'ما التحسينات التي تقترحها؟',
                        'type' => 'textarea',
                        'required' => false,
                    ],
                ],
            ],
            [
                'title' => 'Work Environment Survey',
                'title_ar' => 'استبيان بيئة العمل',
                'type' => 'climate',
                'description' => 'استبيان لقياس مناخ العمل',
                'start_date' => Carbon::now()->subDays(15),
                'end_date' => Carbon::now()->addDays(15),
                'status' => 'active',
                'is_anonymous' => true,
                'target_audience' => 'all',
                'questions' => [
                    [
                        'text' => 'How would you describe the work environment?',
                        'text_ar' => 'كيف تصف بيئة العمل؟',
                        'type' => 'radio',
                        'options' => ['Excellent', 'Good', 'Average', 'Poor'],
                        'required' => true,
                    ],
                    [
                        'text' => 'Do you feel supported by your manager?',
                        'text_ar' => 'هل تشعر بالدعم من مديرك؟',
                        'type' => 'radio',
                        'options' => ['Yes', 'No', 'Sometimes'],
                        'required' => true,
                    ],
                ],
            ],
        ];

        foreach ($surveys as $surveyData) {
            $questions = $surveyData['questions'];
            unset($surveyData['questions']);

            $surveyData['created_by'] = $createdBy;
            $surveyData['total_responses'] = 0;

            $survey = Survey::firstOrCreate(
                ['title' => $surveyData['title']],
                $surveyData
            );

            if ($survey->wasRecentlyCreated && !empty($questions)) {
                foreach ($questions as $index => $questionData) {
                    SurveyQuestion::create([
                        'survey_id' => $survey->id,
                        'question_text' => $questionData['text'],
                        'question_text_ar' => $questionData['text_ar'] ?? null,
                        'question_type' => $questionData['type'],
                        'options' => $questionData['options'] ?? null,
                        'question_order' => $index + 1,
                        'is_required' => $questionData['required'] ?? true,
                    ]);
                }
            }
        }

        $this->command->info('✅ تم إنشاء الاستبيانات بنجاح!');
    }
}
