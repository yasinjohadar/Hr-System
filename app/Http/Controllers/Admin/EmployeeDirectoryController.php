<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Department;
use App\Models\Branch;
use App\Models\Position;
use Illuminate\Http\Request;

class EmployeeDirectoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:employee-directory-view')->only(['index', 'export']);
    }

    /**
     * عرض دليل الموظفين
     */
    public function index(Request $request)
    {
        $query = Employee::where('is_active', true)
            ->with(['department', 'position', 'branch', 'manager']);

        // البحث
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%$search%")
                  ->orWhere('last_name', 'like', "%$search%")
                  ->orWhere('full_name', 'like', "%$search%")
                  ->orWhere('employee_code', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%")
                  ->orWhere('phone', 'like', "%$search%");
            });
        }

        // فلترة حسب القسم
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->input('department_id'));
        }

        // فلترة حسب الفرع
        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->input('branch_id'));
        }

        // فلترة حسب المنصب
        if ($request->filled('position_id')) {
            $query->where('position_id', $request->input('position_id'));
        }

        // ترتيب
        $sortBy = $request->get('sort_by', 'full_name');
        $sortOrder = $request->get('sort_order', 'asc');
        $query->orderBy($sortBy, $sortOrder);

        $employees = $query->paginate(24); // 24 موظف في الصفحة (عرض شبكة)
        
        $departments = Department::where('is_active', true)->get();
        $branches = Branch::where('is_active', true)->get();
        $positions = Position::where('is_active', true)->get();

        return view('admin.pages.employee-directory.index', compact('employees', 'departments', 'branches', 'positions'));
    }

    /**
     * تصدير دليل الموظفين
     */
    public function export(Request $request)
    {
        $query = Employee::where('is_active', true)
            ->with(['department', 'position', 'branch', 'manager']);

        // تطبيق نفس الفلاتر
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%$search%")
                  ->orWhere('last_name', 'like', "%$search%")
                  ->orWhere('full_name', 'like', "%$search%");
            });
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->input('department_id'));
        }

        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->input('branch_id'));
        }

        $employees = $query->get();

        $format = $request->get('format', 'pdf'); // pdf أو excel

        if ($format == 'excel') {
            // تصدير Excel (يتطلب Laravel Excel package)
            // يمكن استخدام Maatwebsite\Excel
            return response()->json(['message' => 'Excel export not implemented yet']);
        } else {
            // تصدير PDF
            return view('admin.pages.employee-directory.export-pdf', compact('employees'));
        }
    }
}
