# المكافآت والتقدير

## الوصف

تعريف أنواع المكافآت وتسجيل مكافآت الموظفين مع إجراء منح (`award`).

## المسارات

- `admin/reward-types` (resource)
- `admin/employee-rewards` (resource)
- `POST admin/employee-rewards/{id}/award` → `admin.employee-rewards.award`

[`routes/admin.php`](../../routes/admin.php)

## النماذج والجداول

- **Models**: `RewardType`, `EmployeeReward`
- **جداول**: `reward_types`, `employee_rewards`

## الواجهات

- `resources/views/admin/pages/reward-types/*`, `employee-rewards/*`

## ملاحظات

- يمكن ربطها ماليًا بمسيرات مستقبلية أو مكافآت متغيرة — انظر [`plan/ideal-system-roadmap.md`](../ideal-system-roadmap.md).
