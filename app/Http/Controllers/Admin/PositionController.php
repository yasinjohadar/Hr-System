<?php

namespace App\Http\Controllers\Admin;

use App\Models\Position;
use App\Models\Department;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PositionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:position-list')->only('index');
        $this->middleware('permission:position-create')->only(['create', 'store']);
        $this->middleware('permission:position-edit')->only(['edit', 'update']);
        $this->middleware('permission:position-delete')->only('destroy');
        $this->middleware('permission:position-show')->only('show');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $positionsQuery = Position::with(['department'])
            ->withCount('employees');

        // فلترة حسب البحث
        if ($request->filled('query')) {
            $search = $request->input('query');
            $positionsQuery->where(function ($q) use ($search) {
                $q->where('title', 'like', "%$search%")
                  ->orWhere('code', 'like', "%$search%");
            });
        }

        // فلترة حسب القسم
        if ($request->filled('department_id')) {
            $positionsQuery->where('department_id', $request->input('department_id'));
        }

        // فلترة حسب الحالة النشطة
        if ($request->filled('is_active')) {
            $positionsQuery->where('is_active', $request->input('is_active'));
        }

        $positions = $positionsQuery->orderBy('title')->paginate(20);
        $departments = Department::where('is_active', true)->get();

        return view("admin.pages.positions.index", compact("positions", "departments"));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $departments = Department::where('is_active', true)->get();
        
        return view("admin.pages.positions.create", compact("departments"));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:positions,code',
            'description' => 'nullable|string',
            'department_id' => 'nullable|exists:departments,id',
            'min_salary' => 'nullable|numeric|min:0',
            'max_salary' => 'nullable|numeric|min:0|gte:min_salary',
            'is_active' => 'boolean',
        ], [
            'title.required' => 'اسم المنصب مطلوب',
            'code.unique' => 'كود المنصب مستخدم بالفعل',
            'max_salary.gte' => 'الراتب الأقصى يجب أن يكون أكبر من أو يساوي الراتب الأدنى',
        ]);

        Position::create([
            'title' => $request->title,
            'code' => $request->code,
            'description' => $request->description,
            'department_id' => $request->department_id,
            'min_salary' => $request->min_salary,
            'max_salary' => $request->max_salary,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route("admin.positions.index")->with("success", "تم إضافة المنصب بنجاح");
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $position = Position::with(['department'])->withCount('employees')->findOrFail($id);
        return view("admin.pages.positions.show", compact("position"));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $position = Position::findOrFail($id);
        $departments = Department::where('is_active', true)->get();
        
        return view("admin.pages.positions.edit", compact("position", "departments"));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $position = Position::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:positions,code,' . $id,
            'description' => 'nullable|string',
            'department_id' => 'nullable|exists:departments,id',
            'min_salary' => 'nullable|numeric|min:0',
            'max_salary' => 'nullable|numeric|min:0|gte:min_salary',
            'is_active' => 'boolean',
        ], [
            'title.required' => 'اسم المنصب مطلوب',
            'code.unique' => 'كود المنصب مستخدم بالفعل',
            'max_salary.gte' => 'الراتب الأقصى يجب أن يكون أكبر من أو يساوي الراتب الأدنى',
        ]);

        $position->update([
            'title' => $request->title,
            'code' => $request->code,
            'description' => $request->description,
            'department_id' => $request->department_id,
            'min_salary' => $request->min_salary,
            'max_salary' => $request->max_salary,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.positions.index')->with('success', 'تم تحديث بيانات المنصب بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $position = Position::findOrFail($request->id);
        $position->delete();

        return redirect()->route("admin.positions.index")->with("success", "تم حذف المنصب بنجاح");
    }
}
