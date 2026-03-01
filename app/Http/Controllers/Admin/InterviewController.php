<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Interview;
use App\Models\JobApplication;
use App\Models\Employee;
use Illuminate\Http\Request;

class InterviewController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:interview-list')->only('index');
        $this->middleware('permission:interview-create')->only(['create', 'store']);
        $this->middleware('permission:interview-edit')->only(['edit', 'update']);
        $this->middleware('permission:interview-delete')->only('destroy');
        $this->middleware('permission:interview-show')->only('show');
    }

    public function index(Request $request)
    {
        $query = Interview::with(['jobApplication', 'candidate', 'jobVacancy', 'scheduler']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->whereHas('candidate', function ($q) use ($search) {
                $q->where('full_name', 'like', "%$search%");
            })->orWhereHas('jobVacancy', function ($q) use ($search) {
                $q->where('title', 'like', "%$search%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('job_application_id')) {
            $query->where('job_application_id', $request->input('job_application_id'));
        }

        if ($request->filled('interview_date')) {
            $query->whereDate('interview_date', $request->input('interview_date'));
        }

        $interviews = $query->orderBy('interview_date', 'desc')->orderBy('interview_time', 'desc')->paginate(20);
        $applications = JobApplication::where('status', '!=', 'rejected')->get();

        return view('admin.pages.interviews.index', compact('interviews', 'applications'));
    }

    public function create()
    {
        $applications = JobApplication::whereIn('status', ['shortlisted', 'reviewing', 'interviewed'])
            ->with(['candidate', 'jobVacancy'])
            ->get();
        $employees = Employee::where('is_active', true)->get();

        return view('admin.pages.interviews.create', compact('applications', 'employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'job_application_id' => 'required|exists:job_applications,id',
            'type' => 'required|in:phone,video,in_person,panel,technical,hr,final',
            'round' => 'required|in:first,second,third,final',
            'interview_date' => 'required|date',
            'interview_time' => 'required',
            'duration' => 'nullable|integer|min:15',
            'location' => 'nullable|string|max:255',
            'meeting_link' => 'nullable|url',
            'interviewers' => 'nullable|array',
        ]);

        $application = JobApplication::findOrFail($request->job_application_id);

        $data = $request->all();
        $data['candidate_id'] = $application->candidate_id;
        $data['job_vacancy_id'] = $application->job_vacancy_id;
        $data['scheduled_by'] = auth()->id();
        $data['created_by'] = auth()->id();

        // تحويل interview_time إلى Carbon
        if ($request->interview_time) {
            $data['interview_time'] = \Carbon\Carbon::parse($request->interview_date . ' ' . $request->interview_time);
        }

        Interview::create($data);

        // تحديث حالة طلب التوظيف
        if ($application->status == 'shortlisted' || $application->status == 'reviewing') {
            $application->update(['status' => 'interviewed']);
        }

        return redirect()->route('admin.interviews.index')->with('success', 'تم جدولة المقابلة بنجاح');
    }

    public function show(string $id)
    {
        $interview = Interview::with(['jobApplication', 'candidate', 'jobVacancy', 'scheduler', 'conductor'])
            ->findOrFail($id);
        return view('admin.pages.interviews.show', compact('interview'));
    }

    public function edit(string $id)
    {
        $interview = Interview::findOrFail($id);
        $applications = JobApplication::whereIn('status', ['shortlisted', 'reviewing', 'interviewed'])
            ->with(['candidate', 'jobVacancy'])
            ->get();
        $employees = Employee::where('is_active', true)->get();

        return view('admin.pages.interviews.edit', compact('interview', 'applications', 'employees'));
    }

    public function update(Request $request, string $id)
    {
        $interview = Interview::findOrFail($id);

        $request->validate([
            'status' => 'required|in:scheduled,confirmed,in_progress,completed,cancelled,rescheduled,no_show',
            'interview_date' => 'required|date',
            'interview_time' => 'required',
            'duration' => 'nullable|integer|min:15',
            'overall_rating' => 'nullable|integer|min:1|max:5',
            'recommendation_status' => 'nullable|in:hire,maybe,reject,pending',
            'cancellation_reason' => 'nullable|string|required_if:status,cancelled',
        ]);

        $data = $request->all();

        // تحويل interview_time
        if ($request->interview_time) {
            $data['interview_time'] = \Carbon\Carbon::parse($request->interview_date . ' ' . $request->interview_time);
        }

        // إذا كانت المقابلة مكتملة، تحديث من أجراها
        if ($request->status == 'completed') {
            $data['conducted_by'] = auth()->id();
        }

        $interview->update($data);

        // تحديث حالة طلب التوظيف بناءً على التوصية
        if ($request->status == 'completed' && $request->recommendation_status) {
            $application = $interview->jobApplication;
            if ($request->recommendation_status == 'hire') {
                $application->update(['status' => 'offered']);
            } elseif ($request->recommendation_status == 'reject') {
                $application->update(['status' => 'rejected', 'rejection_reason' => 'نتيجة المقابلة']);
            }
        }

        return redirect()->route('admin.interviews.index')->with('success', 'تم تحديث المقابلة بنجاح');
    }

    public function destroy(Request $request)
    {
        $interview = Interview::findOrFail($request->id);
        $interview->delete();

        return redirect()->route('admin.interviews.index')->with('success', 'تم حذف المقابلة بنجاح');
    }
}
