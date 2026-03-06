<?php

namespace App\Http\Controllers\Admin;

use App\Models\Employee;
use App\Models\EmployeeJobChange;
use App\Models\Department;
use App\Models\Position;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EmployeeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:employee-list')->only('index');
        $this->middleware('permission:employee-create')->only(['create', 'store']);
        $this->middleware('permission:employee-edit')->only(['edit', 'update']);
        $this->middleware('permission:employee-delete')->only('destroy');
        $this->middleware('permission:employee-show')->only(['show', 'loginAs', 'generateLoginCode']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $departments = Department::where('is_active', true)->get();
        $positions = Position::where('is_active', true)->get();

        // بدء استعلام الموظفين
        $employeesQuery = Employee::with(['user', 'department', 'position', 'manager']);

        // تقييد رئيس القسم بموظفي أقسامه فقط
        if (Auth::user()->isDepartmentHead()) {
            $departmentIds = Auth::user()->getManagedDepartmentIds();
            if (!empty($departmentIds)) {
                $employeesQuery->whereIn('department_id', $departmentIds);
            } else {
                $employeesQuery->whereRaw('1 = 0'); // لا أقسام يديرها = لا موظفين
            }
        }

        // فلترة حسب البحث
        if ($request->filled('query')) {
            $search = $request->input('query');
            $employeesQuery->where(function ($q) use ($search) {
                $q->where('employee_code', 'like', "%$search%")
                  ->orWhere('first_name', 'like', "%$search%")
                  ->orWhere('last_name', 'like', "%$search%")
                  ->orWhere('full_name', 'like', "%$search%")
                  ->orWhere('personal_email', 'like', "%$search%")
                  ->orWhere('personal_phone', 'like', "%$search%")
                  ->orWhere('national_id', 'like', "%$search%");
            });
        }

        // فلترة حسب القسم
        if ($request->filled('department_id')) {
            $employeesQuery->where('department_id', $request->input('department_id'));
        }

        // فلترة حسب المنصب
        if ($request->filled('position_id')) {
            $employeesQuery->where('position_id', $request->input('position_id'));
        }

        // فلترة حسب الحالة الوظيفية
        if ($request->filled('employment_status')) {
            $employeesQuery->where('employment_status', $request->input('employment_status'));
        }

        // فلترة حسب الحالة النشطة
        if ($request->filled('is_active')) {
            $employeesQuery->where('is_active', $request->input('is_active'));
        }

        // تنفيذ الاستعلام
        $employees = $employeesQuery->paginate(10);

        return view("admin.pages.employees.index", compact("employees", "departments", "positions"));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $departments = Department::where('is_active', true)->get();
        $positions = Position::where('is_active', true)->get();
        $managers = Employee::where('is_active', true)->get();
        $users = User::whereDoesntHave('employee')->get();
        
        return view("admin.pages.employees.create", compact("departments", "positions", "managers", "users"));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // التحقق من صحة البيانات
        $request->validate([
            'user_id' => 'required|exists:users,id|unique:employees,user_id',
            'employee_code' => 'required|string|max:50|unique:employees,employee_code',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'national_id' => 'nullable|string|max:50|unique:employees,national_id',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female',
            'marital_status' => 'nullable|string|max:50',
            'department_id' => 'nullable|exists:departments,id',
            'position_id' => 'nullable|exists:positions,id',
            'manager_id' => 'nullable|exists:employees,id',
            'hire_date' => 'required|date',
            'employment_type' => 'required|in:full_time,part_time,contract,intern,freelance',
            'salary' => 'nullable|numeric|min:0',
            'personal_email' => 'nullable|email|max:255',
            'personal_phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'user_id.required' => 'المستخدم مطلوب',
            'user_id.unique' => 'هذا المستخدم لديه موظف بالفعل',
            'employee_code.required' => 'رقم الموظف مطلوب',
            'employee_code.unique' => 'رقم الموظف مستخدم بالفعل',
            'first_name.required' => 'الاسم الأول مطلوب',
            'last_name.required' => 'اسم العائلة مطلوب',
            'hire_date.required' => 'تاريخ التوظيف مطلوب',
            'employment_type.required' => 'نوع التوظيف مطلوب',
        ]);

        // معالجة الصورة
        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $photoName = time() . '_' . $photo->getClientOriginalName();
            $photoPath = $photo->storeAs('employees/photos', $photoName, 'public');
        }

        // إنشاء الموظف
        $employee = Employee::create([
            'user_id' => $request->user_id,
            'employee_code' => $request->employee_code,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'full_name' => $request->first_name . ' ' . $request->last_name,
            'national_id' => $request->national_id,
            'date_of_birth' => $request->date_of_birth,
            'gender' => $request->gender,
            'marital_status' => $request->marital_status,
            'department_id' => $request->department_id,
            'position_id' => $request->position_id,
            'manager_id' => $request->manager_id,
            'hire_date' => $request->hire_date,
            'probation_end_date' => $request->probation_end_date,
            'contract_start_date' => $request->contract_start_date,
            'contract_end_date' => $request->contract_end_date,
            'employment_type' => $request->employment_type,
            'employment_status' => $request->employment_status ?? 'active',
            'salary' => $request->salary,
            'personal_email' => $request->personal_email,
            'personal_phone' => $request->personal_phone,
            'work_email' => $request->work_email,
            'work_phone' => $request->work_phone,
            'work_location' => $request->work_location,
            'address' => $request->address,
            'city' => $request->city,
            'country' => $request->country,
            'postal_code' => $request->postal_code,
            'emergency_contact_name' => $request->emergency_contact_name,
            'emergency_contact_phone' => $request->emergency_contact_phone,
            'emergency_contact_relation' => $request->emergency_contact_relation,
            'notes' => $request->notes,
            'photo' => $photoPath,
            'created_by' => auth()->id(),
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route("admin.employees.index")->with("success", "تم إضافة موظف جديد بنجاح");
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $employee = Employee::with(['user', 'department', 'position', 'manager', 'creator'])->findOrFail($id);

        // رئيس القسم يرى فقط موظفي أقسامه
        if (Auth::user()->isDepartmentHead()) {
            $departmentIds = Auth::user()->getManagedDepartmentIds();
            if (empty($departmentIds) || !in_array($employee->department_id, $departmentIds)) {
                abort(403, 'غير مصرح لك بعرض هذا الموظف.');
            }
        }

        // جلب سجل التغييرات الوظيفية المعتمدة مع العلاقات للعرض
        $jobChangeHistory = EmployeeJobChange::where('employee_id', $employee->id)
            ->where('status', EmployeeJobChange::STATUS_APPROVED)
            ->with(['oldDepartment', 'newDepartment', 'oldPosition', 'newPosition', 'oldBranch', 'newBranch', 'oldManager', 'newManager'])
            ->orderByDesc('effective_date')
            ->get();

        return view("admin.pages.employees.show", compact("employee", "jobChangeHistory"));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $employee = Employee::findOrFail($id);
        $departments = Department::where('is_active', true)->get();
        $positions = Position::where('is_active', true)->get();
        $managers = Employee::where('is_active', true)->where('id', '!=', $id)->get();
        
        return view("admin.pages.employees.edit", compact("employee", "departments", "positions", "managers"));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $employee = Employee::findOrFail($id);

        // التحقق من صحة البيانات
        $request->validate([
            'employee_code' => 'required|string|max:50|unique:employees,employee_code,' . $id,
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'national_id' => 'nullable|string|max:50|unique:employees,national_id,' . $id,
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female',
            'marital_status' => 'nullable|string|max:50',
            'department_id' => 'nullable|exists:departments,id',
            'position_id' => 'nullable|exists:positions,id',
            'manager_id' => 'nullable|exists:employees,id',
            'hire_date' => 'required|date',
            'employment_type' => 'required|in:full_time,part_time,contract,intern,freelance',
            'salary' => 'nullable|numeric|min:0',
            'personal_email' => 'nullable|email|max:255',
            'personal_phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'employee_code.required' => 'رقم الموظف مطلوب',
            'employee_code.unique' => 'رقم الموظف مستخدم بالفعل',
            'first_name.required' => 'الاسم الأول مطلوب',
            'last_name.required' => 'اسم العائلة مطلوب',
            'hire_date.required' => 'تاريخ التوظيف مطلوب',
            'employment_type.required' => 'نوع التوظيف مطلوب',
        ]);

        // تجهيز البيانات للتحديث
        $updateData = [
            'employee_code' => $request->employee_code,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'full_name' => $request->first_name . ' ' . $request->last_name,
            'national_id' => $request->national_id,
            'date_of_birth' => $request->date_of_birth,
            'gender' => $request->gender,
            'marital_status' => $request->marital_status,
            'department_id' => $request->department_id,
            'position_id' => $request->position_id,
            'manager_id' => $request->manager_id,
            'hire_date' => $request->hire_date,
            'probation_end_date' => $request->probation_end_date,
            'contract_start_date' => $request->contract_start_date,
            'contract_end_date' => $request->contract_end_date,
            'employment_type' => $request->employment_type,
            'employment_status' => $request->employment_status ?? 'active',
            'salary' => $request->salary,
            'personal_email' => $request->personal_email,
            'personal_phone' => $request->personal_phone,
            'work_email' => $request->work_email,
            'work_phone' => $request->work_phone,
            'work_location' => $request->work_location,
            'address' => $request->address,
            'city' => $request->city,
            'country' => $request->country,
            'postal_code' => $request->postal_code,
            'emergency_contact_name' => $request->emergency_contact_name,
            'emergency_contact_phone' => $request->emergency_contact_phone,
            'emergency_contact_relation' => $request->emergency_contact_relation,
            'notes' => $request->notes,
            'is_active' => $request->has('is_active'),
        ];

        // معالجة الصورة
        if ($request->hasFile('photo')) {
            // حذف الصورة القديمة إذا كانت موجودة
            if ($employee->photo) {
                Storage::disk('public')->delete($employee->photo);
            }

            $photo = $request->file('photo');
            $photoName = time() . '_' . $photo->getClientOriginalName();
            $photoPath = $photo->storeAs('employees/photos', $photoName, 'public');
            $updateData['photo'] = $photoPath;
        }

        // تحديث الموظف
        $employee->update($updateData);

        return redirect()->route('admin.employees.index')->with('success', 'تم تحديث بيانات الموظف بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $employee = Employee::findOrFail($request->id);
        $employee->delete();

        return redirect()->route("admin.employees.index")->with("success", "تم حذف الموظف بنجاح");
    }

    /**
     * الدخول بحساب الموظف (نسخ الجلسة) بدون كلمة مرور.
     */
    public function loginAs(Employee $employee)
    {
        if (!$employee->user_id) {
            return redirect()->back()->with('error', 'هذا الموظف غير مرتبط بحساب دخول.');
        }

        $user = $employee->user;
        if (!$user || !$user->is_active) {
            return redirect()->back()->with('error', 'حساب الموظف غير نشط.');
        }

        session()->put('impersonator_id', Auth::id());
        Auth::login($user);

        return redirect()->route('employee.dashboard')->with('success', 'تم الدخول بحساب الموظف.');
    }

    /**
     * إنشاء كود دخول لمرة واحدة لاستخدامه في متصفح آخر.
     */
    public function generateLoginCode(Employee $employee)
    {
        if (!$employee->user_id) {
            return response()->json(['error' => 'هذا الموظف غير مرتبط بحساب دخول.'], 422);
        }

        $user = $employee->user;
        if (!$user || !$user->is_active) {
            return response()->json(['error' => 'حساب الموظف غير نشط.'], 422);
        }

        $code = Str::random(12);
        $cacheKey = 'employee_login_code:' . $code;
        Cache::put($cacheKey, ['employee_id' => $employee->id], now()->addMinutes(15));

        $url = route('employee.login-by-code', ['code' => $code]);

        return response()->json([
            'code' => $code,
            'url' => $url,
        ]);
    }
}
