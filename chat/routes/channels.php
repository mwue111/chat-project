<?php

use App\Models\Chat\ChatRoom;
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

Broadcast::channel('chat.room.{uniqid}', function($user, $uniqid) { //$user es el usuario autenticado
    $chatroom = ChatRoom::where('uniqid', $uniqid)->first();
    if($chatroom->chat_group_id){
        //si es un chat grupal, continúa con la petición
        return true;
    }
    else{
        //si no es un chat grupal: continuar con la petición si el usuario autenticado es el que envía o el que recibe el mensaje
        return (int) $user->id === (int) $chatroom->first_user || (int) $user->id === (int) $chatroom->second_user;
    }
});

Broadcast::channel('chat.refresh.room.{id}', function($user, $id) {
    //comprobar si el id es el del usuario autenticado
    return (int) $user->id === (int) $id;
});
