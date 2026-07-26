<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    // public function __construct()
    // {
    //     // يمكنه فقط رؤية قائمة المستخدمين (index)
    //     $this->middleware(['permission:user-list'])->only('index');

    //     // يمكنه فقط إنشاء مستخدم جديد (create + store)
    //     $this->middleware(['permission:user-create'])->only(['create', 'store']);

    //     // يمكنه فقط تعديل المستخدم (edit + update)
    //     $this->middleware(['permission:user-edit'])->only(['edit', 'update']);

    //     // يمكنه فقط حذف المستخدم (destroy)
    //     $this->middleware(['permission:user-delete'])->only('destroy');

    //     // يمكنه فقط رؤية ملف المستخدم (show)
    //     $this->middleware(['permission:user-show'])->only('show');
    // }

    public function __construct()
{
    // تأكد أن المستخدم مصادق أولًا ثم تحقق من الصلاحيات
    $this->middleware('auth');

    // تحذير أمني: أي دالة عامة غير مُدرَجة في أسطر ->only() أدناه تكون مفتوحة
    // لأي مستخدم يمرّ عبر middleware المسار. أضِف كل دالة جديدة هنا فوراً.
    $this->middleware('permission:user-list')->only(['index', 'search']);
    $this->middleware('permission:user-create')->only(['create', 'store']);
    $this->middleware('permission:user-edit')->only(['edit', 'update']);
    $this->middleware('permission:user-delete')->only('destroy');
    $this->middleware('permission:user-show')->only(['show', 'generateLoginCode']);
    $this->middleware('permission:user-change-password')->only('updatePassword');
    $this->middleware('permission:user-toggle-status')->only('toggleStatus');
}

    /**
     * Display a listing of the resource.
     */
