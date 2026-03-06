<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Requisition;
use App\Models\JobVacancy;
use App\Models\Department;
use App\Models\Position;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RequisitionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:requisition-list')->only('index');
        $this->middleware('permission:requisition-create')->only(['create', 'store']);
        $this->middleware('permission:requisition-edit')->only(['edit', 'update']);
        $this->middleware('permission:requisition-delete')->only('destroy');
        $this->middleware('permission:requisition-show')->only('show');
        $this->middleware('permission:requisition-approve')->only(['approve', 'reject']);
    }

    public function index(Request $request)
    {
        $query = Requisition::with(['department', 'position', 'branch', 'creator', 'approvedBy', 'jobVacancy']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }
        if ($request->filled('position_id')) {
            $query->where('position_id', $request->position_id);
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('requisition_code', 'like', "%{$s}%")
                  ->orWhere('justification', 'like', "%{$s}%");
            });
        }

        $requisitions = $query->orderBy('created_at', 'desc')->paginate(15);
        $departments = Department::where('is_active', true)->get(['id', 'name']);
        $positions = Position::where('is_active', true)->get(['id', 'title']);

        return view('admin.pages.requisitions.index', compact('requisitions', 'departments', 'positions'));
    }

    public function create()
    {
        $departments = Department::where('is_active', true)->get(['id', 'name']);
        $positions = Position::where('is_active', true)->get(['id', 'title']);
        $branches = Branch::where('is_active', true)->get(['id', 'name']);

        return view('admin.pages.requisitions.create', compact('departments', 'positions', 'branches'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'position_id' => 'required|exists:positions,id',
            'branch_id' => 'nullable|exists:branches,id',
            'number_of_positions' => 'required|integer|min:1',
            'justification' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        Requisition::create([
            'department_id' => $request->department_id,
            'position_id' => $request->position_id,
            'branch_id' => $request->branch_id,
            'number_of_positions' => $request->number_of_positions,
            'justification' => $request->justification,
            'notes' => $request->notes,
            'status' => Requisition::STATUS_PENDING,
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('admin.requisitions.index')->with('success', 'تم إنشاء طلب التعيين بنجاح.');
    }

    public function show(Requisition $requisition)
    {
        $requisition->load(['department', 'position', 'branch', 'creator', 'approvedBy', 'jobVacancy']);
        return view('admin.pages.requisitions.show', compact('requisition'));
    }

    public function edit(Requisition $requisition)
    {
        if ($requisition->status !== Requisition::STATUS_PENDING) {
            return redirect()->route('admin.requisitions.show', $requisition)
                ->with('error', 'لا يمكن تعديل طلب تمت الموافقة عليه أو رفضه.');
        }
        $departments = Department::where('is_active', true)->get(['id', 'name']);
        $positions = Position::where('is_active', true)->get(['id', 'title']);
        $branches = Branch::where('is_active', true)->get(['id', 'name']);
        return view('admin.pages.requisitions.edit', compact('requisition', 'departments', 'positions', 'branches'));
    }

    public function update(Request $request, Requisition $requisition)
    {
        if ($requisition->status !== Requisition::STATUS_PENDING) {
            return redirect()->route('admin.requisitions.index')->with('error', 'لا يمكن تعديل هذا الطلب.');
        }
        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'position_id' => 'required|exists:positions,id',
            'branch_id' => 'nullable|exists:branches,id',
            'number_of_positions' => 'required|integer|min:1',
            'justification' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        $requisition->update($request->only([
            'department_id', 'position_id', 'branch_id', 'number_of_positions', 'justification', 'notes'
        ]));

        return redirect()->route('admin.requisitions.show', $requisition)->with('success', 'تم تحديث طلب التعيين بنجاح.');
    }

    public function destroy(Requisition $requisition)
    {
        if ($requisition->job_vacancy_id) {
            return redirect()->back()->with('error', 'لا يمكن حذف طلب تمت الموافقة عليه وربطه بشاغر.');
        }
        $requisition->delete();
        return redirect()->route('admin.requisitions.index')->with('success', 'تم حذف طلب التعيين.');
    }

    public function approve(Request $request, Requisition $requisition)
    {
        if (!$requisition->isPending()) {
            return redirect()->back()->with('error', 'هذا الطلب غير قيد الانتظار.');
        }

        $position = $requisition->position;
        $department = $requisition->department;
        $title = $position->title . ' - ' . $department->name;
        $code = 'JV-' . strtoupper(Str::random(8));
        while (JobVacancy::where('code', $code)->exists()) {
            $code = 'JV-' . strtoupper(Str::random(8));
        }

        $vacancy = JobVacancy::create([
            'title' => $title,
            'title_ar' => $title,
            'code' => $code,
            'department_id' => $requisition->department_id,
            'position_id' => $requisition->position_id,
            'branch_id' => $requisition->branch_id,
            'employment_type' => 'full_time',
            'experience_level' => 'mid',
            'posted_date' => now()->toDateString(),
            'status' => 'draft',
            'number_of_positions' => $requisition->number_of_positions,
            'description' => $requisition->justification,
            'notes' => 'تم الإنشاء من طلب تعيين: ' . $requisition->requisition_code,
            'created_by' => auth()->id(),
        ]);

        $requisition->update([
            'status' => Requisition::STATUS_APPROVED,
            'approved_at' => now(),
            'approved_by' => auth()->id(),
            'job_vacancy_id' => $vacancy->id,
        ]);

        return redirect()->route('admin.requisitions.show', $requisition)
            ->with('success', 'تمت الموافقة على طلب التعيين وتم إنشاء شاغر وظيفي: ' . $vacancy->code);
    }

    public function reject(Request $request, Requisition $requisition)
    {
        if (!$requisition->isPending()) {
            return redirect()->back()->with('error', 'هذا الطلب غير قيد الانتظار.');
        }

        $request->validate(['rejection_reason' => 'nullable|string|max:1000']);

        $requisition->update([
            'status' => Requisition::STATUS_REJECTED,
            'approved_at' => now(),
            'approved_by' => auth()->id(),
            'rejection_reason' => $request->rejection_reason,
        ]);

        return redirect()->route('admin.requisitions.show', $requisition)->with('success', 'تم رفض طلب التعيين.');
    }
}
