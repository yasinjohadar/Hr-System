<?php

namespace Database\Seeders;

use App\Models\Candidate;
use App\Models\Country;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Carbon\Carbon;

class CandidateSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('ar_SA');
        $countries = Country::all();
        $adminUser = \App\Models\User::where('email', 'admin@gmail.com')->first();
        $createdBy = $adminUser ? $adminUser->id : 1;

        if ($countries->isEmpty()) {
            $this->command->warn('لا توجد دول!');
            return;
        }

        $firstNames = [
            'أحمد', 'محمد', 'علي', 'خالد', 'سعد', 'فهد', 'عبدالله', 'عمر', 'يوسف', 'حسام',
            'مريم', 'فاطمة', 'خديجة', 'عائشة', 'سارة', 'نورا', 'لينا', 'ريم', 'سلمى', 'هند'
        ];

        $lastNames = [
            'الغامدي', 'العتيبي', 'الدوسري', 'الحربي', 'الزهراني', 'القحطاني', 'السهلي', 'الشمري',
            'الخالدي', 'المطيري', 'الرشيد', 'العبيد', 'المنصور', 'السالم', 'الخليفة', 'الجبير'
        ];

        $statuses = ['new', 'contacted', 'screening', 'interviewed', 'offered', 'hired', 'rejected', 'withdrawn'];

        for ($i = 0; $i < 30; $i++) {
            $firstName = $firstNames[array_rand($firstNames)];
            $lastName = $lastNames[array_rand($lastNames)];
            $fullName = $firstName . ' ' . $lastName;
            $gender = rand(0, 1) ? 'male' : 'female';
            $candidateCode = 'CAND-' . str_pad($i + 1, 6, '0', STR_PAD_LEFT);
            $email = strtolower($firstName) . '.' . strtolower($lastName) . rand(1, 999) . '@example.com';

            Candidate::firstOrCreate(
                ['candidate_code' => $candidateCode],
                [
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'full_name' => $fullName,
                    'email' => $email,
                    'phone' => '05' . rand(10000000, 99999999),
                    'national_id' => rand(1000000000, 9999999999),
                    'date_of_birth' => Carbon::now()->subYears(rand(22, 45)),
                    'gender' => $gender,
                    'address' => $faker->address,
                    'city' => $faker->city,
                    'country_id' => $countries->random()->id,
                    'status' => $statuses[array_rand($statuses)],
                    'notes' => rand(0, 1) ? 'مرشح واعد' : null,
                    'created_by' => $createdBy,
                ]
            );
        }

        $this->command->info('✅ تم إنشاء المرشحين بنجاح!');
    }
}
