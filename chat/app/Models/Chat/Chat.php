<?php

namespace App\Models\Chat;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Chat extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',  //from_user_id
        'chat_room_id',
        'chat_group_id',
        'message',
        'chat_file_id',
        'read_at',
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

    //con user
    public function FromUser() {
        return $this->belongsTo(User::class, 'user_id');
    }

    //con chat rooms
    public function ChatRoom() {
        return $this->belongsTo(ChatRoom::class, 'chat_room_id');
    }

    //con chat groups
    public function ChatGroup() {
        return $this->belongsTo(ChatGroup::class, 'chat_group_id');
    }

    //con chat files
    public function ChatFile() {
        return $this->belongsTo(ChatFile::class, 'chat_file_id');
    }
}
