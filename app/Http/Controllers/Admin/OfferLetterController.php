<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OfferLetter;
use App\Models\JobApplication;
use App\Models\JobVacancy;
use App\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class OfferLetterController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:offer-letter-list')->only('index');
        $this->middleware('permission:offer-letter-create')->only(['create', 'store']);
        $this->middleware('permission:offer-letter-edit')->only(['edit', 'update', 'send', 'accept', 'reject']);
        $this->middleware('permission:offer-letter-delete')->only('destroy');
        $this->middleware('permission:offer-letter-show')->only('show');
    }

    public function index(Request $request)
    {
        $query = OfferLetter::with(['jobApplication.jobVacancy', 'jobApplication.candidate', 'currency', 'creator'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('job_vacancy_id')) {
            $query->whereHas('jobApplication', function ($q) use ($request) {
                $q->where('job_vacancy_id', $request->input('job_vacancy_id'));
            });
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->whereHas('jobApplication.candidate', function ($q) use ($search) {
                $q->where('full_name', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%");
            });
        }

        $offers = $query->paginate(20);
        $vacancies = JobVacancy::where('is_active', true)->get();

        return view('admin.pages.offer-letters.index', compact('offers', 'vacancies'));
    }

    public function create(Request $request)
    {
        $applications = JobApplication::with(['jobVacancy', 'candidate', 'jobVacancy.currency'])
            ->whereIn('status', ['shortlisted', 'interviewed', 'offered'])
            ->orderByDesc('application_date')
            ->get();
        $currencies = Currency::where('is_active', true)->get();
        $preselectedApplicationId = $request->get('job_application_id');

        return view('admin.pages.offer-letters.create', compact('applications', 'currencies', 'preselectedApplicationId'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'job_application_id' => 'required|exists:job_applications,id',
            'job_title' => 'required|string|max:255',
            'salary' => 'nullable|numeric|min:0',
            'currency_id' => 'nullable|exists:currencies,id',
            'start_date' => 'nullable|date',
            'valid_until' => 'nullable|date|after_or_equal:today',
            'notes' => 'nullable|string',
            'document_path' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
        ]);

        $data = $request->only([
            'job_application_id', 'job_title', 'salary', 'currency_id',
            'start_date', 'valid_until', 'notes'
        ]);
        $data['status'] = OfferLetter::STATUS_DRAFT;
        $data['created_by'] = auth()->id();

        if ($request->hasFile('document_path')) {
            $data['document_path'] = $request->file('document_path')->store('offer-letters', 'public');
        }

        $offer = OfferLetter::create($data);

        return redirect()->route('admin.offer-letters.show', $offer)
            ->with('success', 'تم إنشاء عرض التعيين بنجاح.');
    }

    public function show(OfferLetter $offer_letter)
    {
        $offer_letter->load(['jobApplication.jobVacancy', 'jobApplication.candidate', 'currency', 'creator']);
        return view('admin.pages.offer-letters.show', compact('offer_letter'));
    }

    public function edit(OfferLetter $offer_letter)
    {
        if ($offer_letter->status !== OfferLetter::STATUS_DRAFT) {
            return redirect()->route('admin.offer-letters.show', $offer_letter)
                ->with('error', 'لا يمكن تعديل عرض مرسل أو تم الرد عليه.');
        }
        $offer_letter->load(['jobApplication.jobVacancy', 'jobApplication.candidate', 'currency']);
        $currencies = Currency::where('is_active', true)->get();
        return view('admin.pages.offer-letters.edit', compact('offer_letter', 'currencies'));
    }

    public function update(Request $request, OfferLetter $offer_letter)
    {
        if ($offer_letter->status !== OfferLetter::STATUS_DRAFT) {
            return redirect()->route('admin.offer-letters.show', $offer_letter)
                ->with('error', 'لا يمكن تعديل عرض مرسل أو تم الرد عليه.');
        }

        $request->validate([
            'job_title' => 'required|string|max:255',
            'salary' => 'nullable|numeric|min:0',
            'currency_id' => 'nullable|exists:currencies,id',
            'start_date' => 'nullable|date',
            'valid_until' => 'nullable|date',
            'notes' => 'nullable|string',
            'document_path' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
        ]);

        $data = $request->only(['job_title', 'salary', 'currency_id', 'start_date', 'valid_until', 'notes']);

        if ($request->hasFile('document_path')) {
            if ($offer_letter->document_path) {
                Storage::disk('public')->delete($offer_letter->document_path);
            }
            $data['document_path'] = $request->file('document_path')->store('offer-letters', 'public');
        }

        $offer_letter->update($data);

        return redirect()->route('admin.offer-letters.show', $offer_letter)
            ->with('success', 'تم تحديث عرض التعيين بنجاح.');
    }

    public function destroy(OfferLetter $offer_letter)
    {
        if ($offer_letter->document_path) {
            Storage::disk('public')->delete($offer_letter->document_path);
        }
        $offer_letter->delete();
        return redirect()->route('admin.offer-letters.index')
            ->with('success', 'تم حذف عرض التعيين بنجاح.');
    }

    /**
     * Mark offer as sent (and optionally update job application status to offered).
     */
    public function send(Request $request, OfferLetter $offer_letter)
    {
        if ($offer_letter->status !== OfferLetter::STATUS_DRAFT) {
            return redirect()->back()->with('error', 'العرض غير مسودة.');
        }
        $offer_letter->update([
            'status' => OfferLetter::STATUS_SENT,
            'sent_at' => now(),
        ]);
        $offer_letter->jobApplication->update(['status' => 'offered']);
        $offer_letter->jobApplication->candidate->update(['status' => 'offered']);
        return redirect()->back()->with('success', 'تم اعتبار العرض مرسلاً.');
    }

    public function accept(OfferLetter $offer_letter)
    {
        if ($offer_letter->status !== OfferLetter::STATUS_SENT) {
            return redirect()->back()->with('error', 'لا يمكن قبول هذا العرض.');
        }
        $offer_letter->update([
            'status' => OfferLetter::STATUS_ACCEPTED,
            'accepted_at' => now(),
        ]);
        $offer_letter->jobApplication->update(['status' => 'accepted']);
        $offer_letter->jobApplication->candidate->update(['status' => 'hired']);
        return redirect()->back()->with('success', 'تم تسجيل قبول العرض بنجاح.');
    }

    public function reject(Request $request, OfferLetter $offer_letter)
    {
        if ($offer_letter->status !== OfferLetter::STATUS_SENT) {
            return redirect()->back()->with('error', 'لا يمكن رفض هذا العرض.');
        }
        $request->validate(['rejection_reason' => 'nullable|string|max:1000']);
        $offer_letter->update([
            'status' => OfferLetter::STATUS_REJECTED,
            'rejected_at' => now(),
            'rejection_reason' => $request->input('rejection_reason'),
        ]);
        $offer_letter->jobApplication->update(['status' => 'rejected']);
        $offer_letter->jobApplication->candidate->update(['status' => 'rejected']);
        return redirect()->back()->with('success', 'تم تسجيل رفض العرض.');
    }
}
