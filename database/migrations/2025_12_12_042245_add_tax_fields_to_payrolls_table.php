<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            // الضرائب والاستقطاعات
            $table->decimal('income_tax', 12, 2)->default(0)->after('gross_salary'); // ضريبة الدخل
            $table->decimal('social_insurance_employee', 12, 2)->default(0)->after('income_tax'); // التأمينات الاجتماعية (موظف)
            $table->decimal('social_insurance_employer', 12, 2)->default(0)->after('social_insurance_employee'); // التأمينات الاجتماعية (صاحب عمل)
            $table->decimal('health_insurance_employee', 12, 2)->default(0)->after('social_insurance_employer'); // التأمين الصحي (موظف)
            $table->decimal('health_insurance_employer', 12, 2)->default(0)->after('health_insurance_employee'); // التأمين الصحي (صاحب عمل)
            $table->decimal('other_taxes', 12, 2)->default(0)->after('health_insurance_employer'); // ضرائب أخرى
            $table->decimal('total_taxes', 12, 2)->default(0)->after('other_taxes'); // إجمالي الضرائب
            $table->decimal('total_employer_cost', 12, 2)->default(0)->after('net_salary'); // إجمالي تكلفة صاحب العمل
        });
    }

    public function down(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->dropColumn([
                'income_tax',
                'social_insurance_employee',
                'social_insurance_employer',
                'health_insurance_employee',
                'health_insurance_employer',
                'other_taxes',
                'total_taxes',
                'total_employer_cost',
            ]);
        });
    }
};
