<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BenefitType;
use App\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BenefitTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:benefit-type-list')->only('index');
        $this->middleware('permission:benefit-type-create')->only(['create', 'store']);
        $this->middleware('permission:benefit-type-edit')->only(['edit', 'update']);
        $this->middleware('permission:benefit-type-delete')->only('destroy');
        $this->middleware('permission:benefit-type-show')->only('show');
    }

    public function index(Request $request)
    {
        $query = BenefitType::with(['currency'])->withCount('employeeBenefits');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('name_ar', 'like', "%$search%")
                  ->orWhere('code', 'like', "%$search%");
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->input('is_active'));
        }

        $benefitTypes = $query->orderBy('sort_order')->orderBy('name')->paginate(20);

        return view('admin.pages.benefit-types.index', compact('benefitTypes'));
    }

    public function create()
    {
        $currencies = Currency::where('is_active', true)->get();
        return view('admin.pages.benefit-types.create', compact('currencies'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:benefit_types,code',
            'type' => 'required|in:monetary,in_kind,service,insurance,allowance',
            'default_value' => 'nullable|numeric|min:0',
            'currency_id' => 'nullable|exists:currencies,id',
        ]);

        $data = $request->all();
        $data['created_by'] = auth()->id();
        
        if (!$data['code']) {
            $data['code'] = 'BEN-' . strtoupper(Str::random(8));
        }

        BenefitType::create($data);

        return redirect()->route('admin.benefit-types.index')->with('success', 'تم إضافة نوع الميزة بنجاح');
    }

    public function show(string $id)
    {
        $benefitType = BenefitType::with(['currency', 'employeeBenefits.employee'])
            ->withCount('employeeBenefits')
            ->findOrFail($id);
        return view('admin.pages.benefit-types.show', compact('benefitType'));
    }

    public function edit(string $id)
    {
        $benefitType = BenefitType::findOrFail($id);
        $currencies = Currency::where('is_active', true)->get();
        return view('admin.pages.benefit-types.edit', compact('benefitType', 'currencies'));
    }

    public function update(Request $request, string $id)
    {
        $benefitType = BenefitType::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:benefit_types,code,' . $id,
            'type' => 'required|in:monetary,in_kind,service,insurance,allowance',
            'default_value' => 'nullable|numeric|min:0',
            'currency_id' => 'nullable|exists:currencies,id',
        ]);

        $benefitType->update($request->all());

        return redirect()->route('admin.benefit-types.index')->with('success', 'تم تحديث نوع الميزة بنجاح');
    }

    public function destroy(Request $request)
    {
        $benefitType = BenefitType::findOrFail($request->id);
        $benefitType->delete();

        return redirect()->route('admin.benefit-types.index')->with('success', 'تم حذف نوع الميزة بنجاح');
    }
}
