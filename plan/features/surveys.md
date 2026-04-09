# الاستبيانات

## الوصف

بناء استبيانات مع أسئلة وجمع استجابات (استطلاعات رضا، تغذية راجعة، pulse).

## المسارات

- `admin/surveys` (resource) — `SurveyController`

[`routes/admin.php`](../../routes/admin.php)

## النماذج والجداول

- **Models**: `Survey`, `SurveyQuestion`, `SurveyResponse`
- **جداول**: `surveys`, `survey_questions`, `survey_responses`

## الواجهات

- `resources/views/admin/pages/surveys/*`

## ملاحظات

- تعزيز تجربة الموظف بإتاحة الاستجابة من البوابة غير مضاف في [`routes/employee.php`](../../routes/employee.php) حاليًا — مرشّح في [`plan/ideal-system-roadmap.md`](../ideal-system-roadmap.md).
