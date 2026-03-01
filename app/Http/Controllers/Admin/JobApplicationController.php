<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobApplication;
use App\Models\JobVacancy;
use App\Models\Candidate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class JobApplicationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:job-application-list')->only('index');
        $this->middleware('permission:job-application-create')->only(['create', 'store']);
        $this->middleware('permission:job-application-edit')->only(['edit', 'update']);
        $this->middleware('permission:job-application-delete')->only('destroy');
        $this->middleware('permission:job-application-show')->only('show');
    }

    public function index(Request $request)
    {
        $query = JobApplication::with(['jobVacancy', 'candidate', 'reviewer']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->whereHas('candidate', function ($q) use ($search) {
                $q->where('full_name', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%");
            })->orWhereHas('jobVacancy', function ($q) use ($search) {
                $q->where('title', 'like', "%$search%")
                  ->orWhere('code', 'like', "%$search%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('job_vacancy_id')) {
            $query->where('job_vacancy_id', $request->input('job_vacancy_id'));
        }

        $applications = $query->orderBy('application_date', 'desc')->paginate(20);
        $vacancies = JobVacancy::where('is_active', true)->get();

        return view('admin.pages.job-applications.index', compact('applications', 'vacancies'));
    }

    public function create()
    {
        $vacancies = JobVacancy::where('is_active', true)->where('status', 'published')->get();
        $candidates = Candidate::where('is_active', true)->get();
        return view('admin.pages.job-applications.create', compact('vacancies', 'candidates'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'job_vacancy_id' => 'required|exists:job_vacancies,id',
            'candidate_id' => 'required|exists:candidates,id',
            'application_date' => 'required|date',
            'source' => 'required|in:website,linkedin,referral,indeed,other',
            'cv_path' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
            'cover_letter_path' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
        ]);

        // التحقق من عدم التقديم المتكرر
        $existing = JobApplication::where('job_vacancy_id', $request->job_vacancy_id)
            ->where('candidate_id', $request->candidate_id)
            ->first();

        if ($existing) {
            return back()->withErrors(['candidate_id' => 'هذا المرشح قد تقدم بالفعل لهذه الوظيفة'])->withInput();
        }

        $data = $request->all();
        $data['created_by'] = auth()->id();

        // رفع الملفات
        if ($request->hasFile('cv_path')) {
            $data['cv_path'] = $request->file('cv_path')->store('applications/cvs', 'public');
        }

        if ($request->hasFile('cover_letter_path')) {
            $data['cover_letter_path'] = $request->file('cover_letter_path')->store('applications/cover-letters', 'public');
        }

        $application = JobApplication::create($data);

        // تحديث عدد المتقدمين للوظيفة
        $application->jobVacancy->increment('applications_count');

        return redirect()->route('admin.job-applications.index')->with('success', 'تم إضافة طلب التوظيف بنجاح');
    }

    public function show(string $id)
    {
        $application = JobApplication::with(['jobVacancy', 'candidate', 'reviewer', 'interviews'])
            ->findOrFail($id);
        return view('admin.pages.job-applications.show', compact('application'));
    }

    public function edit(string $id)
    {
        $application = JobApplication::findOrFail($id);
        $vacancies = JobVacancy::where('is_active', true)->get();
        $candidates = Candidate::where('is_active', true)->get();
        return view('admin.pages.job-applications.edit', compact('application', 'vacancies', 'candidates'));
    }

    public function update(Request $request, string $id)
    {
        $application = JobApplication::findOrFail($id);

        $request->validate([
            'status' => 'required|in:pending,reviewing,shortlisted,interviewed,offered,accepted,rejected,withdrawn',
            'rating' => 'nullable|integer|min:1|max:5',
            'reviewer_notes' => 'nullable|string',
            'rejection_reason' => 'nullable|string|required_if:status,rejected',
        ]);

        $data = $request->all();

        if ($request->status == 'rejected') {
            $data['rejection_date'] = now();
            $data['reviewed_by'] = auth()->id();
        }

        if ($request->status == 'accepted') {
            $data['reviewed_by'] = auth()->id();
        }

        $application->update($data);

        return redirect()->route('admin.job-applications.index')->with('success', 'تم تحديث طلب التوظيف بنجاح');
    }

    public function destroy(Request $request)
    {
        $application = JobApplication::findOrFail($request->id);
        
        // حذف الملفات
        if ($application->cv_path) {
            Storage::disk('public')->delete($application->cv_path);
        }
        if ($application->cover_letter_path) {
            Storage::disk('public')->delete($application->cover_letter_path);
        }

        // تقليل عدد المتقدمين
        $application->jobVacancy->decrement('applications_count');

        $application->delete();

        return redirect()->route('admin.job-applications.index')->with('success', 'تم حذف طلب التوظيف بنجاح');
    }
}