public function index(Request $request)
    {
        $users = $this->filteredUsersQuery($request, useQueryParam: true)
            ->latest('id')
            ->paginate(10)
            ->withQueryString();
        $sessions = $this->latestSessionsMap();

        if ($request->ajax()) {
            return response()->json([
                'body' => view('admin.partials.users-table-body', compact('users', 'sessions'))->render(),
                'extra' => view('admin.partials.users-table-footer', compact('users'))->render(),
                'from' => $users->firstItem(),
                'to' => $users->lastItem(),
                'total' => $users->total(),
            ]);
        }

        return view('admin.pages.users.index', [
            'users' => $users,
            'sessions' => $sessions,
            'userStats' => $this->userStats(),
        ]);
    }





    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::all();
        return view("admin.pages.users.create" ,compact("roles"));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->normalizeOptionalUserFields($request);
        $this->applyUsernameIntent($request);

        // التحقق من صحة البيانات
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => ['nullable', 'string', 'max:255', Rule::unique('users', 'username')],
            'email' => 'required|string|email|max:255|unique:users,email',
            'phone' => ['nullable', 'string', 'max:20', Rule::unique('users', 'phone')],
            'password' => 'required|string|min:8|confirmed',
            'status' => 'required|in:active,inactive,banned',
            'is_active' => 'boolean',
            'roles' => 'array',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'name.required' => 'الاسم مطلوب',
            'email.required' => 'البريد الإلكتروني مطلوب',
            'email.email' => 'البريد الإلكتروني غير صحيح',
            'email.unique' => 'البريد الإلكتروني مستخدم بالفعل',
            'username.unique' => 'اسم المستخدم مستخدم بالفعل',
            'phone.unique' => 'رقم الهاتف مستخدم بالفعل',
            'password.required' => 'كلمة المرور مطلوبة',
            'password.min' => 'كلمة المرور يجب أن تكون 8 أحرف على الأقل',
            'password.confirmed' => 'تأكيد كلمة المرور غير متطابق',
            'status.required' => 'حالة المستخدم مطلوبة',
            'photo.image' => 'يجب أن يكون الملف صورة',
            'photo.mimes' => 'نوع الصورة غير مدعوم',
            'photo.max' => 'حجم الصورة يجب أن يكون أقل من 2 ميجابايت',
        ]);

        // معالجة الصورة
        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $photoName = time() . '_' . $photo->getClientOriginalName();
            $photoPath = $photo->storeAs('users/photos', $photoName, 'public');
        }

        // إنشاء المستخدم
        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'status' => $request->status,
            'is_active' => $request->has('is_active'),
            'photo' => $photoPath,
            'created_by' => auth()->id(), // المستخدم الذي أنشأ هذا الحساب
        ]);

        // تعيين الأدوار
        if ($request->has('roles')) {
            $user->syncRoles($request->roles);
        }

        return redirect()->route("users.index")->with("success" , "تم إضافة مستخدم جديد بنجاح");
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::with(['roles', 'employee'])->findOrFail($id);
        $lastSession = DB::table('sessions')
            ->where('user_id', $user->id)
            ->orderByDesc('last_activity')
            ->first();

        return view('admin.pages.users.profile', compact('user', 'lastSession'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = User::findOrFail($id);
        $roles = Role::all();
        return view("admin.pages.users.edit" ,compact("roles" , "user"));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $this->normalizeOptionalUserFields($request);
        $this->applyUsernameIntent($request, $user);

        // التحقق من صحة البيانات
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => ['nullable', 'string', 'max:255', Rule::unique('users', 'username')->ignore($user->id)],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:20', Rule::unique('users', 'phone')->ignore($user->id)],
            'password' => 'nullable|string|min:8|confirmed',
            'status' => 'required|in:active,inactive,banned',
            'is_active' => 'boolean',
            'roles' => 'array',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'name.required' => 'الاسم مطلوب',
            'email.required' => 'البريد الإلكتروني مطلوب',
            'email.email' => 'البريد الإلكتروني غير صحيح',
            'email.unique' => 'البريد الإلكتروني مستخدم بالفعل',
            'username.unique' => 'اسم المستخدم مستخدم بالفعل',
            'phone.unique' => 'رقم الهاتف مستخدم بالفعل',
            'password.min' => 'كلمة المرور يجب أن تكون 8 أحرف على الأقل',
            'password.confirmed' => 'تأكيد كلمة المرور غير متطابق',
            'status.required' => 'حالة المستخدم مطلوبة',
            'photo.image' => 'يجب أن يكون الملف صورة',
            'photo.mimes' => 'نوع الصورة غير مدعوم',
            'photo.max' => 'حجم الصورة يجب أن يكون أقل من 2 ميجابايت',
        ]);

        // تجهيز البيانات للتحديث
        $updateData = [
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'phone' => $request->phone,
            'status' => $request->status,
            'is_active' => $request->has('is_active'),
        ];

        // تحديث كلمة المرور فقط إذا تم إدخالها
        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        // معالجة الصورة
        if ($request->hasFile('photo')) {
            // حذف الصورة القديمة إذا كانت موجودة
            if ($user->photo) {
                \Storage::disk('public')->delete($user->photo);
            }

            $photo = $request->file('photo');
            $photoName = time() . '_' . $photo->getClientOriginalName();
            $photoPath = $photo->storeAs('users/photos', $photoName, 'public');
            $updateData['photo'] = $photoPath;
        }

        // تحديث المستخدم
        $user->update($updateData);

        // تحديث الأدوار
        if ($request->has('roles')) {
            $user->syncRoles($request->roles);
        }

        return redirect()->route('users.index')->with('success', 'تم تحديث بيانات المستخدم بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $user = User::findOrFail($request->id);

        $user->delete();

        return redirect()->route("users.index")->with("success" , "تم حذف مستخدم جديد بنجاح");

    }



    public function updatePassword(Request $request, User $user)
{
    $request->validate([
        'password' => 'required|string|min:8|confirmed',
    ], [
        'password.required' => 'كلمة المرور مطلوبة',
        'password.min' => 'كلمة المرور يجب أن تكون 8 أحرف على الأقل',
        'password.confirmed' => 'تأكيد كلمة المرور غير متطابق',
    ]);

    $user->update([
        'password' => Hash::make($request->password),
    ]);

    return redirect()->route('users.index')->with('success', 'تم تحديث كلمة المرور بنجاح');
}

/**
 * تبديل حالة المستخدم (تفعيل/إلغاء تفعيل)
 */
