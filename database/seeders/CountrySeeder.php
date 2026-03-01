<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $countries = [
            [
                'name' => 'Saudi Arabia',
                'name_ar' => 'المملكة العربية السعودية',
                'code' => 'SA',
                'code3' => 'SAU',
                'phone_code' => '+966',
                'currency_code' => 'SAR',
                'flag' => '🇸🇦',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'United Arab Emirates',
                'name_ar' => 'الإمارات العربية المتحدة',
                'code' => 'AE',
                'code3' => 'ARE',
                'phone_code' => '+971',
                'currency_code' => 'AED',
                'flag' => '🇦🇪',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Egypt',
                'name_ar' => 'مصر',
                'code' => 'EG',
                'code3' => 'EGY',
                'phone_code' => '+20',
                'currency_code' => 'EGP',
                'flag' => '🇪🇬',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Jordan',
                'name_ar' => 'الأردن',
                'code' => 'JO',
                'code3' => 'JOR',
                'phone_code' => '+962',
                'currency_code' => 'JOD',
                'flag' => '🇯🇴',
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'name' => 'Kuwait',
                'name_ar' => 'الكويت',
                'code' => 'KW',
                'code3' => 'KWT',
                'phone_code' => '+965',
                'currency_code' => 'KWD',
                'flag' => '🇰🇼',
                'is_active' => true,
                'sort_order' => 5,
            ],
            [
                'name' => 'Qatar',
                'name_ar' => 'قطر',
                'code' => 'QA',
                'code3' => 'QAT',
                'phone_code' => '+974',
                'currency_code' => 'QAR',
                'flag' => '🇶🇦',
                'is_active' => true,
                'sort_order' => 6,
            ],
            [
                'name' => 'Bahrain',
                'name_ar' => 'البحرين',
                'code' => 'BH',
                'code3' => 'BHR',
                'phone_code' => '+973',
                'currency_code' => 'BHD',
                'flag' => '🇧🇭',
                'is_active' => true,
                'sort_order' => 7,
            ],
            [
                'name' => 'Oman',
                'name_ar' => 'سلطنة عمان',
                'code' => 'OM',
                'code3' => 'OMN',
                'phone_code' => '+968',
                'currency_code' => 'OMR',
                'flag' => '🇴🇲',
                'is_active' => true,
                'sort_order' => 8,
            ],
            [
                'name' => 'United States',
                'name_ar' => 'الولايات المتحدة الأمريكية',
                'code' => 'US',
                'code3' => 'USA',
                'phone_code' => '+1',
                'currency_code' => 'USD',
                'flag' => '🇺🇸',
                'is_active' => true,
                'sort_order' => 9,
            ],
            [
                'name' => 'United Kingdom',
                'name_ar' => 'المملكة المتحدة',
                'code' => 'GB',
                'code3' => 'GBR',
                'phone_code' => '+44',
                'currency_code' => 'GBP',
                'flag' => '🇬🇧',
                'is_active' => true,
                'sort_order' => 10,
            ],
        ];

        foreach ($countries as $country) {
            Country::updateOrCreate(
                ['code' => $country['code']],
                $country
            );
        }
    }
}
