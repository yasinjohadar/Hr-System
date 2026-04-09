# المسيرات والمكونات والضرائب والبنوك والدفعات والموافقات

## الوصف

دورة كاملة لمسير الرواتب: إنشاء مسير، حساب البنود، موافقة، كشف راتب (عرض وPDF)، تصدير ملف بنكي، مكونات الراتب، إعدادات ضريبية، حسابات بنكية للموظفين، سجلات دفع (مع `process`)، ومسار موافقات مستقل للمسيرات.

## المسارات (أهمها)

- `admin/payrolls` (resource)
- `POST admin/payrolls/{id}/calculate`, `/approve`
- `GET admin/payrolls/{id}/payslip`, `payslip/pdf`
- `GET admin/payrolls/export-bank-file`
- `admin/salary-components`, `tax-settings`, `bank-accounts`, `payroll-payments` (+ `process`), `payroll-approvals` (+ approve/reject)
- **موظف**: `GET employee/payrolls/{id}/payslip/pdf`

[`routes/admin.php`](../../routes/admin.php), [`routes/employee.php`](../../routes/employee.php)

## النماذج والجداول

- **Models**: `Payroll`, `PayrollItem`, `SalaryComponent`, `TaxSetting`, `EmployeeBankAccount`, `PayrollPayment`, `PayrollApproval`
- **جداول**: `payrolls`, `payroll_items`, `salary_components`, `tax_settings`, `employee_bank_accounts`, `payroll_payments`, `payroll_approvals`

## الواجهات

- `resources/views/admin/pages/payrolls/*`, `payroll-payments/*`, `payroll-approvals/*`, `bank-accounts/*`, `tax-settings/*`, `salary-components/*`

## ملاحظات

- بيانات حساسة جدًا: راجع [`plan/features/audit-logs.md`](audit-logs.md) والصلاحيات.
- تصدير وتقارير رواتب — [`plan/features/data-export.md`](data-export.md), [`plan/features/reports.md`](reports.md).
