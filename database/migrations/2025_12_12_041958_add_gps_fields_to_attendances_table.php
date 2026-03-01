<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->decimal('check_in_latitude', 10, 8)->nullable()->after('check_in');
            $table->decimal('check_in_longitude', 11, 8)->nullable()->after('check_in_latitude');
            $table->string('check_in_location')->nullable()->after('check_in_longitude');
            $table->decimal('check_out_latitude', 10, 8)->nullable()->after('check_out');
            $table->decimal('check_out_longitude', 11, 8)->nullable()->after('check_out_latitude');
            $table->string('check_out_location')->nullable()->after('check_out_longitude');
            $table->foreignId('attendance_location_id')->nullable()->after('check_out_location')
                ->constrained('attendance_locations')->nullOnDelete();
            $table->boolean('location_verified')->default(false)->after('attendance_location_id');
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropForeign(['attendance_location_id']);
            $table->dropColumn([
                'check_in_latitude',
                'check_in_longitude',
                'check_in_location',
                'check_out_latitude',
                'check_out_longitude',
                'check_out_location',
                'attendance_location_id',
                'location_verified',
            ]);
        });
    }
};
