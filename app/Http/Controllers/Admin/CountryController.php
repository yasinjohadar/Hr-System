<?php

namespace App\Http\Controllers\Admin;

use App\Models\Country;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CountryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:country-list')->only('index');
        $this->middleware('permission:country-create')->only(['create', 'store']);
        $this->middleware('permission:country-edit')->only(['edit', 'update']);
        $this->middleware('permission:country-delete')->only('destroy');
        $this->middleware('permission:country-show')->only('show');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $countriesQuery = Country::query();

        // فلترة حسب البحث
        if ($request->filled('query')) {
            $search = $request->input('query');
            $countriesQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('name_ar', 'like', "%$search%")
                  ->orWhere('code', 'like', "%$search%");
            });
        }

        // فلترة حسب الحالة النشطة
        if ($request->filled('is_active')) {
            $countriesQuery->where('is_active', $request->input('is_active'));
        }

        $countries = $countriesQuery->orderBy('sort_order')->orderBy('name')->paginate(20);

        // فلترة عبر AJAX: نرجّع الأجزاء المتغيّرة فقط.
        // الإحصاءات ليست ضمنها لأنها إجماليات عامة لا تتأثّر بالفلاتر.
        if ($request->ajax() || $request->boolean('ajax')) {
            return response()->json([
                'html_rows'       => view('admin.pages.countries._index_rows', compact('countries'))->render(),
                'html_pagination' => view('admin.pages.countries._index_pagination', compact('countries'))->render(),
                'html_meta'       => view('admin.pages.countries._index_meta', compact('countries'))->render(),
                'total'           => $countries->total(),
            ]);
        }

        // إحصاءات البنر — أربعة استعلامات بسيطة (لا حاجة لتجميع؛ عمودان بوليانيان فقط)
        $stats = [
            'total'         => (int) Country::count(),
            'active'        => (int) Country::where('is_active', true)->count(),
            'inactive'      => (int) Country::where('is_active', false)->count(),
            'with_branches' => (int) Country::has('branches')->count(),
        ];

        return view("admin.pages.countries.index", compact("countries", "stats"));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view("admin.pages.countries.create");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'code' => 'required|string|size:2|unique:countries,code',
            'code3' => 'nullable|string|size:3|unique:countries,code3',
            'phone_code' => 'nullable|string|max:10',
            'currency_code' => 'nullable|string|max:3',
            'flag' => 'nullable|string|max:10',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer',
        ], [
            'name.required' => 'اسم الدولة مطلوب',
            'code.required' => 'كود الدولة مطلوب',
            'code.size' => 'كود الدولة يجب أن يكون حرفين',
            'code.unique' => 'كود الدولة مستخدم بالفعل',
        ]);

        Country::create([
            'name' => $request->name,
            'name_ar' => $request->name_ar,
            'code' => strtoupper($request->code),
            'code3' => $request->code3 ? strtoupper($request->code3) : null,
            'phone_code' => $request->phone_code,
            'currency_code' => $request->currency_code ? strtoupper($request->currency_code) : null,
            'flag' => $request->flag,
            'is_active' => $request->has('is_active'),
            'sort_order' => $request->sort_order ?? 0,
        ]);

        return redirect()->route("admin.countries.index")->with("success", "تم إضافة الدولة بنجاح");
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $country = Country::withCount(['employees', 'branches'])->findOrFail($id);
        return view("admin.pages.countries.show", compact("country"));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $country = Country::findOrFail($id);
        return view("admin.pages.countries.edit", compact("country"));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $country = Country::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'code' => 'required|string|size:2|unique:countries,code,' . $id,
            'code3' => 'nullable|string|size:3|unique:countries,code3,' . $id,
            'phone_code' => 'nullable|string|max:10',
            'currency_code' => 'nullable|string|max:3',
            'flag' => 'nullable|string|max:10',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer',
        ], [
            'name.required' => 'اسم الدولة مطلوب',
            'code.required' => 'كود الدولة مطلوب',
            'code.size' => 'كود الدولة يجب أن يكون حرفين',
            'code.unique' => 'كود الدولة مستخدم بالفعل',
        ]);

        $country->update([
            'name' => $request->name,
            'name_ar' => $request->name_ar,
            'code' => strtoupper($request->code),
            'code3' => $request->code3 ? strtoupper($request->code3) : null,
            'phone_code' => $request->phone_code,
            'currency_code' => $request->currency_code ? strtoupper($request->currency_code) : null,
            'flag' => $request->flag,
            'is_active' => $request->has('is_active'),
            'sort_order' => $request->sort_order ?? 0,
        ]);

        return redirect()->route('admin.countries.index')->with('success', 'تم تحديث بيانات الدولة بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        // كان يقرأ $request->id من حقل مخفي في مودال الحذف القديم بدل
        // معرّف المسار — يعمل فقط مع ذلك المودال بالذات. النموذج المركزي
        // الجديد (data-delete-url) يرسل _token فقط، فيجب قراءة المعرّف
        // من المسار كبقية النظام.
        $country = Country::findOrFail($id);
        $country->delete();

        return redirect()->route("admin.countries.index")->with("success", "تم حذف الدولة بنجاح");
    }
}
