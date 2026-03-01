<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FeedbackRequest;
use App\Models\Employee;
use Illuminate\Http\Request;

class FeedbackRequestController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:feedback-request-list')->only('index');
        $this->middleware('permission:feedback-request-create')->only(['create', 'store']);
        $this->middleware('permission:feedback-request-edit')->only(['edit', 'update']);
        $this->middleware('permission:feedback-request-delete')->only('destroy');
        $this->middleware('permission:feedback-request-show')->only('show');
    }

    public function index(Request $request)
    {
        $query = FeedbackRequest::with(['employee', 'creator'])->withCount('responses');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('request_code', 'like', "%$search%")
                  ->orWhereHas('employee', function($q) use ($search) {
                      $q->where('full_name', 'like', "%$search%");
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('feedback_type')) {
            $query->where('feedback_type', $request->input('feedback_type'));
        }

        $requests = $query->latest()->paginate(15);
        $employees = Employee::where('is_active', true)->get();

        return view('admin.pages.feedback-requests.index', compact('requests', 'employees'));
    }

    public function create()
    {
        $employees = Employee::where('is_active', true)->get();
        return view('admin.pages.feedback-requests.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'feedback_type' => 'required|in:360_degree,peer,subordinate,self,custom',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'instructions' => 'nullable|string',
            'is_anonymous' => 'boolean',
        ]);

        $data = $request->all();
        $data['created_by'] = auth()->id();
        $data['status'] = 'draft';

        FeedbackRequest::create($data);

        return redirect()->route('admin.feedback-requests.index')->with('success', 'تم إنشاء طلب التقييم بنجاح.');
    }

    public function show(string $id)
    {
        $feedbackRequest = FeedbackRequest::with([
            'employee',
            'responses.respondent',
            'creator'
        ])->findOrFail($id);

        return view('admin.pages.feedback-requests.show', compact('feedbackRequest'));
    }

    public function edit(string $id)
    {
        $feedbackRequest = FeedbackRequest::findOrFail($id);
        $employees = Employee::where('is_active', true)->get();

        return view('admin.pages.feedback-requests.edit', compact('feedbackRequest', 'employees'));
    }

    public function update(Request $request, string $id)
    {
        $feedbackRequest = FeedbackRequest::findOrFail($id);

        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'feedback_type' => 'required|in:360_degree,peer,subordinate,self,custom',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status' => 'required|in:draft,active,in_progress,completed,cancelled',
            'instructions' => 'nullable|string',
            'is_anonymous' => 'boolean',
        ]);

        $feedbackRequest->update($request->all());

        return redirect()->route('admin.feedback-requests.index')->with('success', 'تم تحديث طلب التقييم بنجاح.');
    }

    public function destroy(string $id)
    {
        $feedbackRequest = FeedbackRequest::findOrFail($id);
        $feedbackRequest->delete();

        return redirect()->route('admin.feedback-requests.index')->with('success', 'تم حذف طلب التقييم بنجاح.');
    }
}
