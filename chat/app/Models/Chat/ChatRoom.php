<?php

namespace App\Models\Chat;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class ChatRoom extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'first_user',
        'second_user',
        'chat_group_id',
        'last_at',
        'uniqid',
    ];

    //mutators para que las columnas de creación y actualización estén en la zona horaria española (útil para producción)
    public function setCreatedAttribute($value) {
        date_default_timezone_set("Europe/Madrid");
        $this->attributes['created_at'] = Carbon::now();
    }

    public function setUpdatedAttribute($value) {
        date_default_timezone_set("Europe/Madrid");
        $this->attributes['updated_at'] = Carbon::now();
    }

    //relaciones

    //con usuario:
    public function FirstUser() {
        return $this->belongsTo(User::class, 'first_user');
    }

    public function SecondUser() {
        return $this->belongsTo(User::class, 'second_user');
    }

    //con chat_groups:
    public function ChatGroup() {
        return $this->belongsTo(ChatGroup::class, 'chat_group_id');
    }

    //con chats:
    public function Chats() {
        return $this->hasMany(Chat::class, 'chat_room_id'); //nombre de la columna que relaciona ambas tablas en chat
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
    public function getLastTimeCreatedAtAttribute() {
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
