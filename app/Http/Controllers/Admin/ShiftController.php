<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Shift;
use Illuminate\Http\Request;

class ShiftController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:shift-list')->only('index');
        $this->middleware('permission:shift-create')->only(['create', 'store']);
        $this->middleware('permission:shift-edit')->only(['edit', 'update']);
        $this->middleware('permission:shift-delete')->only('destroy');
        $this->middleware('permission:shift-show')->only('show');
    }

    public function index(Request $request)
    {
        $query = Shift::with('creator')->withCount('activeAssignments');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('name_ar', 'like', "%$search%")
                  ->orWhere('shift_code', 'like', "%$search%");
            });
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->input('is_active'));
        }

        $shifts = $query->latest()->paginate(20);

        return view('admin.pages.shifts.index', compact('shifts'));
    }

    public function create()
    {
        return view('admin.pages.shifts.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'duration_hours' => 'required|integer|min:1|max:24',
            'grace_period_minutes' => 'nullable|integer|min:0',
            'break_duration_minutes' => 'nullable|integer|min:0',
            'overtime_rate' => 'nullable|numeric|min:1',
            'overtime_threshold_minutes' => 'nullable|integer|min:0',
        ]);

        $data = $request->all();
        $data['created_by'] = auth()->id();
        
        // تعيين القيم الافتراضية للأيام
        $data['monday'] = $request->has('monday') ? 1 : 0;
        $data['tuesday'] = $request->has('tuesday') ? 1 : 0;
        $data['wednesday'] = $request->has('wednesday') ? 1 : 0;
        $data['thursday'] = $request->has('thursday') ? 1 : 0;
        $data['friday'] = $request->has('friday') ? 1 : 0;
        $data['saturday'] = $request->has('saturday') ? 1 : 0;
        $data['sunday'] = $request->has('sunday') ? 1 : 0;
        $data['is_active'] = $request->has('is_active') ? 1 : 0;

        Shift::create($data);

        return redirect()->route('admin.shifts.index')
            ->with('success', 'تم إضافة المناوبة بنجاح.');
    }

    public function show(string $id)
    {
        $shift = Shift::with(['assignments.employee', 'creator'])->findOrFail($id);
        return view('admin.pages.shifts.show', compact('shift'));
    }

    public function edit(string $id)
    {
        $shift = Shift::findOrFail($id);
        return view('admin.pages.shifts.edit', compact('shift'));
    }

    public function update(Request $request, string $id)
    {
        $shift = Shift::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'duration_hours' => 'required|integer|min:1|max:24',
            'grace_period_minutes' => 'nullable|integer|min:0',
            'break_duration_minutes' => 'nullable|integer|min:0',
            'overtime_rate' => 'nullable|numeric|min:1',
            'overtime_threshold_minutes' => 'nullable|integer|min:0',
        ]);

        $data = $request->all();
        
        // تعيين القيم للأيام
        $data['monday'] = $request->has('monday') ? 1 : 0;
        $data['tuesday'] = $request->has('tuesday') ? 1 : 0;
        $data['wednesday'] = $request->has('wednesday') ? 1 : 0;
        $data['thursday'] = $request->has('thursday') ? 1 : 0;
        $data['friday'] = $request->has('friday') ? 1 : 0;
        $data['saturday'] = $request->has('saturday') ? 1 : 0;
        $data['sunday'] = $request->has('sunday') ? 1 : 0;
        $data['is_active'] = $request->has('is_active') ? 1 : 0;

        $shift->update($data);

        return redirect()->route('admin.shifts.index')
            ->with('success', 'تم تحديث المناوبة بنجاح.');
    }

    public function destroy(string $id)
    {
        $shift = Shift::findOrFail($id);

        if ($shift->assignments()->count() > 0) {
            return redirect()->back()
                ->with('error', 'لا يمكن حذف المناوبة لأنها مستخدمة في تعيينات.');
        }

        $shift->delete();

        return redirect()->route('admin.shifts.index')
            ->with('success', 'تم حذف المناوبة بنجاح.');
    }
}
