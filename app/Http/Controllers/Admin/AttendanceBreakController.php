<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttendanceBreak;
use App\Models\Attendance;
use Illuminate\Http\Request;

class AttendanceBreakController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:attendance-break-list')->only('index', 'show');
        $this->middleware('permission:attendance-break-create')->only('create', 'store');
        $this->middleware('permission:attendance-break-edit')->only('edit', 'update');
        $this->middleware('permission:attendance-break-delete')->only('destroy');
    }

    public function index(Request $request)
    {
        $query = AttendanceBreak::with(['attendance.employee']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('break_type', 'like', "%$search%")
                  ->orWhere('notes', 'like', "%$search%")
                  ->orWhereHas('attendance.employee', function($q) use ($search) {
                      $q->where('full_name', 'like', "%$search%");
                  });
            });
        }

        if ($request->filled('attendance_id')) {
            $query->where('attendance_id', $request->input('attendance_id'));
        }

        if ($request->filled('break_type')) {
            $query->where('break_type', $request->input('break_type'));
        }

        $breaks = $query->latest()->paginate(20);
        $attendances = Attendance::with('employee')->latest()->get();

        return view('admin.pages.attendance-breaks.index', compact('breaks', 'attendances'));
    }

    public function create(Request $request)
    {
        $attendanceId = $request->input('attendance_id');
        $attendance = $attendanceId ? Attendance::with('employee')->findOrFail($attendanceId) : null;
        
        $attendances = Attendance::with('employee')->latest()->get();

        return view('admin.pages.attendance-breaks.create', compact('attendance', 'attendances'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'attendance_id' => 'required|exists:attendances,id',
            'break_type' => 'required|in:lunch,coffee,prayer,other',
            'break_start' => 'required|date_format:H:i',
            'break_end' => 'nullable|date_format:H:i|after:break_start',
            'notes' => 'nullable|string',
        ], [
            'attendance_id.required' => 'سجل الحضور مطلوب',
            'break_type.required' => 'نوع الاستراحة مطلوب',
            'break_start.required' => 'وقت بدء الاستراحة مطلوب',
            'break_end.after' => 'وقت انتهاء الاستراحة يجب أن يكون بعد وقت البدء',
        ]);

        $data = $request->all();
        
        // حساب المدة تلقائياً
        if ($request->filled('break_start') && $request->filled('break_end')) {
            $start = \Carbon\Carbon::parse($request->break_start);
            $end = \Carbon\Carbon::parse($request->break_end);
            $data['duration_minutes'] = $start->diffInMinutes($end);
        } else {
            $data['duration_minutes'] = 0;
        }

        AttendanceBreak::create($data);

        return redirect()->route('admin.attendance-breaks.index')
            ->with('success', 'تم إضافة الاستراحة بنجاح.');
    }

    public function show(string $id)
    {
        $break = AttendanceBreak::with(['attendance.employee'])->findOrFail($id);
        return view('admin.pages.attendance-breaks.show', compact('break'));
    }

    public function edit(string $id)
    {
        $break = AttendanceBreak::with('attendance.employee')->findOrFail($id);
        $attendances = Attendance::with('employee')->latest()->get();

        return view('admin.pages.attendance-breaks.edit', compact('break', 'attendances'));
    }

    public function update(Request $request, string $id)
    {
        $break = AttendanceBreak::findOrFail($id);

        $request->validate([
            'attendance_id' => 'required|exists:attendances,id',
            'break_type' => 'required|in:lunch,coffee,prayer,other',
            'break_start' => 'required|date_format:H:i',
            'break_end' => 'nullable|date_format:H:i|after:break_start',
            'notes' => 'nullable|string',
        ]);

        $data = $request->all();
        
        // حساب المدة تلقائياً
        if ($request->filled('break_start') && $request->filled('break_end')) {
            $start = \Carbon\Carbon::parse($request->break_start);
            $end = \Carbon\Carbon::parse($request->break_end);
            $data['duration_minutes'] = $start->diffInMinutes($end);
        } else {
            $data['duration_minutes'] = 0;
        }

        $break->update($data);

        return redirect()->route('admin.attendance-breaks.index')
            ->with('success', 'تم تحديث الاستراحة بنجاح.');
    }

    public function destroy(string $id)
    {
        $break = AttendanceBreak::findOrFail($id);
        $break->delete();

        return redirect()->route('admin.attendance-breaks.index')
            ->with('success', 'تم حذف الاستراحة بنجاح.');
    }
}

