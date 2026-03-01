<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Survey;
use App\Models\SurveyQuestion;
use Illuminate\Http\Request;

class SurveyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:survey-list')->only('index');
        $this->middleware('permission:survey-create')->only(['create', 'store']);
        $this->middleware('permission:survey-edit')->only(['edit', 'update']);
        $this->middleware('permission:survey-delete')->only('destroy');
        $this->middleware('permission:survey-show')->only('show');
    }

    public function index(Request $request)
    {
        $query = Survey::with('creator')->withCount('responses');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%$search%")
                  ->orWhere('survey_code', 'like', "%$search%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        $surveys = $query->latest()->paginate(15);

        return view('admin.pages.surveys.index', compact('surveys'));
    }

    public function create()
    {
        return view('admin.pages.surveys.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'title_ar' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:satisfaction,climate,engagement,exit,custom',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_anonymous' => 'boolean',
            'target_audience' => 'required|in:all,department,branch,position,custom',
            'target_ids' => 'nullable|array',
        ]);

        $data = $request->all();
        $data['created_by'] = auth()->id();
        $data['status'] = 'draft';
        $data['total_responses'] = 0;

        $survey = Survey::create($data);

        // إضافة الأسئلة إذا كانت موجودة
        if ($request->filled('questions')) {
            foreach ($request->questions as $index => $question) {
                SurveyQuestion::create([
                    'survey_id' => $survey->id,
                    'question_text' => $question['text'],
                    'question_text_ar' => $question['text_ar'] ?? null,
                    'question_type' => $question['type'],
                    'options' => $question['options'] ?? null,
                    'question_order' => $index + 1,
                    'is_required' => $question['required'] ?? true,
                    'help_text' => $question['help_text'] ?? null,
                ]);
            }
        }

        return redirect()->route('admin.surveys.index')->with('success', 'تم إنشاء الاستبيان بنجاح.');
    }

    public function show(string $id)
    {
        $survey = Survey::with(['questions', 'responses.employee', 'creator'])->findOrFail($id);
        return view('admin.pages.surveys.show', compact('survey'));
    }

    public function edit(string $id)
    {
        $survey = Survey::with('questions')->findOrFail($id);
        return view('admin.pages.surveys.edit', compact('survey'));
    }

    public function update(Request $request, string $id)
    {
        $survey = Survey::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'title_ar' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:satisfaction,climate,engagement,exit,custom',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status' => 'required|in:draft,active,closed,cancelled',
            'is_anonymous' => 'boolean',
            'target_audience' => 'required|in:all,department,branch,position,custom',
            'target_ids' => 'nullable|array',
        ]);

        $survey->update($request->all());

        return redirect()->route('admin.surveys.index')->with('success', 'تم تحديث الاستبيان بنجاح.');
    }

    public function destroy(string $id)
    {
        $survey = Survey::findOrFail($id);

        if ($survey->responses()->count() > 0) {
            return redirect()->back()->with('error', 'لا يمكن حذف الاستبيان لأنه يحتوي على ردود.');
        }

        $survey->delete();

        return redirect()->route('admin.surveys.index')->with('success', 'تم حذف الاستبيان بنجاح.');
    }
}
