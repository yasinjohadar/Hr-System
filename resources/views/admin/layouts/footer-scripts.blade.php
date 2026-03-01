<!-- Scroll To Top -->
<div class="scrollToTop">
    <span class="arrow"><i class="las la-angle-double-up"></i></span>
</div>
<div id="responsive-overlay"></div>
<!-- Scroll To Top -->

<!-- Popper JS -->
<script src="{{ asset('assets/libs/@popperjs/core/umd/popper.min.js') }}"></script>
{{-- <script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script> --}}
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>


<!-- Defaultmenu JS -->
<script src="{{ asset('assets/js/defaultmenu.min.js') }}"></script>

<!-- Node Waves JS -->
<script src="{{ asset('assets/libs/node-waves/waves.min.js') }}"></script>

<!-- Sticky JS -->
<script src="{{ asset('assets/js/sticky.js') }}"></script>

<!-- Simplebar JS -->
<script src="{{ asset('assets/libs/simplebar/simplebar.min.js') }}"></script>
<script src="{{ asset('assets/js/simplebar.js') }}"></script>

<!-- Color Picker JS -->
<script src="{{ asset('assets/libs/@simonwep/pickr/pickr.es5.min.js') }}"></script>

<!-- Apex Charts JS -->
<script src="{{ asset('assets/libs/apexcharts/apexcharts.min.js') }}"></script>

<!-- JSVector Maps JS -->
<script src="{{ asset('assets/libs/jsvectormap/js/jsvectormap.min.js') }}"></script>

<!-- JSVector Maps MapsJS -->
<script src="{{ asset('assets/libs/jsvectormap/maps/world-merc.js') }}"></script>
<script src="{{ asset('assets/js/us-merc-en.js') }}"></script>

<!-- Chartjs Chart JS -->
<script src="{{ asset('assets/js/index.js') }}"></script>

<!-- Custom-Switcher JS -->
<script src="{{ asset('assets/js/custom-switcher.min.js') }}"></script>

<!-- Custom JS -->
<script src="{{ asset('assets/js/custom.js') }}"></script>

<!-- Sidebar Active Link JS -->
<script src="{{ asset('assets/js/sidebar-active.js') }}"></script>

