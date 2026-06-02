<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\JobApplication;
use App\Models\JobVacancy;
use Illuminate\Http\Request;

class CareersController extends Controller
{
    public function index()
    {
        $vacancies = JobVacancy::where('status', 'published')
            ->with('department', 'position')
            ->latest()
            ->paginate(12);

        return view('public.careers.index', compact('vacancies'));
    }

    public function show(string $id)
    {
        $vacancy = JobVacancy::where('status', 'published')->with(['department', 'position'])->findOrFail($id);

        return view('public.careers.show', compact('vacancy'));
    }

    public function apply(Request $request, string $id)
    {
        $vacancy = JobVacancy::where('status', 'published')->findOrFail($id);

        $data = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:50',
            'cover_letter' => 'nullable|string|max:5000',
            'resume' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
        ]);

        $candidate = Candidate::firstOrCreate(
            ['email' => $data['email']],
            [
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'full_name' => trim($data['first_name'] . ' ' . $data['last_name']),
                'phone' => $data['phone'] ?? null,
            ]
        );

        $cvPath = null;
        if ($request->hasFile('resume')) {
            $cvPath = $request->file('resume')->store('careers/resumes', 'public');
            $candidate->update(['cv_path' => $cvPath]);
        }

        JobApplication::create([
            'job_vacancy_id' => $vacancy->id,
            'candidate_id' => $candidate->id,
            'application_date' => now(),
            'status' => 'pending',
            'source' => 'careers_portal',
            'notes' => $data['cover_letter'] ?? null,
            'cv_path' => $cvPath,
        ]);

        return redirect()->route('careers.show', $vacancy->id)
            ->with('success', 'تم استلام طلبك بنجاح.');
    }
}
