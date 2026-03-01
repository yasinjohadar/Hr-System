<?php

namespace App\Http\Controllers\Admin;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BranchController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:branch-list')->only('index');
        $this->middleware('permission:branch-create')->only(['create', 'store']);
        $this->middleware('permission:branch-edit')->only(['edit', 'update']);
        $this->middleware('permission:branch-delete')->only('destroy');
        $this->middleware('permission:branch-show')->only('show');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $branchesQuery = Branch::with(['manager', 'creator'])
            ->withCount('employees');

        // فلترة حسب البحث
        if ($request->filled('query')) {
            $search = $request->input('query');
            $branchesQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('code', 'like', "%$search%")
                  ->orWhere('city', 'like', "%$search%");
            });
        }

        // فلترة حسب الحالة النشطة
        if ($request->filled('is_active')) {
            $branchesQuery->where('is_active', $request->input('is_active'));
        }

        // فلترة حسب الفرع الرئيسي
        if ($request->filled('is_main')) {
            $branchesQuery->where('is_main', $request->input('is_main'));
        }

        $branches = $branchesQuery->paginate(10);

        return view("admin.pages.branches.index", compact("branches"));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $managers = User::where('is_active', true)->get();
        
        return view("admin.pages.branches.create", compact("managers"));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:branches,code',
            'description' => 'nullable|string',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:20',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'manager_name' => 'nullable|string|max:255',
            'manager_id' => 'nullable|exists:users,id',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'opening_time' => 'nullable|date_format:H:i',
            'closing_time' => 'nullable|date_format:H:i',
            'working_days' => 'nullable|array',
            'is_main' => 'boolean',
            'is_active' => 'boolean',
        ], [
            'name.required' => 'اسم الفرع مطلوب',
            'code.unique' => 'كود الفرع مستخدم بالفعل',
            'email.email' => 'البريد الإلكتروني غير صحيح',
        ]);

        Branch::create([
            'name' => $request->name,
            'code' => $request->code,
            'description' => $request->description,
            'address' => $request->address,
            'city' => $request->city,
            'country' => $request->country ?? 'السعودية',
            'postal_code' => $request->postal_code,
            'phone' => $request->phone,
            'email' => $request->email,
            'manager_name' => $request->manager_name,
            'manager_id' => $request->manager_id,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'opening_time' => $request->opening_time,
            'closing_time' => $request->closing_time,
            'working_days' => $request->working_days,
            'is_main' => $request->has('is_main'),
            'is_active' => $request->has('is_active'),
            'created_by' => auth()->id(),
        ]);

        return redirect()->route("admin.branches.index")->with("success", "تم إضافة الفرع بنجاح");
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $branch = Branch::with(['manager', 'creator', 'employees.user'])
            ->withCount('employees')
            ->findOrFail($id);
        return view("admin.pages.branches.show", compact("branch"));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $branch = Branch::findOrFail($id);
        $managers = User::where('is_active', true)->get();
        
        return view("admin.pages.branches.edit", compact("branch", "managers"));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $branch = Branch::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:branches,code,' . $id,
            'description' => 'nullable|string',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:20',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'manager_name' => 'nullable|string|max:255',
            'manager_id' => 'nullable|exists:users,id',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'opening_time' => 'nullable|date_format:H:i',
            'closing_time' => 'nullable|date_format:H:i',
            'working_days' => 'nullable|array',
            'is_main' => 'boolean',
            'is_active' => 'boolean',
        ], [
            'name.required' => 'اسم الفرع مطلوب',
            'code.unique' => 'كود الفرع مستخدم بالفعل',
            'email.email' => 'البريد الإلكتروني غير صحيح',
        ]);

        $branch->update([
            'name' => $request->name,
            'code' => $request->code,
            'description' => $request->description,
            'address' => $request->address,
            'city' => $request->city,
            'country' => $request->country ?? 'السعودية',
            'postal_code' => $request->postal_code,
            'phone' => $request->phone,
            'email' => $request->email,
            'manager_name' => $request->manager_name,
            'manager_id' => $request->manager_id,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'opening_time' => $request->opening_time,
            'closing_time' => $request->closing_time,
            'working_days' => $request->working_days,
            'is_main' => $request->has('is_main'),
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.branches.index')->with('success', 'تم تحديث بيانات الفرع بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $branch = Branch::findOrFail($request->id);
        $branch->delete();

        return redirect()->route("admin.branches.index")->with("success", "تم حذف الفرع بنجاح");
    }
}
