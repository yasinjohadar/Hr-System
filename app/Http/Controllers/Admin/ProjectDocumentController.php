<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProjectDocumentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:project-edit');
    }

    public function store(Request $request, Project $project)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file' => 'required|file|max:15360|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt,jpg,jpeg,png,gif,zip',
        ]);

        $file = $request->file('file');
        $path = $file->store('project_documents/'.$project->id, 'public');

        ProjectDocument::create([
            'project_id' => $project->id,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'file_path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'file_size' => $file->getSize(),
            'uploaded_by' => auth()->id(),
        ]);

        return redirect()
            ->route('admin.projects.show', $project)
            ->with('success', 'تم رفع المستند.');
    }

    public function destroy(Project $project, ProjectDocument $document)
    {
        abort_unless((int) $document->project_id === (int) $project->id, 404);

        if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }

        $document->delete();

        return redirect()
            ->route('admin.projects.show', $project)
            ->with('success', 'تم حذف المستند.');
    }
}
