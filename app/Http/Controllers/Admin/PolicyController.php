<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Policy;
use App\Models\PolicyAcknowledgment;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PolicyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = Policy::with(['creator', 'acknowledgments'])->latest();

        if ($request->filled('is_active')) {
            if ($request->input('is_active') === '1') {
                $query->where('is_active', true);
            } elseif ($request->input('is_active') === '0') {
                $query->where('is_active', false);
            }
        }

        if ($request->filled('category')) {
            $query->where('category', $request->input('category'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%$search%")
                    ->orWhere('content', 'like', "%$search%")
                    ->orWhere('category', 'like', "%$search%");
            });
        }

        $policies = $query->paginate(15);

        return view('admin.pages.policies.index', compact('policies'));
    }

    public function create()
    {
        return view('admin.pages.policies.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:policies,slug',
            'content' => 'nullable|string',
            'category' => 'nullable|string|max:100',
            'effective_date' => 'nullable|date',
            'version' => 'nullable|string|max:50',
            'is_active' => 'boolean',
            'document_path' => 'nullable|string|max:500',
        ]);

        $data = $request->only([
            'title', 'content', 'category', 'effective_date', 'version', 'document_path',
        ]);
        $data['slug'] = $request->filled('slug') ? Str::slug($request->slug) : Str::slug($request->title);
        $data['is_active'] = $request->boolean('is_active');
        $data['created_by'] = auth()->id();

        Policy::create($data);

        return redirect()->route('admin.policies.index')
            ->with('success', 'تم إنشاء السياسة بنجاح.');
    }

    public function show(Policy $policy)
    {
        $policy->load(['creator', 'acknowledgments.employee']);
        $employees = Employee::where('is_active', true)->get(['id', 'first_name', 'last_name', 'full_name']);
        return view('admin.pages.policies.show', compact('policy', 'employees'));
    }

    public function edit(Policy $policy)
    {
        return view('admin.pages.policies.edit', compact('policy'));
    }

    public function update(Request $request, Policy $policy)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:policies,slug,' . $policy->id,
            'content' => 'nullable|string',
            'category' => 'nullable|string|max:100',
            'effective_date' => 'nullable|date',
            'version' => 'nullable|string|max:50',
            'is_active' => 'boolean',
            'document_path' => 'nullable|string|max:500',
        ]);

        $data = $request->only([
            'title', 'content', 'category', 'effective_date', 'version', 'document_path',
        ]);
        if ($request->filled('slug')) {
            $data['slug'] = Str::slug($request->slug);
        }
        $data['is_active'] = $request->boolean('is_active');

        $policy->update($data);

        return redirect()->route('admin.policies.show', $policy)
            ->with('success', 'تم تحديث السياسة بنجاح.');
    }

    public function destroy(Policy $policy)
    {
        $policy->delete();
        return redirect()->route('admin.policies.index')
            ->with('success', 'تم حذف السياسة بنجاح.');
    }

    /**
     * تسجيل اعتراف موظف بسياسة
     */
    public function acknowledge(Request $request, Policy $policy)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
        ]);

        $employeeId = (int) $request->employee_id;

        $exists = PolicyAcknowledgment::where('policy_id', $policy->id)
            ->where('employee_id', $employeeId)
            ->exists();

        if ($exists) {
            return redirect()->route('admin.policies.show', $policy)
                ->with('info', 'الموظف معترف مسبقاً بهذه السياسة.');
        }

        PolicyAcknowledgment::create([
            'policy_id' => $policy->id,
            'employee_id' => $employeeId,
            'acknowledged_at' => now(),
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('admin.policies.show', $policy)
            ->with('success', 'تم تسجيل الاعتراف بنجاح.');
    }
}
