<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>كشف الراتب - {{ $payroll->payroll_code }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
        }
        .payslip-header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #000;
            padding-bottom: 20px;
        }
        .payslip-body {
            margin-top: 30px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: right;
        }
        table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .total-row {
            background-color: #e7f3ff;
            font-weight: bold;
        }
        .net-row {
            background-color: #d4edda;
            font-weight: bold;
            font-size: 1.1em;
        }
        @media print {
            body { padding: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="payslip-header">
        <h1>كشف الراتب</h1>
        <p>كود الكشف: {{ $payroll->payroll_code }}</p>
        <p>الفترة: {{ $payroll->month_name }} / {{ $payroll->payroll_year }}</p>
    </div>

    <div class="payslip-body">
        <table>
            <tr>
                <th>اسم الموظف</th>
                <td>{{ $payroll->employee->full_name }}</td>
                <th>كود الموظف</th>
                <td>{{ $payroll->employee->employee_code }}</td>
            </tr>
            <tr>
                <th>القسم</th>
                <td>{{ $payroll->employee->department->name ?? '-' }}</td>
                <th>المنصب</th>
                <td>{{ $payroll->employee->position->name ?? '-' }}</td>
            </tr>
        </table>

        <h3>تفاصيل الراتب</h3>
        <table>
            <thead>
                <tr>
                    <th>البند</th>
                    <th>القيمة</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>الراتب الأساسي</td>
                    <td>{{ number_format($payroll->base_salary, 2) }} {{ $payroll->currency->code ?? '' }}</td>
                </tr>
                <tr>
                    <td>إجمالي البدلات</td>
                    <td>{{ number_format($payroll->total_allowances, 2) }}</td>
                </tr>
                <tr>
                    <td>المكافآت</td>
                    <td>{{ number_format($payroll->bonuses, 2) }}</td>
                </tr>
                <tr>
                    <td>الساعات الإضافية</td>
                    <td>{{ number_format($payroll->overtime_amount, 2) }}</td>
                </tr>
                <tr class="total-row">
                    <td>الراتب الإجمالي</td>
                    <td>{{ number_format($payroll->gross_salary, 2) }}</td>
                </tr>
                <tr>
                    <td>إجمالي الخصومات</td>
                    <td>- {{ number_format($payroll->total_deductions, 2) }}</td>
                </tr>
                <tr>
                    <td>خصم الإجازات</td>
                    <td>- {{ number_format($payroll->leave_deduction, 2) }}</td>
                </tr>
                <tr>
                    <td>خصم التأخير</td>
                    <td>- {{ number_format($payroll->late_deduction, 2) }}</td>
                </tr>
                <tr class="net-row">
                    <td>الراتب الصافي</td>
                    <td>{{ number_format($payroll->net_salary, 2) }} {{ $payroll->currency->code ?? '' }}</td>
                </tr>
            </tbody>
        </table>

        @if($payroll->items->count() > 0)
        <h3>بنود الراتب التفصيلية</h3>
        <table>
            <thead>
                <tr>
                    <th>النوع</th>
                    <th>اسم البند</th>
                    <th>القيمة</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($payroll->items as $item)
                    <tr>
                        <td>{{ $item->item_type_name_ar }}</td>
                        <td>{{ $item->item_name_ar ?? $item->item_name }}</td>
                        <td>{{ number_format($item->amount, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        @endif

        <div style="margin-top: 30px; text-align: center;">
            <p>تم الإنشاء في: {{ $payroll->created_at->format('Y-m-d H:i') }}</p>
            @if($payroll->approved_by)
            <p>تمت الموافقة من: {{ $payroll->approvedBy->name ?? '-' }} في {{ $payroll->approved_at->format('Y-m-d H:i') }}</p>
            @endif
        </div>
    </div>

    <div class="no-print" style="margin-top: 20px; text-align: center;">
        <button onclick="window.print()" class="btn btn-primary">طباعة</button>
        <button onclick="window.close()" class="btn btn-secondary">إغلاق</button>
    </div>
</body>
</html>

