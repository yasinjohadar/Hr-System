<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailTemplate;
use Illuminate\Http\Request;

class EmailTemplateController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:email-template-list')->only('index');
        $this->middleware('permission:email-template-create')->only(['create', 'store']);
        $this->middleware('permission:email-template-edit')->only(['edit', 'update']);
        $this->middleware('permission:email-template-delete')->only('destroy');
        $this->middleware('permission:email-template-show')->only('show');
    }

    public function index(Request $request)
    {
        $query = EmailTemplate::with('creator');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('code', 'like', "%$search%");
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->input('is_active'));
        }

        $templates = $query->latest()->paginate(15);

        return view('admin.pages.email-templates.index', compact('templates'));
    }

    public function create()
    {
        return view('admin.pages.email-templates.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'code' => 'required|string|max:50|unique:email_templates,code',
            'subject' => 'required|string|max:255',
            'subject_ar' => 'nullable|string|max:255',
            'body' => 'required|string',
            'body_ar' => 'nullable|string',
            'type' => 'required|in:welcome,leave_approved,leave_rejected,salary,birthday,anniversary,custom',
            'variables' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        $data = $request->all();
        $data['created_by'] = auth()->id();

        EmailTemplate::create($data);

        return redirect()->route('admin.email-templates.index')->with('success', 'تم إضافة قالب البريد بنجاح.');
    }

    public function show(string $id)
    {
        $template = EmailTemplate::with('creator')->findOrFail($id);
        return view('admin.pages.email-templates.show', compact('template'));
    }

    public function edit(string $id)
    {
        $template = EmailTemplate::findOrFail($id);
        return view('admin.pages.email-templates.edit', compact('template'));
    }

    public function update(Request $request, string $id)
    {
        $template = EmailTemplate::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'code' => 'required|string|max:50|unique:email_templates,code,' . $id,
            'subject' => 'required|string|max:255',
            'subject_ar' => 'nullable|string|max:255',
            'body' => 'required|string',
            'body_ar' => 'nullable|string',
            'type' => 'required|in:welcome,leave_approved,leave_rejected,salary,birthday,anniversary,custom',
            'variables' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        $template->update($request->all());

        return redirect()->route('admin.email-templates.index')->with('success', 'تم تحديث قالب البريد بنجاح.');
    }

    public function destroy(string $id)
    {
        $template = EmailTemplate::findOrFail($id);
        $template->delete();

        return redirect()->route('admin.email-templates.index')->with('success', 'تم حذف قالب البريد بنجاح.');
    }
}
