<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ScheduledReport;
use Illuminate\Http\Request;

class ScheduledReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:report-view');
    }

    public function index()
    {
        $reports = ScheduledReport::orderByDesc('created_at')->paginate(20);

        return view('admin.pages.scheduled-reports.index', compact('reports'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'report_type' => 'required|string|in:employees,attendance,leaves,payroll',
            'frequency' => 'required|string|in:daily,weekly,monthly',
            'recipients' => 'required|string',
        ]);

        ScheduledReport::create([
            'name' => $data['name'],
            'report_type' => $data['report_type'],
            'frequency' => $data['frequency'],
            'recipients' => array_filter(array_map('trim', explode(',', $data['recipients']))),
            'is_active' => true,
            'created_by' => auth()->id(),
        ]);

        return back()->with('success', 'تم جدولة التقرير.');
    }

    public function destroy(string $id)
    {
        ScheduledReport::findOrFail($id)->delete();

        return back()->with('success', 'تم الحذف.');
    }
}
