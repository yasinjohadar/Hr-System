<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // الإعدادات العامة
            [
                'key' => 'site_name',
                'group' => 'general',
                'label' => 'Site Name',
                'label_ar' => 'اسم الموقع',
                'value' => 'HR System',
                'type' => 'text',
                'is_required' => true,
                'sort_order' => 1,
            ],
            [
                'key' => 'site_name_ar',
                'group' => 'general',
                'label' => 'Site Name (Arabic)',
                'label_ar' => 'اسم الموقع (عربي)',
                'value' => 'نظام إدارة الموارد البشرية',
                'type' => 'text',
                'is_required' => false,
                'sort_order' => 2,
            ],
            [
                'key' => 'site_email',
                'group' => 'general',
                'label' => 'Site Email',
                'label_ar' => 'البريد الإلكتروني',
                'value' => 'info@hrsystem.com',
                'type' => 'email',
                'is_required' => true,
                'sort_order' => 3,
            ],
            [
                'key' => 'site_phone',
                'group' => 'general',
                'label' => 'Site Phone',
                'label_ar' => 'رقم الهاتف',
                'value' => '+966500000000',
                'type' => 'text',
                'is_required' => false,
                'sort_order' => 4,
            ],
            [
                'key' => 'timezone',
                'group' => 'general',
                'label' => 'Timezone',
                'label_ar' => 'المنطقة الزمنية',
                'value' => 'Asia/Riyadh',
                'type' => 'select',
                'options' => ['Asia/Riyadh', 'UTC', 'America/New_York', 'Europe/London'],
                'is_required' => true,
                'sort_order' => 5,
            ],
            [
                'key' => 'date_format',
                'group' => 'general',
                'label' => 'Date Format',
                'label_ar' => 'تنسيق التاريخ',
                'value' => 'Y-m-d',
                'type' => 'select',
                'options' => ['Y-m-d', 'd/m/Y', 'm/d/Y', 'd-m-Y'],
                'is_required' => true,
                'sort_order' => 6,
            ],
            [
                'key' => 'time_format',
                'group' => 'general',
                'label' => 'Time Format',
                'label_ar' => 'تنسيق الوقت',
                'value' => '24',
                'type' => 'select',
                'options' => ['24', '12'],
                'is_required' => true,
                'sort_order' => 7,
            ],
            [
                'key' => 'language',
                'group' => 'general',
                'label' => 'Default Language',
                'label_ar' => 'اللغة الافتراضية',
                'value' => 'ar',
                'type' => 'select',
                'options' => ['ar', 'en'],
                'is_required' => true,
                'sort_order' => 8,
            ],

            // إعدادات البريد الإلكتروني
            [
                'key' => 'mail_from_address',
                'group' => 'email',
                'label' => 'From Email Address',
                'label_ar' => 'عنوان البريد الإلكتروني المرسل',
                'value' => 'noreply@hrsystem.com',
                'type' => 'email',
                'is_required' => true,
                'sort_order' => 1,
            ],
            [
                'key' => 'mail_from_name',
                'group' => 'email',
                'label' => 'From Name',
                'label_ar' => 'اسم المرسل',
                'value' => 'HR System',
                'type' => 'text',
                'is_required' => true,
                'sort_order' => 2,
            ],
            [
                'key' => 'mail_enabled',
                'group' => 'email',
                'label' => 'Enable Email Notifications',
                'label_ar' => 'تفعيل إشعارات البريد الإلكتروني',
                'value' => '1',
                'type' => 'boolean',
                'is_required' => false,
                'sort_order' => 3,
            ],

            // إعدادات الحضور
            [
                'key' => 'attendance_check_in_time',
                'group' => 'attendance',
                'label' => 'Default Check-in Time',
                'label_ar' => 'وقت الدخول الافتراضي',
                'value' => '09:00',
                'type' => 'text',
                'is_required' => true,
                'sort_order' => 1,
            ],
            [
                'key' => 'attendance_check_out_time',
                'group' => 'attendance',
                'label' => 'Default Check-out Time',
                'label_ar' => 'وقت الخروج الافتراضي',
                'value' => '17:00',
                'type' => 'text',
                'is_required' => true,
                'sort_order' => 2,
            ],
            [
                'key' => 'attendance_late_threshold',
                'group' => 'attendance',
                'label' => 'Late Threshold (minutes)',
                'label_ar' => 'حد التأخير (بالدقائق)',
                'value' => '15',
                'type' => 'number',
                'is_required' => true,
                'sort_order' => 3,
            ],
            [
                'key' => 'attendance_auto_calculate',
                'group' => 'attendance',
                'label' => 'Auto Calculate Hours',
                'label_ar' => 'حساب الساعات تلقائياً',
                'value' => '1',
                'type' => 'boolean',
                'is_required' => false,
                'sort_order' => 4,
            ],

            // إعدادات الرواتب
            [
                'key' => 'salary_currency',
                'group' => 'salary',
                'label' => 'Default Currency',
                'label_ar' => 'العملة الافتراضية',
                'value' => 'SAR',
                'type' => 'text',
                'is_required' => true,
                'sort_order' => 1,
            ],
            [
                'key' => 'salary_payment_day',
                'group' => 'salary',
                'label' => 'Payment Day',
                'label_ar' => 'يوم الدفع',
                'value' => '25',
                'type' => 'number',
                'is_required' => true,
                'sort_order' => 2,
            ],
            [
                'key' => 'salary_auto_calculate',
                'group' => 'salary',
                'label' => 'Auto Calculate Gross Salary',
                'label_ar' => 'حساب الراتب الإجمالي تلقائياً',
                'value' => '1',
                'type' => 'boolean',
                'is_required' => false,
                'sort_order' => 3,
            ],

            // إعدادات الإجازات
            [
                'key' => 'leave_auto_approve',
                'group' => 'leave',
                'label' => 'Auto Approve Leave Requests',
                'label_ar' => 'الموافقة التلقائية على طلبات الإجازة',
                'value' => '0',
                'type' => 'boolean',
                'is_required' => false,
                'sort_order' => 1,
            ],
            [
                'key' => 'leave_max_days_per_request',
                'group' => 'leave',
                'label' => 'Max Days Per Request',
                'label_ar' => 'الحد الأقصى للأيام لكل طلب',
                'value' => '30',
                'type' => 'number',
                'is_required' => true,
                'sort_order' => 2,
            ],
            [
                'key' => 'leave_advance_booking_days',
                'group' => 'leave',
                'label' => 'Advance Booking Days',
                'label_ar' => 'أيام الحجز المسبق',
                'value' => '7',
                'type' => 'number',
                'is_required' => true,
                'sort_order' => 3,
            ],

            // إعدادات النظام
            [
                'key' => 'system_maintenance_mode',
                'group' => 'system',
                'label' => 'Maintenance Mode',
                'label_ar' => 'وضع الصيانة',
                'value' => '0',
                'type' => 'boolean',
                'is_required' => false,
                'sort_order' => 1,
            ],
            [
                'key' => 'system_session_timeout',
                'group' => 'system',
                'label' => 'Session Timeout (minutes)',
                'label_ar' => 'انتهاء الجلسة (بالدقائق)',
                'value' => '120',
                'type' => 'number',
                'is_required' => true,
                'sort_order' => 2,
            ],
            [
                'key' => 'system_backup_enabled',
                'group' => 'system',
                'label' => 'Enable Auto Backup',
                'label_ar' => 'تفعيل النسخ الاحتياطي التلقائي',
                'value' => '1',
                'type' => 'boolean',
                'is_required' => false,
                'sort_order' => 3,
            ],
        ];

        foreach ($settings as $setting) {
            Setting::firstOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
