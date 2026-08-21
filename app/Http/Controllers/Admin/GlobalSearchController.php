<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * بحث الهيدر السريع عن الأشخاص.
 *
 * يخدم مربّع البحث في أعلى الصفحة (assets/js/admin-header-search.js).
 * يرجّع الموظفين والمستخدمين معاً في قائمة واحدة، ويحترم صلاحيات
 * المستخدم الحالي: من لا يملك employee-show لا يرى نتائج موظفين، ومن لا
 * يملك user-show لا يرى نتائج مستخدمين — فلا يكشف البحث ما لا يُسمح بفتحه.
 */
class GlobalSearchController extends Controller
{
    /** أقصى عدد نتائج لكل نوع. */
    private const PER_TYPE = 6;

    /** أدنى طول للكلمة المفتاحية — أقصر من ذلك يرجّع فارغاً بلا استعلام. */
    private const MIN_CHARS = 2;

    public function people(Request $request): JsonResponse
    {
        $term = trim((string) $request->query('q', ''));

        if (mb_strlen($term) < self::MIN_CHARS) {
            return response()->json([
                'query'   => $term,
                'results' => [],
                'message' => 'اكتب حرفين على الأقل',
            ]);
        }

        $user = $request->user();

        $canEmployees = $user->can('employee-show');
        $canUsers     = $user->can('user-show');

        if (! $canEmployees && ! $canUsers) {
            return response()->json([
                'query'   => $term,
                'results' => [],
                'message' => 'لا تملك صلاحية البحث عن الأشخاص',
            ], 403);
        }

        $employees = $canEmployees ? $this->searchEmployees($term) : collect();

        // لا نكرّر الشخص نفسه: الموظف يحمل user_id، فنستثني حسابات
        // المستخدمين التي ظهرت أصلاً كموظفين.
        $linkedUserIds = $employees->pluck('user_id')->filter()->all();
        $users = $canUsers ? $this->searchUsers($term, $linkedUserIds) : collect();

        return response()->json([
            'query'   => $term,
            'results' => $employees->concat($users)->values()->all(),
        ]);
    }

    /**
     * @return \Illuminate\Support\Collection<int, array<string, mixed>>
     */
    private function searchEmployees(string $term)
    {
        $like = '%' . $term . '%';

        return Employee::query()
            ->with(['user:id,name,email,photo', 'department:id,name', 'position:id,title'])
            ->where(function ($q) use ($like) {
                $q->where('full_name', 'like', $like)
                    ->orWhere('first_name', 'like', $like)
                    ->orWhere('last_name', 'like', $like)
                    ->orWhere('employee_code', 'like', $like)
                    ->orWhere('national_id', 'like', $like)
                    ->orWhere('work_email', 'like', $like)
                    ->orWhere('personal_email', 'like', $like)
                    ->orWhere('work_phone', 'like', $like)
                    ->orWhere('personal_phone', 'like', $like)
                    // اسم/بريد الحساب المرتبط — يجد الموظف بالبحث بحسابه
                    ->orWhereHas('user', function ($u) use ($like) {
                        $u->where('name', 'like', $like)
                            ->orWhere('email', 'like', $like);
                    });
            })
            ->orderBy('full_name')
            ->limit(self::PER_TYPE)
            ->get()
            ->map(fn (Employee $e) => [
                'type'    => 'employee',
                'type_ar' => 'موظف',
                'id'      => $e->id,
                'user_id' => $e->user_id,
                'title'   => $e->full_name ?: trim($e->first_name . ' ' . $e->last_name),
                'meta'    => $this->employeeMeta($e),
                'code'    => $e->employee_code,
                'avatar'  => $this->photoUrl($e->user?->photo),
                'initial' => $this->initial($e->full_name ?: $e->first_name),
                'url'     => route('admin.employees.show', $e->id),
            ]);
    }

    /**
     * @param  array<int, int>  $excludeUserIds
     * @return \Illuminate\Support\Collection<int, array<string, mixed>>
     */
    private function searchUsers(string $term, array $excludeUserIds)
    {
        $like = '%' . $term . '%';

        return User::query()
            ->with('roles:id,name')
            ->when($excludeUserIds !== [], fn ($q) => $q->whereNotIn('id', $excludeUserIds))
            ->where(function ($q) use ($like) {
                $q->where('name', 'like', $like)
                    ->orWhere('username', 'like', $like)
                    ->orWhere('email', 'like', $like)
                    ->orWhere('phone', 'like', $like);
            })
            ->orderBy('name')
            ->limit(self::PER_TYPE)
            ->get()
            ->map(fn (User $u) => [
                'type'    => 'user',
                'type_ar' => 'مستخدم',
                'id'      => $u->id,
                'user_id' => $u->id,
                'title'   => $u->name,
                'meta'    => $u->email ?: ($u->roles->pluck('name')->implode(', ') ?: '—'),
                'code'    => $u->roles->pluck('name')->implode(', ') ?: null,
                'avatar'  => $this->photoUrl($u->photo),
                'initial' => $this->initial($u->name),
                'url'     => route('users.show', $u->id),
            ]);
    }

    private function employeeMeta(Employee $employee): string
    {
        $parts = array_filter([
            $employee->employee_code,
            $employee->position?->title,
            $employee->department?->name,
        ]);

        return $parts !== [] ? implode(' · ', $parts) : ($employee->user?->email ?: '—');
    }

    /**
     * الصور مخزّنة على قرص public — نرجّع null لا مساراً مكسوراً إن غابت،
     * فتعرض الواجهة الحرف الأول بدلاً منها.
     */
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
