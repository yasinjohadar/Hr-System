<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;

class ExpenseCategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:expense-category-list')->only('index');
        $this->middleware('permission:expense-category-create')->only(['create', 'store']);
        $this->middleware('permission:expense-category-edit')->only(['edit', 'update']);
        $this->middleware('permission:expense-category-delete')->only('destroy');
        $this->middleware('permission:expense-category-show')->only('show');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = ExpenseCategory::with('creator')->withCount('expenseRequests');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('name_ar', 'like', "%$search%")
                  ->orWhere('code', 'like', "%$search%");
            });
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->input('is_active'));
        }

        $categories = $query->latest()->paginate(15);

        return view('admin.pages.expense-categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.pages.expense-categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'code' => 'nullable|string|max:50|unique:expense_categories,code',
            'description' => 'nullable|string',
            'description_ar' => 'nullable|string',
            'max_amount' => 'nullable|numeric|min:0',
            'requires_receipt' => 'boolean',
            'requires_approval' => 'boolean',
            'approval_levels' => 'required|integer|min:1|max:5',
            'is_active' => 'boolean',
        ]);

        $data = $request->all();
        $data['created_by'] = auth()->id();

        ExpenseCategory::create($data);

        return redirect()->route('admin.expense-categories.index')->with('success', 'تم إضافة تصنيف المصروف بنجاح.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $category = ExpenseCategory::with(['creator', 'expenseRequests'])->findOrFail($id);
        return view('admin.pages.expense-categories.show', compact('category'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $category = ExpenseCategory::findOrFail($id);
        return view('admin.pages.expense-categories.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $category = ExpenseCategory::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'code' => 'nullable|string|max:50|unique:expense_categories,code,' . $id,
            'description' => 'nullable|string',
            'description_ar' => 'nullable|string',
            'max_amount' => 'nullable|numeric|min:0',
            'requires_receipt' => 'boolean',
            'requires_approval' => 'boolean',
            'approval_levels' => 'required|integer|min:1|max:5',
            'is_active' => 'boolean',
        ]);

        $category->update($request->all());

        return redirect()->route('admin.expense-categories.index')->with('success', 'تم تحديث تصنيف المصروف بنجاح.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $category = ExpenseCategory::findOrFail($id);

        if ($category->expenseRequests()->count() > 0) {
            return redirect()->back()->with('error', 'لا يمكن حذف التصنيف لأنه يحتوي على طلبات مصروفات.');
        }

        $category->delete();

        return redirect()->route('admin.expense-categories.index')->with('success', 'تم حذف تصنيف المصروف بنجاح.');
    }
}
