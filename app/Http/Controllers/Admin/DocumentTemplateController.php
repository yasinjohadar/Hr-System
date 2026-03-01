<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DocumentTemplate;
use Illuminate\Http\Request;

class DocumentTemplateController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:document-template-list')->only('index');
        $this->middleware('permission:document-template-create')->only(['create', 'store']);
        $this->middleware('permission:document-template-edit')->only(['edit', 'update']);
        $this->middleware('permission:document-template-delete')->only('destroy');
        $this->middleware('permission:document-template-show')->only('show');
    }

    public function index(Request $request)
    {
        $query = DocumentTemplate::with('creator');

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

        return view('admin.pages.document-templates.index', compact('templates'));
    }

    public function create()
    {
        return view('admin.pages.document-templates.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'code' => 'required|string|max:50|unique:document_templates,code',
            'description' => 'nullable|string',
            'type' => 'required|in:contract,letter,certificate,report,custom',
            'content' => 'required|string',
            'content_ar' => 'nullable|string',
            'variables' => 'nullable|array',
            'file_format' => 'required|in:pdf,docx,html',
            'is_active' => 'boolean',
        ]);

        $data = $request->all();
        $data['created_by'] = auth()->id();

        DocumentTemplate::create($data);

        return redirect()->route('admin.document-templates.index')->with('success', 'تم إضافة قالب المستند بنجاح.');
    }

    public function show(string $id)
    {
        $template = DocumentTemplate::with('creator')->findOrFail($id);
        return view('admin.pages.document-templates.show', compact('template'));
    }

    public function edit(string $id)
    {
        $template = DocumentTemplate::findOrFail($id);
        return view('admin.pages.document-templates.edit', compact('template'));
    }

    public function update(Request $request, string $id)
    {
        $template = DocumentTemplate::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'code' => 'required|string|max:50|unique:document_templates,code,' . $id,
            'description' => 'nullable|string',
            'type' => 'required|in:contract,letter,certificate,report,custom',
            'content' => 'required|string',
            'content_ar' => 'nullable|string',
            'variables' => 'nullable|array',
            'file_format' => 'required|in:pdf,docx,html',
            'is_active' => 'boolean',
        ]);

        $template->update($request->all());

        return redirect()->route('admin.document-templates.index')->with('success', 'تم تحديث قالب المستند بنجاح.');
    }

    public function destroy(string $id)
    {
        $template = DocumentTemplate::findOrFail($id);
        $template->delete();

        return redirect()->route('admin.document-templates.index')->with('success', 'تم حذف قالب المستند بنجاح.');
    }
}
