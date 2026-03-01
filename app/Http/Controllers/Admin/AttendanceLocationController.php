<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttendanceLocation;
use App\Models\Employee;
use App\Models\Department;
use App\Models\Position;
use Illuminate\Http\Request;

class AttendanceLocationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:attendance-location-list')->only('index', 'show');
        $this->middleware('permission:attendance-location-create')->only('create', 'store');
        $this->middleware('permission:attendance-location-edit')->only('edit', 'update');
        $this->middleware('permission:attendance-location-delete')->only('destroy');
    }

    public function index(Request $request)
    {
        $query = AttendanceLocation::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('name_ar', 'like', "%$search%")
                  ->orWhere('code', 'like', "%$search%")
                  ->orWhere('address', 'like', "%$search%");
            });
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->input('is_active') == '1');
        }

        $locations = $query->latest()->paginate(20);

        return view('admin.pages.attendance-locations.index', compact('locations'));
    }

    public function create()
    {
        $employees = Employee::where('is_active', true)->get();
        $departments = Department::where('is_active', true)->get();
        $positions = Position::where('is_active', true)->get();

        return view('admin.pages.attendance-locations.create', compact('employees', 'departments', 'positions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'code' => 'nullable|string|max:50|unique:attendance_locations,code',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius_meters' => 'required|integer|min:10|max:10000',
            'address' => 'nullable|string|max:500',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'require_location' => 'boolean',
            'allowed_employees' => 'nullable|array',
            'allowed_employees.*' => 'exists:employees,id',
            'allowed_departments' => 'nullable|array',
            'allowed_departments.*' => 'exists:departments,id',
            'allowed_positions' => 'nullable|array',
            'allowed_positions.*' => 'exists:positions,id',
        ], [
            'name.required' => 'اسم الموقع مطلوب',
            'latitude.required' => 'خط العرض مطلوب',
            'longitude.required' => 'خط الطول مطلوب',
            'radius_meters.required' => 'نصف القطر مطلوب',
            'code.unique' => 'كود الموقع موجود مسبقاً',
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active');
        $data['require_location'] = $request->has('require_location');
        $data['created_by'] = auth()->id();

        AttendanceLocation::create($data);

        return redirect()->route('admin.attendance-locations.index')
            ->with('success', 'تم إنشاء موقع الحضور بنجاح.');
    }

    public function show(string $id)
    {
        $location = AttendanceLocation::with(['creator', 'attendances.employee'])->findOrFail($id);
        return view('admin.pages.attendance-locations.show', compact('location'));
    }

    public function edit(string $id)
    {
        $location = AttendanceLocation::findOrFail($id);
        $employees = Employee::where('is_active', true)->get();
        $departments = Department::where('is_active', true)->get();
        $positions = Position::where('is_active', true)->get();

        return view('admin.pages.attendance-locations.edit', compact('location', 'employees', 'departments', 'positions'));
    }

    public function update(Request $request, string $id)
    {
        $location = AttendanceLocation::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'code' => 'nullable|string|max:50|unique:attendance_locations,code,' . $id,
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius_meters' => 'required|integer|min:10|max:10000',
            'address' => 'nullable|string|max:500',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'require_location' => 'boolean',
            'allowed_employees' => 'nullable|array',
            'allowed_employees.*' => 'exists:employees,id',
            'allowed_departments' => 'nullable|array',
            'allowed_departments.*' => 'exists:departments,id',
            'allowed_positions' => 'nullable|array',
            'allowed_positions.*' => 'exists:positions,id',
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active');
        $data['require_location'] = $request->has('require_location');

        $location->update($data);

        return redirect()->route('admin.attendance-locations.index')
            ->with('success', 'تم تحديث موقع الحضور بنجاح.');
    }

    public function destroy(string $id)
    {
        $location = AttendanceLocation::findOrFail($id);
        $location->delete();

        return redirect()->route('admin.attendance-locations.index')
            ->with('success', 'تم حذف موقع الحضور بنجاح.');
    }
}
