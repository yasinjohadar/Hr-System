<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\Employee;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:ticket-list')->only('index');
        $this->middleware('permission:ticket-create')->only(['create', 'store']);
        $this->middleware('permission:ticket-edit')->only(['edit', 'update']);
        $this->middleware('permission:ticket-delete')->only('destroy');
        $this->middleware('permission:ticket-show')->only('show');
        $this->middleware('permission:ticket-assign')->only(['assign', 'resolve']);
    }

    public function index(Request $request)
    {
        $query = Ticket::with(['employee', 'assignedTo', 'creator'])->withCount('comments');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%$search%")
                  ->orWhere('ticket_code', 'like', "%$search%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('category')) {
            $query->where('category', $request->input('category'));
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->input('priority'));
        }

        $tickets = $query->latest()->paginate(15);
        $employees = Employee::where('is_active', true)->get();

        return view('admin.pages.tickets.index', compact('tickets', 'employees'));
    }

    public function create()
    {
        $employees = Employee::where('is_active', true)->get();
        return view('admin.pages.tickets.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|in:technical,hr,it,facilities,other',
            'priority' => 'required|in:low,medium,high,urgent',
            'employee_id' => 'nullable|exists:employees,id',
        ]);

        $data = $request->all();
        $data['created_by'] = auth()->id();
        $data['status'] = 'open';

        Ticket::create($data);

        return redirect()->route('admin.tickets.index')->with('success', 'تم إنشاء التذكرة بنجاح.');
    }

    public function show(string $id)
    {
        $ticket = Ticket::with([
            'employee',
            'assignedTo',
            'comments.user',
            'comments.employee',
            'creator'
        ])->findOrFail($id);

        $employees = Employee::where('is_active', true)->get();

        return view('admin.pages.tickets.show', compact('ticket', 'employees'));
    }

    public function edit(string $id)
    {
        $ticket = Ticket::findOrFail($id);
        $employees = Employee::where('is_active', true)->get();

        return view('admin.pages.tickets.edit', compact('ticket', 'employees'));
    }

    public function update(Request $request, string $id)
    {
        $ticket = Ticket::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|in:technical,hr,it,facilities,other',
            'priority' => 'required|in:low,medium,high,urgent',
            'status' => 'required|in:open,in_progress,resolved,closed,cancelled',
            'assigned_to' => 'nullable|exists:employees,id',
        ]);

        $ticket->update($request->all());

        return redirect()->route('admin.tickets.show', $ticket->id)->with('success', 'تم تحديث التذكرة بنجاح.');
    }

    public function destroy(string $id)
    {
        $ticket = Ticket::findOrFail($id);
        $ticket->delete();

        return redirect()->route('admin.tickets.index')->with('success', 'تم حذف التذكرة بنجاح.');
    }

    public function assign(Request $request, string $id)
    {
        $ticket = Ticket::findOrFail($id);

        $request->validate([
            'assigned_to' => 'required|exists:employees,id',
        ]);

        $ticket->update([
            'assigned_to' => $request->assigned_to,
            'status' => 'in_progress'
        ]);

        return redirect()->back()->with('success', 'تم تعيين التذكرة بنجاح.');
    }

    public function resolve(Request $request, string $id)
    {
        $ticket = Ticket::findOrFail($id);

        $request->validate([
            'resolution_notes' => 'required|string',
        ]);

        $ticket->update([
            'status' => 'resolved',
            'resolved_at' => now(),
            'resolution_notes' => $request->resolution_notes
        ]);

        return redirect()->back()->with('success', 'تم حل التذكرة بنجاح.');
    }
}
