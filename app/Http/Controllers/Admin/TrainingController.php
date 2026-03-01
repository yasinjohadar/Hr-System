<?php

namespace App\Http\Controllers\Admin;

use App\Models\Training;
use App\Models\Employee;
use App\Models\Currency;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TrainingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:training-list')->only('index');
        $this->middleware('permission:training-create')->only(['create', 'store']);
        $this->middleware('permission:training-edit')->only(['edit', 'update']);
        $this->middleware('permission:training-delete')->only('destroy');
        $this->middleware('permission:training-show')->only('show');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $trainingsQuery = Training::with(['instructor.user', 'currency', 'trainingRecords']);

        // فلترة حسب النوع
        if ($request->filled('type')) {
            $trainingsQuery->where('type', $request->input('type'));
        }

        // فلترة حسب الحالة
        if ($request->filled('status')) {
            $trainingsQuery->where('status', $request->input('status'));
        }

        // فلترة حسب المدرب
        if ($request->filled('instructor_id')) {
            $trainingsQuery->where('instructor_id', $request->input('instructor_id'));
        }

        // البحث
        if ($request->filled('search')) {
            $search = $request->input('search');
            $trainingsQuery->where(function($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('title_ar', 'like', '%' . $search . '%')
                  ->orWhere('code', 'like', '%' . $search . '%');
            });
        }

        $trainings = $trainingsQuery->orderBy('start_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $instructors = Employee::where('is_active', true)->with('user')->get();
        $currencies = Currency::where('is_active', true)->get();

        return view("admin.pages.trainings.index", compact("trainings", "instructors", "currencies"));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $instructors = Employee::where('is_active', true)->with('user')->get();
        $currencies = Currency::where('is_active', true)->get();
        
        return view("admin.pages.trainings.create", compact("instructors", "currencies"));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'title_ar' => 'nullable|string|max:255',
            'code' => 'required|string|max:255|unique:trainings,code',
            'description' => 'nullable|string',
            'description_ar' => 'nullable|string',
            'type' => 'required|in:internal,external,online,workshop',
            'provider' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'start_time' => 'nullable',
            'end_time' => 'nullable',
            'duration_hours' => 'nullable|integer|min:0',
            'max_participants' => 'nullable|integer|min:1',
            'cost' => 'nullable|numeric|min:0',
            'currency_id' => 'nullable|exists:currencies,id',
            'status' => 'required|in:planned,ongoing,completed,cancelled',
            'instructor_id' => 'nullable|exists:employees,id',
            'objectives' => 'nullable|string',
            'content' => 'nullable|string',
            'materials' => 'nullable|string',
            'notes' => 'nullable|string',
        ], [
            'title.required' => 'عنوان الدورة مطلوب',
            'code.required' => 'كود الدورة مطلوب',
            'code.unique' => 'كود الدورة موجود مسبقاً',
            'type.required' => 'نوع التدريب مطلوب',
            'start_date.required' => 'تاريخ البدء مطلوب',
            'end_date.required' => 'تاريخ الانتهاء مطلوب',
            'end_date.after_or_equal' => 'تاريخ الانتهاء يجب أن يكون بعد أو يساوي تاريخ البدء',
            'status.required' => 'الحالة مطلوبة',
        ]);

        Training::create([
            'title' => $request->title,
            'title_ar' => $request->title_ar,
            'code' => $request->code,
            'description' => $request->description,
            'description_ar' => $request->description_ar,
            'type' => $request->type,
            'provider' => $request->provider,
            'location' => $request->location,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'duration_hours' => $request->duration_hours ?? 0,
            'max_participants' => $request->max_participants,
            'cost' => $request->cost ?? 0,
            'currency_id' => $request->currency_id,
            'status' => $request->status,
            'instructor_id' => $request->instructor_id,
            'objectives' => $request->objectives,
            'content' => $request->content,
            'materials' => $request->materials,
            'notes' => $request->notes,
            'created_by' => auth()->id(),
        ]);

        return redirect()->route("admin.trainings.index")->with("success", "تم إضافة الدورة التدريبية بنجاح");
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $training = Training::with(['instructor.user', 'currency', 'trainingRecords.employee.user', 'creator'])->findOrFail($id);
        return view("admin.pages.trainings.show", compact("training"));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $training = Training::findOrFail($id);
        $instructors = Employee::where('is_active', true)->with('user')->get();
        $currencies = Currency::where('is_active', true)->get();
        
        return view("admin.pages.trainings.edit", compact("training", "instructors", "currencies"));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $training = Training::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'title_ar' => 'nullable|string|max:255',
            'code' => 'required|string|max:255|unique:trainings,code,' . $id,
            'description' => 'nullable|string',
            'description_ar' => 'nullable|string',
            'type' => 'required|in:internal,external,online,workshop',
            'provider' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'start_time' => 'nullable',
            'end_time' => 'nullable',
            'duration_hours' => 'nullable|integer|min:0',
            'max_participants' => 'nullable|integer|min:1',
            'cost' => 'nullable|numeric|min:0',
            'currency_id' => 'nullable|exists:currencies,id',
            'status' => 'required|in:planned,ongoing,completed,cancelled',
            'instructor_id' => 'nullable|exists:employees,id',
            'objectives' => 'nullable|string',
            'content' => 'nullable|string',
            'materials' => 'nullable|string',
            'notes' => 'nullable|string',
        ], [
            'title.required' => 'عنوان الدورة مطلوب',
            'code.required' => 'كود الدورة مطلوب',
            'code.unique' => 'كود الدورة موجود مسبقاً',
            'type.required' => 'نوع التدريب مطلوب',
            'start_date.required' => 'تاريخ البدء مطلوب',
            'end_date.required' => 'تاريخ الانتهاء مطلوب',
            'end_date.after_or_equal' => 'تاريخ الانتهاء يجب أن يكون بعد أو يساوي تاريخ البدء',
            'status.required' => 'الحالة مطلوبة',
        ]);

        $training->update([
            'title' => $request->title,
            'title_ar' => $request->title_ar,
            'code' => $request->code,
            'description' => $request->description,
            'description_ar' => $request->description_ar,
            'type' => $request->type,
            'provider' => $request->provider,
            'location' => $request->location,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'duration_hours' => $request->duration_hours ?? 0,
            'max_participants' => $request->max_participants,
            'cost' => $request->cost ?? 0,
            'currency_id' => $request->currency_id,
            'status' => $request->status,
            'instructor_id' => $request->instructor_id,
            'objectives' => $request->objectives,
            'content' => $request->content,
            'materials' => $request->materials,
            'notes' => $request->notes,
        ]);

        return redirect()->route('admin.trainings.index')->with('success', 'تم تحديث الدورة التدريبية بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $training = Training::findOrFail($request->id);
        $training->delete();

        return redirect()->route("admin.trainings.index")->with("success", "تم حذف الدورة التدريبية بنجاح");
    }
}
