<?php

namespace App\Http\Controllers\Admin;

use App\Models\TrainingRecord;
use App\Models\Training;
use App\Models\Employee;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TrainingRecordController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:training-record-list')->only('index');
        $this->middleware('permission:training-record-create')->only(['create', 'store']);
        $this->middleware('permission:training-record-edit')->only(['edit', 'update']);
        $this->middleware('permission:training-record-delete')->only('destroy');
        $this->middleware('permission:training-record-show')->only('show');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $recordsQuery = TrainingRecord::with(['training', 'employee.user']);

        // فلترة حسب الدورة
        if ($request->filled('training_id')) {
            $recordsQuery->where('training_id', $request->input('training_id'));
        }

        // فلترة حسب الموظف
        if ($request->filled('employee_id')) {
            $recordsQuery->where('employee_id', $request->input('employee_id'));
        }

        // فلترة حسب الحالة
        if ($request->filled('status')) {
            $recordsQuery->where('status', $request->input('status'));
        }

        $records = $recordsQuery->orderBy('created_at', 'desc')
            ->paginate(20);

        $trainings = Training::where('status', '!=', 'cancelled')->get();
        $employees = Employee::where('is_active', true)->with('user')->get();

        return view("admin.pages.training-records.index", compact("records", "trainings", "employees"));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $trainings = Training::where('status', '!=', 'cancelled')->get();
        $employees = Employee::where('is_active', true)->with('user')->get();
        
        return view("admin.pages.training-records.create", compact("trainings", "employees"));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'training_id' => 'required|exists:trainings,id',
            'employee_id' => 'required|exists:employees,id',
            'status' => 'required|in:registered,attending,completed,failed,cancelled',
            'registration_date' => 'nullable|date',
            'completion_date' => 'nullable|date',
            'score' => 'nullable|numeric|min:0|max:100',
            'feedback' => 'nullable|string',
            'evaluation' => 'nullable|string',
            'certificate_issued' => 'nullable|boolean',
            'certificate_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ], [
            'training_id.required' => 'الدورة التدريبية مطلوبة',
            'employee_id.required' => 'الموظف مطلوب',
            'status.required' => 'الحالة مطلوبة',
        ]);

        // التحقق من عدم وجود تسجيل مسبق
        $existingRecord = TrainingRecord::where('training_id', $request->training_id)
            ->where('employee_id', $request->employee_id)
            ->first();

        if ($existingRecord) {
            return back()->withInput()->with('error', 'الموظف مسجل بالفعل في هذه الدورة');
        }

        // التحقق من إمكانية التسجيل في الدورة
        $training = Training::findOrFail($request->training_id);
        if (!$training->canRegister()) {
            return back()->withInput()->with('error', 'لا يمكن التسجيل في هذه الدورة');
        }

        TrainingRecord::create([
            'training_id' => $request->training_id,
            'employee_id' => $request->employee_id,
            'status' => $request->status,
            'registration_date' => $request->registration_date ?? now(),
            'completion_date' => $request->completion_date,
            'score' => $request->score,
            'feedback' => $request->feedback,
            'evaluation' => $request->evaluation,
            'certificate_issued' => $request->certificate_issued ?? false,
            'certificate_date' => $request->certificate_date,
            'notes' => $request->notes,
            'created_by' => auth()->id(),
        ]);

        return redirect()->route("admin.training-records.index")->with("success", "تم إضافة سجل التدريب بنجاح");
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $record = TrainingRecord::with(['training', 'employee.user', 'creator'])->findOrFail($id);
        return view("admin.pages.training-records.show", compact("record"));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $record = TrainingRecord::findOrFail($id);
        $trainings = Training::where('status', '!=', 'cancelled')->get();
        $employees = Employee::where('is_active', true)->with('user')->get();
        
        return view("admin.pages.training-records.edit", compact("record", "trainings", "employees"));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $record = TrainingRecord::findOrFail($id);

        $request->validate([
            'training_id' => 'required|exists:trainings,id',
            'employee_id' => 'required|exists:employees,id',
            'status' => 'required|in:registered,attending,completed,failed,cancelled',
            'registration_date' => 'nullable|date',
            'completion_date' => 'nullable|date',
            'score' => 'nullable|numeric|min:0|max:100',
            'feedback' => 'nullable|string',
            'evaluation' => 'nullable|string',
            'certificate_issued' => 'nullable|boolean',
            'certificate_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ], [
            'training_id.required' => 'الدورة التدريبية مطلوبة',
            'employee_id.required' => 'الموظف مطلوب',
            'status.required' => 'الحالة مطلوبة',
        ]);

        // التحقق من عدم وجود تسجيل مسبق (إذا تم تغيير الدورة أو الموظف)
        if ($record->training_id != $request->training_id || $record->employee_id != $request->employee_id) {
            $existingRecord = TrainingRecord::where('training_id', $request->training_id)
                ->where('employee_id', $request->employee_id)
                ->where('id', '!=', $id)
                ->first();

            if ($existingRecord) {
                return back()->withInput()->with('error', 'الموظف مسجل بالفعل في هذه الدورة');
            }
        }

        $record->update([
            'training_id' => $request->training_id,
            'employee_id' => $request->employee_id,
            'status' => $request->status,
            'registration_date' => $request->registration_date,
            'completion_date' => $request->completion_date,
            'score' => $request->score,
            'feedback' => $request->feedback,
            'evaluation' => $request->evaluation,
            'certificate_issued' => $request->certificate_issued ?? false,
            'certificate_date' => $request->certificate_date,
            'notes' => $request->notes,
        ]);

        return redirect()->route('admin.training-records.index')->with('success', 'تم تحديث سجل التدريب بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $record = TrainingRecord::findOrFail($request->id);
        $record->delete();

        return redirect()->route("admin.training-records.index")->with("success", "تم حذف سجل التدريب بنجاح");
    }
}
