<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TaxSetting;
use Illuminate\Http\Request;

class TaxSettingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:tax-setting-list')->only('index', 'show');
        $this->middleware('permission:tax-setting-create')->only('create', 'store');
        $this->middleware('permission:tax-setting-edit')->only('edit', 'update');
        $this->middleware('permission:tax-setting-delete')->only('destroy');
    }

    public function index(Request $request)
    {
        $query = TaxSetting::query();

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
            $query->where('is_active', $request->input('is_active') == '1');
        }

        $taxSettings = $query->latest()->paginate(20);

        return view('admin.pages.tax-settings.index', compact('taxSettings'));
    }

    public function create()
    {
        return view('admin.pages.tax-settings.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'code' => 'required|string|max:50|unique:tax_settings,code',
            'type' => 'required|in:income_tax,social_insurance,health_insurance,other',
            'calculation_method' => 'required|in:percentage,slab,fixed',
            'rate' => 'required|numeric|min:0|max:100',
            'min_amount' => 'nullable|numeric|min:0',
            'max_amount' => 'nullable|numeric|min:0|gt:min_amount',
            'exemption_amount' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
            'effective_from' => 'nullable|date',
            'effective_to' => 'nullable|date|after:effective_from',
            'description' => 'nullable|string',
            'slabs' => 'nullable|array',
        ], [
            'name.required' => 'اسم الضريبة مطلوب',
            'code.required' => 'كود الضريبة مطلوب',
            'code.unique' => 'كود الضريبة موجود مسبقاً',
            'type.required' => 'نوع الضريبة مطلوب',
            'calculation_method.required' => 'طريقة الحساب مطلوبة',
            'rate.required' => 'النسبة مطلوبة',
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active');
        $data['created_by'] = auth()->id();

        // معالجة الشرائح
        if ($request->filled('slabs') && is_array($request->slabs)) {
            $slabs = [];
            foreach ($request->slabs as $slab) {
                if (!empty($slab['min']) && !empty($slab['max']) && !empty($slab['rate'])) {
                    $slabs[] = [
                        'min' => (float)$slab['min'],
                        'max' => (float)$slab['max'],
                        'rate' => (float)$slab['rate'],
                    ];
                }
            }
            $data['slabs'] = !empty($slabs) ? $slabs : null;
        }

        TaxSetting::create($data);

        return redirect()->route('admin.tax-settings.index')
            ->with('success', 'تم إنشاء إعداد الضريبة بنجاح.');
    }

    public function show(string $id)
    {
        $taxSetting = TaxSetting::with('creator')->findOrFail($id);
        return view('admin.pages.tax-settings.show', compact('taxSetting'));
    }

    public function edit(string $id)
    {
        $taxSetting = TaxSetting::findOrFail($id);
        return view('admin.pages.tax-settings.edit', compact('taxSetting'));
    }

    public function update(Request $request, string $id)
    {
        $taxSetting = TaxSetting::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'code' => 'required|string|max:50|unique:tax_settings,code,' . $id,
            'type' => 'required|in:income_tax,social_insurance,health_insurance,other',
            'calculation_method' => 'required|in:percentage,slab,fixed',
            'rate' => 'required|numeric|min:0|max:100',
            'min_amount' => 'nullable|numeric|min:0',
            'max_amount' => 'nullable|numeric|min:0|gt:min_amount',
            'exemption_amount' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
            'effective_from' => 'nullable|date',
            'effective_to' => 'nullable|date|after:effective_from',
            'description' => 'nullable|string',
            'slabs' => 'nullable|array',
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active');

        // معالجة الشرائح
        if ($request->filled('slabs') && is_array($request->slabs)) {
            $slabs = [];
            foreach ($request->slabs as $slab) {
                if (!empty($slab['min']) && !empty($slab['max']) && !empty($slab['rate'])) {
                    $slabs[] = [
                        'min' => (float)$slab['min'],
                        'max' => (float)$slab['max'],
                        'rate' => (float)$slab['rate'],
                    ];
                }
            }
            $data['slabs'] = !empty($slabs) ? $slabs : null;
        }

        $taxSetting->update($data);

        return redirect()->route('admin.tax-settings.index')
            ->with('success', 'تم تحديث إعداد الضريبة بنجاح.');
    }

    public function destroy(string $id)
    {
        $taxSetting = TaxSetting::findOrFail($id);
        $taxSetting->delete();

        return redirect()->route('admin.tax-settings.index')
            ->with('success', 'تم حذف إعداد الضريبة بنجاح.');
    }
}
