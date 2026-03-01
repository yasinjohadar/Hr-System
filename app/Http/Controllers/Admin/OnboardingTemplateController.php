<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OnboardingTemplate;
use Illuminate\Http\Request;

class OnboardingTemplateController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:onboarding-template-list')->only('index');
        $this->middleware('permission:onboarding-template-create')->only(['create', 'store']);
        $this->middleware('permission:onboarding-template-edit')->only(['edit', 'update']);
        $this->middleware('permission:onboarding-template-delete')->only('destroy');
        $this->middleware('permission:onboarding-template-show')->only('show');
    }

    public function index(Request $request)
    {
        $query = OnboardingTemplate::with('creator')->withCount('processes');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('name_ar', 'like', "%$search%");
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->input('is_active'));
        }

        $templates = $query->latest()->paginate(15);

        return view('admin.pages.onboarding-templates.index', compact('templates'));
    }

    public function create()
    {
        return view('admin.pages.onboarding-templates.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:standard,executive,contractor,intern,custom',
            'steps' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        $data = $request->all();
        $data['created_by'] = auth()->id();

        OnboardingTemplate::create($data);

        return redirect()->route('admin.onboarding-templates.index')->with('success', 'تم إضافة قالب الاستقبال بنجاح.');
    }

    public function show(string $id)
    {
        $template = OnboardingTemplate::with(['creator', 'processes'])->findOrFail($id);
        return view('admin.pages.onboarding-templates.show', compact('template'));
    }

    public function edit(string $id)
    {
        $template = OnboardingTemplate::findOrFail($id);
        return view('admin.pages.onboarding-templates.edit', compact('template'));
    }

    public function update(Request $request, string $id)
    {
        $template = OnboardingTemplate::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:standard,executive,contractor,intern,custom',
            'steps' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        $template->update($request->all());

        return redirect()->route('admin.onboarding-templates.index')->with('success', 'تم تحديث قالب الاستقبال بنجاح.');
    }

    public function destroy(string $id)
    {
        $template = OnboardingTemplate::findOrFail($id);

        if ($template->processes()->count() > 0) {
            return redirect()->back()->with('error', 'لا يمكن حذف القالب لأنه مستخدم في عمليات استقبال.');
        }

        $template->delete();

        return redirect()->route('admin.onboarding-templates.index')->with('success', 'تم حذف قالب الاستقبال بنجاح.');
    }
}
