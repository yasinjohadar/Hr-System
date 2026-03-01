<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ViolationType;
use Illuminate\Http\Request;

class ViolationTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:violation-type-list')->only('index');
        $this->middleware('permission:violation-type-create')->only(['create', 'store']);
        $this->middleware('permission:violation-type-edit')->only(['edit', 'update']);
        $this->middleware('permission:violation-type-delete')->only('destroy');
        $this->middleware('permission:violation-type-show')->only('show');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = ViolationType::with('creator')->withCount('employeeViolations');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('name_ar', 'like', "%$search%")
                  ->orWhere('code', 'like', "%$search%");
            });
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->input('is_active'));
        }

        $violationTypes = $query->latest()->paginate(15);

        return view('admin.pages.violation-types.index', compact('violationTypes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.pages.violation-types.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'code' => 'nullable|string|max:50|unique:violation_types,code',
            'description' => 'nullable|string',
            'description_ar' => 'nullable|string',
            'severity_level' => 'required|integer|min:1|max:5',
            'requires_warning' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $data = $request->all();
        $data['created_by'] = auth()->id();

        ViolationType::create($data);

        return redirect()->route('admin.violation-types.index')->with('success', 'تم إضافة نوع المخالفة بنجاح.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $violationType = ViolationType::with(['creator', 'employeeViolations'])->findOrFail($id);
        return view('admin.pages.violation-types.show', compact('violationType'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $violationType = ViolationType::findOrFail($id);
        return view('admin.pages.violation-types.edit', compact('violationType'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $violationType = ViolationType::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'code' => 'nullable|string|max:50|unique:violation_types,code,' . $id,
            'description' => 'nullable|string',
            'description_ar' => 'nullable|string',
            'severity_level' => 'required|integer|min:1|max:5',
            'requires_warning' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $violationType->update($request->all());

        return redirect()->route('admin.violation-types.index')->with('success', 'تم تحديث نوع المخالفة بنجاح.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $violationType = ViolationType::findOrFail($id);

        if ($violationType->employeeViolations()->count() > 0) {
            return redirect()->back()->with('error', 'لا يمكن حذف نوع المخالفة لأنه يحتوي على مخالفات.');
        }

        $violationType->delete();

        return redirect()->route('admin.violation-types.index')->with('success', 'تم حذف نوع المخالفة بنجاح.');
    }
}
