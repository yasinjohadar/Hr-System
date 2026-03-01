@extends('admin.layouts.master')

@section('page-title')
    الإشعارات
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">الإشعارات</h5>
                </div>
                <div>
                    <button type="button" class="btn btn-primary" id="markAllAsRead">
                        <i class="fas fa-check-double me-2"></i>تحديد الكل كمقروء
                    </button>
                    @can('notification-create')
                    <a href="{{ route('admin.notifications.create') }}" class="btn btn-success">
                        <i class="fas fa-plus me-2"></i>إرسال إشعار جديد
                    </a>
                    @endcan
                </div>
            </div>

            <!-- فلترة -->
            <div class="card mb-3">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.notifications.index') }}" class="row g-3">
                        <div class="col-md-4">
                            <select name="type" class="form-select">
                                <option value="">كل الأنواع</option>
                                <option value="leave_request" {{ request('type') == 'leave_request' ? 'selected' : '' }}>طلب إجازة</option>
                                <option value="attendance" {{ request('type') == 'attendance' ? 'selected' : '' }}>حضور</option>
                                <option value="salary" {{ request('type') == 'salary' ? 'selected' : '' }}>راتب</option>
                                <option value="performance_review" {{ request('type') == 'performance_review' ? 'selected' : '' }}>تقييم أداء</option>
                                <option value="system" {{ request('type') == 'system' ? 'selected' : '' }}>نظام</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <select name="is_read" class="form-select">
                                <option value="">كل الحالات</option>
                                <option value="0" {{ request('is_read') == '0' ? 'selected' : '' }}>غير مقروء</option>
                                <option value="1" {{ request('is_read') == '1' ? 'selected' : '' }}>مقروء</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary w-100">تطبيق الفلترة</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- قائمة الإشعارات -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">الإشعارات ({{ $notifications->total() }})</h5>
                </div>
                <div class="card-body">
                    <div class="list-group" id="notificationsList">
                        @forelse ($notifications as $notification)
                            <div class="list-group-item list-group-item-action {{ !$notification->is_read ? 'bg-light' : '' }}" 
                                 data-notification-id="{{ $notification->id }}">
                                <div class="d-flex w-100 justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <div class="d-flex align-items-center mb-2">
                                            @if ($notification->icon)
                                                <i class="{{ $notification->icon }} me-2 text-{{ $notification->color }}"></i>
                                            @endif
                                            <h6 class="mb-0 {{ !$notification->is_read ? 'fw-bold' : '' }}">
                                                {{ $notification->title }}
                                                @if (!$notification->is_read)
                                                    <span class="badge bg-danger ms-2">جديد</span>
                                                @endif
                                            </h6>
                                        </div>
                                        <p class="mb-1">{{ $notification->message_ar ?? $notification->message }}</p>
                                        <small class="text-muted">
                                            <i class="fas fa-clock me-1"></i>{{ $notification->created_at->diffForHumans() }}
                                        </small>
                                    </div>
                                    <div class="flex-shrink-0 ms-3">
                                        @if ($notification->action_url)
                                            <a href="{{ $notification->action_url }}" class="btn btn-sm btn-{{ $notification->color }}">
                                                {{ $notification->action_text ?? 'عرض' }}
                                            </a>
                                        @endif
                                        <a href="{{ route('admin.notifications.show', $notification->id) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @can('notification-delete')
                                        <button type="button" class="btn btn-sm btn-danger delete-notification" data-id="{{ $notification->id }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        @endcan
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="alert alert-info text-center">
                                <i class="fas fa-bell-slash fa-2x mb-2"></i>
                                <p class="mb-0">لا توجد إشعارات</p>
                            </div>
                        @endforelse
                    </div>

                    <div class="mt-3">
                        {{ $notifications->withQueryString()->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<script>
    // إعداد Pusher للـ Real-time notifications
    const pusher = new Pusher('{{ config('broadcasting.connections.pusher.key') }}', {
        cluster: '{{ config('broadcasting.connections.pusher.options.cluster') }}',
        encrypted: true
    });

    // الاستماع للإشعارات
    const channel = pusher.subscribe('private-user.{{ auth()->id() }}');
    
    channel.bind('notification.sent', function(data) {
        // إضافة الإشعار الجديد للقائمة
        addNotificationToList(data);
        
        // تحديث عداد الإشعارات
        updateNotificationCount();
        
        // عرض إشعار Toast
        showToastNotification(data);
    });

    // الاستماع للقناة العامة
    const publicChannel = pusher.subscribe('notifications');
    publicChannel.bind('notification.sent', function(data) {
        if (data.user_id === {{ auth()->id() }}) {
            addNotificationToList(data);
            updateNotificationCount();
            showToastNotification(data);
        }
    });

    function addNotificationToList(data) {
        const list = document.getElementById('notificationsList');
        const notificationHtml = `
            <div class="list-group-item list-group-item-action bg-light" data-notification-id="${data.id}">
                <div class="d-flex w-100 justify-content-between align-items-start">
                    <div class="flex-grow-1">
                        <div class="d-flex align-items-center mb-2">
                            <i class="${data.icon || 'fas fa-bell'} me-2 text-${data.color}"></i>
                            <h6 class="mb-0 fw-bold">
                                ${data.title}
                                <span class="badge bg-danger ms-2">جديد</span>
                            </h6>
                        </div>
                        <p class="mb-1">${data.message}</p>
                        <small class="text-muted">
                            <i class="fas fa-clock me-1"></i>الآن
                        </small>
                    </div>
                    <div class="flex-shrink-0 ms-3">
                        ${data.action_url ? `<a href="${data.action_url}" class="btn btn-sm btn-${data.color}">${data.action_text || 'عرض'}</a>` : ''}
                        <a href="/admin/notifications/${data.id}" class="btn btn-sm btn-info">
                            <i class="fas fa-eye"></i>
                        </a>
                    </div>
                </div>
            </div>
        `;
        list.insertAdjacentHTML('afterbegin', notificationHtml);
    }

    function updateNotificationCount() {
        fetch('/admin/notifications/unread-count')
            .then(response => response.json())
            .then(data => {
                const badge = document.getElementById('notificationBadge');
                if (badge) {
                    badge.textContent = data.count;
                    badge.style.display = data.count > 0 ? 'inline' : 'none';
                }
            });
    }

    function showToastNotification(data) {
        // إنشاء Toast notification
        const toast = document.createElement('div');
        toast.className = `alert alert-${data.color} alert-dismissible fade show position-fixed`;
        toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        toast.innerHTML = `
            <strong>${data.title}</strong><br>
            ${data.message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.remove();
        }, 5000);
    }

    // تحديد الكل كمقروء
    document.getElementById('markAllAsRead')?.addEventListener('click', function() {
        fetch('/admin/notifications/mark-all-read', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        });
    });

    // حذف إشعار
    document.querySelectorAll('.delete-notification').forEach(btn => {
        btn.addEventListener('click', function() {
            if (confirm('هل أنت متأكد من حذف هذا الإشعار؟')) {
                const id = this.getAttribute('data-id');
                fetch(`/admin/notifications/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ id: id })
                })
                .then(() => {
                    this.closest('.list-group-item').remove();
                });
            }
        });
    });
</script>
@stop


