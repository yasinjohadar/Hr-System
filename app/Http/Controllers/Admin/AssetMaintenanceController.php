<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\AssetMaintenance;
use App\Models\User;
use App\Services\AssetLifecycleRecorder;
use Illuminate\Http\Request;

class AssetMaintenanceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:asset-maintenance-list')->only('index');
        $this->middleware('permission:asset-maintenance-create')->only(['create', 'store']);
        $this->middleware('permission:asset-maintenance-edit')->only(['edit', 'update']);
        $this->middleware('permission:asset-maintenance-delete')->only('destroy');
        $this->middleware('permission:asset-maintenance-show')->only('show');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = AssetMaintenance::with(['asset', 'creator']);

        // فلترة حسب الأصل
        if ($request->filled('asset_id')) {
            $query->where('asset_id', $request->input('asset_id'));
        }

        // فلترة حسب الحالة
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // فلترة حسب نوع الصيانة
        if ($request->filled('maintenance_type')) {
            $query->where('maintenance_type', $request->input('maintenance_type'));
        }

        $maintenances = $query->latest()->paginate(15);
        $assets = Asset::all();

        return view('admin.pages.asset-maintenances.index', compact('maintenances', 'assets'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $assets = Asset::all();
        $users = User::where('is_active', true)->get();
        
        return view('admin.pages.asset-maintenances.create', compact('assets', 'users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'maintenance_type' => 'required|in:preventive,corrective,upgrade,cleaning,inspection',
            'title' => 'required|string|max:255',
            'scheduled_date' => 'nullable|date',
            'actual_date' => 'nullable|date',
            'cost' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'status' => 'required|in:scheduled,in_progress,completed,cancelled,postponed',
            'next_maintenance_date' => 'nullable|date',
            'service_provider' => 'nullable|string|max:255',
            'service_provider_contact' => 'nullable|string|max:255',
            'performed_by' => 'nullable|exists:users,id',
            'notes' => 'nullable|string',
        ]);

        $data = $request->all();
        $data['created_by'] = auth()->id();

        $maintenance = AssetMaintenance::create($data);

        app(AssetLifecycleRecorder::class)->recordMaintenanceCreated($maintenance);

        // تحديث حالة الأصل إذا كانت الصيانة قيد التنفيذ
        if ($request->status === 'in_progress') {
            $asset = Asset::findOrFail($request->asset_id);
            $asset->update(['status' => 'maintenance']);
        }

        return redirect()->route('admin.asset-maintenances.index')->with('success', 'تم إضافة سجل الصيانة بنجاح.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $maintenance = AssetMaintenance::with(['asset', 'creator', 'performer'])->findOrFail($id);

        return view('admin.pages.asset-maintenances.show', compact('maintenance'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $maintenance = AssetMaintenance::findOrFail($id);
        $assets = Asset::all();
        $users = User::where('is_active', true)->get();
        
        return view('admin.pages.asset-maintenances.edit', compact('maintenance', 'assets', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $maintenance = AssetMaintenance::findOrFail($id);
        $oldStatus = $maintenance->status;

        $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'maintenance_type' => 'required|in:preventive,corrective,upgrade,cleaning,inspection',
            'title' => 'required|string|max:255',
            'scheduled_date' => 'nullable|date',
            'actual_date' => 'nullable|date',
            'cost' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'status' => 'required|in:scheduled,in_progress,completed,cancelled,postponed',
            'next_maintenance_date' => 'nullable|date',
            'service_provider' => 'nullable|string|max:255',
            'service_provider_contact' => 'nullable|string|max:255',
            'performed_by' => 'nullable|exists:users,id',
            'notes' => 'nullable|string',
        ]);

        $maintenance->update($request->all());
        $maintenance->refresh();

        app(AssetLifecycleRecorder::class)->recordMaintenanceStatusChanged($maintenance, $oldStatus);

        // تحديث حالة الأصل
        $asset = Asset::findOrFail($request->asset_id);
        
        if ($request->status === 'in_progress') {
            $asset->update(['status' => 'maintenance']);
        } elseif ($request->status === 'completed' && $oldStatus === 'in_progress') {
            // إذا اكتملت الصيانة، نعيد الأصل إلى الحالة السابقة
            $hasActiveAssignment = $asset->assignments()->where('assignment_status', 'active')->exists();
            $asset->update(['status' => $hasActiveAssignment ? 'assigned' : 'available']);
        } elseif ($request->status === 'cancelled' && $oldStatus === 'in_progress') {
            // إذا ألغيت الصيانة، نعيد الأصل إلى الحالة السابقة
            $hasActiveAssignment = $asset->assignments()->where('assignment_status', 'active')->exists();
            $asset->update(['status' => $hasActiveAssignment ? 'assigned' : 'available']);
        }

        return redirect()->route('admin.asset-maintenances.index')->with('success', 'تم تحديث سجل الصيانة بنجاح.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $maintenance = AssetMaintenance::findOrFail($id);
        $maintenance->delete();

        return redirect()->route('admin.asset-maintenances.index')->with('success', 'تم حذف سجل الصيانة بنجاح.');
    }
}
