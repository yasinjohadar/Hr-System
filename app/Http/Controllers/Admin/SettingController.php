<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SettingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:setting-view')->only(['index', 'show']);
        $this->middleware('permission:setting-edit')->only(['update', 'updateGroup']);
    }

    /**
     * عرض جميع الإعدادات
     */
    public function index()
    {
        $groups = Setting::select('group')
            ->distinct()
            ->orderBy('group')
            ->pluck('group');

        $settings = Setting::orderBy('group')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('group');

        return view('admin.pages.settings.index', compact('groups', 'settings'));
    }

    /**
     * عرض إعدادات مجموعة معينة
     */
    public function show($group)
    {
        $settings = Setting::where('group', $group)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        if ($settings->isEmpty()) {
            return redirect()->route('admin.settings.index')
                ->with('error', 'المجموعة غير موجودة');
        }

        return view('admin.pages.settings.group', compact('settings', 'group'));
    }

    /**
     * تحديث إعداد واحد
     */
    public function update(Request $request, $id)
    {
        $setting = Setting::findOrFail($id);

        $request->validate([
            'value' => $setting->is_required ? 'required' : 'nullable',
        ]);

        $setting->update([
            'value' => $request->input('value'),
        ]);

        // مسح الكاش
        Cache::forget('setting_' . $setting->key);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث الإعداد بنجاح'
        ]);
    }

    /**
     * تحديث مجموعة من الإعدادات
     */
    public function updateGroup(Request $request, $group)
    {
        $settings = Setting::where('group', $group)->where('is_active', true)->get();
        
        foreach ($settings as $setting) {
            $value = $request->input($setting->key);
            
            if ($setting->is_required && empty($value)) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', "حقل {$setting->label} مطلوب");
            }

            // التحقق من القيمة حسب النوع
            if ($setting->type == 'number' && !is_numeric($value)) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', "حقل {$setting->label} يجب أن يكون رقماً");
            }

            if ($setting->type == 'boolean') {
                $value = $request->has($setting->key) ? '1' : '0';
            }

            $setting->update(['value' => $value]);
            
            // مسح الكاش
            Cache::forget('setting_' . $setting->key);
        }

        return redirect()->back()->with('success', 'تم تحديث الإعدادات بنجاح');
    }

    /**
     * إنشاء إعداد جديد
     */
    public function create()
    {
        return view('admin.pages.settings.create');
    }

    /**
     * حفظ إعداد جديد
     */
    public function store(Request $request)
    {
        $request->validate([
            'key' => 'required|string|unique:settings,key',
            'group' => 'required|string',
            'label' => 'required|string',
            'type' => 'required|in:text,textarea,number,boolean,select,file,email,url',
            'value' => 'nullable',
        ]);

        Setting::create($request->all());

        return redirect()->route('admin.settings.index')
            ->with('success', 'تم إنشاء الإعداد بنجاح');
    }
}
