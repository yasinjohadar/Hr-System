<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SalaryComponent;
use App\Models\Position;
use App\Models\Department;
use Illuminate\Http\Request;

class SalaryComponentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:salary-component-list')->only('index');
        $this->middleware('permission:salary-component-create')->only(['create', 'store']);
        $this->middleware('permission:salary-component-edit')->only(['edit', 'update']);
        $this->middleware('permission:salary-component-delete')->only('destroy');
        $this->middleware('permission:salary-component-show')->only('show');
    }

    public function index(Request $request)
    {
        $query = SalaryComponent::with('creator');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('name_ar', 'like', "%$search%")
                  ->orWhere('code', 'like', "%$search%");
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->input('is_active'));
        }

        $components = $query->latest()->paginate(20);

        return view('admin.pages.salary-components.index', compact('components'));
    }

    public function create()
    {
        $positions = Position::where('is_active', true)->get();
        $departments = Department::where('is_active', true)->get();

        return view('admin.pages.salary-components.create', compact('positions', 'departments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:50|unique:salary_components,code',
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'type' => 'required|in:allowance,deduction,bonus,overtime',
            'calculation_type' => 'required|in:fixed,percentage,formula,attendance_based,leave_based',
            'default_value' => 'nullable|numeric|min:0',
            'percentage' => 'nullable|numeric|min:0|max:100',
            'formula' => 'nullable|string',
            'is_taxable' => 'boolean',
            'is_required' => 'boolean',
            'apply_to_all' => 'boolean',
        ]);

        $data = $request->all();
        $data['created_by'] = auth()->id();
        $data['is_taxable'] = $request->has('is_taxable') ? 1 : 0;
        $data['is_required'] = $request->has('is_required') ? 1 : 0;
        $data['apply_to_all'] = $request->has('apply_to_all') ? 1 : 0;
        $data['is_active'] = $request->has('is_active') ? 1 : 0;

        if ($request->filled('applicable_positions')) {
            $data['applicable_positions'] = $request->applicable_positions;
        }

        if ($request->filled('applicable_departments')) {
            $data['applicable_departments'] = $request->applicable_departments;
        }

        SalaryComponent::create($data);

        return redirect()->route('admin.salary-components.index')
            ->with('success', 'تم إضافة مكون الراتب بنجاح.');
    }

    public function show(string $id)
    {
        $component = SalaryComponent::with('creator')->findOrFail($id);
        return view('admin.pages.salary-components.show', compact('component'));
    }

    public function edit(string $id)
    {
        $component = SalaryComponent::findOrFail($id);
        $positions = Position::where('is_active', true)->get();
        $departments = Department::where('is_active', true)->get();

        return view('admin.pages.salary-components.edit', compact('component', 'positions', 'departments'));
    }

    public function update(Request $request, string $id)
    {
        $component = SalaryComponent::findOrFail($id);

        $request->validate([
            'code' => 'required|string|max:50|unique:salary_components,code,' . $id,
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'type' => 'required|in:allowance,deduction,bonus,overtime',
            'calculation_type' => 'required|in:fixed,percentage,formula,attendance_based,leave_based',
            'default_value' => 'nullable|numeric|min:0',
            'percentage' => 'nullable|numeric|min:0|max:100',
            'formula' => 'nullable|string',
            'is_taxable' => 'boolean',
            'is_required' => 'boolean',
            'apply_to_all' => 'boolean',
        ]);

        $data = $request->all();
        $data['is_taxable'] = $request->has('is_taxable') ? 1 : 0;
        $data['is_required'] = $request->has('is_required') ? 1 : 0;
        $data['apply_to_all'] = $request->has('apply_to_all') ? 1 : 0;
        $data['is_active'] = $request->has('is_active') ? 1 : 0;

        if ($request->filled('applicable_positions')) {
            $data['applicable_positions'] = $request->applicable_positions;
        } else {
            $data['applicable_positions'] = null;
        }

        if ($request->filled('applicable_departments')) {
            $data['applicable_departments'] = $request->applicable_departments;
        } else {
            $data['applicable_departments'] = null;
        }

        $component->update($data);

        return redirect()->route('admin.salary-components.index')
            ->with('success', 'تم تحديث مكون الراتب بنجاح.');
    }

    public function destroy(string $id)
    {
        $component = SalaryComponent::findOrFail($id);
        $component->delete();

        return redirect()->route('admin.salary-components.index')
            ->with('success', 'تم حذف مكون الراتب بنجاح.');
    }
}
