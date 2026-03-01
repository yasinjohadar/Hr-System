<?php

namespace Database\Seeders;

use App\Models\FeedbackRequest;
use App\Models\FeedbackResponse;
use App\Models\Employee;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class FeedbackResponseSeeder extends Seeder
{
    public function run(): void
    {
        $feedbackRequests = FeedbackRequest::all();
        $employees = Employee::where('is_active', true)->get();

        if ($feedbackRequests->isEmpty() || $employees->isEmpty()) {
            $this->command->warn('لا توجد طلبات تقييم أو موظفين!');
            return;
        }

        $relationshipTypes = ['manager', 'peer', 'subordinate', 'self', 'other'];
        $statuses = ['pending', 'in_progress', 'submitted', 'draft'];

        foreach ($feedbackRequests as $request) {
            $targetEmployee = $request->employee;
            if (!$targetEmployee) {
                continue;
            }

            // الحصول على المدير
            $manager = $targetEmployee->manager;
            
            // الحصول على زملاء من نفس القسم
            $peers = Employee::where('department_id', $targetEmployee->department_id)
                ->where('id', '!=', $targetEmployee->id)
                ->where('is_active', true)
                ->take(3)
                ->get();

            // الحصول على مرؤوسين
            $subordinates = $targetEmployee->subordinates()->where('is_active', true)->take(2)->get();

            $respondents = collect();

            // إضافة المدير
            if ($manager) {
                $respondents->push(['employee' => $manager, 'type' => 'manager']);
            }

            // إضافة الزملاء
            foreach ($peers as $peer) {
                $respondents->push(['employee' => $peer, 'type' => 'peer']);
            }

            // إضافة المرؤوسين
            foreach ($subordinates as $subordinate) {
                $respondents->push(['employee' => $subordinate, 'type' => 'subordinate']);
            }

            // إضافة التقييم الذاتي
            $respondents->push(['employee' => $targetEmployee, 'type' => 'self']);

            foreach ($respondents as $respondentData) {
                $respondent = $respondentData['employee'];
                $relationshipType = $respondentData['type'];

                // التحقق من عدم وجود رد مسبق
                $existing = FeedbackResponse::where('feedback_request_id', $request->id)
                    ->where('respondent_id', $respondent->id)
                    ->first();

                if ($existing) {
                    continue;
                }

                $status = $statuses[array_rand($statuses)];
                $submittedAt = $status === 'submitted' ? Carbon::now()->subDays(rand(1, 10)) : null;

                // إنشاء تقييمات
                $ratings = [
                    'communication' => rand(3, 5),
                    'teamwork' => rand(3, 5),
                    'leadership' => rand(3, 5),
                    'problem_solving' => rand(3, 5),
                    'overall' => rand(3, 5),
                ];

                FeedbackResponse::create([
                    'feedback_request_id' => $request->id,
                    'respondent_id' => $respondent->id,
                    'relationship_type' => $relationshipType,
                    'ratings' => $ratings,
                    'strengths' => 'نقاط القوة: العمل الجاد، الالتزام، المهارات التقنية',
                    'weaknesses' => 'نقاط الضعف: يحتاج إلى تحسين في التواصل',
                    'recommendations' => 'التوصيات: حضور دورات تدريبية إضافية',
                    'comments' => 'تعليقات عامة على الأداء',
                    'status' => $status,
                    'submitted_at' => $submittedAt,
                ]);
            }
        }

        $totalResponses = FeedbackResponse::count();
        $this->command->info("✅ تم إنشاء $totalResponses رد تقييم 360");
    }
}
