<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('🚀 بدء إنشاء البيانات...');
        
        // تشغيل seeders بالترتيب الصحيح
        $this->call([
            // ========== المرحلة 1: الأساسيات ==========
            PermissionSeeder::class,
            CountrySeeder::class,
            CurrencySeeder::class,
            SettingSeeder::class,
            
            // ========== المرحلة 2: الهيكل التنظيمي ==========
            DepartmentSeeder::class,
            BranchSeeder::class,
            PositionSeeder::class,
            
            // ========== المرحلة 3: المستخدمين والموظفين ==========
            AdminUserSeeder::class,
            EmployeeSeeder::class,
            
            // ========== المرحلة 4: الإجازات ==========
            LeaveTypeSeeder::class,
            LeaveBalanceSeeder::class,
            LeaveRequestSeeder::class,
            
            // ========== المرحلة 5: الحضور والرواتب ==========
            AttendanceSeeder::class,
            SalarySeeder::class,
            
            // ========== المرحلة 5.1: نظام الحضور المتقدم ==========
            AttendanceRuleSeeder::class,
            AttendanceLocationSeeder::class,
            AttendanceBreakSeeder::class,
            OvertimeRecordSeeder::class,
            
            // ========== المرحلة 5.2: نظام الرواتب المتقدم ==========
            SalaryComponentSeeder::class,
            TaxSettingSeeder::class,
            EmployeeBankAccountSeeder::class,
            ShiftSeeder::class,
            ShiftAssignmentSeeder::class,
            PayrollSeeder::class,
            PayrollItemSeeder::class,
            PayrollApprovalSeeder::class,
            PayrollPaymentSeeder::class,
            
            // ========== المرحلة 6: التقييمات والتدريب ==========
            PerformanceReviewSeeder::class,
            TrainingSeeder::class,
            TrainingRecordSeeder::class,
            
            // ========== المرحلة 7: التوظيف ==========
            JobVacancySeeder::class,
            CandidateSeeder::class,
            JobApplicationSeeder::class,
            InterviewSeeder::class,
            
            // ========== المرحلة 8: المزايا والتعويضات ==========
            BenefitTypeSeeder::class,
            EmployeeBenefitSeeder::class,
            
            // ========== المرحلة 9: المستندات والمهارات ==========
            EmployeeDocumentSeeder::class,
            EmployeeSkillSeeder::class,
            EmployeeCertificateSeeder::class,
            EmployeeGoalSeeder::class,
            
            // ========== المرحلة 10: الأصول ==========
            AssetSeeder::class,
            AssetAssignmentSeeder::class,
            AssetMaintenanceSeeder::class,
            
            // ========== المرحلة 11: المصروفات ==========
            ExpenseCategorySeeder::class,
            ExpenseRequestSeeder::class,
            ExpenseApprovalSeeder::class,
            
            // ========== المرحلة 12: الإجراءات التأديبية ==========
            ViolationTypeSeeder::class,
            DisciplinaryActionSeeder::class,
            EmployeeViolationSeeder::class,
            
            // ========== المرحلة 13: المهام والمشاريع ==========
            ProjectSeeder::class,
            TaskSeeder::class,
            TaskAssignmentSeeder::class,
            TaskCommentSeeder::class,
            TaskAttachmentSeeder::class,
            
            // ========== المرحلة 14: سير العمل والتعاقب ==========
            WorkflowSeeder::class,
            WorkflowInstanceSeeder::class,
            SuccessionPlanSeeder::class,
            SuccessionCandidateSeeder::class,
            
            // ========== المرحلة 15: التذاكر والاجتماعات ==========
            TicketSeeder::class,
            TicketCommentSeeder::class,
            MeetingSeeder::class,
            MeetingAttendeeSeeder::class,
            
            // ========== المرحلة 15.1: التقويم ==========
            CalendarEventSeeder::class,
            
            // ========== المرحلة 16: التقييم 360 والمكافآت ==========
            FeedbackRequestSeeder::class,
            FeedbackResponseSeeder::class,
            RewardTypeSeeder::class,
            EmployeeRewardSeeder::class,
            
            // ========== المرحلة 17: الاستبيانات ==========
            SurveySeeder::class,
            SurveyResponseSeeder::class,
            
            // ========== المرحلة 18: القوالب ==========
            OnboardingTemplateSeeder::class,
            OnboardingTaskSeeder::class,
            OnboardingProcessSeeder::class,
            OnboardingChecklistSeeder::class,
            EmailTemplateSeeder::class,
            DocumentTemplateSeeder::class,
        ]);
        
        $this->command->info('');
        $this->command->info('✅ تم إنشاء جميع البيانات بنجاح!');
        $this->command->info('');
        $this->command->info('📧 بيانات الدخول:');
        $this->command->info('   Email: admin@gmail.com');
        $this->command->info('   Password: password');
        $this->command->info('');
    }
}
