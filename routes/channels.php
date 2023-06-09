<?php

use App\Events\AvailableUser;
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

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});


//Broadcast::channel('sendMessagePrivate.{sender}.{receiver}', function ($user) {
//    return !is_null($user);
//});

Broadcast::channel('sendMessagePrivate.{receiver}', function ($user) {
    return !is_null($user);
});
//Broadcast::channel('receiveMessagePrivate.{receiver}.{sender}', function ($user) {
//    return !is_null($user);
//});
Broadcast::channel('presence.online.{receiver}', function ($user) {
    if (!is_null($user)){
        return $user;
    }
});

Broadcast::channel("group_chat.{roomId}", function ($user,$roomId) {
    if (true) {
        return ['id' => $user->id, 'name' => $user->name];
    }
});
