<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * بيانات لوحة الهيدر الجانبية (offcanvas).
 *
 * كانت هذه اللوحة تعرض ثلاثة تبويبات مزيّفة بالكامل من القالب الأصلي:
 * "Chat" برسائل نصّية ثابتة («New Websites is Created»)، "Notifications"
 * بصور أشخاص غير موجودين، و"Friends" بأسماء وهمية («Mozelle Belt») تفتح
 * مودالاً غير مُضمَّن في الصفحة أصلاً (#chatmodel من modal-video-audio.blade.php
 * الذي لا يُستدعى في master.blade.php).
 *
 * استُبدلت بتبويبين ببيانات حقيقية: من يستخدم النظام الآن، ومن يملك صلاحيات
 * إدارية. تُجلب عند فتح اللوحة فقط (lazy) لا مع كل تحميل صفحة.
 */
class HeaderPanelController extends Controller
{
    /** يُعتبر المستخدم "نشطاً الآن" إن كان له نشاط جلسة خلال هذه المدة. */
    private const ONLINE_WINDOW_MINUTES = 5;

    /** أقصى عدد لكل قائمة — لوحة جانبية لا صفحة تصفّح. */
    private const PER_LIST = 12;

    /** الأدوار التي تُعرض في تبويب «الإدارة». */
    private const MANAGEMENT_ROLES = ['admin', 'general_manager', 'executive_director', 'department_head'];

    public function people(): JsonResponse
    {
        $activeSessions = DB::table('sessions')
            ->whereNotNull('user_id')
            ->where('last_activity', '>=', now()->subMinutes(self::ONLINE_WINDOW_MINUTES)->timestamp)
            ->select('user_id', DB::raw('MAX(last_activity) as last_activity'))
            ->groupBy('user_id')
            ->orderByDesc('last_activity')
            ->limit(self::PER_LIST)
            ->get();

        $activeUserIds = $activeSessions->pluck('user_id')->all();

        $managers = User::role(self::MANAGEMENT_ROLES)
            ->with('roles:id,name')
            ->orderBy('name')
            ->limit(self::PER_LIST)
            ->get();

        // كل معرّفات المستخدمين المطلوبة دفعة واحدة بدل استعلام لكل شخص
        $neededIds = array_unique(array_merge($activeUserIds, $managers->pluck('id')->all()));
        $users = User::whereIn('id', $neededIds)->with('roles:id,name')->get()->keyBy('id');

        // الأقسام التي يديرها كل مستخدم — استعلام واحد مجمّع
        $managedDepartments = Department::whereIn('manager_id', $neededIds)
            ->get(['manager_id', 'name'])
            ->groupBy('manager_id');

        $active = collect($activeUserIds)
            ->map(fn ($id) => $users->get($id))
            ->filter()
            ->map(fn (User $user) => $this->present($user, $managedDepartments, activeNow: true))
            ->values();

        $managerList = $managers
            ->map(fn (User $user) => $this->present($user, $managedDepartments, activeNow: in_array($user->id, $activeUserIds, true)))
            ->values();

        return response()->json([
            'active'   => $active,
            'managers' => $managerList,
        ]);
    }

    /**
     * @param  \Illuminate\Support\Collection<int, \Illuminate\Support\Collection<int, Department>>  $managedDepartments
     * @return array<string, mixed>
     */
    private function present(User $user, $managedDepartments, bool $activeNow): array
    {
        $roleNames = $user->roles->pluck('name');
        // أوّل دور إداري يملكه المستخدم بترتيب الأهمية، وإلا أوّل دور لديه
        $primaryRole = collect(self::MANAGEMENT_ROLES)->first(fn ($role) => $roleNames->contains($role))
            ?? $roleNames->first();

        $departments = $managedDepartments->get($user->id, collect())->pluck('name');

        $subtitle = $departments->isNotEmpty()
            ? $this->roleLabel($primaryRole) . ' · ' . $departments->implode('، ')
            : $this->roleLabel($primaryRole);

        return [
            'id'      => $user->id,
            'name'    => $user->name,
            'avatar'  => $this->photoUrl($user->photo),
            'initial' => $this->initial($user->name),
            'subtitle' => $subtitle,
            'active'  => $activeNow,
            'url'     => route('users.show', $user->id),
        ];
    }

    private function roleLabel(?string $role): string
    {
        return match ($role) {
            'admin' => 'مدير النظام',
            'general_manager' => 'مدير عام',
            'executive_director' => 'مدير تنفيذي',
            'department_head' => 'رئيس قسم',
            'employee' => 'موظف',
            default => 'مستخدم',
        };
    }

    private function photoUrl(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        return Storage::disk('public')->exists($path)
            ? Storage::disk('public')->url($path)
            : null;
    }

    private function initial(?string $name): string
    {
        $name = trim((string) $name);

        return $name === '' ? '؟' : mb_substr($name, 0, 1);
    }
}
