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

Broadcast::channel('App.User.{id}', function ($user, $id) {
    return (int)$user->id === (int)$id;
});

Broadcast::channel('skate', function ($user) {
    return [];
});

Broadcast::channel('skating.aid', function ($user) {
    return [];
});

Broadcast::channel('gate.ticket', function ($user) {
    return [];
});

Broadcast::channel('gate.rink.ticket', function ($user) {
    return [];
});

Broadcast::channel('skate.transaction', function ($user) {
    return [];
});

Broadcast::channel('skating.aid.transaction', function ($user) {
    return [];
});