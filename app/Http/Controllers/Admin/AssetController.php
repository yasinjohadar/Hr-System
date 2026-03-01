<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\Branch;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AssetController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:asset-list')->only('index');
        $this->middleware('permission:asset-create')->only(['create', 'store']);
        $this->middleware('permission:asset-edit')->only(['edit', 'update']);
        $this->middleware('permission:asset-delete')->only('destroy');
        $this->middleware('permission:asset-show')->only('show');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Asset::with(['branch', 'department', 'creator', 'currentAssignment.employee']);

        // فلترة حسب البحث
        if ($request->filled('query')) {
            $search = $request->input('query');
            $query->where(function ($q) use ($search) {
                $q->where('asset_code', 'like', "%$search%")
                  ->orWhere('name', 'like', "%$search%")
                  ->orWhere('name_ar', 'like', "%$search%")
                  ->orWhere('serial_number', 'like', "%$search%")
                  ->orWhere('barcode', 'like', "%$search%")
                  ->orWhere('manufacturer', 'like', "%$search%")
                  ->orWhere('model', 'like', "%$search%");
            });
        }

        // فلترة حسب الحالة
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // فلترة حسب الفئة
        if ($request->filled('category')) {
            $query->where('category', $request->input('category'));
        }

        // فلترة حسب الفرع
        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->input('branch_id'));
        }

        // فلترة حسب القسم
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->input('department_id'));
        }

        $assets = $query->latest()->paginate(15);
        $branches = Branch::where('is_active', true)->get();
        $departments = Department::where('is_active', true)->get();

        return view('admin.pages.assets.index', compact('assets', 'branches', 'departments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $branches = Branch::where('is_active', true)->get();
        $departments = Department::where('is_active', true)->get();
        
        return view('admin.pages.assets.create', compact('branches', 'departments'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'category' => 'required|in:technical,office,mobile,other',
            'type' => 'nullable|string|max:255',
            'manufacturer' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'serial_number' => 'nullable|string|max:255|unique:assets,serial_number',
            'barcode' => 'nullable|string|max:255|unique:assets,barcode',
            'purchase_date' => 'nullable|date',
            'purchase_cost' => 'nullable|numeric|min:0',
            'current_value' => 'nullable|numeric|min:0',
            'status' => 'required|in:available,assigned,maintenance,damaged,lost,disposed',
            'branch_id' => 'nullable|exists:branches,id',
            'department_id' => 'nullable|exists:departments,id',
            'warranty_expiry' => 'nullable|date',
            'description' => 'nullable|string',
            'notes' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->except('image');
        
        // إنشاء كود الأصل تلقائياً إذا لم يتم توفيره
        if (empty($data['asset_code'])) {
            $data['asset_code'] = 'AST-' . strtoupper(Str::random(8));
        }

        $data['created_by'] = auth()->id();

        // رفع الصورة
        if ($request->hasFile('image')) {
            $data['photo'] = $request->file('image')->store('assets', 'public');
        }

        Asset::create($data);

        return redirect()->route('admin.assets.index')->with('success', 'تم إضافة الأصل بنجاح.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $asset = Asset::with([
            'branch', 
            'department', 
            'creator',
            'assignments.employee',
            'assignments.assigner',
            'assignments.returner',
            'maintenances'
        ])->findOrFail($id);

        return view('admin.pages.assets.show', compact('asset'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $asset = Asset::findOrFail($id);
        $branches = Branch::where('is_active', true)->get();
        $departments = Department::where('is_active', true)->get();
        
        return view('admin.pages.assets.edit', compact('asset', 'branches', 'departments'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $asset = Asset::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'category' => 'required|in:technical,office,mobile,other',
            'type' => 'nullable|string|max:255',
            'manufacturer' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'serial_number' => 'nullable|string|max:255|unique:assets,serial_number,' . $id,
            'barcode' => 'nullable|string|max:255|unique:assets,barcode,' . $id,
            'purchase_date' => 'nullable|date',
            'purchase_cost' => 'nullable|numeric|min:0',
            'current_value' => 'nullable|numeric|min:0',
            'status' => 'required|in:available,assigned,maintenance,damaged,lost,disposed',
            'branch_id' => 'nullable|exists:branches,id',
            'department_id' => 'nullable|exists:departments,id',
            'warranty_expiry' => 'nullable|date',
            'description' => 'nullable|string',
            'notes' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->except('image');

        // رفع صورة جديدة
        if ($request->hasFile('image')) {
            // حذف الصورة القديمة
            if ($asset->photo) {
                Storage::disk('public')->delete($asset->photo);
            }
            $data['photo'] = $request->file('image')->store('assets', 'public');
        }

        $asset->update($data);

        return redirect()->route('admin.assets.index')->with('success', 'تم تحديث الأصل بنجاح.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $asset = Asset::findOrFail($id);

        // التحقق من وجود توزيعات نشطة
        if ($asset->assignments()->where('assignment_status', 'active')->exists()) {
            return redirect()->back()->with('error', 'لا يمكن حذف الأصل لأنه موزع على موظف.');
        }

        // حذف الصورة
        if ($asset->photo) {
            Storage::disk('public')->delete($asset->photo);
        }

        $asset->delete();

        return redirect()->route('admin.assets.index')->with('success', 'تم حذف الأصل بنجاح.');
    }
}
