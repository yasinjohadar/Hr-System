<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobVacancy;
use App\Models\Department;
use App\Models\Position;
use App\Models\Branch;
use App\Models\Currency;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class JobVacancyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:job-vacancy-list')->only('index');
        $this->middleware('permission:job-vacancy-create')->only(['create', 'store']);
        $this->middleware('permission:job-vacancy-edit')->only(['edit', 'update']);
        $this->middleware('permission:job-vacancy-delete')->only('destroy');
        $this->middleware('permission:job-vacancy-show')->only('show');
    }

    public function index(Request $request)
    {
        $query = JobVacancy::with(['department', 'position', 'branch', 'currency'])
            ->withCount('applications');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%$search%")
                  ->orWhere('title_ar', 'like', "%$search%")
                  ->orWhere('code', 'like', "%$search%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->input('department_id'));
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->input('is_active'));
        }

        $vacancies = $query->orderBy('posted_date', 'desc')->paginate(20);
        $departments = Department::where('is_active', true)->get();

        return view('admin.pages.job-vacancies.index', compact('vacancies', 'departments'));
    }

    public function create()
    {
        $departments = Department::where('is_active', true)->get();
        $positions = Position::where('is_active', true)->get();
        $branches = Branch::where('is_active', true)->get();
        $currencies = Currency::where('is_active', true)->get();
        $employees = Employee::where('is_active', true)->get();

        return view('admin.pages.job-vacancies.create', compact('departments', 'positions', 'branches', 'currencies', 'employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:job_vacancies,code',
            'department_id' => 'nullable|exists:departments,id',
            'position_id' => 'nullable|exists:positions,id',
            'branch_id' => 'nullable|exists:branches,id',
            'employment_type' => 'required|in:full_time,part_time,contract,intern,freelance',
            'experience_level' => 'required|in:entry,junior,mid,senior,lead,executive',
            'posted_date' => 'required|date',
            'closing_date' => 'nullable|date|after:posted_date',
            'status' => 'required|in:draft,published,closed,filled,cancelled',
            'number_of_positions' => 'required|integer|min:1',
            'min_salary' => 'nullable|numeric|min:0',
            'max_salary' => 'nullable|numeric|min:0|gte:min_salary',
        ]);

        $data = $request->all();
        $data['created_by'] = auth()->id();
        if (!$data['code']) {
            $data['code'] = 'JV-' . strtoupper(Str::random(8));
        }

        JobVacancy::create($data);

        return redirect()->route('admin.job-vacancies.index')->with('success', 'تم إضافة الوظيفة الشاغرة بنجاح');
    }

    public function show(string $id)
    {
        $vacancy = JobVacancy::with(['department', 'position', 'branch', 'currency', 'hiringManager', 'applications.candidate'])
            ->findOrFail($id);
        return view('admin.pages.job-vacancies.show', compact('vacancy'));
    }

    public function edit(string $id)
    {
        $vacancy = JobVacancy::findOrFail($id);
        $departments = Department::where('is_active', true)->get();
        $positions = Position::where('is_active', true)->get();
        $branches = Branch::where('is_active', true)->get();
        $currencies = Currency::where('is_active', true)->get();
        $employees = Employee::where('is_active', true)->get();

        return view('admin.pages.job-vacancies.edit', compact('vacancy', 'departments', 'positions', 'branches', 'currencies', 'employees'));
    }

    public function update(Request $request, string $id)
    {
        $vacancy = JobVacancy::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:job_vacancies,code,' . $id,
            'department_id' => 'nullable|exists:departments,id',
            'position_id' => 'nullable|exists:positions,id',
            'branch_id' => 'nullable|exists:branches,id',
            'employment_type' => 'required|in:full_time,part_time,contract,intern,freelance',
            'experience_level' => 'required|in:entry,junior,mid,senior,lead,executive',
            'posted_date' => 'required|date',
            'closing_date' => 'nullable|date|after:posted_date',
            'status' => 'required|in:draft,published,closed,filled,cancelled',
            'number_of_positions' => 'required|integer|min:1',
            'min_salary' => 'nullable|numeric|min:0',
            'max_salary' => 'nullable|numeric|min:0|gte:min_salary',
        ]);

        $vacancy->update($request->all());

        return redirect()->route('admin.job-vacancies.index')->with('success', 'تم تحديث الوظيفة الشاغرة بنجاح');
    }

    public function destroy(Request $request)
    {
        $vacancy = JobVacancy::findOrFail($request->id);
        $vacancy->delete();

        return redirect()->route('admin.job-vacancies.index')->with('success', 'تم حذف الوظيفة الشاغرة بنجاح');
    }
}
