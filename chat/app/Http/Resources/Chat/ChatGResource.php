<?php

namespace App\Http\Resources\Chat;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChatGResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request)
    {
        date_default_timezone_set("Europe/Madrid");
        return [
            //Estamos en la tabla chat_rooms. Si el usuario autenticado NO es first_user, devuélveme los datos. Si lo es, devuelve null (porque no se necesitan los datos del usuario autenticado, se necesitan los datos del otro usuario/grupo).
            'friend_first' => $this->resource->first_user != auth('api')->user()->id ?
                [
                    'id' => $this->resource->FirstUser->id,   //se hace referencia a la función en el modelo ChatRoom
                    'full_name' => $this->resource->FirstUser->name . ' ' . $this->resource->FirstUser->surname,
                    'avatar' =>  $this->resource->FirstUser->avatar ? env('APP_URL') . 'storage/' . $this->resource->FirstUser->avatar : null, //ruta igual que en el resource de usuario (previa comprobación de si hay avatar o no)
                ] : null,
            //puede que no exista second_user (cuando se trata de un grupo), por eso aquí hay dos terciarias anidadas
            'friend_second' => $this->resource->second_user ?
            $this->resource->second_user != auth('api')->user()->id ?
                [
                    'id' => $this->resource->SecondUser->id,
                    'full_name' => $this->resource->SecondUser->name . ' ' . $this->resource->SecondUser->surname,
                    'avatar' => $this->resource->SecondUser->avatar ? env('APP_URL') . 'storage/' . $this->resource->SecondUser->avatar : null,
                ] : null
            : null,
            'group_chat' => $this->resource->chat_group_id ?
                [
                    'id' => $this->resource->ChatGroup->id,
                    'name' => $this->resource->ChatGroup->name,
                    'avatar' => null,   //se pondrá un placeholder

                    'last_message' => $this->resource->ChatGroup->last_message,
                    'last_message_is_mine' => $this->resource->ChatGroup->last_message_user ?
                            $this->resource->ChatGroup->last_message_user === auth('api')->user()->id
                        : null,
                    'last_time' => $this->resource->ChatGroup->last_time_created_at,
                    'count_message' => $this->resource->ChatGroup->getCountMessages(auth('api')->user()->id),

                ] : null,
            'uniqid' => $this->resource->uniqid,
            'is_active' => false,
            'last_message' => $this->resource->last_message, //$this->resource->Chats->sortByDesc('id')->first() o llamada al mutator getLastMessageAttribute en el modelo como last_message.
            'last_message_is_mine' => $this->resource->last_message_user ? $this->resource->last_message_user === auth('api')->user()->id : null,
            'last_time' => $this->resource->last_time_created_at,
            // 'last_time' => $this->resource->last_time_created_at ? $this->resource->last_time_created_at : null,
            'count_message' => $this->resource->getCountMessages(auth('api')->user()->id),
        ];
    }
}
