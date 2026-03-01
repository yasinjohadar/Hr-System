<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmployeeCertificate;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EmployeeCertificateController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:employee-certificate-list')->only(['index', 'show']);
        $this->middleware('permission:employee-certificate-create')->only(['create', 'store']);
        $this->middleware('permission:employee-certificate-edit')->only(['edit', 'update']);
        $this->middleware('permission:employee-certificate-delete')->only('destroy');
    }

    public function index(Request $request)
    {
        $query = EmployeeCertificate::with(['employee']);

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->input('employee_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('expiring_soon')) {
            $query->expiringSoon(30);
        }

        $certificates = $query->orderBy('created_at', 'desc')->paginate(20);
        $employees = Employee::where('is_active', true)->get();

        return view('admin.pages.employee-certificates.index', compact('certificates', 'employees'));
    }

    public function create()
    {
        $employees = Employee::where('is_active', true)->get();
        return view('admin.pages.employee-certificates.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'certificate_name' => 'required|string|max:255',
            'issuing_organization' => 'required|string|max:255',
            'issue_date' => 'required|date',
            'expiry_date' => 'nullable|date|after:issue_date',
            'file_path' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
        ]);

        $data = $request->all();
        $data['created_by'] = auth()->id();

        // رفع الملف
        if ($request->hasFile('file_path')) {
            $data['file_path'] = $request->file('file_path')->store('employee-certificates', 'public');
        }

        // التحقق من انتهاء الصلاحية
        if (!$data['does_not_expire'] && isset($data['expiry_date']) && \Carbon\Carbon::parse($data['expiry_date'])->isPast()) {
            $data['status'] = 'expired';
        }

        EmployeeCertificate::create($data);

        return redirect()->route('admin.employee-certificates.index')->with('success', 'تم إضافة الشهادة بنجاح');
    }

    public function show(string $id)
    {
        $certificate = EmployeeCertificate::with(['employee'])->findOrFail($id);
        return view('admin.pages.employee-certificates.show', compact('certificate'));
    }

    public function edit(string $id)
    {
        $certificate = EmployeeCertificate::findOrFail($id);
        $employees = Employee::where('is_active', true)->get();
        return view('admin.pages.employee-certificates.edit', compact('certificate', 'employees'));
    }

    public function update(Request $request, string $id)
    {
        $certificate = EmployeeCertificate::findOrFail($id);

        $request->validate([
            'certificate_name' => 'required|string|max:255',
            'issuing_organization' => 'required|string|max:255',
            'issue_date' => 'required|date',
            'expiry_date' => 'nullable|date|after:issue_date',
            'file_path' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
        ]);

        $data = $request->all();

        // رفع ملف جديد
        if ($request->hasFile('file_path')) {
            if ($certificate->file_path) {
                Storage::disk('public')->delete($certificate->file_path);
            }
            $data['file_path'] = $request->file('file_path')->store('employee-certificates', 'public');
        } else {
            unset($data['file_path']);
        }

        // التحقق من انتهاء الصلاحية
        if (!$data['does_not_expire'] && isset($data['expiry_date']) && \Carbon\Carbon::parse($data['expiry_date'])->isPast()) {
            $data['status'] = 'expired';
        }

        $certificate->update($data);

        return redirect()->route('admin.employee-certificates.index')->with('success', 'تم تحديث الشهادة بنجاح');
    }

    public function destroy(Request $request)
    {
        $certificate = EmployeeCertificate::findOrFail($request->id);

        if ($certificate->file_path) {
            Storage::disk('public')->delete($certificate->file_path);
        }

        $certificate->delete();

        return redirect()->route('admin.employee-certificates.index')->with('success', 'تم حذف الشهادة بنجاح');
    }
}
