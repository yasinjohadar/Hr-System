<?php

namespace App\Http\Controllers\Admin;

use App\Models\Currency;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CurrencyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:currency-list')->only('index');
        $this->middleware('permission:currency-create')->only(['create', 'store']);
        $this->middleware('permission:currency-edit')->only(['edit', 'update']);
        $this->middleware('permission:currency-delete')->only('destroy');
        $this->middleware('permission:currency-show')->only('show');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $currenciesQuery = Currency::query();

        // فلترة حسب البحث
        if ($request->filled('query')) {
            $search = $request->input('query');
            $currenciesQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('name_ar', 'like', "%$search%")
                  ->orWhere('code', 'like', "%$search%");
            });
        }

        // فلترة حسب الحالة النشطة
        if ($request->filled('is_active')) {
            $currenciesQuery->where('is_active', $request->input('is_active'));
        }

        $currencies = $currenciesQuery->orderBy('sort_order')->orderBy('name')->paginate(20);

        return view("admin.pages.currencies.index", compact("currencies"));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view("admin.pages.currencies.create");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'code' => 'required|string|size:3|unique:currencies,code',
            'symbol' => 'nullable|string|max:10',
            'symbol_ar' => 'nullable|string|max:10',
            'decimal_places' => 'nullable|integer|min:0|max:4',
            'exchange_rate' => 'nullable|numeric|min:0',
            'is_base_currency' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer',
        ], [
            'name.required' => 'اسم العملة مطلوب',
            'code.required' => 'كود العملة مطلوب',
            'code.size' => 'كود العملة يجب أن يكون 3 أحرف',
            'code.unique' => 'كود العملة مستخدم بالفعل',
        ]);

        // إذا تم تحديد عملة أساسية، إلغاء العملة الأساسية السابقة
        if ($request->has('is_base_currency') && $request->is_base_currency) {
            Currency::where('is_base_currency', true)->update(['is_base_currency' => false]);
        }

        Currency::create([
            'name' => $request->name,
            'name_ar' => $request->name_ar,
            'code' => strtoupper($request->code),
            'symbol' => $request->symbol,
            'symbol_ar' => $request->symbol_ar,
            'decimal_places' => $request->decimal_places ?? 2,
            'exchange_rate' => $request->exchange_rate ?? 1.0000,
            'is_base_currency' => $request->has('is_base_currency'),
            'is_active' => $request->has('is_active'),
            'sort_order' => $request->sort_order ?? 0,
        ]);

        return redirect()->route("admin.currencies.index")->with("success", "تم إضافة العملة بنجاح");
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $currency = Currency::findOrFail($id);
        return view("admin.pages.currencies.show", compact("currency"));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $currency = Currency::findOrFail($id);
        return view("admin.pages.currencies.edit", compact("currency"));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $currency = Currency::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'code' => 'required|string|size:3|unique:currencies,code,' . $id,
            'symbol' => 'nullable|string|max:10',
            'symbol_ar' => 'nullable|string|max:10',
            'decimal_places' => 'nullable|integer|min:0|max:4',
            'exchange_rate' => 'nullable|numeric|min:0',
            'is_base_currency' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer',
        ], [
            'name.required' => 'اسم العملة مطلوب',
            'code.required' => 'كود العملة مطلوب',
            'code.size' => 'كود العملة يجب أن يكون 3 أحرف',
            'code.unique' => 'كود العملة مستخدم بالفعل',
        ]);

        // إذا تم تحديد عملة أساسية، إلغاء العملة الأساسية السابقة
        if ($request->has('is_base_currency') && $request->is_base_currency && !$currency->is_base_currency) {
            Currency::where('is_base_currency', true)->where('id', '!=', $id)->update(['is_base_currency' => false]);
        }

        $currency->update([
            'name' => $request->name,
            'name_ar' => $request->name_ar,
            'code' => strtoupper($request->code),
            'symbol' => $request->symbol,
            'symbol_ar' => $request->symbol_ar,
            'decimal_places' => $request->decimal_places ?? 2,
            'exchange_rate' => $request->exchange_rate ?? 1.0000,
            'is_base_currency' => $request->has('is_base_currency'),
            'is_active' => $request->has('is_active'),
            'sort_order' => $request->sort_order ?? 0,
        ]);

        return redirect()->route('admin.currencies.index')->with('success', 'تم تحديث بيانات العملة بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $currency = Currency::findOrFail($request->id);
        $currency->delete();

        return redirect()->route("admin.currencies.index")->with("success", "تم حذف العملة بنجاح");
    }
}
