# دليل نظام الإشعارات Real-time للموافقات

## ✅ ما تم إضافته

### 1. ملفات جديدة
- ✅ `app/Notifications/ApprovalRequestNotification.php` - Notification class للموافقات
- ✅ `app/Events/ApprovalRequestSent.php` - Event للبث المباشر
- ✅ `routes/channels.php` - تعريف القنوات والصلاحيات
- ✅ Route للـ broadcasting authentication في `routes/web.php`

### 2. تحديثات
- ✅ `app/Services/WorkflowService.php` - تفعيل إرسال الإشعارات
- ✅ `resources/views/admin/layouts/footer-scripts.blade.php` - JavaScript للاستماع Real-time
- ✅ `bootstrap/app.php` - إضافة routes/channels.php

## 🚀 الإعداد السريع

### الخطوة 1: اختيار طريقة البث

#### الخيار A: Pusher (سهل - يحتاج حساب مجاني)
```bash
composer require pusher/pusher-php-server
```

في `.env`:
```env
BROADCAST_CONNECTION=pusher

PUSHER_APP_ID=your-app-id
PUSHER_APP_KEY=your-app-key
PUSHER_APP_SECRET=your-app-secret
PUSHER_APP_CLUSTER=mt1
```

#### الخيار B: Redis + Soketi (مجاني بالكامل)
```bash
# تثبيت Redis (Windows)
choco install redis-64

# تثبيت Soketi
npm install -g @soketi/soketi

# تشغيل Soketi
soketi start
```

في `.env`:
```env
BROADCAST_CONNECTION=redis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
```

### الخطوة 2: تشغيل Queue Worker
```bash
php artisan queue:work
```
**مهم:** يجب تشغيل Queue Worker لأن Notifications تستخدم Queue.

### الخطوة 3: مسح الكاش
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

## 📱 كيف يعمل النظام

### عند بدء Workflow جديد:

1. **WorkflowService** يستدعي `notifyApprover()`
2. يتم إرسال **Notification** (Database + Broadcast)
3. يتم إرسال **Event** للبث المباشر
4. **Frontend** يستقبل الإشعار فوراً عبر WebSocket
5. يظهر **Toast Notification** في الزاوية العلوية اليمنى

### أنواع الإشعارات:

- ✅ **إشعارات الموافقات:** عند بدء workflow جديد
- ✅ **إشعارات عامة:** من نظام الإشعارات الموجود

## 🎨 الميزات

1. **Real-time:** إشعارات فورية بدون تحديث الصفحة
2. **Toast Notifications:** إشعارات جميلة مع زر مباشر
3. **تحديث تلقائي:** عداد الإشعارات يتحدث تلقائياً
4. **صوت تنبيه:** صوت خفيف عند وصول إشعار (اختياري)
5. **قنوات خاصة:** كل مستخدم يستقبل إشعاراته فقط

## 🔧 استكشاف الأخطاء

### المشكلة: الإشعارات لا تظهر

**الحل:**
1. تأكد من تشغيل `php artisan queue:work`
2. تحقق من إعدادات `.env` (BROADCAST_CONNECTION)
3. افتح Console في المتصفح (F12) وتحقق من الأخطاء
4. تأكد من أن Pusher/Soketi يعمل

### المشكلة: القنوات الخاصة لا تعمل

**الحل:**
1. تحقق من `routes/channels.php` موجود
2. تحقق من `bootstrap/app.php` يشير إليه
3. تأكد من route `/broadcasting/auth` موجود
4. تحقق من CSRF token

### المشكلة: JavaScript errors

**الحل:**
1. تأكد من تحميل Pusher.js أو Laravel Echo
2. تحقق من أن `pusherKey` ليس `'your-pusher-key'`
3. تأكد من أن المستخدم مسجل دخول (`@auth`)

## 📝 ملاحظات مهمة

1. **Queue Workers:** يجب تشغيلها دائماً
2. **Private Channels:** تحتاج authentication
3. **CORS:** قد تحتاج إعداد CORS إذا كان Frontend على domain مختلف
4. **Pusher Free Plan:** يدعم حتى 200K رسالة/يوم

## 🎯 الخطوات التالية (اختياري)

- [ ] إضافة إشعارات عند الموافقة/الرفض
- [ ] إضافة إشعارات عند انتقال الطلب لخطوة جديدة
- [ ] إضافة تذكيرات للموافقات المعلقة
- [ ] إضافة إشعارات للموظف عند تغيير حالة طلبه
- [ ] إضافة إشعارات للـ Dashboard

## 📚 الملفات المرجعية

- `REALTIME_NOTIFICATIONS_SETUP.md` - دليل إعداد مفصل
- `APPROVAL_SYSTEM_DOCUMENTATION.md` - توثيق نظام الموافقات
- Laravel Broadcasting Docs: https://laravel.com/docs/broadcasting
