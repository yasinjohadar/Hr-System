<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CalendarEvent;
use App\Models\Employee;
use App\Models\Department;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CalendarEventController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:calendar-list')->only('index', 'show', 'getEvents');
        $this->middleware('permission:calendar-create')->only('create', 'store');
        $this->middleware('permission:calendar-edit')->only('edit', 'update');
        $this->middleware('permission:calendar-delete')->only('destroy');
    }

    /**
     * عرض صفحة التقويم
     */
    public function index()
    {
        $employees = Employee::where('is_active', true)->get();
        $departments = Department::where('is_active', true)->get();
        return view('admin.pages.calendar.index', compact('employees', 'departments'));
    }

    /**
     * الحصول على الأحداث (API للتقويم)
     */
    public function getEvents(Request $request)
    {
        $user = auth()->user();
        $start = $request->input('start');
        $end = $request->input('end');

        $query = CalendarEvent::where('is_active', true);

        // فلترة حسب التاريخ
        if ($start && $end) {
            $query->where(function($q) use ($start, $end) {
                $q->whereBetween('start_date', [$start, $end])
                  ->orWhereBetween('end_date', [$start, $end])
                  ->orWhere(function($q2) use ($start, $end) {
                      $q2->where('start_date', '<=', $start)
                         ->where('end_date', '>=', $end);
                  });
            });
        }

        $events = $query->get();

        // فلترة الأحداث حسب الصلاحيات
        $filteredEvents = $events->filter(function($event) use ($user) {
            return $event->canViewBy($user);
        });

        // تحويل الأحداث إلى تنسيق FullCalendar
        $formattedEvents = $filteredEvents->map(function($event) {
            return [
                'id' => $event->id,
                'title' => $event->title_ar ?? $event->title,
                'start' => $event->start_date->toIso8601String(),
                'end' => $event->end_date ? $event->end_date->toIso8601String() : null,
                'allDay' => $event->is_all_day,
                'color' => $event->color,
                'type' => $event->type,
                'description' => $event->description,
                'url' => route('admin.calendar-events.show', $event->id),
            ];
        });

        return response()->json($formattedEvents);
    }

    /**
     * عرض نموذج إنشاء حدث
     */
    public function create()
    {
        $employees = Employee::where('is_active', true)->get();
        $departments = Department::where('is_active', true)->get();
        return view('admin.pages.calendar.create', compact('employees', 'departments'));
    }

    /**
     * حفظ حدث جديد
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'title_ar' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'type' => 'required|in:personal,public,department,employee,all',
            'employee_id' => 'required_if:type,employee|exists:employees,id',
            'department_id' => 'required_if:type,department|exists:departments,id',
            'color' => 'nullable|string|max:7',
            'is_all_day' => 'boolean',
            'is_reminder' => 'boolean',
            'reminder_minutes' => 'nullable|integer|min:1',
            'is_recurring' => 'boolean',
            'recurrence_type' => 'required_if:is_recurring,1|in:daily,weekly,monthly,yearly',
            'recurrence_interval' => 'nullable|integer|min:1',
            'recurrence_end_date' => 'nullable|date',
        ], [
            'title.required' => 'عنوان الحدث مطلوب',
            'start_date.required' => 'تاريخ البدء مطلوب',
            'type.required' => 'نوع الحدث مطلوب',
            'employee_id.required_if' => 'يجب اختيار موظف للحدث الشخصي',
            'department_id.required_if' => 'يجب اختيار قسم للحدث',
        ]);

        $data = $request->all();
        $data['created_by'] = auth()->id();
        $data['is_all_day'] = $request->has('is_all_day');
        $data['is_reminder'] = $request->has('is_reminder');
        $data['is_recurring'] = $request->has('is_recurring');

        // إذا كان الحدث شخصي، يجب أن يكون employee_id هو الموظف الحالي
        if ($data['type'] === 'personal') {
            $user = auth()->user();
            $employee = $user->employee;
            if (!$employee) {
                return back()->withInput()->withErrors(['error' => 'يجب أن يكون لديك ملف موظف لإنشاء حدث شخصي']);
            }
            $data['employee_id'] = $employee->id;
        }

        // إذا كان الحدث للجميع أو عام، لا حاجة لـ employee_id أو department_id
        if (in_array($data['type'], ['all', 'public'])) {
            $data['employee_id'] = null;
            $data['department_id'] = null;
        }

        CalendarEvent::create($data);

        return redirect()->route('admin.calendar-events.index')
            ->with('success', 'تم إنشاء الحدث بنجاح.');
    }

    /**
     * عرض تفاصيل حدث
     */
    public function show(string $id)
    {
        $event = CalendarEvent::with(['creator', 'employee', 'department'])->findOrFail($id);
        
        // التحقق من الصلاحيات
        if (!$event->canViewBy(auth()->user())) {
            abort(403, 'ليس لديك صلاحية لعرض هذا الحدث');
        }

        return view('admin.pages.calendar.show', compact('event'));
    }

    /**
     * عرض نموذج تعديل حدث
     */
    public function edit(string $id)
    {
        $event = CalendarEvent::findOrFail($id);
        
        // التحقق من الصلاحيات (فقط منشئ الحدث يمكنه التعديل)
        if ($event->created_by !== auth()->id() && !auth()->user()->hasPermissionTo('calendar-edit-all')) {
            abort(403, 'ليس لديك صلاحية لتعديل هذا الحدث');
        }

        $employees = Employee::where('is_active', true)->get();
        $departments = Department::where('is_active', true)->get();
        
        return view('admin.pages.calendar.edit', compact('event', 'employees', 'departments'));
    }

    /**
     * تحديث حدث
     */
    public function update(Request $request, string $id)
    {
        $event = CalendarEvent::findOrFail($id);
        
        // التحقق من الصلاحيات
        if ($event->created_by !== auth()->id() && !auth()->user()->hasPermissionTo('calendar-edit-all')) {
            abort(403, 'ليس لديك صلاحية لتعديل هذا الحدث');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'title_ar' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'type' => 'required|in:personal,public,department,employee,all',
            'employee_id' => 'required_if:type,employee|exists:employees,id',
            'department_id' => 'required_if:type,department|exists:departments,id',
            'color' => 'nullable|string|max:7',
            'is_all_day' => 'boolean',
            'is_reminder' => 'boolean',
            'reminder_minutes' => 'nullable|integer|min:1',
            'is_recurring' => 'boolean',
            'recurrence_type' => 'required_if:is_recurring,1|in:daily,weekly,monthly,yearly',
            'recurrence_interval' => 'nullable|integer|min:1',
            'recurrence_end_date' => 'nullable|date',
        ]);

        $data = $request->all();
        $data['is_all_day'] = $request->has('is_all_day');
        $data['is_reminder'] = $request->has('is_reminder');
        $data['is_recurring'] = $request->has('is_recurring');

        // نفس منطق store
        if ($data['type'] === 'personal') {
            $user = auth()->user();
            $employee = $user->employee;
            if (!$employee) {
                return back()->withInput()->withErrors(['error' => 'يجب أن يكون لديك ملف موظف']);
            }
            $data['employee_id'] = $employee->id;
        }

        if (in_array($data['type'], ['all', 'public'])) {
            $data['employee_id'] = null;
            $data['department_id'] = null;
        }

        $event->update($data);

        return redirect()->route('admin.calendar-events.index')
            ->with('success', 'تم تحديث الحدث بنجاح.');
    }

    /**
     * حذف حدث
     */
    public function destroy(string $id)
    {
        $event = CalendarEvent::findOrFail($id);
        
        // التحقق من الصلاحيات
        if ($event->created_by !== auth()->id() && !auth()->user()->hasPermissionTo('calendar-delete-all')) {
            abort(403, 'ليس لديك صلاحية لحذف هذا الحدث');
        }

        $event->delete();

        return redirect()->route('admin.calendar-events.index')
            ->with('success', 'تم حذف الحدث بنجاح.');
    }
}
