<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class CandidateController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:candidate-list')->only('index');
        $this->middleware('permission:candidate-create')->only(['create', 'store']);
        $this->middleware('permission:candidate-edit')->only(['edit', 'update']);
        $this->middleware('permission:candidate-delete')->only('destroy');
        $this->middleware('permission:candidate-show')->only('show');
    }

    public function index(Request $request)
    {
        $query = Candidate::with(['country'])->withCount('applications');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%")
                  ->orWhere('phone', 'like', "%$search%")
                  ->orWhere('candidate_code', 'like', "%$search%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $candidates = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.pages.candidates.index', compact('candidates'));
    }

    public function create()
    {
        $countries = Country::where('is_active', true)->get();
        return view('admin.pages.candidates.create', compact('countries'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:candidates,email',
            'phone' => 'required|string|max:20',
            'national_id' => 'nullable|string|unique:candidates,national_id',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female',
            'country_id' => 'nullable|exists:countries,id',
            'cv_path' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->all();
        $data['full_name'] = $request->first_name . ' ' . $request->last_name;
        $data['created_by'] = auth()->id();

        if (!$data['candidate_code']) {
            $data['candidate_code'] = 'CAND-' . strtoupper(Str::random(8));
        }

        // رفع الملفات
        if ($request->hasFile('cv_path')) {
            $data['cv_path'] = $request->file('cv_path')->store('candidates/cvs', 'public');
        }

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('candidates/photos', 'public');
        }

        Candidate::create($data);

        return redirect()->route('admin.candidates.index')->with('success', 'تم إضافة المرشح بنجاح');
    }

    public function show(string $id)
    {
        $candidate = Candidate::with(['country', 'applications.jobVacancy', 'interviews'])
            ->findOrFail($id);
        return view('admin.pages.candidates.show', compact('candidate'));
    }

    public function edit(string $id)
    {
        $candidate = Candidate::findOrFail($id);
        $countries = Country::where('is_active', true)->get();
        return view('admin.pages.candidates.edit', compact('candidate', 'countries'));
    }

    public function update(Request $request, string $id)
    {
        $candidate = Candidate::findOrFail($id);

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:candidates,email,' . $id,
            'phone' => 'required|string|max:20',
            'national_id' => 'nullable|string|unique:candidates,national_id,' . $id,
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female',
            'country_id' => 'nullable|exists:countries,id',
            'cv_path' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->all();
        $data['full_name'] = $request->first_name . ' ' . $request->last_name;

        // رفع الملفات
        if ($request->hasFile('cv_path')) {
            if ($candidate->cv_path) {
                Storage::disk('public')->delete($candidate->cv_path);
            }
            $data['cv_path'] = $request->file('cv_path')->store('candidates/cvs', 'public');
        }

        if ($request->hasFile('photo')) {
            if ($candidate->photo) {
                Storage::disk('public')->delete($candidate->photo);
            }
            $data['photo'] = $request->file('photo')->store('candidates/photos', 'public');
        }

        $candidate->update($data);

        return redirect()->route('admin.candidates.index')->with('success', 'تم تحديث بيانات المرشح بنجاح');
    }

    public function destroy(Request $request)
    {
        $candidate = Candidate::findOrFail($request->id);
        
        // حذف الملفات
        if ($candidate->cv_path) {
            Storage::disk('public')->delete($candidate->cv_path);
        }
        if ($candidate->photo) {
            Storage::disk('public')->delete($candidate->photo);
        }

        $candidate->delete();

        return redirect()->route('admin.candidates.index')->with('success', 'تم حذف المرشح بنجاح');
    }
}
