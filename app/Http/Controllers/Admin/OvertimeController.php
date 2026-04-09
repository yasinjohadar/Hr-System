<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OvertimeRecord;
use App\Models\Employee;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Carbon\Carbon;

class OvertimeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:overtime-list')->only('index');
        $this->middleware('permission:overtime-create')->only(['create', 'store']);
        $this->middleware('permission:overtime-edit')->only(['edit', 'update', 'approve', 'reject', 'calculateFromAttendance']);
        $this->middleware('permission:overtime-delete')->only('destroy');
        $this->middleware('permission:overtime-show')->only('show');
    }

    public function index(Request $request)
    {
        $query = OvertimeRecord::with(['employee', 'attendance', 'approvedBy']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->whereHas('employee', function($q) use ($search) {
                    $q->where('full_name', 'like', "%$search%");
                });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->input('employee_id'));
        }

        if ($request->filled('date_from')) {
            $query->where('overtime_date', '>=', $request->input('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->where('overtime_date', '<=', $request->input('date_to'));
        }

        $overtimes = $query->latest('overtime_date')->paginate(20);
        $employees = Employee::where('is_active', true)->get();

        return view('admin.pages.overtimes.index', compact('overtimes', 'employees'));
    }

    public function create()
    {
        $employees = Employee::where('is_active', true)->get();
        $attendances = Attendance::whereNotNull('check_in')
            ->whereNotNull('check_out')
            ->whereNull('overtime_minutes')
            ->with('employee')
            ->latest()
            ->limit(50)
            ->get();

        return view('admin.pages.overtimes.create', compact('employees', 'attendances'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'overtime_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'overtime_type' => 'required|in:regular,holiday,night,weekend',
            'rate_multiplier' => 'nullable|numeric|min:1',
            'reason' => 'nullable|string',
        ]);

        $start = Carbon::parse($request->overtime_date . ' ' . $request->start_time);
        $end = Carbon::parse($request->overtime_date . ' ' . $request->end_time);
        
        if ($end->lt($start)) {
            $end->addDay(); // إذا كان الوقت عبر منتصف الليل
        }

        $overtimeMinutes = $start->diffInMinutes($end);
        $overtimeHours = round($overtimeMinutes / 60, 2);

        $employee = Employee::findOrFail($request->employee_id);
        $hourlyRate = ($employee->salary ?? 0) / (30 * 8); // معدل الساعة (افتراض 8 ساعات يومياً)
        $rateMultiplier = $request->rate_multiplier ?? 1.5;
        $overtimeAmount = $hourlyRate * $overtimeHours * $rateMultiplier;

        $overtime = new OvertimeRecord();
        $overtime->employee_id = $request->employee_id;
        $overtime->attendance_id = $request->attendance_id;
        $overtime->overtime_date = $request->overtime_date;
        $overtime->start_time = $request->start_time;
        $overtime->end_time = $request->end_time;
        $overtime->overtime_minutes = $overtimeMinutes;
        $overtime->overtime_hours = $overtimeHours;
        $overtime->overtime_type = $request->overtime_type;
        $overtime->rate_multiplier = $rateMultiplier;
        $overtime->hourly_rate = $hourlyRate;
        $overtime->overtime_amount = $overtimeAmount;
        $overtime->reason = $request->reason;
        $overtime->status = 'pending';
        $overtime->created_by = auth()->id();
        $overtime->save();

        return redirect()->route('admin.overtimes.index')
            ->with('success', 'تم إضافة سجل الساعات الإضافية بنجاح.');
    }

    public function show(string $id)
    {
        $overtime = OvertimeRecord::with(['employee', 'attendance', 'approvedBy', 'payroll', 'creator'])->findOrFail($id);
        return view('admin.pages.overtimes.show', compact('overtime'));
    }

    public function edit(string $id)
    {
        $overtime = OvertimeRecord::findOrFail($id);
        
        if ($overtime->status === 'paid') {
            return redirect()->back()->with('error', 'لا يمكن تعديل سجل مدفوع.');
        }

        $employees = Employee::where('is_active', true)->get();
        return view('admin.pages.overtimes.edit', compact('overtime', 'employees'));
    }

    public function update(Request $request, string $id)
    {
        $overtime = OvertimeRecord::findOrFail($id);

        if ($overtime->status === 'paid') {
            return redirect()->back()->with('error', 'لا يمكن تعديل سجل مدفوع.');
        }

        $request->validate([
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'overtime_type' => 'required|in:regular,holiday,night,weekend',
            'rate_multiplier' => 'nullable|numeric|min:1',
            'reason' => 'nullable|string',
        ]);

        $start = Carbon::parse($overtime->overtime_date->format('Y-m-d') . ' ' . $request->start_time);
        $end = Carbon::parse($overtime->overtime_date->format('Y-m-d') . ' ' . $request->end_time);
        
        if ($end->lt($start)) {
            $end->addDay();
        }

        $overtimeMinutes = $start->diffInMinutes($end);
        $overtimeHours = round($overtimeMinutes / 60, 2);

        $rateMultiplier = $request->rate_multiplier ?? $overtime->rate_multiplier;
        $overtimeAmount = $overtime->hourly_rate * $overtimeHours * $rateMultiplier;

        $overtime->start_time = $request->start_time;
        $overtime->end_time = $request->end_time;
        $overtime->overtime_minutes = $overtimeMinutes;
        $overtime->overtime_hours = $overtimeHours;
        $overtime->overtime_type = $request->overtime_type;
        $overtime->rate_multiplier = $rateMultiplier;
        $overtime->overtime_amount = $overtimeAmount;
        $overtime->reason = $request->reason;
        $overtime->save();

        return redirect()->route('admin.overtimes.show', $id)
            ->with('success', 'تم تحديث سجل الساعات الإضافية بنجاح.');
    }

    public function destroy(string $id)
    {
        $overtime = OvertimeRecord::findOrFail($id);

        if ($overtime->status === 'paid') {
            return redirect()->back()->with('error', 'لا يمكن حذف سجل مدفوع.');
        }

        $overtime->delete();

        return redirect()->route('admin.overtimes.index')
            ->with('success', 'تم حذف سجل الساعات الإضافية بنجاح.');
    }

    /**
     * الموافقة على الساعات الإضافية
     */
    public function approve(Request $request, string $id)
    {
        $overtime = OvertimeRecord::findOrFail($id);

        if ($overtime->status !== 'pending') {
            return redirect()->back()->with('error', 'يمكن الموافقة على السجلات المعلقة فقط.');
        }

        $overtime->status = 'approved';
        $overtime->approved_by = auth()->id();
        $overtime->approved_at = now();
        $overtime->approval_notes = $request->approval_notes;
        $overtime->save();

        return redirect()->back()->with('success', 'تم الموافقة على الساعات الإضافية بنجاح.');
    }

    /**
     * رفض الساعات الإضافية
     */
    public function reject(Request $request, string $id)
    {
        $overtime = OvertimeRecord::findOrFail($id);

        if ($overtime->status !== 'pending') {
            return redirect()->back()->with('error', 'يمكن رفض السجلات المعلقة فقط.');
        }

        $overtime->status = 'rejected';
        $overtime->approved_by = auth()->id();
        $overtime->approved_at = now();
        $overtime->approval_notes = $request->approval_notes;
        $overtime->save();

        return redirect()->back()->with('success', 'تم رفض الساعات الإضافية.');
    }

    /**
     * حساب الساعات الإضافية تلقائياً من الحضور
     */
    public function calculateFromAttendance(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
        ]);

        $employee = Employee::findOrFail($request->employee_id);
        $shiftAssignment = $employee->shiftAssignments()
            ->where('is_active', true)
            ->where(function($q) use ($request) {
                $q->whereNull('end_date')
                  ->orWhere('end_date', '>=', $request->date_from);
            })
            ->where('start_date', '<=', $request->date_to)
            ->first();

        if (!$shiftAssignment || !$shiftAssignment->shift) {
            return redirect()->back()
                ->with('error', 'لا يوجد مناوبة معينة لهذا الموظف في هذه الفترة.');
        }

        $shift = $shiftAssignment->shift;
        $expectedHours = $shift->duration_hours * 60; // بالدقائق

        $attendances = Attendance::where('employee_id', $request->employee_id)
            ->whereBetween('attendance_date', [$request->date_from, $request->date_to])
            ->where('status', 'present')
            ->whereNotNull('check_in')
            ->whereNotNull('check_out')
            ->get();

        $created = 0;

        foreach ($attendances as $attendance) {
            // التحقق من عدم وجود سجل ساعات إضافية مسبق
            $existing = OvertimeRecord::where('attendance_id', $attendance->id)->first();
            if ($existing) {
                continue;
            }

            if ($attendance->hours_worked > $expectedHours) {
                $overtimeMinutes = $attendance->hours_worked - $expectedHours;
                
                // التحقق من الحد الأدنى
                if ($overtimeMinutes >= $shift->overtime_threshold_minutes) {
                    $overtimeHours = round($overtimeMinutes / 60, 2);
                    $hourlyRate = ($employee->salary ?? 0) / (30 * 8);
                    $overtimeAmount = $hourlyRate * $overtimeHours * $shift->overtime_rate;

                    OvertimeRecord::create([
                        'employee_id' => $employee->id,
                        'attendance_id' => $attendance->id,
                        'overtime_date' => $attendance->attendance_date,
                        'start_time' => $shift->end_time,
                        'end_time' => $attendance->check_out,
                        'overtime_minutes' => $overtimeMinutes,
                        'overtime_hours' => $overtimeHours,
                        'overtime_type' => 'regular',
                        'rate_multiplier' => $shift->overtime_rate,
                        'hourly_rate' => $hourlyRate,
                        'overtime_amount' => $overtimeAmount,
                        'status' => 'pending',
                        'created_by' => auth()->id(),
                    ]);

                    $created++;
                }
            }
        }

        return redirect()->route('admin.overtimes.index')
            ->with('success', "تم إنشاء $created سجل ساعات إضافية تلقائياً.");
    }
}
