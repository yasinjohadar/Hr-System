<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomNotification;
use App\Models\User;
use App\Models\Employee;
use App\Events\NotificationSent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class NotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:notification-list')->only(['index', 'show']);
        $this->middleware('permission:notification-create')->only(['create', 'store']);
        $this->middleware('permission:notification-delete')->only('destroy');
    }

    /**
     * عرض جميع الإشعارات
     */
    public function index(Request $request)
    {
        $query = CustomNotification::with(['user', 'creator'])
            ->where('user_id', auth()->id())
            ->orWhere(function ($q) {
                $q->where('recipient_type', 'all')
                  ->orWhereJsonContains('recipient_ids', auth()->id());
            });

        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        if ($request->filled('is_read')) {
            $query->where('is_read', $request->input('is_read'));
        }

        $notifications = $query->orderBy('created_at', 'desc')->paginate(20);
        $unreadCount = CustomNotification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->count();

        return view('admin.pages.notifications.index', compact('notifications', 'unreadCount'));
    }

    /**
     * عرض إشعار محدد
     */
    public function show(string $id)
    {
        $notification = CustomNotification::findOrFail($id);
        
        // تحديد الإشعار كمقروء
        if ($notification->user_id == auth()->id() && !$notification->is_read) {
            $notification->markAsRead();
        }

        return view('admin.pages.notifications.show', compact('notification'));
    }

    /**
     * إنشاء إشعار جديد
     */
    public function create()
    {
        $users = User::where('is_active', true)->get();
        $employees = Employee::where('is_active', true)->get();
        return view('admin.pages.notifications.create', compact('users', 'employees'));
    }

    /**
     * حفظ إشعار جديد
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'required|string',
            'recipient_type' => 'required|in:user,role,all,department,branch',
            'user_id' => 'required_if:recipient_type,user|exists:users,id',
            'color' => 'nullable|in:info,success,warning,danger',
        ]);

        $data = $request->all();
        $data['created_by'] = auth()->id();
        $data['is_sent'] = true;
        $data['sent_at'] = now();

        // إنشاء الإشعار
        $notification = CustomNotification::create($data);

        // إرسال الإشعار Real-time
        if ($notification->user_id) {
            // إرسال لمستخدم محدد
            event(new NotificationSent($notification));
            
            // إرسال عبر Laravel Notifications (اختياري)
            // $user = User::find($notification->user_id);
            // if ($user) {
            //     $user->notify(new \App\Notifications\CustomNotification(
            //         $notification->title,
            //         $notification->message,
            //         $notification->type,
            //         $notification->action_url,
            //         $notification->action_text,
            //         $notification->icon,
            //         $notification->color,
            //         $notification->message_ar,
            //         $notification->data
            //     ));
            // }
        } elseif ($notification->recipient_type == 'all') {
            // إرسال لجميع المستخدمين
            $users = User::where('is_active', true)->get();
            foreach ($users as $user) {
                $userNotification = CustomNotification::create([
                    'type' => $notification->type,
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'message_ar' => $notification->message_ar,
                    'user_id' => $user->id,
                    'recipient_type' => $notification->recipient_type,
                    'action_url' => $notification->action_url,
                    'action_text' => $notification->action_text,
                    'icon' => $notification->icon,
                    'color' => $notification->color,
                    'data' => $notification->data,
                    'is_sent' => true,
                    'sent_at' => now(),
                    'created_by' => auth()->id(),
                ]);
                event(new NotificationSent($userNotification));
            }
        }

        return redirect()->route('admin.notifications.index')->with('success', 'تم إرسال الإشعار بنجاح');
    }

    /**
     * تحديد إشعار كمقروء
     */
    public function markAsRead(Request $request, string $id)
    {
        $notification = CustomNotification::findOrFail($id);
        
        if ($notification->user_id == auth()->id()) {
            $notification->markAsRead();
            return response()->json(['success' => true, 'message' => 'تم تحديد الإشعار كمقروء']);
        }

        return response()->json(['success' => false, 'message' => 'غير مصرح'], 403);
    }

    /**
     * تحديد جميع الإشعارات كمقروءة
     */
    public function markAllAsRead()
    {
        CustomNotification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        return response()->json(['success' => true, 'message' => 'تم تحديد جميع الإشعارات كمقروءة']);
    }

    /**
     * حذف إشعار
     */
    public function destroy(Request $request)
    {
        $notification = CustomNotification::findOrFail($request->id);
        
        if ($notification->user_id == auth()->id()) {
            $notification->delete();
            return redirect()->route('admin.notifications.index')->with('success', 'تم حذف الإشعار بنجاح');
        }

        return redirect()->route('admin.notifications.index')->with('error', 'غير مصرح لك بحذف هذا الإشعار');
    }

    /**
     * الحصول على عدد الإشعارات غير المقروءة (API)
     */
    public function getUnreadCount()
    {
        $count = CustomNotification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->count();

        return response()->json(['count' => $count]);
    }

    /**
     * الحصول على آخر الإشعارات (API)
     */
    public function getLatest()
    {
        $notifications = CustomNotification::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'title' => $notification->title,
                    'message' => $notification->message_ar ?? $notification->message,
                    'icon' => $notification->icon,
                    'color' => $notification->color,
                    'is_read' => $notification->is_read,
                    'created_at' => $notification->created_at->diffForHumans(),
                ];
            });

        return response()->json($notifications);
    }
}

