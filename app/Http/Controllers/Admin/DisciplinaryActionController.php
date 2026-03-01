<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DisciplinaryAction;
use Illuminate\Http\Request;

class DisciplinaryActionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:disciplinary-action-list')->only('index');
        $this->middleware('permission:disciplinary-action-create')->only(['create', 'store']);
        $this->middleware('permission:disciplinary-action-edit')->only(['edit', 'update']);
        $this->middleware('permission:disciplinary-action-delete')->only('destroy');
        $this->middleware('permission:disciplinary-action-show')->only('show');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = DisciplinaryAction::with('creator')->withCount('employeeViolations');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('name_ar', 'like', "%$search%")
                  ->orWhere('code', 'like', "%$search%");
            });
        }

        if ($request->filled('action_type')) {
            $query->where('action_type', $request->input('action_type'));
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->input('is_active'));
        }

        $disciplinaryActions = $query->latest()->paginate(15);

        return view('admin.pages.disciplinary-actions.index', compact('disciplinaryActions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.pages.disciplinary-actions.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'code' => 'nullable|string|max:50|unique:disciplinary_actions,code',
            'description' => 'nullable|string',
            'description_ar' => 'nullable|string',
            'action_type' => 'required|in:verbal_warning,written_warning,final_warning,deduction,suspension,termination',
            'severity_level' => 'required|integer|min:1|max:5',
            'deduction_amount' => 'nullable|numeric|min:0',
            'suspension_days' => 'nullable|integer|min:0',
            'requires_approval' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $data = $request->all();
        $data['created_by'] = auth()->id();

        DisciplinaryAction::create($data);

        return redirect()->route('admin.disciplinary-actions.index')->with('success', 'تم إضافة الإجراء التأديبي بنجاح.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $disciplinaryAction = DisciplinaryAction::with(['creator', 'employeeViolations'])->findOrFail($id);
        return view('admin.pages.disciplinary-actions.show', compact('disciplinaryAction'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $disciplinaryAction = DisciplinaryAction::findOrFail($id);
        return view('admin.pages.disciplinary-actions.edit', compact('disciplinaryAction'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $disciplinaryAction = DisciplinaryAction::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'code' => 'nullable|string|max:50|unique:disciplinary_actions,code,' . $id,
            'description' => 'nullable|string',
            'description_ar' => 'nullable|string',
            'action_type' => 'required|in:verbal_warning,written_warning,final_warning,deduction,suspension,termination',
            'severity_level' => 'required|integer|min:1|max:5',
            'deduction_amount' => 'nullable|numeric|min:0',
            'suspension_days' => 'nullable|integer|min:0',
            'requires_approval' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $disciplinaryAction->update($request->all());

        return redirect()->route('admin.disciplinary-actions.index')->with('success', 'تم تحديث الإجراء التأديبي بنجاح.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $disciplinaryAction = DisciplinaryAction::findOrFail($id);

        if ($disciplinaryAction->employeeViolations()->count() > 0) {
            return redirect()->back()->with('error', 'لا يمكن حذف الإجراء التأديبي لأنه مستخدم في مخالفات.');
        }

        $disciplinaryAction->delete();

        return redirect()->route('admin.disciplinary-actions.index')->with('success', 'تم حذف الإجراء التأديبي بنجاح.');
    }
}
