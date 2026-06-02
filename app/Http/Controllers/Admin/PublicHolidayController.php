<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Country;
use App\Models\PublicHoliday;
use Illuminate\Http\Request;

class PublicHolidayController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:settings-manage');
    }

    public function index(Request $request)
    {
        $holidays = PublicHoliday::with(['country', 'branch'])
            ->when($request->country_id, fn ($q) => $q->where('country_id', $request->country_id))
            ->when($request->year, fn ($q) => $q->whereYear('holiday_date', $request->year))
            ->orderBy('holiday_date')
            ->paginate(20);

        $countries = Country::where('is_active', true)->orderBy('name')->get();

        return view('admin.pages.public-holidays.index', compact('holidays', 'countries'));
    }

    public function create()
    {
        $countries = Country::where('is_active', true)->get();
        $branches = Branch::where('is_active', true)->get();

        return view('admin.pages.public-holidays.create', compact('countries', 'branches'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'country_id' => 'nullable|exists:countries,id',
            'branch_id' => 'nullable|exists:branches,id',
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'holiday_date' => 'required|date',
            'is_recurring' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        $data['created_by'] = auth()->id();
        $data['is_active'] = true;
        $data['is_recurring'] = $request->boolean('is_recurring');

        PublicHoliday::create($data);

        return redirect()->route('admin.public-holidays.index')->with('success', 'تم إضافة العطلة.');
    }

    public function destroy(string $id)
    {
        PublicHoliday::findOrFail($id)->delete();

        return back()->with('success', 'تم الحذف.');
    }
}
