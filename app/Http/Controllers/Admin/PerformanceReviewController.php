<?php

namespace App\Http\Controllers\Admin;

use App\Models\PerformanceReview;
use App\Models\Employee;
use App\Services\ApprovalService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class PerformanceReviewController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:performance-review-list')->only('index');
        $this->middleware('permission:performance-review-create')->only(['create', 'store']);
        $this->middleware('permission:performance-review-edit')->only(['edit', 'update']);
        $this->middleware('permission:performance-review-delete')->only('destroy');
        $this->middleware('permission:performance-review-show')->only('show');
        $this->middleware('permission:performance-review-approve')->only(['approve', 'reject']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $reviewsQuery = PerformanceReview::with(['employee.user', 'reviewer.user', 'approver']);

        if (Auth::user()->isDepartmentHead()) {
            $employeeIds = Auth::user()->getManagedEmployeeIds();
            if (!empty($employeeIds)) {
                $reviewsQuery->whereIn('employee_id', $employeeIds);
            } else {
                $reviewsQuery->whereRaw('1 = 0');
            }
        }

        // فلترة حسب الموظف
        if ($request->filled('employee_id')) {
            $reviewsQuery->where('employee_id', $request->input('employee_id'));
        }

        // فلترة حسب المقيّم
        if ($request->filled('reviewer_id')) {
            $reviewsQuery->where('reviewer_id', $request->input('reviewer_id'));
        }

        // فلترة حسب الحالة
        if ($request->filled('status')) {
            $reviewsQuery->where('status', $request->input('status'));
        }

        // فلترة حسب فترة التقييم
        if ($request->filled('review_period')) {
            $reviewsQuery->where('review_period', 'like', '%' . $request->input('review_period') . '%');
        }

        $reviews = $reviewsQuery->orderBy('review_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $employees = Employee::where('is_active', true)->with('user')->get();
        if (Auth::user()->isDepartmentHead()) {
            $managedIds = Auth::user()->getManagedEmployeeIds();
            $employees = $employees->whereIn('id', $managedIds)->values();
        }

        return view("admin.pages.performance-reviews.index", compact("reviews", "employees"));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $employees = Employee::where('is_active', true)->with('user')->get();
        $reviewers = Employee::where('is_active', true)->with('user')->get(); // المدراء/المقيّمين
        
        return view("admin.pages.performance-reviews.create", compact("employees", "reviewers"));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'reviewer_id' => 'required|exists:employees,id',
            'review_period' => 'required|string|max:255',
            'review_date' => 'required|date',
            'period_start_date' => 'required|date',
            'period_end_date' => 'required|date|after_or_equal:period_start_date',
            'job_knowledge' => 'nullable|integer|min:1|max:5',
            'work_quality' => 'nullable|integer|min:1|max:5',
            'productivity' => 'nullable|integer|min:1|max:5',
            'communication' => 'nullable|integer|min:1|max:5',
            'teamwork' => 'nullable|integer|min:1|max:5',
            'initiative' => 'nullable|integer|min:1|max:5',
            'problem_solving' => 'nullable|integer|min:1|max:5',
            'attendance_punctuality' => 'nullable|integer|min:1|max:5',
            'strengths' => 'nullable|string',
            'weaknesses' => 'nullable|string',
            'goals_achieved' => 'nullable|string',
            'future_goals' => 'nullable|string',
            'comments' => 'nullable|string',
            'status' => 'required|in:draft,completed,approved,rejected',
        ], [
            'employee_id.required' => 'الموظف مطلوب',
            'reviewer_id.required' => 'المقيّم مطلوب',
            'review_period.required' => 'فترة التقييم مطلوبة',
            'review_date.required' => 'تاريخ التقييم مطلوب',
            'period_start_date.required' => 'تاريخ بداية الفترة مطلوب',
            'period_end_date.required' => 'تاريخ نهاية الفترة مطلوب',
            'period_end_date.after_or_equal' => 'تاريخ النهاية يجب أن يكون بعد أو يساوي تاريخ البداية',
        ]);

        $employee = Employee::findOrFail($request->employee_id);
        
        // إذا لم يتم تحديد reviewer_id، استخدام المدير المباشر تلقائياً
        $reviewerId = $request->reviewer_id;
        if (!$reviewerId) {
            $directManager = $employee->getDirectManager();
            if ($directManager) {
                $reviewerId = $directManager->id;
            } else {
                // إذا لم يكن هناك مدير مباشر، استخدام مدير القسم
                $deptManager = $employee->getDepartmentManagerEmployee();
                if ($deptManager) {
                    $reviewerId = $deptManager->id;
                }
            }
        }

        if (!$reviewerId) {
            return back()->withInput()->withErrors(['reviewer_id' => 'يجب تحديد مقيّم أو تعيين مدير للموظف']);
        }

        $review = PerformanceReview::create([
            'employee_id' => $request->employee_id,
            'reviewer_id' => $reviewerId,
            'review_period' => $request->review_period,
            'review_date' => $request->review_date,
            'period_start_date' => $request->period_start_date,
            'period_end_date' => $request->period_end_date,
            'job_knowledge' => $request->job_knowledge ?? 0,
            'work_quality' => $request->work_quality ?? 0,
            'productivity' => $request->productivity ?? 0,
            'communication' => $request->communication ?? 0,
            'teamwork' => $request->teamwork ?? 0,
            'initiative' => $request->initiative ?? 0,
            'problem_solving' => $request->problem_solving ?? 0,
            'attendance_punctuality' => $request->attendance_punctuality ?? 0,
            'strengths' => $request->strengths,
            'weaknesses' => $request->weaknesses,
            'goals_achieved' => $request->goals_achieved,
            'future_goals' => $request->future_goals,
            'comments' => $request->comments,
            'status' => $request->status,
            'created_by' => auth()->id(),
        ]);

        // حساب التقييم الإجمالي
        $review->calculateOverallRating();
        $review->save();

        return redirect()->route("admin.performance-reviews.index")->with("success", "تم إضافة التقييم بنجاح");
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $review = PerformanceReview::with(['employee.user', 'reviewer.user', 'approver', 'creator'])->findOrFail($id);

        if (Auth::user()->isDepartmentHead()) {
            $employeeIds = Auth::user()->getManagedEmployeeIds();
            if (!in_array($review->employee_id, $employeeIds)) {
                abort(403, 'غير مصرح لك بعرض هذا التقييم.');
            }
        }

        return view("admin.pages.performance-reviews.show", compact("review"));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $review = PerformanceReview::findOrFail($id);
        
        // لا يمكن تعديل تقييم موافق عليه
        if ($review->status == 'approved') {
            return redirect()->route('admin.performance-reviews.index')
                ->with('error', 'لا يمكن تعديل تقييم موافق عليه');
        }

        $employees = Employee::where('is_active', true)->with('user')->get();
        $reviewers = Employee::where('is_active', true)->with('user')->get();
        
        return view("admin.pages.performance-reviews.edit", compact("review", "employees", "reviewers"));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $review = PerformanceReview::findOrFail($id);

        // لا يمكن تعديل تقييم موافق عليه
        if ($review->status == 'approved') {
            return redirect()->route('admin.performance-reviews.index')
                ->with('error', 'لا يمكن تعديل تقييم موافق عليه');
        }

        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'reviewer_id' => 'required|exists:employees,id',
            'review_period' => 'required|string|max:255',
            'review_date' => 'required|date',
            'period_start_date' => 'required|date',
            'period_end_date' => 'required|date|after_or_equal:period_start_date',
            'job_knowledge' => 'nullable|integer|min:1|max:5',
            'work_quality' => 'nullable|integer|min:1|max:5',
            'productivity' => 'nullable|integer|min:1|max:5',
            'communication' => 'nullable|integer|min:1|max:5',
            'teamwork' => 'nullable|integer|min:1|max:5',
            'initiative' => 'nullable|integer|min:1|max:5',
            'problem_solving' => 'nullable|integer|min:1|max:5',
            'attendance_punctuality' => 'nullable|integer|min:1|max:5',
            'strengths' => 'nullable|string',
            'weaknesses' => 'nullable|string',
            'goals_achieved' => 'nullable|string',
            'future_goals' => 'nullable|string',
            'comments' => 'nullable|string',
            'status' => 'required|in:draft,completed,approved,rejected',
        ], [
            'employee_id.required' => 'الموظف مطلوب',
            'reviewer_id.required' => 'المقيّم مطلوب',
            'review_period.required' => 'فترة التقييم مطلوبة',
            'review_date.required' => 'تاريخ التقييم مطلوب',
            'period_start_date.required' => 'تاريخ بداية الفترة مطلوب',
            'period_end_date.required' => 'تاريخ نهاية الفترة مطلوب',
            'period_end_date.after_or_equal' => 'تاريخ النهاية يجب أن يكون بعد أو يساوي تاريخ البداية',
        ]);

        $review->update([
            'employee_id' => $request->employee_id,
            'reviewer_id' => $request->reviewer_id,
            'review_period' => $request->review_period,
            'review_date' => $request->review_date,
            'period_start_date' => $request->period_start_date,
            'period_end_date' => $request->period_end_date,
            'job_knowledge' => $request->job_knowledge ?? 0,
            'work_quality' => $request->work_quality ?? 0,
            'productivity' => $request->productivity ?? 0,
            'communication' => $request->communication ?? 0,
            'teamwork' => $request->teamwork ?? 0,
            'initiative' => $request->initiative ?? 0,
            'problem_solving' => $request->problem_solving ?? 0,
            'attendance_punctuality' => $request->attendance_punctuality ?? 0,
            'strengths' => $request->strengths,
            'weaknesses' => $request->weaknesses,
            'goals_achieved' => $request->goals_achieved,
            'future_goals' => $request->future_goals,
            'comments' => $request->comments,
            'status' => $request->status,
        ]);

        // إعادة حساب التقييم الإجمالي
        $review->calculateOverallRating();
        $review->save();

        return redirect()->route('admin.performance-reviews.index')->with('success', 'تم تحديث التقييم بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $review = PerformanceReview::findOrFail($request->id);
        $review->delete();

        return redirect()->route("admin.performance-reviews.index")->with("success", "تم حذف التقييم بنجاح");
    }

    /**
     * الموافقة على التقييم
     */
    public function approve(Request $request, string $id)
    {
        $review = PerformanceReview::findOrFail($id);
        $employee = $review->employee;

        if (Auth::user()->isDepartmentHead()) {
            $employeeIds = Auth::user()->getManagedEmployeeIds();
            if (!in_array($review->employee_id, $employeeIds)) {
                abort(403, 'غير مصرح لك بالموافقة على هذا التقييم.');
            }
        }

        // التحقق من أن المستخدم الحالي هو المقيّم أو لديه صلاحية الموافقة
        $approvalService = app(ApprovalService::class);
        $currentUser = auth()->user();
        $reviewerUser = $review->reviewer->user ?? null;

        // التحقق من أن المستخدم هو المقيّم أو المدير المباشر أو مدير القسم
        $canApprove = false;
        
        if ($reviewerUser && $reviewerUser->id === $currentUser->id) {
            $canApprove = true; // المقيّم نفسه
        } elseif ($employee->getDirectManager() && $employee->getDirectManager()->user_id === $currentUser->id) {
            $canApprove = true; // المدير المباشر
        } elseif ($employee->getDepartmentManager() && $employee->getDepartmentManager()->id === $currentUser->id) {
            $canApprove = true; // مدير القسم
        } elseif ($currentUser->hasPermissionTo('performance-review-approve-all')) {
            $canApprove = true; // صلاحية عامة
        }

        if (!$canApprove) {
            return back()->with('error', 'ليس لديك صلاحية الموافقة على هذا التقييم');
        }

        if ($review->status != 'completed') {
            return back()->with('error', 'يمكن الموافقة فقط على التقييمات المكتملة');
        }

        $review->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'تم الموافقة على التقييم بنجاح');
    }

    /**
     * رفض التقييم
     */
    public function reject(Request $request, string $id)
    {
        $review = PerformanceReview::findOrFail($id);

        if (Auth::user()->isDepartmentHead()) {
            $employeeIds = Auth::user()->getManagedEmployeeIds();
            if (!in_array($review->employee_id, $employeeIds)) {
                abort(403, 'غير مصرح لك برفض هذا التقييم.');
            }
        }

        if ($review->status != 'completed') {
            return back()->with('error', 'يمكن رفض فقط التقييمات المكتملة');
        }

        $review->update([
            'status' => 'rejected',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'تم رفض التقييم بنجاح');
    }
}
