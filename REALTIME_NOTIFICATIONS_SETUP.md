# دليل إعداد نظام الإشعارات Real-time

## نظرة عامة

تم إعداد نظام إشعارات Real-time للموافقات باستخدام Laravel Broadcasting. النظام يدعم:
- **Pusher** (خدمة مدفوعة/مجانية محدودة)
- **Redis + Soketi** (بديل مجاني ومفتوح المصدر)

## المكونات المضافة

### 1. Notification Class
- `app/Notifications/ApprovalRequestNotification.php`
- يرسل إشعارات للموافقين عند بدء سير عمل جديد

### 2. Event Class
- `app/Events/ApprovalRequestSent.php`
- Event للبث المباشر (Real-time) عبر WebSockets

### 3. WorkflowService Updates
- تم تحديث `app/Services/WorkflowService.php` لاستخدام النظام الجديد
- إرسال إشعارات تلقائية عند بدء workflow

### 4. Frontend JavaScript
- تم تحديث `resources/views/admin/layouts/footer-scripts.blade.php`
- إضافة دعم للاستماع لإشعارات الموافقات Real-time

## الإعداد

### الخيار 1: استخدام Pusher (سهل - يحتاج حساب)

1. **إنشاء حساب Pusher:**
   - اذهب إلى https://pusher.com
   - أنشئ حساب مجاني (يدعم حتى 200K رسالة/يوم)
   - احصل على App ID, Key, Secret, Cluster

2. **تثبيت Pusher PHP SDK:**
   ```bash
   composer require pusher/pusher-php-server
   ```

3. **إعداد `.env`:**
   ```env
   BROADCAST_CONNECTION=pusher
   
   PUSHER_APP_ID=your-app-id
   PUSHER_APP_KEY=your-app-key
   PUSHER_APP_SECRET=your-app-secret
   PUSHER_APP_CLUSTER=mt1
   ```

4. **إعداد `config/broadcasting.php`:**
   ```php
   'pusher' => [
       'driver' => 'pusher',
       'key' => env('PUSHER_APP_KEY'),
       'secret' => env('PUSHER_APP_SECRET'),
       'app_id' => env('PUSHER_APP_ID'),
       'options' => [
           'cluster' => env('PUSHER_APP_CLUSTER'),
           'encrypted' => true,
       ],
   ],
   ```

### الخيار 2: استخدام Redis + Soketi (مجاني بالكامل)

1. **تثبيت Redis:**
   ```bash
   # Windows (باستخدام Chocolatey)
   choco install redis-64
   
   # أو استخدام Redis من WSL
   ```

2. **تثبيت Soketi:**
   ```bash
   npm install -g @soketi/soketi
   ```

3. **تشغيل Soketi:**
   ```bash
   soketi start
   ```
   سيعمل على `http://localhost:6001` افتراضياً

4. **إعداد `.env`:**
   ```env
   BROADCAST_CONNECTION=redis
   
   REDIS_CLIENT=phpredis
   REDIS_HOST=127.0.0.1
   REDIS_PASSWORD=null
   REDIS_PORT=6379
   ```

5. **إعداد `config/broadcasting.php`:**
   ```php
   'redis' => [
       'driver' => 'redis',
       'connection' => 'default',
   ],
   ```

6. **تحديث JavaScript في `footer-scripts.blade.php`:**
   ```javascript
   // استبدل Pusher بـ Laravel Echo
   <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.15.3/dist/echo.iife.js"></script>
   <script src="https://cdn.jsdelivr.net/npm/pusher-js@8.2.0/dist/web/pusher.min.js"></script>
   
   <script>
       window.Echo = new Echo({
           broadcaster: 'pusher',
           key: 'your-soketi-key', // يمكن أن يكون أي قيمة
           wsHost: window.location.hostname,
           wsPort: 6001,
           wssPort: 6001,
           forceTLS: false,
           encrypted: true,
           disableStats: true,
           enabledTransports: ['ws', 'wss'],
       });
       
       // الاستماع لإشعارات الموافقات
       Echo.private(`user.${userId}`)
           .listen('.approval.request', (e) => {
               showApprovalNotification(e);
           });
   </script>
   ```

## الاستخدام

### في الكود

عند بدء workflow جديد، سيتم إرسال الإشعارات تلقائياً:

```php
use App\Services\WorkflowService;

$workflowService = app(WorkflowService::class);
$instance = $workflowService->startWorkflow(
    'leave_request',
    $employee,
    'LeaveRequest',
    $leaveRequest->id
);
// سيتم إرسال الإشعار تلقائياً للموافق الأول
```

### في Frontend

الإشعارات ستظهر تلقائياً كـ Toast notifications في الزاوية العلوية اليمنى.

## الميزات

1. **إشعارات Real-time:** تظهر فوراً بدون تحديث الصفحة
2. **Toast Notifications:** إشعارات جميلة مع زر مباشر للانتقال للطلب
3. **تحديث تلقائي:** عداد الإشعارات وقائمة الإشعارات تتحدث تلقائياً
4. **صوت تنبيه:** صوت خفيف عند وصول إشعار جديد (اختياري)

## استكشاف الأخطاء

### الإشعارات لا تظهر

1. **تحقق من إعدادات Broadcasting:**
   ```bash
   php artisan config:clear
   php artisan cache:clear
   ```

2. **تحقق من Queue:**
   ```bash
   php artisan queue:work
   ```
   (لأن Notifications تستخدم Queue)

3. **تحقق من Console في المتصفح:**
   - افتح Developer Tools (F12)
   - اذهب إلى Console
   - تحقق من وجود أخطاء JavaScript

4. **تحقق من Pusher/Soketi:**
   - تأكد أن الخدمة تعمل
   - تحقق من الاتصال

### القنوات الخاصة لا تعمل

1. **تحقق من `routes/channels.php`:**
   - تأكد أن الملف موجود
   - تأكد من `bootstrap/app.php` يشير إليه

2. **تحقق من Authentication:**
   - تأكد أن المستخدم مسجل دخول
   - تحقق من CSRF token

## ملاحظات مهمة

1. **Queue Workers:** يجب تشغيل `php artisan queue:work` لأن Notifications تستخدم Queue
2. **Private Channels:** تحتاج إلى authentication endpoint (`/broadcasting/auth`)
3. **CORS:** تأكد من إعداد CORS بشكل صحيح إذا كان Frontend على domain مختلف

## الخطوات التالية

1. إضافة إشعارات عند الموافقة/الرفض
2. إضافة إشعارات عند انتقال الطلب لخطوة جديدة
3. إضافة تذكيرات للموافقات المعلقة
4. إضافة إشعارات للموظف عند تغيير حالة طلبه
