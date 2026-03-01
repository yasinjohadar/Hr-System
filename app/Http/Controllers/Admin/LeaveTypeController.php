<?php

namespace App\Http\Controllers\Admin;

use App\Models\LeaveType;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LeaveTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:leave-type-list')->only('index');
        $this->middleware('permission:leave-type-create')->only(['create', 'store']);
        $this->middleware('permission:leave-type-edit')->only(['edit', 'update']);
        $this->middleware('permission:leave-type-delete')->only('destroy');
        $this->middleware('permission:leave-type-show')->only('show');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $leaveTypesQuery = LeaveType::query();

        // فلترة حسب البحث
        if ($request->filled('query')) {
            $search = $request->input('query');
            $leaveTypesQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('name_ar', 'like', "%$search%")
                  ->orWhere('code', 'like', "%$search%");
            });
        }

        // فلترة حسب الحالة النشطة
        if ($request->filled('is_active')) {
            $leaveTypesQuery->where('is_active', $request->input('is_active'));
        }

        $leaveTypes = $leaveTypesQuery->orderBy('sort_order')->orderBy('name')->paginate(20);

        return view("admin.pages.leave-types.index", compact("leaveTypes"));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view("admin.pages.leave-types.create");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'code' => 'required|string|max:50|unique:leave_types,code',
            'description' => 'nullable|string',
            'max_days' => 'nullable|integer|min:0',
            'is_paid' => 'boolean',
            'requires_approval' => 'boolean',
            'carry_forward' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer',
        ], [
            'name.required' => 'اسم نوع الإجازة مطلوب',
            'code.required' => 'كود نوع الإجازة مطلوب',
            'code.unique' => 'كود نوع الإجازة مستخدم بالفعل',
        ]);

        LeaveType::create([
            'name' => $request->name,
            'name_ar' => $request->name_ar,
            'code' => strtoupper($request->code),
            'description' => $request->description,
            'max_days' => $request->max_days,
            'is_paid' => $request->has('is_paid'),
            'requires_approval' => $request->has('requires_approval'),
            'carry_forward' => $request->has('carry_forward'),
            'is_active' => $request->has('is_active'),
            'sort_order' => $request->sort_order ?? 0,
        ]);

        return redirect()->route("admin.leave-types.index")->with("success", "تم إضافة نوع الإجازة بنجاح");
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $leaveType = LeaveType::withCount(['leaveRequests', 'leaveBalances'])->findOrFail($id);
        return view("admin.pages.leave-types.show", compact("leaveType"));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $leaveType = LeaveType::findOrFail($id);
        return view("admin.pages.leave-types.edit", compact("leaveType"));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $leaveType = LeaveType::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'code' => 'required|string|max:50|unique:leave_types,code,' . $id,
            'description' => 'nullable|string',
            'max_days' => 'nullable|integer|min:0',
            'is_paid' => 'boolean',
            'requires_approval' => 'boolean',
            'carry_forward' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer',
        ], [
            'name.required' => 'اسم نوع الإجازة مطلوب',
            'code.required' => 'كود نوع الإجازة مطلوب',
            'code.unique' => 'كود نوع الإجازة مستخدم بالفعل',
        ]);

        $leaveType->update([
            'name' => $request->name,
            'name_ar' => $request->name_ar,
            'code' => strtoupper($request->code),
            'description' => $request->description,
            'max_days' => $request->max_days,
            'is_paid' => $request->has('is_paid'),
            'requires_approval' => $request->has('requires_approval'),
            'carry_forward' => $request->has('carry_forward'),
            'is_active' => $request->has('is_active'),
            'sort_order' => $request->sort_order ?? 0,
        ]);

        return redirect()->route('admin.leave-types.index')->with('success', 'تم تحديث بيانات نوع الإجازة بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $leaveType = LeaveType::findOrFail($request->id);
        $leaveType->delete();

        return redirect()->route("admin.leave-types.index")->with("success", "تم حذف نوع الإجازة بنجاح");
    }
}
