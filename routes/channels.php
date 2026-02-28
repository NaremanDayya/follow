<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\Client;

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

Broadcast::channel('client-chat.{clientId}', function ($user, $clientId) {
    $client = Client::find($clientId);
    return $client && $user->canAccessClient($client);
});

Broadcast::channel('client.{clientId}', function ($user, $clientId) {
    $client = Client::find($clientId);
    return $client && $user->canAccessClient($client);
});

Broadcast::channel('clients', function ($user) {
    return $user->canManageClients();
});

Broadcast::channel('notifications.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});