<!-- Laravel Echo & Pusher for Real-time Notifications -->
@auth
<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.15.3/dist/echo.iife.js"></script>
<script>
    // إعداد Laravel Echo للـ Real-time notifications
    const pusherKey = '{{ config('broadcasting.connections.pusher.key', 'your-pusher-key') }}';
    const pusherCluster = '{{ config('broadcasting.connections.pusher.options.cluster', 'mt1') }}';
    const broadcastDriver = '{{ config('broadcasting.default', 'log') }}';
    
    // استخدام Laravel Echo إذا كان Pusher مفعل
    if (broadcastDriver === 'pusher' && pusherKey && pusherKey !== 'your-pusher-key') {
        window.Echo = new Echo({
            broadcaster: 'pusher',
            key: pusherKey,
            cluster: pusherCluster,
            encrypted: true,
            authEndpoint: '{{ route('broadcasting.auth') }}',
            auth: {
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                }
            }
        });

        // استخدام Pusher مباشر كبديل
        const pusher = new Pusher(pusherKey, {
            cluster: pusherCluster,
            encrypted: true,
            authEndpoint: '{{ route('broadcasting.auth') }}',
            auth: {
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                }
            }
        });

        // استخدام Laravel Echo (الأفضل)
        if (window.Echo) {
            // الاستماع لإشعارات الموافقات على القناة الخاصة
            Echo.private(`user.{{ auth()->id() }}`)
                .listen('.approval.request', (data) => {
                    updateNotificationCount();
                    loadLatestNotifications();
                    showApprovalNotification(data);
                });

            // الاستماع للإشعارات العامة
            Echo.private(`user.{{ auth()->id() }}`)
                .listen('.notification.sent', (data) => {
                    if (data.user_id === {{ auth()->id() }}) {
                        updateNotificationCount();
                        loadLatestNotifications();
                        showToastNotification(data);
                    }
                });

            // الاستماع للقناة العامة للموافقات
            Echo.channel('approvals')
                .listen('.approval.request', (data) => {
                    updateNotificationCount();
                    loadLatestNotifications();
                    showApprovalNotification(data);
                });
        } else {
            // استخدام Pusher مباشر كبديل
            const privateChannel = pusher.subscribe('private-user.{{ auth()->id() }}');
            
            privateChannel.bind('notification.sent', function(data) {
                updateNotificationCount();
                loadLatestNotifications();
                showToastNotification(data);
            });

            privateChannel.bind('approval.request', function(data) {
                updateNotificationCount();
                loadLatestNotifications();
                showApprovalNotification(data);
            });

            const publicChannel = pusher.subscribe('notifications');
            publicChannel.bind('notification.sent', function(data) {
                if (data.user_id === {{ auth()->id() }}) {
                    updateNotificationCount();
                    loadLatestNotifications();
                    showToastNotification(data);
                }
            });

            const approvalsChannel = pusher.subscribe('approvals');
            approvalsChannel.bind('approval.request', function(data) {
                updateNotificationCount();
                loadLatestNotifications();
                showApprovalNotification(data);
            });
        }
    }

    // تحديث عداد الإشعارات
    function updateNotificationCount() {
        fetch('{{ route('admin.notifications.unread-count') }}')
            .then(response => response.json())
            .then(data => {
                const badge = document.getElementById('notificationBadge');
                const countText = document.getElementById('notificationCountText');
                
                if (badge) {
                    if (data.count > 0) {
                        badge.textContent = data.count;
                        badge.style.display = 'inline';
                    } else {
                        badge.style.display = 'none';
                    }
                }
                
                if (countText) {
                    countText.textContent = `لديك ${data.count} إشعارات جديدة`;
                }
            })
            .catch(error => console.error('Error updating notification count:', error));
    }

    // تحميل آخر الإشعارات
    function loadLatestNotifications() {
        fetch('{{ route('admin.notifications.latest') }}')
            .then(response => response.json())
            .then(data => {
                const list = document.getElementById('header-notification-scroll');
                if (!list) return;
                
                if (data.length === 0) {
                    list.innerHTML = '<li class="dropdown-item text-center py-3"><p class="text-muted mb-0">لا توجد إشعارات جديدة</p></li>';
                    return;
                }
                
                list.innerHTML = data.map(notif => `
                    <li class="dropdown-item ${!notif.is_read ? 'bg-light' : ''}">
                        <div class="d-flex align-items-start">
                            <div class="flex-shrink-0">
                                <i class="${notif.icon || 'fas fa-bell'} text-${notif.color}"></i>
                            </div>
                            <div class="flex-grow-1 ms-2">
                                <h6 class="mb-1 ${!notif.is_read ? 'fw-bold' : ''}">${notif.title}</h6>
                                <p class="mb-1 small">${notif.message}</p>
                                <small class="text-muted">${notif.created_at}</small>
                            </div>
                        </div>
                    </li>
                `).join('');
            })
            .catch(error => console.error('Error loading notifications:', error));
    }

    // عرض Toast notification
    function showToastNotification(data) {
        // إنشاء Toast notification
        const toast = document.createElement('div');
        toast.className = `alert alert-${data.color || 'info'} alert-dismissible fade show position-fixed`;
        toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px; max-width: 400px;';
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

    // عرض إشعار الموافقة مع زر مباشر
    function showApprovalNotification(data) {
        // إنشاء Toast notification خاص بالموافقات
        const toast = document.createElement('div');
        toast.className = `alert alert-${data.color || 'warning'} alert-dismissible fade show position-fixed`;
        toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 350px; max-width: 450px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);';
        toast.innerHTML = `
            <div class="d-flex align-items-start">
                <div class="flex-shrink-0 me-2">
                    <i class="${data.icon || 'fas fa-bell'} fa-2x"></i>
                </div>
                <div class="flex-grow-1">
                    <strong class="d-block mb-1">${data.title}</strong>
                    <p class="mb-2 small">${data.message}</p>
                    ${data.action_url ? `<a href="${data.action_url}" class="btn btn-sm btn-primary">${data.action_text || 'عرض الطلب'}</a>` : ''}
                </div>
                <button type="button" class="btn-close ms-2" data-bs-dismiss="alert"></button>
            </div>
        `;
        document.body.appendChild(toast);
        
        // إضافة صوت تنبيه (اختياري)
        if (typeof Audio !== 'undefined') {
            try {
                const audio = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBjGH0fPTgjMGHm7A7+OZURAJR6Tf8sN0JgUwgM/z2IU5CBxsvO3mnlEQDE+n4fC2YxwGOJLX8sx5LAUkcMfw3ZBACxRetOnrqFUUCkaf4PK+bCEGMYfR89OCMwYebsDv45lREAlHpN/yw3QmBTCAz/PYhTkIHGy87eaeURAMT6fh8LZjHAY4ktfy');
                audio.volume = 0.3;
                audio.play().catch(() => {});
            } catch (e) {}
        }
        
        setTimeout(() => {
            toast.remove();
        }, 8000);
    }

    // تحديد الكل كمقروء
    document.getElementById('markAllReadHeader')?.addEventListener('click', function() {
        fetch('{{ route('admin.notifications.mark-all-read') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateNotificationCount();
                loadLatestNotifications();
            }
        });
    });

    // تحميل الإشعارات عند تحميل الصفحة
    document.addEventListener('DOMContentLoaded', function() {
        updateNotificationCount();
        loadLatestNotifications();
    });
</script>
@endauth

@yield('js')
