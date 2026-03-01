<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Meeting;
use App\Models\Employee;
use Illuminate\Http\Request;

class MeetingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:meeting-list')->only('index');
        $this->middleware('permission:meeting-create')->only(['create', 'store']);
        $this->middleware('permission:meeting-edit')->only(['edit', 'update']);
        $this->middleware('permission:meeting-delete')->only('destroy');
        $this->middleware('permission:meeting-show')->only('show');
    }

    public function index(Request $request)
    {
        $query = Meeting::with(['organizer', 'creator'])->withCount('attendees');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%$search%")
                  ->orWhere('meeting_code', 'like', "%$search%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        if ($request->filled('start_date')) {
            $query->whereDate('start_time', '>=', $request->input('start_date'));
        }

        if ($request->filled('end_date')) {
            $query->whereDate('start_time', '<=', $request->input('end_date'));
        }

        $meetings = $query->latest('start_time')->paginate(15);
        $employees = Employee::where('is_active', true)->get();

        return view('admin.pages.meetings.index', compact('meetings', 'employees'));
    }

    public function create()
    {
        $employees = Employee::where('is_active', true)->get();
        return view('admin.pages.meetings.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'title_ar' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'location' => 'nullable|string|max:255',
            'meeting_link' => 'nullable|url',
            'type' => 'required|in:in_person,virtual,hybrid',
            'organizer_id' => 'nullable|exists:employees,id',
            'agenda' => 'nullable|string',
        ]);

        $data = $request->all();
        $data['created_by'] = auth()->id();
        $data['status'] = 'scheduled';

        $meeting = Meeting::create($data);

        // إضافة الحضور
        if ($request->filled('attendees')) {
            foreach ($request->attendees as $employeeId) {
                $meeting->attendees()->create([
                    'employee_id' => $employeeId,
                    'status' => 'invited',
                    'is_required' => $request->has('required_attendees') && in_array($employeeId, $request->required_attendees ?? [])
                ]);
            }
        }

        return redirect()->route('admin.meetings.index')->with('success', 'تم إنشاء الاجتماع بنجاح.');
    }

    public function show(string $id)
    {
        $meeting = Meeting::with([
            'organizer',
            'attendees.employee',
            'creator'
        ])->findOrFail($id);

        $employees = Employee::where('is_active', true)->get();

        return view('admin.pages.meetings.show', compact('meeting', 'employees'));
    }

    public function edit(string $id)
    {
        $meeting = Meeting::with('attendees')->findOrFail($id);
        $employees = Employee::where('is_active', true)->get();

        return view('admin.pages.meetings.edit', compact('meeting', 'employees'));
    }

    public function update(Request $request, string $id)
    {
        $meeting = Meeting::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'title_ar' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'location' => 'nullable|string|max:255',
            'meeting_link' => 'nullable|url',
            'type' => 'required|in:in_person,virtual,hybrid',
            'status' => 'required|in:scheduled,in_progress,completed,cancelled,postponed',
            'organizer_id' => 'nullable|exists:employees,id',
            'agenda' => 'nullable|string',
            'minutes' => 'nullable|string',
            'action_items' => 'nullable|string',
        ]);

        $meeting->update($request->all());

        return redirect()->route('admin.meetings.show', $meeting->id)->with('success', 'تم تحديث الاجتماع بنجاح.');
    }

    public function destroy(string $id)
    {
        $meeting = Meeting::findOrFail($id);
        $meeting->delete();

        return redirect()->route('admin.meetings.index')->with('success', 'تم حذف الاجتماع بنجاح.');
    }
}
