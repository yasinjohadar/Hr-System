<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RewardType;
use Illuminate\Http\Request;

class RewardTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:reward-type-list')->only('index');
        $this->middleware('permission:reward-type-create')->only(['create', 'store']);
        $this->middleware('permission:reward-type-edit')->only(['edit', 'update']);
        $this->middleware('permission:reward-type-delete')->only('destroy');
        $this->middleware('permission:reward-type-show')->only('show');
    }

    public function index(Request $request)
    {
        $query = RewardType::with('creator')->withCount('employeeRewards');

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

        $rewardTypes = $query->latest()->paginate(15);

        return view('admin.pages.reward-types.index', compact('rewardTypes'));
    }

    public function create()
    {
        return view('admin.pages.reward-types.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'code' => 'nullable|string|max:50|unique:reward_types,code',
            'description' => 'nullable|string',
            'type' => 'required|in:monetary,non_monetary,points,recognition,gift',
            'default_value' => 'nullable|numeric|min:0',
            'default_points' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $data = $request->all();
        $data['created_by'] = auth()->id();

        RewardType::create($data);

        return redirect()->route('admin.reward-types.index')->with('success', 'تم إضافة نوع المكافأة بنجاح.');
    }

    public function show(string $id)
    {
        $rewardType = RewardType::with(['creator', 'employeeRewards'])->findOrFail($id);
        return view('admin.pages.reward-types.show', compact('rewardType'));
    }

    public function edit(string $id)
    {
        $rewardType = RewardType::findOrFail($id);
        return view('admin.pages.reward-types.edit', compact('rewardType'));
    }

    public function update(Request $request, string $id)
    {
        $rewardType = RewardType::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'code' => 'nullable|string|max:50|unique:reward_types,code,' . $id,
            'description' => 'nullable|string',
            'type' => 'required|in:monetary,non_monetary,points,recognition,gift',
            'default_value' => 'nullable|numeric|min:0',
            'default_points' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $rewardType->update($request->all());

        return redirect()->route('admin.reward-types.index')->with('success', 'تم تحديث نوع المكافأة بنجاح.');
    }

    public function destroy(string $id)
    {
        $rewardType = RewardType::findOrFail($id);

        if ($rewardType->employeeRewards()->count() > 0) {
            return redirect()->back()->with('error', 'لا يمكن حذف نوع المكافأة لأنه مستخدم في مكافآت.');
        }

        $rewardType->delete();

        return redirect()->route('admin.reward-types.index')->with('success', 'تم حذف نوع المكافأة بنجاح.');
    }
}
