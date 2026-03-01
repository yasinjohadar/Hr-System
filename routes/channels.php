<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

// قناة خاصة لكل مستخدم - فقط المستخدم نفسه يمكنه الاستماع
Broadcast::channel('user.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});

// قناة عامة للإشعارات - جميع المستخدمين المصرح لهم
Broadcast::channel('notifications', function ($user) {
    return $user !== null;
});

// قناة عامة للموافقات - جميع المستخدمين المصرح لهم
Broadcast::channel('approvals', function ($user) {
    return $user !== null;
});