public function toggleStatus(Request $request, $id)
{
    try {
        \Log::info('Toggle status request received', [
            'user_id' => $id,
            'request_data' => $request->all(),
            'request_method' => $request->method(),
            'request_url' => $request->url(),
            'request_headers' => $request->headers->all(),
            'auth_user' => auth()->id()
        ]);

        $user = User::findOrFail($id);

        \Log::info('User found', [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'current_is_active' => $user->is_active
        ]);

        // التحقق من أن المستخدم لا يحاول إلغاء تفعيل نفسه
        if ($user->id === auth()->id()) {
            \Log::warning('User tried to deactivate themselves', [
                'user_id' => $user->id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'لا يمكنك إلغاء تفعيل حسابك'
            ], 400);
        }

        // حفظ الحالة القديمة
        $oldStatus = $user->is_active;

        // تبديل الحالة
        $newStatus = !$user->is_active;

        // تحديث الحالة باستخدام update للتأكد من التحديث
        $user->update(['is_active' => $newStatus]);

        // إعادة تحميل المستخدم للتأكد من الحصول على القيمة المحدثة
        $user->refresh();

        \Log::info('User status updated', [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'old_status' => $oldStatus,
            'new_status' => $user->is_active,
            'toggled_by' => auth()->id()
        ]);

        $status = $user->is_active ? 'مفعل' : 'غير مفعل';

        $response = [
            'success' => true,
            'message' => "تم تحديث حالة المستخدم إلى: {$status}",
            'is_active' => (bool) $user->is_active
        ];

        \Log::info('Toggle status response', [
            'user_id' => $user->id,
            'response' => $response
        ]);

        return response()->json($response);

    } catch (\Exception $e) {
        \Log::error('Error toggling user status', [
            'user_id' => $id,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'toggled_by' => auth()->id()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'حدث خطأ أثناء تحديث حالة المستخدم: ' . $e->getMessage()
        ], 500);
    }
}

/**
 * توليد كود دخول مؤقت لمستخدم (يستخدم لمرة واحدة، صالح 15 دقيقة).
 */
    public function generateLoginCode(User $user)
    {
        if (!$user->is_active) {
            return response()->json(['error' => 'حساب المستخدم غير نشط.'], 422);
        }

        $code = Str::random(12);
        $cacheKey = 'user_login_code:' . $code;
        Cache::put($cacheKey, ['user_id' => $user->id], now()->addMinutes(15));

        $url = route('employee.login-by-code', ['code' => $code]);

        return response()->json([
            'code' => $code,
            'url' => $url,
        ]);
    }

    /**
     * بحث حي (AJAX) عن المستخدمين.
     */
    public function search(Request $request)
    {
        $users = $this->filteredUsersQuery($request, useQueryParam: false)->paginate(10);

        return view('admin.pages.users._table', [
            'users' => $users,
            'sessions' => $this->latestSessionsMap(),
        ]);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder<User>
     */
    private function filteredUsersQuery(Request $request, bool $useQueryParam = false)
    {
        $usersQuery = User::query()->with('roles');

        $searchKey = $useQueryParam ? 'query' : 'q';
        if ($request->filled($searchKey)) {
            $search = $request->input($searchKey);
            $usersQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $usersQuery->where('status', $request->input('status'));
        }

        if ($request->filled('is_active')) {
            $usersQuery->where('is_active', $request->input('is_active'));
        }

        return $usersQuery;
    }

    /**
     * @return \Illuminate\Support\Collection<int, object{user_id: int, last_activity: int}>
     */
    private function latestSessionsMap()
    {
        return DB::table('sessions')
            ->whereNotNull('user_id')
            ->select('user_id', DB::raw('MAX(last_activity) as last_activity'))
            ->groupBy('user_id')
            ->get()
            ->keyBy('user_id');
    }

    /**
     * تحويل الحقول الاختيارية الفارغة إلى null (وتجنب تعارض unique على سلسلة فارغة).
     */
    private function normalizeOptionalUserFields(Request $request): void
    {
        $request->merge([
            'username' => $request->filled('username') ? trim((string) $request->username) : null,
            'phone' => $request->filled('phone') ? trim((string) $request->phone) : null,
        ]);
    }

    /**
     * تجاهل اسم المستخدم المرسل تلقائياً من المتصفح ما لم يُفعَّل خيار التعيين صراحةً.
     */
    private function applyUsernameIntent(Request $request, ?User $user = null): void
    {
        if ($user && filled($user->username)) {
            return;
        }

        if (! $request->boolean('set_username')) {
            $request->merge(['username' => null]);
        }
    }

    /**
     * @return array<string, int>
     */
    private function userStats(): array
    {
        $row = User::query()->selectRaw('
            COUNT(*) as total,
            SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active_login,
            SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as inactive_status,
            SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as banned
        ', ['inactive', 'banned'])->first();

        return [
            'total' => (int) ($row->total ?? 0),
            'active_login' => (int) ($row->active_login ?? 0),
            'inactive_status' => (int) ($row->inactive_status ?? 0),
            'banned' => (int) ($row->banned ?? 0),
        ];
    }
}
