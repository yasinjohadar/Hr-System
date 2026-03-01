<?php

namespace Database\Seeders;

use App\Models\OnboardingTemplate;
use App\Models\User;
use Illuminate\Database\Seeder;

class OnboardingTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $adminUser = User::where('email', 'admin@gmail.com')->first();
        $createdBy = $adminUser ? $adminUser->id : 1;

        $templates = [
            [
                'name' => 'Standard Onboarding',
                'name_ar' => 'استقبال قياسي',
                'description' => 'Standard employee onboarding process',
                'department_id' => 'all',
                'position_id' => 'all',
                'is_active' => true,
                'created_by' => $createdBy,
            ],
            [
                'name' => 'Executive Onboarding',
                'name_ar' => 'استقبال تنفيذي',
                'description' => 'Executive level onboarding process',
                'department_id' => 'all',
                'position_id' => 'all',
                'is_active' => true,
                'created_by' => $createdBy,
            ],
        ];

        foreach ($templates as $template) {
            OnboardingTemplate::firstOrCreate(
                ['name' => $template['name']],
                $template
            );
        }
    }
}
