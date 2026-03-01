<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmployeeDocument;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EmployeeDocumentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:employee-document-list')->only(['index', 'show']);
        $this->middleware('permission:employee-document-create')->only(['create', 'store']);
        $this->middleware('permission:employee-document-edit')->only(['edit', 'update']);
        $this->middleware('permission:employee-document-delete')->only('destroy');
    }

    public function index(Request $request)
    {
        $query = EmployeeDocument::with(['employee', 'uploader']);

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
        $employees = Employee::where('is_active', true)->get();

        return view('admin.pages.employee-documents.index', compact('documents', 'employees'));
    }

    public function create()
    {
        $employees = Employee::where('is_active', true)->get();
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
        return view('admin.pages.employee-documents.show', compact('document'));
    }

    public function edit(string $id)
    {
        $document = EmployeeDocument::findOrFail($id);
        $employees = Employee::where('is_active', true)->get();
        return view('admin.pages.employee-documents.edit', compact('document', 'employees'));
    }

    public function update(Request $request, string $id)
    {
        $document = EmployeeDocument::findOrFail($id);

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
        
        if (!Storage::disk('public')->exists($document->file_path)) {
            return redirect()->back()->with('error', 'الملف غير موجود');
        }

        return Storage::disk('public')->download($document->file_path, $document->file_name);
    }
}
