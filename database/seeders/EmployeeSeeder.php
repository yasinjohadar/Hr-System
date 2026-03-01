<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Employee;
use App\Models\Department;
use App\Models\Position;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        
        // قائمة أسماء عربية
        $firstNames = [
            'أحمد', 'محمد', 'علي', 'خالد', 'سعد', 'فهد', 'عبدالله', 'عمر', 'يوسف', 'حسام',
            'مريم', 'فاطمة', 'خديجة', 'عائشة', 'سارة', 'نورا', 'لينا', 'ريم', 'سلمى', 'هند',
            'عبدالرحمن', 'عبدالعزيز', 'صالح', 'نواف', 'تركي', 'بندر', 'سلطان', 'فيصل', 'نايف', 'مشعل',
            'نورة', 'لطيفة', 'منيرة', 'عبدية', 'زينب', 'أسماء', 'حفصة', 'رقية', 'أم كلثوم', 'زينب'
        ];
        
        $lastNames = [
            'الغامدي', 'العتيبي', 'الدوسري', 'الحربي', 'الزهراني', 'القحطاني', 'السهلي', 'الشمري',
            'الخالدي', 'المطيري', 'الرشيد', 'العبيد', 'المنصور', 'السالم', 'الخليفة', 'الجبير',
            'النجار', 'الخياط', 'الصالح', 'المالك', 'الرحمن', 'الرحيم', 'الكريم', 'العزيز'
        ];

        // الحصول على الأقسام والمناصب الموجودة
        $departments = Department::all();
        $positions = Position::all();

        // إذا لم تكن هناك أقسام، أنشئ بعض الأقسام
        if ($departments->isEmpty()) {
            $departments = collect([
                Department::create(['name' => 'قسم الموارد البشرية', 'code' => 'HR', 'is_active' => true]),
                Department::create(['name' => 'قسم المبيعات', 'code' => 'SALES', 'is_active' => true]),
                Department::create(['name' => 'قسم التسويق', 'code' => 'MARKETING', 'is_active' => true]),
                Department::create(['name' => 'قسم التطوير', 'code' => 'DEV', 'is_active' => true]),
                Department::create(['name' => 'قسم المالية', 'code' => 'FINANCE', 'is_active' => true]),
                Department::create(['name' => 'قسم الإدارة', 'code' => 'ADMIN', 'is_active' => true]),
            ]);
        }

        // إذا لم تكن هناك مناصب، أنشئ بعض المناصب
        if ($positions->isEmpty()) {
            $positions = collect([
                Position::create(['title' => 'مدير', 'code' => 'MGR', 'department_id' => $departments->first()->id, 'is_active' => true]),
                Position::create(['title' => 'مطور', 'code' => 'DEV', 'department_id' => $departments->where('code', 'DEV')->first()->id ?? $departments->first()->id, 'is_active' => true]),
                Position::create(['title' => 'محاسب', 'code' => 'ACC', 'department_id' => $departments->where('code', 'FINANCE')->first()->id ?? $departments->first()->id, 'is_active' => true]),
                Position::create(['title' => 'منسق', 'code' => 'COORD', 'department_id' => $departments->random()->id, 'is_active' => true]),
                Position::create(['title' => 'أخصائي', 'code' => 'SPEC', 'department_id' => $departments->random()->id, 'is_active' => true]),
            ]);
        }

        // الحصول على المدير (admin) لاستخدامه كـ created_by
        $admin = User::where('email', 'admin@gmail.com')->first();

        // إنشاء 50 موظف
        $employees = [];
        for ($i = 1; $i <= 50; $i++) {
            // إنشاء اسم عربي
            $firstName = $faker->randomElement($firstNames);
            $lastName = $faker->randomElement($lastNames);
            $fullName = $firstName . ' ' . $lastName;

            // إنشاء رقم موظف
            $employeeCode = 'EMP' . str_pad($i, 4, '0', STR_PAD_LEFT);

            // التحقق من وجود الموظف
            $existingEmployee = Employee::where('employee_code', $employeeCode)->first();
            if ($existingEmployee) {
                continue;
            }

            // إنشاء مستخدم للموظف
            $user = User::create([
                'name' => $fullName,
                'email' => $faker->unique()->safeEmail(),
                'phone' => $faker->phoneNumber(),
                'password' => Hash::make('123456789'),
                'is_active' => true,
                'email_verified_at' => now(),
                'created_by' => $admin ? $admin->id : null,
            ]);

            // اختيار قسم ومنصب عشوائي
            $department = $departments->random();
            $position = $positions->random();

            // تاريخ توظيف عشوائي (خلال آخر 5 سنوات)
            $hireDate = $faker->dateTimeBetween('-5 years', 'now');

            // إنشاء الموظف
            $employee = Employee::create([
                'employee_code' => $employeeCode,
                'user_id' => $user->id,
                'department_id' => $department->id,
                'position_id' => $position->id,
                'manager_id' => $i > 5 && count($employees) > 0 ? $employees[array_rand($employees)]->id : null,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'full_name' => $fullName,
                'national_id' => $faker->unique()->numerify('1##############'),
                'date_of_birth' => $faker->dateTimeBetween('-50 years', '-20 years'),
                'gender' => $faker->randomElement(['male', 'female']),
                'marital_status' => $faker->randomElement(['أعزب', 'متزوج', 'مطلق', 'أرمل']),
                'address' => $faker->address(),
                'city' => $faker->city(),
                'country' => 'السعودية',
                'postal_code' => $faker->postcode(),
                'personal_email' => $faker->safeEmail(),
                'personal_phone' => $faker->phoneNumber(),
                'emergency_contact_name' => $faker->randomElement($firstNames) . ' ' . $faker->randomElement($lastNames),
                'emergency_contact_phone' => $faker->phoneNumber(),
                'emergency_contact_relation' => $faker->randomElement(['زوج/زوجة', 'أب', 'أم', 'أخ', 'أخت']),
                'hire_date' => $hireDate,
                'probation_end_date' => $faker->optional()->dateTimeBetween($hireDate, '+3 months'),
                'contract_start_date' => $hireDate,
                'contract_end_date' => $faker->optional()->dateTimeBetween('now', '+2 years'),
                'employment_type' => $faker->randomElement(['full_time', 'part_time', 'contract']),
                'employment_status' => $faker->randomElement(['active', 'active', 'active', 'on_leave']), // معظمهم نشط
                'salary' => $faker->numberBetween(3000, 15000),
                'work_location' => $faker->randomElement(['الرياض', 'جدة', 'الدمام', 'المدينة المنورة']),
                'work_phone' => $faker->phoneNumber(),
                'work_email' => $user->email,
                'notes' => $faker->optional()->sentence(),
                'created_by' => $admin ? $admin->id : null,
                'is_active' => true,
            ]);

            $employees[] = $employee;

            // إضافة دور "user" للموظفين
            $userRole = \Spatie\Permission\Models\Role::where('name', 'user')->first();
            if ($userRole) {
                $user->assignRole($userRole);
            }
        }

        $this->command->info('تم إنشاء 50 موظف بنجاح!');
    }
}
