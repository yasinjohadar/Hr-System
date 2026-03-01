<?php

namespace App\Http\Controllers\Admin;

use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DepartmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:department-list')->only('index');
        $this->middleware('permission:department-create')->only(['create', 'store']);
        $this->middleware('permission:department-edit')->only(['edit', 'update']);
        $this->middleware('permission:department-delete')->only('destroy');
        $this->middleware('permission:department-show')->only('show');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $departmentsQuery = Department::with(['manager', 'parent', 'children'])
            ->withCount('employees');

        // فلترة حسب البحث
        if ($request->filled('query')) {
            $search = $request->input('query');
            $departmentsQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('code', 'like', "%$search%");
            });
        }

        // فلترة حسب الحالة النشطة
        if ($request->filled('is_active')) {
            $departmentsQuery->where('is_active', $request->input('is_active'));
        }

        $departments = $departmentsQuery->paginate(10);

        return view("admin.pages.departments.index", compact("departments"));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $managers = User::where('is_active', true)->get();
        $departments = Department::where('is_active', true)->get();
        
        return view("admin.pages.departments.create", compact("managers", "departments"));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:departments,code',
            'description' => 'nullable|string',
            'manager_id' => 'nullable|exists:users,id',
            'parent_id' => 'nullable|exists:departments,id',
            'is_active' => 'boolean',
        ], [
            'name.required' => 'اسم القسم مطلوب',
            'code.unique' => 'كود القسم مستخدم بالفعل',
        ]);

        Department::create([
            'name' => $request->name,
            'code' => $request->code,
            'description' => $request->description,
            'manager_id' => $request->manager_id,
            'parent_id' => $request->parent_id,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route("admin.departments.index")->with("success", "تم إضافة القسم بنجاح");
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $department = Department::with(['manager', 'parent', 'children', 'employees.user', 'positions'])
            ->withCount('employees')
            ->findOrFail($id);
        return view("admin.pages.departments.show", compact("department"));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $department = Department::findOrFail($id);
        $managers = User::where('is_active', true)->get();
        $departments = Department::where('is_active', true)->where('id', '!=', $id)->get();
        
        return view("admin.pages.departments.edit", compact("department", "managers", "departments"));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $department = Department::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:departments,code,' . $id,
            'description' => 'nullable|string',
            'manager_id' => 'nullable|exists:users,id',
            'parent_id' => 'nullable|exists:departments,id',
            'is_active' => 'boolean',
        ], [
            'name.required' => 'اسم القسم مطلوب',
            'code.unique' => 'كود القسم مستخدم بالفعل',
        ]);

        $department->update([
            'name' => $request->name,
            'code' => $request->code,
            'description' => $request->description,
            'manager_id' => $request->manager_id,
            'parent_id' => $request->parent_id,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.departments.index')->with('success', 'تم تحديث بيانات القسم بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $department = Department::findOrFail($request->id);
        $department->delete();

        return redirect()->route("admin.departments.index")->with("success", "تم حذف القسم بنجاح");
    }
}
