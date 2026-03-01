<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currencies = [
            [
                'name' => 'Saudi Riyal',
                'name_ar' => 'ريال سعودي',
                'code' => 'SAR',
                'symbol' => 'SR',
                'symbol_ar' => 'ر.س',
                'decimal_places' => 2,
                'exchange_rate' => 1.0000,
                'is_base_currency' => true,
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'US Dollar',
                'name_ar' => 'دولار أمريكي',
                'code' => 'USD',
                'symbol' => '$',
                'symbol_ar' => '$',
                'decimal_places' => 2,
                'exchange_rate' => 3.7500,
                'is_base_currency' => false,
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Euro',
                'name_ar' => 'يورو',
                'code' => 'EUR',
                'symbol' => '€',
                'symbol_ar' => '€',
                'decimal_places' => 2,
                'exchange_rate' => 4.1000,
                'is_base_currency' => false,
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'UAE Dirham',
                'name_ar' => 'درهم إماراتي',
                'code' => 'AED',
                'symbol' => 'AED',
                'symbol_ar' => 'د.إ',
                'decimal_places' => 2,
                'exchange_rate' => 1.0200,
                'is_base_currency' => false,
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'name' => 'Egyptian Pound',
                'name_ar' => 'جنيه مصري',
                'code' => 'EGP',
                'symbol' => 'EGP',
                'symbol_ar' => 'ج.م',
                'decimal_places' => 2,
                'exchange_rate' => 0.1200,
                'is_base_currency' => false,
                'is_active' => true,
                'sort_order' => 5,
            ],
            [
                'name' => 'Jordanian Dinar',
                'name_ar' => 'دينار أردني',
                'code' => 'JOD',
                'symbol' => 'JOD',
                'symbol_ar' => 'د.أ',
                'decimal_places' => 3,
                'exchange_rate' => 5.2900,
                'is_base_currency' => false,
                'is_active' => true,
                'sort_order' => 6,
            ],
            [
                'name' => 'Kuwaiti Dinar',
                'name_ar' => 'دينار كويتي',
                'code' => 'KWD',
                'symbol' => 'KWD',
                'symbol_ar' => 'د.ك',
                'decimal_places' => 3,
                'exchange_rate' => 12.2000,
                'is_base_currency' => false,
                'is_active' => true,
                'sort_order' => 7,
            ],
            [
                'name' => 'Qatari Riyal',
                'name_ar' => 'ريال قطري',
                'code' => 'QAR',
                'symbol' => 'QR',
                'symbol_ar' => 'ر.ق',
                'decimal_places' => 2,
                'exchange_rate' => 1.0300,
                'is_base_currency' => false,
                'is_active' => true,
                'sort_order' => 8,
            ],
            [
                'name' => 'Bahraini Dinar',
                'name_ar' => 'دينار بحريني',
                'code' => 'BHD',
                'symbol' => 'BHD',
                'symbol_ar' => 'د.ب',
                'decimal_places' => 3,
                'exchange_rate' => 9.9500,
                'is_base_currency' => false,
                'is_active' => true,
                'sort_order' => 9,
            ],
            [
                'name' => 'Omani Rial',
                'name_ar' => 'ريال عماني',
                'code' => 'OMR',
                'symbol' => 'OMR',
                'symbol_ar' => 'ر.ع',
                'decimal_places' => 3,
                'exchange_rate' => 9.7500,
                'is_base_currency' => false,
                'is_active' => true,
                'sort_order' => 10,
            ],
            [
                'name' => 'British Pound',
                'name_ar' => 'جنيه إسترليني',
                'code' => 'GBP',
                'symbol' => '£',
                'symbol_ar' => '£',
                'decimal_places' => 2,
                'exchange_rate' => 4.7500,
                'is_base_currency' => false,
                'is_active' => true,
                'sort_order' => 11,
            ],
        ];

        foreach ($currencies as $currency) {
            Currency::updateOrCreate(
                ['code' => $currency['code']],
                $currency
            );
        }
    }
}
