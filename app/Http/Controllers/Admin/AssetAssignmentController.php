<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\AssetAssignment;
use App\Models\Employee;
use App\Services\AssetLifecycleRecorder;
use Illuminate\Http\Request;

class AssetAssignmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:asset-assignment-list')->only('index');
        $this->middleware('permission:asset-assignment-create')->only(['create', 'store']);
        $this->middleware('permission:asset-assignment-edit')->only(['edit', 'update', 'showReturnForm', 'return']);
        $this->middleware('permission:asset-assignment-delete')->only('destroy');
        $this->middleware('permission:asset-assignment-show')->only('show');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = AssetAssignment::with(['asset', 'employee', 'assigner', 'returner']);

        // فلترة حسب الموظف
        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->input('employee_id'));
        }

        // فلترة حسب الأصل
        if ($request->filled('asset_id')) {
            $query->where('asset_id', $request->input('asset_id'));
        }

        // فلترة حسب الحالة
        if ($request->filled('assignment_status')) {
            $query->where('assignment_status', $request->input('assignment_status'));
        }

        $assignments = $query->latest()->paginate(15);
        $employees = Employee::where('is_active', true)->get();
        $assets = Asset::whereIn('status', ['available', 'assigned'])->get();

        return view('admin.pages.asset-assignments.index', compact('assignments', 'employees', 'assets'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $assets = Asset::where('status', 'available')->get();
        $employees = Employee::where('is_active', true)->get();
        
        return view('admin.pages.asset-assignments.create', compact('assets', 'employees'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'employee_id' => 'required|exists:employees,id',
            'assigned_date' => 'required|date',
            'expected_return_date' => 'nullable|date|after:assigned_date',
            'condition_on_assignment' => 'required|in:excellent,good,fair,poor',
            'assignment_notes' => 'nullable|string',
        ]);

        $asset = Asset::findOrFail($request->asset_id);

        // التحقق من أن الأصل متاح
        if (!$asset->isAvailable()) {
            return redirect()->back()->with('error', 'الأصل غير متاح للتوزيع.');
        }

        // التحقق من عدم وجود توزيع نشط آخر
        $activeAssignment = AssetAssignment::where('asset_id', $request->asset_id)
            ->where('assignment_status', 'active')
            ->first();

        if ($activeAssignment) {
            return redirect()->back()->with('error', 'الأصل موزع بالفعل على موظف آخر.');
        }

        $data = $request->all();
        $data['assigned_by'] = auth()->id();
        $data['created_by'] = auth()->id();
        $data['assignment_status'] = 'active';

        $assignment = AssetAssignment::create($data);

        // تحديث حالة الأصل
        $asset->update(['status' => 'assigned']);

        app(AssetLifecycleRecorder::class)->recordAssignmentStarted($assignment);

        return redirect()->route('admin.asset-assignments.index')->with('success', 'تم توزيع الأصل بنجاح.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $assignment = AssetAssignment::with([
            'asset',
            'employee',
            'assigner',
            'returner',
            'creator'
        ])->findOrFail($id);

        return view('admin.pages.asset-assignments.show', compact('assignment'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $assignment = AssetAssignment::findOrFail($id);
        $assets = Asset::all();
        $employees = Employee::where('is_active', true)->get();
        
        return view('admin.pages.asset-assignments.edit', compact('assignment', 'assets', 'employees'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $assignment = AssetAssignment::findOrFail($id);

        $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'employee_id' => 'required|exists:employees,id',
            'assigned_date' => 'required|date',
            'expected_return_date' => 'nullable|date|after:assigned_date',
            'condition_on_assignment' => 'required|in:excellent,good,fair,poor',
            'assignment_notes' => 'nullable|string',
        ]);

        $assignment->update($request->all());

        return redirect()->route('admin.asset-assignments.index')->with('success', 'تم تحديث التوزيع بنجاح.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $assignment = AssetAssignment::findOrFail($id);

        if ($assignment->assignment_status === 'active') {
            return redirect()->back()->with('error', 'لا يمكن حذف توزيع نشط. يرجى استرجاع الأصل أولاً.');
        }

        $assignment->delete();

        return redirect()->route('admin.asset-assignments.index')->with('success', 'تم حذف التوزيع بنجاح.');
    }

    /**
     * عرض نموذج استرجاع الأصل
     */
    public function showReturnForm(string $id)
    {
        $assignment = AssetAssignment::with(['asset', 'employee'])->findOrFail($id);
        
        if ($assignment->assignment_status !== 'active') {
            return redirect()->back()->with('error', 'هذا التوزيع غير نشط.');
        }
        
        return view('admin.pages.asset-assignments.return', compact('assignment'));
    }

    /**
     * استرجاع أصل من موظف
     */
    public function return(Request $request, string $id)
    {
        $assignment = AssetAssignment::findOrFail($id);

        if ($assignment->assignment_status !== 'active') {
            return redirect()->back()->with('error', 'هذا التوزيع غير نشط.');
        }

        $request->validate([
            'actual_return_date' => 'required|date',
            'condition_on_return' => 'required|in:excellent,good,fair,poor,damaged',
            'return_notes' => 'nullable|string',
        ]);

        $assignment->update([
            'actual_return_date' => $request->actual_return_date,
            'condition_on_return' => $request->condition_on_return,
            'return_notes' => $request->return_notes,
            'assignment_status' => 'returned',
            'returned_by' => auth()->id(),
        ]);

        $assignment->refresh();

        app(AssetLifecycleRecorder::class)->recordAssignmentReturned($assignment);

        // تحديث حالة الأصل
        $asset = $assignment->asset;
        if ($request->condition_on_return === 'damaged') {
            $asset->update(['status' => 'damaged']);
        } else {
            $asset->update(['status' => 'available']);
        }

        return redirect()->route('admin.asset-assignments.index')->with('success', 'تم استرجاع الأصل بنجاح.');
    }
}
