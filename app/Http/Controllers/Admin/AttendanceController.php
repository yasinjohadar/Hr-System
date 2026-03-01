<?php

namespace App\Http\Controllers\Admin;

use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:attendance-list')->only('index');
        $this->middleware('permission:attendance-create')->only(['create', 'store']);
        $this->middleware('permission:attendance-edit')->only(['edit', 'update']);
        $this->middleware('permission:attendance-delete')->only('destroy');
        $this->middleware('permission:attendance-show')->only('show');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $attendancesQuery = Attendance::with(['employee.user']);

        // فلترة حسب الموظف
        if ($request->filled('employee_id')) {
            $attendancesQuery->where('employee_id', $request->input('employee_id'));
        }

        // فلترة حسب التاريخ
        if ($request->filled('start_date')) {
            $attendancesQuery->where('attendance_date', '>=', $request->input('start_date'));
        }
        if ($request->filled('end_date')) {
            $attendancesQuery->where('attendance_date', '<=', $request->input('end_date'));
        }

        // فلترة حسب الحالة
        if ($request->filled('status')) {
            $attendancesQuery->where('status', $request->input('status'));
        }

        // إذا لم يتم تحديد تاريخ، عرض آخر 30 يوم
        if (!$request->filled('start_date') && !$request->filled('end_date')) {
            $attendancesQuery->where('attendance_date', '>=', Carbon::now()->subDays(30));
        }

        $attendances = $attendancesQuery->orderBy('attendance_date', 'desc')
            ->orderBy('check_in', 'desc')
            ->paginate(50);

        $employees = Employee::where('is_active', true)->with('user')->get();
        $currentStartDate = $request->input('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
        $currentEndDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));

        return view("admin.pages.attendances.index", compact("attendances", "employees", "currentStartDate", "currentEndDate"));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $employees = Employee::where('is_active', true)->with('user')->get();
        $today = Carbon::now()->format('Y-m-d');
        
        return view("admin.pages.attendances.create", compact("employees", "today"));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'attendance_date' => 'required|date',
            'check_in' => 'nullable|date_format:H:i',
            'check_out' => 'nullable|date_format:H:i|after:check_in',
            'expected_check_in' => 'nullable|date_format:H:i',
            'expected_check_out' => 'nullable|date_format:H:i',
            'status' => 'required|in:present,absent,late,half_day,on_leave,holiday',
            'notes' => 'nullable|string',
        ], [
            'employee_id.required' => 'الموظف مطلوب',
            'attendance_date.required' => 'تاريخ الحضور مطلوب',
            'status.required' => 'حالة الحضور مطلوبة',
            'check_out.after' => 'وقت الخروج يجب أن يكون بعد وقت الدخول',
        ]);

        // التحقق من عدم وجود سجل حضور لنفس الموظف في نفس اليوم
        $existingAttendance = Attendance::where('employee_id', $request->employee_id)
            ->where('attendance_date', $request->attendance_date)
            ->first();

        if ($existingAttendance) {
            return back()->withInput()->withErrors(['error' => 'يوجد سجل حضور بالفعل لهذا الموظف في نفس اليوم']);
        }

        $attendance = Attendance::create([
            'employee_id' => $request->employee_id,
            'attendance_date' => $request->attendance_date,
            'check_in' => $request->check_in,
            'check_out' => $request->check_out,
            'expected_check_in' => $request->expected_check_in ?? '09:00',
            'expected_check_out' => $request->expected_check_out ?? '17:00',
            'status' => $request->status,
            'notes' => $request->notes,
            'created_by' => auth()->id(),
        ]);

        // حساب ساعات العمل تلقائياً
        $attendance->calculateHours();
        $attendance->save();

        return redirect()->route("admin.attendances.index")->with("success", "تم إضافة سجل الحضور بنجاح");
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $attendance = Attendance::with(['employee.user', 'creator'])->findOrFail($id);
        return view("admin.pages.attendances.show", compact("attendance"));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $attendance = Attendance::findOrFail($id);
        $employees = Employee::where('is_active', true)->with('user')->get();
        
        return view("admin.pages.attendances.edit", compact("attendance", "employees"));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $attendance = Attendance::findOrFail($id);

        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'attendance_date' => 'required|date',
            'check_in' => 'nullable|date_format:H:i',
            'check_out' => 'nullable|date_format:H:i',
            'expected_check_in' => 'nullable|date_format:H:i',
            'expected_check_out' => 'nullable|date_format:H:i',
            'status' => 'required|in:present,absent,late,half_day,on_leave,holiday',
            'notes' => 'nullable|string',
        ], [
            'employee_id.required' => 'الموظف مطلوب',
            'attendance_date.required' => 'تاريخ الحضور مطلوب',
            'status.required' => 'حالة الحضور مطلوبة',
        ]);

        // التحقق من عدم وجود سجل حضور آخر لنفس الموظف في نفس اليوم (عدا السجل الحالي)
        $existingAttendance = Attendance::where('employee_id', $request->employee_id)
            ->where('attendance_date', $request->attendance_date)
            ->where('id', '!=', $id)
            ->first();

        if ($existingAttendance) {
            return back()->withInput()->withErrors(['error' => 'يوجد سجل حضور بالفعل لهذا الموظف في نفس اليوم']);
        }

        $attendance->update([
            'employee_id' => $request->employee_id,
            'attendance_date' => $request->attendance_date,
            'check_in' => $request->check_in,
            'check_out' => $request->check_out,
            'expected_check_in' => $request->expected_check_in ?? '09:00',
            'expected_check_out' => $request->expected_check_out ?? '17:00',
            'status' => $request->status,
            'notes' => $request->notes,
        ]);

        // إعادة حساب ساعات العمل
        $attendance->calculateHours();
        $attendance->save();

        return redirect()->route('admin.attendances.index')->with('success', 'تم تحديث سجل الحضور بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $attendance = Attendance::findOrFail($request->id);
        $attendance->delete();

        return redirect()->route("admin.attendances.index")->with("success", "تم حذف سجل الحضور بنجاح");
    }

    /**
     * تسجيل الدخول
     */
    public function checkIn(Request $request, $employeeId)
    {
        $employee = Employee::findOrFail($employeeId);
        $today = Carbon::now()->format('Y-m-d');
        $now = Carbon::now()->format('H:i');

        // التحقق من عدم وجود تسجيل دخول اليوم
        $existingAttendance = Attendance::where('employee_id', $employeeId)
            ->where('attendance_date', $today)
            ->first();

        if ($existingAttendance && $existingAttendance->check_in) {
            return back()->with('error', 'تم تسجيل الدخول بالفعل اليوم');
        }

        if ($existingAttendance) {
            $existingAttendance->update([
                'check_in' => $now,
                'status' => 'present',
            ]);
            $existingAttendance->calculateHours();
            $existingAttendance->save();
        } else {
            $attendance = Attendance::create([
                'employee_id' => $employeeId,
                'attendance_date' => $today,
                'check_in' => $now,
                'expected_check_in' => '09:00',
                'expected_check_out' => '17:00',
                'status' => 'present',
                'created_by' => auth()->id(),
            ]);
            $attendance->calculateHours();
            $attendance->save();
        }

        return back()->with('success', 'تم تسجيل الدخول بنجاح');
    }

    /**
     * تسجيل الخروج
     */
    public function checkOut(Request $request, $employeeId)
    {
        $employee = Employee::findOrFail($employeeId);
        $today = Carbon::now()->format('Y-m-d');
        $now = Carbon::now()->format('H:i');

        $attendance = Attendance::where('employee_id', $employeeId)
            ->where('attendance_date', $today)
            ->first();

        if (!$attendance) {
            return back()->with('error', 'لم يتم تسجيل الدخول اليوم');
        }

        if ($attendance->check_out) {
            return back()->with('error', 'تم تسجيل الخروج بالفعل اليوم');
        }

        $attendance->update([
            'check_out' => $now,
        ]);
        $attendance->calculateHours();
        $attendance->save();

        return back()->with('success', 'تم تسجيل الخروج بنجاح');
    }
}
