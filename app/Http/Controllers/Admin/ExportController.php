<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Department;
use App\Models\Branch;
use App\Models\Position;
use App\Models\Salary;
use App\Models\LeaveType;
use App\Models\LeaveRequest;
use App\Models\LeaveBalance;
use App\Models\Attendance;
use App\Models\PerformanceReview;
use App\Models\Training;
use App\Models\TrainingRecord;
use App\Models\JobVacancy;
use App\Models\Candidate;
use App\Models\JobApplication;
use App\Models\Interview;
use App\Models\BenefitType;
use App\Models\EmployeeBenefit;
use App\Models\ExpenseCategory;
use App\Models\ExpenseRequest;
use App\Models\Asset;
use App\Models\AssetAssignment;
use App\Models\AssetMaintenance;
use App\Models\Payroll;
use App\Models\PayrollPayment;
use App\Models\PayrollItem;
use App\Models\Shift;
use App\Models\OvertimeRecord;
use App\Models\ViolationType;
use App\Models\DisciplinaryAction;
use App\Models\EmployeeViolation;
use App\Models\Project;
use App\Models\Task;
use App\Models\Ticket;
use App\Models\Meeting;
use App\Models\CalendarEvent;
use App\Models\TaxSetting;
use App\Models\SalaryComponent;
use App\Models\EmployeeBankAccount;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ExportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:export-data');
    }

    /**
     * تصدير جدول الموظفين
     */
    public function employees()
    {
        return Excel::download(new class implements FromCollection, WithHeadings, WithMapping, WithStyles {
            public function collection()
            {
                return Employee::with(['user', 'department', 'position', 'branch'])->get();
            }

            public function headings(): array
            {
                return [
                    'الرقم الوظيفي',
                    'الاسم الكامل',
                    'البريد الإلكتروني',
                    'الهاتف',
                    'القسم',
                    'المنصب',
                    'الفرع',
                    'تاريخ التوظيف',
                    'الراتب',
                    'الحالة',
                    'تاريخ الإنشاء'
                ];
            }

            public function map($employee): array
            {
                return [
                    $employee->employee_number,
                    $employee->full_name,
                    $employee->user->email ?? '',
                    $employee->phone,
                    $employee->department->name_ar ?? $employee->department->name ?? '',
                    $employee->position->name_ar ?? $employee->position->name ?? '',
                    $employee->branch->name_ar ?? $employee->branch->name ?? '',
                    $employee->hire_date?->format('Y-m-d'),
                    $employee->salary,
                    $employee->is_active ? 'نشط' : 'غير نشط',
                    $employee->created_at->format('Y-m-d H:i'),
                ];
            }

            public function styles(Worksheet $sheet)
            {
                return [
                    1 => [
                        'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    ],
                ];
            }
        }, 'employees_' . date('Y-m-d') . '.xlsx');
    }

    /**
     * تصدير جدول الأقسام
     */
    public function departments()
    {
        return Excel::download(new class implements FromCollection, WithHeadings, WithMapping, WithStyles {
            public function collection()
            {
                return Department::with('manager')->get();
            }

            public function headings(): array
            {
                return ['الاسم', 'الاسم بالعربية', 'المدير', 'الحالة', 'تاريخ الإنشاء'];
            }

            public function map($department): array
            {
                return [
                    $department->name,
                    $department->name_ar ?? '',
                    $department->manager->name ?? '',
                    $department->is_active ? 'نشط' : 'غير نشط',
                    $department->created_at->format('Y-m-d H:i'),
                ];
            }

            public function styles(Worksheet $sheet)
            {
                return [
                    1 => [
                        'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    ],
                ];
            }
        }, 'departments_' . date('Y-m-d') . '.xlsx');
    }

    /**
     * تصدير جدول الفروع
     */
    public function branches()
    {
        return Excel::download(new class implements FromCollection, WithHeadings, WithMapping, WithStyles {
            public function collection()
            {
                return Branch::all();
            }

            public function headings(): array
            {
                return ['الاسم', 'الاسم بالعربية', 'العنوان', 'الهاتف', 'الحالة', 'تاريخ الإنشاء'];
            }

            public function map($branch): array
            {
                return [
                    $branch->name,
                    $branch->name_ar ?? '',
                    $branch->address ?? '',
                    $branch->phone ?? '',
                    $branch->is_active ? 'نشط' : 'غير نشط',
                    $branch->created_at->format('Y-m-d H:i'),
                ];
            }

            public function styles(Worksheet $sheet)
            {
                return [
                    1 => [
                        'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    ],
                ];
            }
        }, 'branches_' . date('Y-m-d') . '.xlsx');
    }

    /**
     * تصدير جدول المناصب
     */
    public function positions()
    {
        return Excel::download(new class implements FromCollection, WithHeadings, WithMapping, WithStyles {
            public function collection()
            {
                return Position::all();
            }

            public function headings(): array
            {
                return ['الاسم', 'الاسم بالعربية', 'القسم', 'الحالة', 'تاريخ الإنشاء'];
            }

            public function map($position): array
            {
                return [
                    $position->name,
                    $position->name_ar ?? '',
                    $position->department->name_ar ?? $position->department->name ?? '',
                    $position->is_active ? 'نشط' : 'غير نشط',
                    $position->created_at->format('Y-m-d H:i'),
                ];
            }

            public function styles(Worksheet $sheet)
            {
                return [
                    1 => [
                        'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    ],
                ];
            }
        }, 'positions_' . date('Y-m-d') . '.xlsx');
    }

    /**
     * تصدير جدول الرواتب
     */
    public function salaries()
    {
        return Excel::download(new class implements FromCollection, WithHeadings, WithMapping, WithStyles {
            public function collection()
            {
                return Salary::with(['employee', 'currency'])->get();
            }

            public function headings(): array
            {
                return ['الموظف', 'الراتب الأساسي', 'العملة', 'تاريخ البدء', 'تاريخ الانتهاء', 'الحالة', 'تاريخ الإنشاء'];
            }

            public function map($salary): array
            {
                return [
                    $salary->employee->full_name,
                    $salary->base_salary,
                    $salary->currency->code ?? '',
                    $salary->start_date?->format('Y-m-d'),
                    $salary->end_date?->format('Y-m-d'),
                    $salary->is_active ? 'نشط' : 'غير نشط',
                    $salary->created_at->format('Y-m-d H:i'),
                ];
            }

            public function styles(Worksheet $sheet)
            {
                return [
                    1 => [
                        'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    ],
                ];
            }
        }, 'salaries_' . date('Y-m-d') . '.xlsx');
    }

    /**
     * تصدير جدول طلبات الإجازات
     */
    public function leaveRequests()
    {
        return Excel::download(new class implements FromCollection, WithHeadings, WithMapping, WithStyles {
            public function collection()
            {
                return LeaveRequest::with(['employee', 'leaveType', 'approvedBy'])->get();
            }

            public function headings(): array
            {
                return ['الموظف', 'نوع الإجازة', 'تاريخ البدء', 'تاريخ الانتهاء', 'عدد الأيام', 'الحالة', 'الموافق', 'تاريخ الإنشاء'];
            }

            public function map($request): array
            {
                return [
                    $request->employee->full_name,
                    $request->leaveType->name_ar ?? $request->leaveType->name,
                    $request->start_date->format('Y-m-d'),
                    $request->end_date?->format('Y-m-d'),
                    $request->days,
                    $request->status,
                    $request->approvedBy->name ?? '',
                    $request->created_at->format('Y-m-d H:i'),
                ];
            }

            public function styles(Worksheet $sheet)
            {
                return [
                    1 => [
                        'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    ],
                ];
            }
        }, 'leave_requests_' . date('Y-m-d') . '.xlsx');
    }

    /**
     * تصدير جدول الحضور
     */
    public function attendances()
    {
        return Excel::download(new class implements FromCollection, WithHeadings, WithMapping, WithStyles {
            public function collection()
            {
                return Attendance::with('employee')->get();
            }

            public function headings(): array
            {
                return ['الموظف', 'تاريخ الحضور', 'وقت الدخول', 'وقت الخروج', 'ساعات العمل', 'الحالة', 'تاريخ الإنشاء'];
            }

            public function map($attendance): array
            {
                return [
                    $attendance->employee->full_name,
                    $attendance->attendance_date->format('Y-m-d'),
                    $attendance->check_in?->format('H:i'),
                    $attendance->check_out?->format('H:i'),
                    $attendance->hours_worked ?? 0,
                    $attendance->status,
                    $attendance->created_at->format('Y-m-d H:i'),
                ];
            }

            public function styles(Worksheet $sheet)
            {
                return [
                    1 => [
                        'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    ],
                ];
            }
        }, 'attendances_' . date('Y-m-d') . '.xlsx');
    }

    /**
     * تصدير جدول كشوف الرواتب
     */
    public function payrolls()
    {
        return Excel::download(new class implements FromCollection, WithHeadings, WithMapping, WithStyles {
            public function collection()
            {
                return Payroll::with(['employee', 'currency'])->get();
            }

            public function headings(): array
            {
                return ['كود الكشف', 'الموظف', 'الشهر', 'السنة', 'الراتب الأساسي', 'الإجمالي', 'الصافي', 'العملة', 'الحالة', 'تاريخ الإنشاء'];
            }

            public function map($payroll): array
            {
                return [
                    $payroll->payroll_code,
                    $payroll->employee->full_name,
                    $payroll->payroll_month,
                    $payroll->payroll_year,
                    $payroll->base_salary,
                    $payroll->gross_salary,
                    $payroll->net_salary,
                    $payroll->currency->code ?? '',
                    $payroll->status,
                    $payroll->created_at->format('Y-m-d H:i'),
                ];
            }

            public function styles(Worksheet $sheet)
            {
                return [
                    1 => [
                        'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    ],
                ];
            }
        }, 'payrolls_' . date('Y-m-d') . '.xlsx');
    }

    /**
     * تصدير جدول التدريبات
     */
    public function trainings()
    {
        return Excel::download(new class implements FromCollection, WithHeadings, WithMapping, WithStyles {
            public function collection()
            {
                return Training::all();
            }

            public function headings(): array
            {
                return ['الاسم', 'الاسم بالعربية', 'النوع', 'تاريخ البدء', 'تاريخ الانتهاء', 'المدرب', 'الحالة', 'تاريخ الإنشاء'];
            }

            public function map($training): array
            {
                return [
                    $training->name,
                    $training->name_ar ?? '',
                    $training->type,
                    $training->start_date?->format('Y-m-d'),
                    $training->end_date?->format('Y-m-d'),
                    $training->trainer,
                    $training->status,
                    $training->created_at->format('Y-m-d H:i'),
                ];
            }

            public function styles(Worksheet $sheet)
            {
                return [
                    1 => [
                        'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    ],
                ];
            }
        }, 'trainings_' . date('Y-m-d') . '.xlsx');
    }

    /**
     * تصدير جدول التقييمات
     */
    public function performanceReviews()
    {
        return Excel::download(new class implements FromCollection, WithHeadings, WithMapping, WithStyles {
            public function collection()
            {
                return PerformanceReview::with(['employee', 'reviewer'])->get();
            }

            public function headings(): array
            {
                return ['الموظف', 'المقيّم', 'الفترة', 'التقييم', 'الحالة', 'تاريخ الإنشاء'];
            }

            public function map($review): array
            {
                return [
                    $review->employee->full_name,
                    $review->reviewer->name ?? '',
                    $review->review_period,
                    $review->overall_rating,
                    $review->status,
                    $review->created_at->format('Y-m-d H:i'),
                ];
            }

            public function styles(Worksheet $sheet)
            {
                return [
                    1 => [
                        'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    ],
                ];
            }
        }, 'performance_reviews_' . date('Y-m-d') . '.xlsx');
    }

    /**
     * تصدير جدول المصروفات
     */
    public function expenseRequests()
    {
        return Excel::download(new class implements FromCollection, WithHeadings, WithMapping, WithStyles {
            public function collection()
            {
                return ExpenseRequest::with(['employee', 'category', 'approvedBy'])->get();
            }

            public function headings(): array
            {
                return ['الموظف', 'التصنيف', 'المبلغ', 'التاريخ', 'الحالة', 'الموافق', 'تاريخ الإنشاء'];
            }

            public function map($expense): array
            {
                return [
                    $expense->employee->full_name,
                    $expense->category->name_ar ?? $expense->category->name,
                    $expense->amount,
                    $expense->expense_date->format('Y-m-d'),
                    $expense->status,
                    $expense->approvedBy->name ?? '',
                    $expense->created_at->format('Y-m-d H:i'),
                ];
            }

            public function styles(Worksheet $sheet)
            {
                return [
                    1 => [
                        'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    ],
                ];
            }
        }, 'expense_requests_' . date('Y-m-d') . '.xlsx');
    }

    /**
     * تصدير جدول الأصول
     */
    public function assets()
    {
        return Excel::download(new class implements FromCollection, WithHeadings, WithMapping, WithStyles {
            public function collection()
            {
                return Asset::all();
            }

            public function headings(): array
            {
                return ['الاسم', 'النوع', 'الرقم التسلسلي', 'الحالة', 'القيمة', 'تاريخ الشراء', 'تاريخ الإنشاء'];
            }

            public function map($asset): array
            {
                return [
                    $asset->name,
                    $asset->type,
                    $asset->serial_number ?? '',
                    $asset->status,
                    $asset->purchase_value,
                    $asset->purchase_date?->format('Y-m-d'),
                    $asset->created_at->format('Y-m-d H:i'),
                ];
            }

            public function styles(Worksheet $sheet)
            {
                return [
                    1 => [
                        'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    ],
                ];
            }
        }, 'assets_' . date('Y-m-d') . '.xlsx');
    }

    /**
     * تصدير جدول أنواع الإجازات
     */
    public function leaveTypes()
    {
        return Excel::download(new class implements FromCollection, WithHeadings, WithMapping, WithStyles {
            public function collection()
            {
                return LeaveType::all();
            }

            public function headings(): array
            {
                return ['الاسم', 'الاسم بالعربية', 'عدد الأيام', 'مدفوعة', 'قابلة للترحيل', 'الحالة', 'تاريخ الإنشاء'];
            }

            public function map($type): array
            {
                return [
                    $type->name,
                    $type->name_ar ?? '',
                    $type->days_per_year,
                    $type->is_paid ? 'نعم' : 'لا',
                    $type->is_carry_forward ? 'نعم' : 'لا',
                    $type->is_active ? 'نشط' : 'غير نشط',
                    $type->created_at->format('Y-m-d H:i'),
                ];
            }

            public function styles(Worksheet $sheet)
            {
                return [
                    1 => [
                        'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    ],
                ];
            }
        }, 'leave_types_' . date('Y-m-d') . '.xlsx');
    }

    /**
     * تصدير جدول أرصدة الإجازات
     */
    public function leaveBalances()
    {
        return Excel::download(new class implements FromCollection, WithHeadings, WithMapping, WithStyles {
            public function collection()
            {
                return LeaveBalance::with(['employee', 'leaveType'])->get();
            }

            public function headings(): array
            {
                return ['الموظف', 'نوع الإجازة', 'الرصيد', 'المستخدم', 'المتبقي', 'السنة', 'تاريخ الإنشاء'];
            }

            public function map($balance): array
            {
                return [
                    $balance->employee->full_name,
                    $balance->leaveType->name_ar ?? $balance->leaveType->name,
                    $balance->balance,
                    $balance->used,
                    $balance->remaining,
                    $balance->year,
                    $balance->created_at->format('Y-m-d H:i'),
                ];
            }

            public function styles(Worksheet $sheet)
            {
                return [
                    1 => [
                        'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    ],
                ];
            }
        }, 'leave_balances_' . date('Y-m-d') . '.xlsx');
    }

    /**
     * تصدير جدول سجلات التدريب
     */
    public function trainingRecords()
    {
        return Excel::download(new class implements FromCollection, WithHeadings, WithMapping, WithStyles {
            public function collection()
            {
                return TrainingRecord::with(['employee', 'training'])->get();
            }

            public function headings(): array
            {
                return ['الموظف', 'التدريب', 'تاريخ البدء', 'تاريخ الانتهاء', 'الحالة', 'التقييم', 'تاريخ الإنشاء'];
            }

            public function map($record): array
            {
                return [
                    $record->employee->full_name,
                    $record->training->name_ar ?? $record->training->name,
                    $record->start_date?->format('Y-m-d'),
                    $record->end_date?->format('Y-m-d'),
                    $record->status,
                    $record->rating ?? '',
                    $record->created_at->format('Y-m-d H:i'),
                ];
            }

            public function styles(Worksheet $sheet)
            {
                return [
                    1 => [
                        'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    ],
                ];
            }
        }, 'training_records_' . date('Y-m-d') . '.xlsx');
    }

    /**
     * تصدير جدول الوظائف الشاغرة
     */
    public function jobVacancies()
    {
        return Excel::download(new class implements FromCollection, WithHeadings, WithMapping, WithStyles {
            public function collection()
            {
                return JobVacancy::with(['department', 'position'])->get();
            }

            public function headings(): array
            {
                return ['العنوان', 'القسم', 'المنصب', 'عدد الشواغر', 'الحالة', 'تاريخ الإغلاق', 'تاريخ الإنشاء'];
            }

            public function map($vacancy): array
            {
                return [
                    $vacancy->title_ar ?? $vacancy->title,
                    $vacancy->department->name_ar ?? $vacancy->department->name ?? '',
                    $vacancy->position->name_ar ?? $vacancy->position->name ?? '',
                    $vacancy->number_of_positions,
                    $vacancy->status,
                    $vacancy->closing_date?->format('Y-m-d'),
                    $vacancy->created_at->format('Y-m-d H:i'),
                ];
            }

            public function styles(Worksheet $sheet)
            {
                return [
                    1 => [
                        'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    ],
                ];
            }
        }, 'job_vacancies_' . date('Y-m-d') . '.xlsx');
    }

    /**
     * تصدير جدول المرشحين
     */
    public function candidates()
    {
        return Excel::download(new class implements FromCollection, WithHeadings, WithMapping, WithStyles {
            public function collection()
            {
                return Candidate::all();
            }

            public function headings(): array
            {
                return ['الاسم', 'البريد الإلكتروني', 'الهاتف', 'المنصب المطلوب', 'الحالة', 'تاريخ الإنشاء'];
            }

            public function map($candidate): array
            {
                return [
                    $candidate->full_name,
                    $candidate->email,
                    $candidate->phone,
                    $candidate->position_applied,
                    $candidate->status,
                    $candidate->created_at->format('Y-m-d H:i'),
                ];
            }

            public function styles(Worksheet $sheet)
            {
                return [
                    1 => [
                        'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    ],
                ];
            }
        }, 'candidates_' . date('Y-m-d') . '.xlsx');
    }

    /**
     * تصدير جدول طلبات التوظيف
     */
    public function jobApplications()
    {
        return Excel::download(new class implements FromCollection, WithHeadings, WithMapping, WithStyles {
            public function collection()
            {
                return JobApplication::with(['candidate', 'jobVacancy'])->get();
            }

            public function headings(): array
            {
                return ['المرشح', 'الوظيفة', 'الحالة', 'تاريخ التقديم', 'تاريخ الإنشاء'];
            }

            public function map($application): array
            {
                return [
                    $application->candidate->full_name,
                    $application->jobVacancy->title_ar ?? $application->jobVacancy->title,
                    $application->status,
                    $application->application_date->format('Y-m-d'),
                    $application->created_at->format('Y-m-d H:i'),
                ];
            }

            public function styles(Worksheet $sheet)
            {
                return [
                    1 => [
                        'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    ],
                ];
            }
        }, 'job_applications_' . date('Y-m-d') . '.xlsx');
    }

    /**
     * تصدير جدول المقابلات
     */
    public function interviews()
    {
        return Excel::download(new class implements FromCollection, WithHeadings, WithMapping, WithStyles {
            public function collection()
            {
                return Interview::with(['candidate', 'interviewer'])->get();
            }

            public function headings(): array
            {
                return ['المرشح', 'المقابل', 'التاريخ', 'الوقت', 'النوع', 'النتيجة', 'تاريخ الإنشاء'];
            }

            public function map($interview): array
            {
                return [
                    $interview->candidate->full_name,
                    $interview->interviewer->name ?? '',
                    $interview->interview_date->format('Y-m-d'),
                    $interview->interview_time,
                    $interview->type,
                    $interview->result ?? '',
                    $interview->created_at->format('Y-m-d H:i'),
                ];
            }

            public function styles(Worksheet $sheet)
            {
                return [
                    1 => [
                        'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    ],
                ];
            }
        }, 'interviews_' . date('Y-m-d') . '.xlsx');
    }

    /**
     * تصدير جدول أنواع المزايا
     */
    public function benefitTypes()
    {
        return Excel::download(new class implements FromCollection, WithHeadings, WithMapping, WithStyles {
            public function collection()
            {
                return BenefitType::all();
            }

            public function headings(): array
            {
                return ['الاسم', 'الاسم بالعربية', 'النوع', 'القيمة', 'الحالة', 'تاريخ الإنشاء'];
            }

            public function map($type): array
            {
                return [
                    $type->name,
                    $type->name_ar ?? '',
                    $type->type,
                    $type->value,
                    $type->is_active ? 'نشط' : 'غير نشط',
                    $type->created_at->format('Y-m-d H:i'),
                ];
            }

            public function styles(Worksheet $sheet)
            {
                return [
                    1 => [
                        'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    ],
                ];
            }
        }, 'benefit_types_' . date('Y-m-d') . '.xlsx');
    }

    /**
     * تصدير جدول مزايا الموظفين
     */
    public function employeeBenefits()
    {
        return Excel::download(new class implements FromCollection, WithHeadings, WithMapping, WithStyles {
            public function collection()
            {
                return EmployeeBenefit::with(['employee', 'benefitType'])->get();
            }

            public function headings(): array
            {
                return ['الموظف', 'نوع الميزة', 'القيمة', 'تاريخ البدء', 'تاريخ الانتهاء', 'الحالة', 'تاريخ الإنشاء'];
            }

            public function map($benefit): array
            {
                return [
                    $benefit->employee->full_name,
                    $benefit->benefitType->name_ar ?? $benefit->benefitType->name,
                    $benefit->amount,
                    $benefit->start_date?->format('Y-m-d'),
                    $benefit->end_date?->format('Y-m-d'),
                    $benefit->is_active ? 'نشط' : 'غير نشط',
                    $benefit->created_at->format('Y-m-d H:i'),
                ];
            }

            public function styles(Worksheet $sheet)
            {
                return [
                    1 => [
                        'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    ],
                ];
            }
        }, 'employee_benefits_' . date('Y-m-d') . '.xlsx');
    }

    /**
     * تصدير جدول المناوبات
     */
    public function shifts()
    {
        return Excel::download(new class implements FromCollection, WithHeadings, WithMapping, WithStyles {
            public function collection()
            {
                return Shift::all();
            }

            public function headings(): array
            {
                return ['الاسم', 'الاسم بالعربية', 'وقت البدء', 'وقت الانتهاء', 'ساعات العمل', 'الحالة', 'تاريخ الإنشاء'];
            }

            public function map($shift): array
            {
                return [
                    $shift->name,
                    $shift->name_ar ?? '',
                    $shift->start_time,
                    $shift->end_time,
                    $shift->hours_per_day,
                    $shift->is_active ? 'نشط' : 'غير نشط',
                    $shift->created_at->format('Y-m-d H:i'),
                ];
            }

            public function styles(Worksheet $sheet)
            {
                return [
                    1 => [
                        'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    ],
                ];
            }
        }, 'shifts_' . date('Y-m-d') . '.xlsx');
    }

    /**
     * تصدير جدول الساعات الإضافية
     */
    public function overtimes()
    {
        return Excel::download(new class implements FromCollection, WithHeadings, WithMapping, WithStyles {
            public function collection()
            {
                return OvertimeRecord::with(['employee', 'approvedBy'])->get();
            }

            public function headings(): array
            {
                return ['الموظف', 'التاريخ', 'الساعات', 'المبلغ', 'الحالة', 'الموافق', 'تاريخ الإنشاء'];
            }

            public function map($overtime): array
            {
                return [
                    $overtime->employee->full_name,
                    $overtime->overtime_date->format('Y-m-d'),
                    $overtime->hours,
                    $overtime->amount,
                    $overtime->status,
                    $overtime->approvedBy->name ?? '',
                    $overtime->created_at->format('Y-m-d H:i'),
                ];
            }

            public function styles(Worksheet $sheet)
            {
                return [
                    1 => [
                        'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    ],
                ];
            }
        }, 'overtimes_' . date('Y-m-d') . '.xlsx');
    }

    /**
     * تصدير جدول سجلات الدفع
     */
    public function payrollPayments()
    {
        return Excel::download(new class implements FromCollection, WithHeadings, WithMapping, WithStyles {
            public function collection()
            {
                return PayrollPayment::with(['payroll.employee', 'currency'])->get();
            }

            public function headings(): array
            {
                return ['كود الدفع', 'الموظف', 'المبلغ', 'العملة', 'طريقة الدفع', 'تاريخ الدفع', 'الحالة', 'تاريخ الإنشاء'];
            }

            public function map($payment): array
            {
                return [
                    $payment->payment_code,
                    $payment->payroll->employee->full_name,
                    $payment->amount,
                    $payment->currency->code ?? '',
                    $payment->payment_method,
                    $payment->payment_date->format('Y-m-d'),
                    $payment->status,
                    $payment->created_at->format('Y-m-d H:i'),
                ];
            }

            public function styles(Worksheet $sheet)
            {
                return [
                    1 => [
                        'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    ],
                ];
            }
        }, 'payroll_payments_' . date('Y-m-d') . '.xlsx');
    }

    /**
     * تصدير جدول الحسابات البنكية
     */
    public function bankAccounts()
    {
        return Excel::download(new class implements FromCollection, WithHeadings, WithMapping, WithStyles {
            public function collection()
            {
                return EmployeeBankAccount::with('employee')->get();
            }

            public function headings(): array
            {
                return ['الموظف', 'اسم البنك', 'رقم الحساب', 'IBAN', 'نوع الحساب', 'حساب أساسي', 'الحالة', 'تاريخ الإنشاء'];
            }

            public function map($account): array
            {
                return [
                    $account->employee->full_name,
                    $account->bank_name_ar ?? $account->bank_name,
                    $account->account_number,
                    $account->iban ?? '',
                    $account->account_type,
                    $account->is_primary ? 'نعم' : 'لا',
                    $account->is_active ? 'نشط' : 'غير نشط',
                    $account->created_at->format('Y-m-d H:i'),
                ];
            }

            public function styles(Worksheet $sheet)
            {
                return [
                    1 => [
                        'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    ],
                ];
            }
        }, 'bank_accounts_' . date('Y-m-d') . '.xlsx');
    }

    /**
     * تصدير جدول إعدادات الضرائب
     */
    public function taxSettings()
    {
        return Excel::download(new class implements FromCollection, WithHeadings, WithMapping, WithStyles {
            public function collection()
            {
                return TaxSetting::all();
            }

            public function headings(): array
            {
                return ['الكود', 'الاسم', 'النوع', 'طريقة الحساب', 'النسبة/القيمة', 'الحالة', 'تاريخ الإنشاء'];
            }

            public function map($tax): array
            {
                return [
                    $tax->code ?? '',
                    $tax->name_ar ?? $tax->name,
                    $tax->type,
                    $tax->calculation_method,
                    $tax->rate,
                    $tax->is_active ? 'نشط' : 'غير نشط',
                    $tax->created_at->format('Y-m-d H:i'),
                ];
            }

            public function styles(Worksheet $sheet)
            {
                return [
                    1 => [
                        'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    ],
                ];
            }
        }, 'tax_settings_' . date('Y-m-d') . '.xlsx');
    }

    /**
     * تصدير جدول مكونات الراتب
     */
    public function salaryComponents()
    {
        return Excel::download(new class implements FromCollection, WithHeadings, WithMapping, WithStyles {
            public function collection()
            {
                return SalaryComponent::all();
            }

            public function headings(): array
            {
                return ['الكود', 'الاسم', 'النوع', 'طريقة الحساب', 'القيمة الافتراضية', 'الحالة', 'تاريخ الإنشاء'];
            }

            public function map($component): array
            {
                return [
                    $component->code,
                    $component->name_ar ?? $component->name,
                    $component->type,
                    $component->calculation_type,
                    $component->default_value,
                    $component->is_active ? 'نشط' : 'غير نشط',
                    $component->created_at->format('Y-m-d H:i'),
                ];
            }

            public function styles(Worksheet $sheet)
            {
                return [
                    1 => [
                        'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    ],
                ];
            }
        }, 'salary_components_' . date('Y-m-d') . '.xlsx');
    }

    /**
     * تصدير جدول توزيعات الأصول
     */
    public function assetAssignments()
    {
        return Excel::download(new class implements FromCollection, WithHeadings, WithMapping, WithStyles {
            public function collection()
            {
                return AssetAssignment::with(['asset', 'employee'])->get();
            }

            public function headings(): array
            {
                return ['الأصل', 'الموظف', 'تاريخ التوزيع', 'تاريخ الإرجاع المتوقع', 'الحالة', 'تاريخ الإنشاء'];
            }

            public function map($assignment): array
            {
                return [
                    $assignment->asset->name,
                    $assignment->employee->full_name,
                    $assignment->assigned_date->format('Y-m-d'),
                    $assignment->expected_return_date?->format('Y-m-d'),
                    $assignment->status,
                    $assignment->created_at->format('Y-m-d H:i'),
                ];
            }

            public function styles(Worksheet $sheet)
            {
                return [
                    1 => [
                        'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    ],
                ];
            }
        }, 'asset_assignments_' . date('Y-m-d') . '.xlsx');
    }

    /**
     * تصدير جدول صيانة الأصول
     */
    public function assetMaintenances()
    {
        return Excel::download(new class implements FromCollection, WithHeadings, WithMapping, WithStyles {
            public function collection()
            {
                return AssetMaintenance::with('asset')->get();
            }

            public function headings(): array
            {
                return ['الأصل', 'نوع الصيانة', 'التاريخ', 'التكلفة', 'المزود', 'الحالة', 'تاريخ الإنشاء'];
            }

            public function map($maintenance): array
            {
                return [
                    $maintenance->asset->name,
                    $maintenance->maintenance_type,
                    $maintenance->maintenance_date->format('Y-m-d'),
                    $maintenance->cost,
                    $maintenance->vendor ?? '',
                    $maintenance->status,
                    $maintenance->created_at->format('Y-m-d H:i'),
                ];
            }

            public function styles(Worksheet $sheet)
            {
                return [
                    1 => [
                        'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    ],
                ];
            }
        }, 'asset_maintenances_' . date('Y-m-d') . '.xlsx');
    }

    /**
     * تصدير جدول تصنيفات المصروفات
     */
    public function expenseCategories()
    {
        return Excel::download(new class implements FromCollection, WithHeadings, WithMapping, WithStyles {
            public function collection()
            {
                return ExpenseCategory::all();
            }

            public function headings(): array
            {
                return ['الاسم', 'الاسم بالعربية', 'الوصف', 'الحالة', 'تاريخ الإنشاء'];
            }

            public function map($category): array
            {
                return [
                    $category->name,
                    $category->name_ar ?? '',
                    $category->description ?? '',
                    $category->is_active ? 'نشط' : 'غير نشط',
                    $category->created_at->format('Y-m-d H:i'),
                ];
            }

            public function styles(Worksheet $sheet)
            {
                return [
                    1 => [
                        'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    ],
                ];
            }
        }, 'expense_categories_' . date('Y-m-d') . '.xlsx');
    }

    /**
     * تصدير جدول أنواع المخالفات
     */
    public function violationTypes()
    {
        return Excel::download(new class implements FromCollection, WithHeadings, WithMapping, WithStyles {
            public function collection()
            {
                return ViolationType::all();
            }

            public function headings(): array
            {
                return ['الاسم', 'الاسم بالعربية', 'الوصف', 'الحالة', 'تاريخ الإنشاء'];
            }

            public function map($type): array
            {
                return [
                    $type->name,
                    $type->name_ar ?? '',
                    $type->description ?? '',
                    $type->is_active ? 'نشط' : 'غير نشط',
                    $type->created_at->format('Y-m-d H:i'),
                ];
            }

            public function styles(Worksheet $sheet)
            {
                return [
                    1 => [
                        'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    ],
                ];
            }
        }, 'violation_types_' . date('Y-m-d') . '.xlsx');
    }

    /**
     * تصدير جدول الإجراءات التأديبية
     */
    public function disciplinaryActions()
    {
        return Excel::download(new class implements FromCollection, WithHeadings, WithMapping, WithStyles {
            public function collection()
            {
                return DisciplinaryAction::all();
            }

            public function headings(): array
            {
                return ['الاسم', 'الاسم بالعربية', 'النوع', 'الحالة', 'تاريخ الإنشاء'];
            }

            public function map($action): array
            {
                return [
                    $action->name,
                    $action->name_ar ?? '',
                    $action->type,
                    $action->is_active ? 'نشط' : 'غير نشط',
                    $action->created_at->format('Y-m-d H:i'),
                ];
            }

            public function styles(Worksheet $sheet)
            {
                return [
                    1 => [
                        'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    ],
                ];
            }
        }, 'disciplinary_actions_' . date('Y-m-d') . '.xlsx');
    }

    /**
     * تصدير جدول مخالفات الموظفين
     */
    public function employeeViolations()
    {
        return Excel::download(new class implements FromCollection, WithHeadings, WithMapping, WithStyles {
            public function collection()
            {
                return EmployeeViolation::with(['employee', 'violationType', 'disciplinaryAction'])->get();
            }

            public function headings(): array
            {
                return ['الموظف', 'نوع المخالفة', 'الإجراء التأديبي', 'التاريخ', 'الحالة', 'تاريخ الإنشاء'];
            }

            public function map($violation): array
            {
                return [
                    $violation->employee->full_name,
                    $violation->violationType->name_ar ?? $violation->violationType->name,
                    $violation->disciplinaryAction->name_ar ?? $violation->disciplinaryAction->name,
                    $violation->violation_date->format('Y-m-d'),
                    $violation->status,
                    $violation->created_at->format('Y-m-d H:i'),
                ];
            }

            public function styles(Worksheet $sheet)
            {
                return [
                    1 => [
                        'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    ],
                ];
            }
        }, 'employee_violations_' . date('Y-m-d') . '.xlsx');
    }

    /**
     * تصدير جدول المشاريع
     */
    public function projects()
    {
        return Excel::download(new class implements FromCollection, WithHeadings, WithMapping, WithStyles {
            public function collection()
            {
                return Project::with(['manager', 'department'])->get();
            }

            public function headings(): array
            {
                return ['الاسم', 'المدير', 'القسم', 'تاريخ البدء', 'تاريخ الانتهاء', 'الحالة', 'تاريخ الإنشاء'];
            }

            public function map($project): array
            {
                return [
                    $project->name_ar ?? $project->name,
                    $project->manager->full_name ?? '',
                    $project->department->name_ar ?? $project->department->name ?? '',
                    $project->start_date?->format('Y-m-d'),
                    $project->end_date?->format('Y-m-d'),
                    $project->status,
                    $project->created_at->format('Y-m-d H:i'),
                ];
            }

            public function styles(Worksheet $sheet)
            {
                return [
                    1 => [
                        'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    ],
                ];
            }
        }, 'projects_' . date('Y-m-d') . '.xlsx');
    }

    /**
     * تصدير جدول المهام
     */
    public function tasks()
    {
        return Excel::download(new class implements FromCollection, WithHeadings, WithMapping, WithStyles {
            public function collection()
            {
                return Task::with(['project', 'assignedTo'])->get();
            }

            public function headings(): array
            {
                return ['العنوان', 'المشروع', 'المكلف', 'تاريخ الاستحقاق', 'الأولوية', 'الحالة', 'تاريخ الإنشاء'];
            }

            public function map($task): array
            {
                return [
                    $task->title_ar ?? $task->title,
                    $task->project->name_ar ?? $task->project->name ?? '',
                    $task->assignedTo->full_name ?? '',
                    $task->due_date?->format('Y-m-d'),
                    $task->priority,
                    $task->status,
                    $task->created_at->format('Y-m-d H:i'),
                ];
            }

            public function styles(Worksheet $sheet)
            {
                return [
                    1 => [
                        'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    ],
                ];
            }
        }, 'tasks_' . date('Y-m-d') . '.xlsx');
    }

    /**
     * تصدير جدول التذاكر
     */
    public function tickets()
    {
        return Excel::download(new class implements FromCollection, WithHeadings, WithMapping, WithStyles {
            public function collection()
            {
                return Ticket::with(['employee', 'assignedTo'])->get();
            }

            public function headings(): array
            {
                return ['الموظف', 'العنوان', 'النوع', 'الأولوية', 'المكلف', 'الحالة', 'تاريخ الإنشاء'];
            }

            public function map($ticket): array
            {
                return [
                    $ticket->employee->full_name,
                    $ticket->title_ar ?? $ticket->title,
                    $ticket->type,
                    $ticket->priority,
                    $ticket->assignedTo->name ?? '',
                    $ticket->status,
                    $ticket->created_at->format('Y-m-d H:i'),
                ];
            }

            public function styles(Worksheet $sheet)
            {
                return [
                    1 => [
                        'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    ],
                ];
            }
        }, 'tickets_' . date('Y-m-d') . '.xlsx');
    }

    /**
     * تصدير جدول الاجتماعات
     */
    public function meetings()
    {
        return Excel::download(new class implements FromCollection, WithHeadings, WithMapping, WithStyles {
            public function collection()
            {
                return Meeting::with(['organizer', 'department'])->get();
            }

            public function headings(): array
            {
                return ['العنوان', 'المنظم', 'القسم', 'التاريخ', 'الوقت', 'المكان', 'الحالة', 'تاريخ الإنشاء'];
            }

            public function map($meeting): array
            {
                return [
                    $meeting->title_ar ?? $meeting->title,
                    $meeting->organizer->name ?? '',
                    $meeting->department->name_ar ?? $meeting->department->name ?? '',
                    $meeting->meeting_date->format('Y-m-d'),
                    $meeting->meeting_time,
                    $meeting->location ?? '',
                    $meeting->status,
                    $meeting->created_at->format('Y-m-d H:i'),
                ];
            }

            public function styles(Worksheet $sheet)
            {
                return [
                    1 => [
                        'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    ],
                ];
            }
        }, 'meetings_' . date('Y-m-d') . '.xlsx');
    }

    /**
     * تصدير جدول أحداث التقويم
     */
    public function calendarEvents()
    {
        return Excel::download(new class implements FromCollection, WithHeadings, WithMapping, WithStyles {
            public function collection()
            {
                return CalendarEvent::with(['creator', 'employee', 'department'])->get();
            }

            public function headings(): array
            {
                return ['العنوان', 'النوع', 'الموظف', 'القسم', 'تاريخ البدء', 'تاريخ الانتهاء', 'الحالة', 'تاريخ الإنشاء'];
            }

            public function map($event): array
            {
                return [
                    $event->title_ar ?? $event->title,
                    $event->type_name_ar,
                    $event->employee->full_name ?? '',
                    $event->department->name_ar ?? $event->department->name ?? '',
                    $event->start_date->format('Y-m-d H:i'),
                    $event->end_date?->format('Y-m-d H:i'),
                    $event->is_active ? 'نشط' : 'غير نشط',
                    $event->created_at->format('Y-m-d H:i'),
                ];
            }

            public function styles(Worksheet $sheet)
            {
                return [
                    1 => [
                        'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    ],
                ];
            }
        }, 'calendar_events_' . date('Y-m-d') . '.xlsx');
    }
}

