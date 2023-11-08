<?php

namespace App\Models\Chat;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class ChatGroup extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'uniqid',
    ];

    //para que las columnas de creación y actualización estén en la zona horaria española (útil para producción)
    public function setCreatedAtAttribute($value) {
        date_default_timezone_set("Europe/Madrid");
        $this->attributes['created_at'] = Carbon::now();
    }

    public function setUpdatedAtAttribute($value) {
        date_default_timezone_set("Europe/Madrid");
        $this->attributes['updated_at'] = Carbon::now();
    }

    //relaciones

    //con chats
    public function Chats() {
        return $this->hasMany(Chat::class, 'chat_group_id');
    }

    //con chat rooms
    public function ChatRooms() {
        return $this->hasMany(ChatRoom::class, 'chat_group_id');
    }

    //mutator para obtener el último mensaje en la función listMyChats de ChatController
    public function getLastMessageAttribute() {

        $chat = $this->Chats->sortByDesc('id')->first();
        return $chat ?
            $chat->message ?
                $chat->message : 'Archivo enviado'
            : null;
    }

    //mutator para comprobar si el último mensaje enviado es del usuario autenticado o no en la función listMyChats de ChatController
    public function getLastMessageUserAttribute() {

        $chat = $this->Chats->sortByDesc('id')->first();

        return $chat ? $chat->user_id : null;
    }

    //mutator que devuelve la fecha de creación del último mensaje enviado
    public function getLastTimeCreatedAtAttibute() {
        $chat = $this->Chats->sortByDesc('id')->first();

        return $chat ? $chat->created_at->diffForHumans() : null;
    }

    //función para contar la cantidad de mensajes no leídos por el usuario autenticado
    public function getCountMessages($user) {
        return $this->Chats->where('user_id', '<>', $user)
                        ->where('read_at', null)
                        ->count();
    }
}
