<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\Concerns\ScopesByDepartment;
use App\Models\EmployeeDocument;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EmployeeDocumentController extends Controller
{
    use ScopesByDepartment;

    public function __construct()
    {
        $this->middleware('auth');
        // تحذير أمني: مستندات الموظفين بيانات حساسة (هويات، عقود، تقارير طبية).
        // كل دالة عامة هنا يجب أن تكون مُدرَجة في أحد أسطر ->only() أدناه،
        // وأن تتحقق من نطاق القسم عبر authorizeManagedEmployee().
        $this->middleware('permission:employee-document-list')->only(['index', 'show', 'download']);
        $this->middleware('permission:employee-document-create')->only(['create', 'store']);
        $this->middleware('permission:employee-document-edit')->only(['edit', 'update']);
        $this->middleware('permission:employee-document-delete')->only('destroy');
    }

    public function index(Request $request)
    {
        $query = EmployeeDocument::with(['employee', 'uploader']);

        // تقييد النتائج بالموظفين الذين يديرهم المستخدم الحالي
        $this->scopeByEmployeeQuery($query);

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->input('employee_id'));
        }

        if ($request->filled('document_type')) {
            $query->where('document_type', $request->input('document_type'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('expiring_soon')) {
            $query->expiringSoon(30);
        }

        $documents = $query->orderBy('created_at', 'desc')->paginate(20);
        $employees = $this->scopeEmployeesQuery(Employee::query()->where('is_active', true))->get();

        return view('admin.pages.employee-documents.index', compact('documents', 'employees'));
    }

    public function create()
    {
        $employees = $this->scopeEmployeesQuery(Employee::query()->where('is_active', true))->get();
        return view('admin.pages.employee-documents.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'document_type' => 'required|string',
            'title' => 'required|string|max:255',
            'file_path' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
            'issue_date' => 'nullable|date',
            'expiry_date' => 'nullable|date|after:issue_date',
        ]);

        // منع رفع مستند لموظف خارج نطاق المستخدم الحالي
        $this->authorizeManagedEmployeeId((int) $request->input('employee_id'));

        $data = $request->all();
        $data['uploaded_by'] = auth()->id();

        // رفع الملف
        if ($request->hasFile('file_path')) {
            $file = $request->file('file_path');
            $data['file_name'] = $file->getClientOriginalName();
            $data['file_size'] = $file->getSize();
            $data['mime_type'] = $file->getMimeType();
            $data['file_path'] = $file->store('employee-documents', 'public');
        }

        // التحقق من انتهاء الصلاحية
        if ($data['expiry_date'] && \Carbon\Carbon::parse($data['expiry_date'])->isPast()) {
            $data['is_expired'] = true;
            $data['status'] = 'expired';
        }

        EmployeeDocument::create($data);

        return redirect()->route('admin.employee-documents.index')->with('success', 'تم إضافة المستند بنجاح');
    }

    public function show(string $id)
    {
        $document = EmployeeDocument::with(['employee', 'uploader'])->findOrFail($id);
        // نتحقق بالمعرّف لا بكائن العلاقة: authorizeManagedEmployee() تعمل abort(404)
        // إذا كان الموظف محذوفاً (soft delete) حتى لأدمن كامل، فتُصبح المستندات
        // اليتيمة غير قابلة للعرض أو الحذف.
        $this->authorizeManagedEmployeeId((int) $document->employee_id);

        return view('admin.pages.employee-documents.show', compact('document'));
    }

    public function edit(string $id)
    {
        $document = EmployeeDocument::findOrFail($id);
        // نتحقق بالمعرّف لا بكائن العلاقة: authorizeManagedEmployee() تعمل abort(404)
        // إذا كان الموظف محذوفاً (soft delete) حتى لأدمن كامل، فتُصبح المستندات
        // اليتيمة غير قابلة للعرض أو الحذف.
        $this->authorizeManagedEmployeeId((int) $document->employee_id);

        $employees = $this->scopeEmployeesQuery(Employee::query()->where('is_active', true))->get();
        return view('admin.pages.employee-documents.edit', compact('document', 'employees'));
    }

    public function update(Request $request, string $id)
    {
        $document = EmployeeDocument::findOrFail($id);
        // نتحقق بالمعرّف لا بكائن العلاقة: authorizeManagedEmployee() تعمل abort(404)
        // إذا كان الموظف محذوفاً (soft delete) حتى لأدمن كامل، فتُصبح المستندات
        // اليتيمة غير قابلة للعرض أو الحذف.
        $this->authorizeManagedEmployeeId((int) $document->employee_id);

        $request->validate([
            'document_type' => 'required|string',
            'title' => 'required|string|max:255',
            'file_path' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
            'issue_date' => 'nullable|date',
            'expiry_date' => 'nullable|date|after:issue_date',
        ]);

        $data = $request->all();

        // رفع ملف جديد
        if ($request->hasFile('file_path')) {
            // حذف الملف القديم
            if ($document->file_path) {
                Storage::disk('public')->delete($document->file_path);
            }

            $file = $request->file('file_path');
            $data['file_name'] = $file->getClientOriginalName();
            $data['file_size'] = $file->getSize();
            $data['mime_type'] = $file->getMimeType();
            $data['file_path'] = $file->store('employee-documents', 'public');
        } else {
            unset($data['file_path']);
        }

        // التحقق من انتهاء الصلاحية
        if (isset($data['expiry_date']) && \Carbon\Carbon::parse($data['expiry_date'])->isPast()) {
            $data['is_expired'] = true;
            $data['status'] = 'expired';
        } else {
            $data['is_expired'] = false;
        }

        $document->update($data);

        return redirect()->route('admin.employee-documents.index')->with('success', 'تم تحديث المستند بنجاح');
    }

    public function destroy(Request $request)
    {
        $document = EmployeeDocument::findOrFail($request->id);
        // نتحقق بالمعرّف لا بكائن العلاقة: authorizeManagedEmployee() تعمل abort(404)
        // إذا كان الموظف محذوفاً (soft delete) حتى لأدمن كامل، فتُصبح المستندات
        // اليتيمة غير قابلة للعرض أو الحذف.
        $this->authorizeManagedEmployeeId((int) $document->employee_id);

        // حذف الملف
        if ($document->file_path) {
            Storage::disk('public')->delete($document->file_path);
        }

        $document->delete();

        return redirect()->route('admin.employee-documents.index')->with('success', 'تم حذف المستند بنجاح');
    }

    public function download(string $id)
    {
        $document = EmployeeDocument::findOrFail($id);

        // بدون هذا التحقق كان بالإمكان تعداد /employee-documents/{1..N}/download
        // وسحب مستندات كل موظفي الشركة.
        // نتحقق بالمعرّف لا بكائن العلاقة: authorizeManagedEmployee() تعمل abort(404)
        // إذا كان الموظف محذوفاً (soft delete) حتى لأدمن كامل، فتُصبح المستندات
        // اليتيمة غير قابلة للعرض أو الحذف.
        $this->authorizeManagedEmployeeId((int) $document->employee_id);

        if (! $document->file_path || ! Storage::disk('public')->exists($document->file_path)) {
            return redirect()->back()->with('error', 'الملف غير موجود');
        }

        return Storage::disk('public')->download($document->file_path, $document->file_name);
    }
}
