<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Department;
use App\Models\Branch;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:announcement-list')->only('index');
        $this->middleware('permission:announcement-create')->only(['create', 'store']);
        $this->middleware('permission:announcement-edit')->only(['edit', 'update']);
        $this->middleware('permission:announcement-delete')->only('destroy');
        $this->middleware('permission:announcement-show')->only('show');
    }

    public function index(Request $request)
    {
        $query = Announcement::with(['creator', 'department', 'branch'])->latest();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%$search%")
                  ->orWhere('content', 'like', "%$search%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $announcements = $query->paginate(15);

        return view('admin.pages.announcements.index', compact('announcements'));
    }

    public function create()
    {
        $departments = Department::where('is_active', true)->get();
        $branches = Branch::where('is_active', true)->get();
        return view('admin.pages.announcements.create', compact('departments', 'branches'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'publish_date' => 'nullable|date',
            'expiry_date' => 'nullable|date|after_or_equal:publish_date',
            'status' => 'required|in:draft,published,archived',
            'target_type' => 'required|in:all,department,branch',
            'department_id' => 'nullable|required_if:target_type,department|exists:departments,id',
            'branch_id' => 'nullable|required_if:target_type,branch|exists:branches,id',
        ]);

        $data = $request->only([
            'title', 'content', 'publish_date', 'expiry_date', 'status',
            'target_type', 'department_id', 'branch_id',
        ]);
        $data['created_by'] = auth()->id();

        Announcement::create($data);

        return redirect()->route('admin.announcements.index')
            ->with('success', 'تم إنشاء الإعلان بنجاح.');
    }

    public function show(Announcement $announcement)
    {
        $announcement->load(['creator', 'department', 'branch']);
        return view('admin.pages.announcements.show', compact('announcement'));
    }

    public function edit(Announcement $announcement)
    {
        $departments = Department::where('is_active', true)->get();
        $branches = Branch::where('is_active', true)->get();
        return view('admin.pages.announcements.edit', compact('announcement', 'departments', 'branches'));
    }

    public function update(Request $request, Announcement $announcement)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'publish_date' => 'nullable|date',
            'expiry_date' => 'nullable|date',
            'status' => 'required|in:draft,published,archived',
            'target_type' => 'required|in:all,department,branch',
            'department_id' => 'nullable|required_if:target_type,department|exists:departments,id',
            'branch_id' => 'nullable|required_if:target_type,branch|exists:branches,id',
        ]);

        $data = $request->only([
            'title', 'content', 'publish_date', 'expiry_date', 'status',
            'target_type', 'department_id', 'branch_id',
        ]);

        $announcement->update($data);

        return redirect()->route('admin.announcements.index')
            ->with('success', 'تم تحديث الإعلان بنجاح.');
    }

    public function destroy(Announcement $announcement)
    {
        $announcement->delete();
        return redirect()->route('admin.announcements.index')
            ->with('success', 'تم حذف الإعلان بنجاح.');
    }
}
