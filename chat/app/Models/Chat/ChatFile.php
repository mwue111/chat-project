<?php

namespace App\Models\Chat;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ChatFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'file_name',
        'type',
        'resolution',
        'size',
        'uniqid',
        'file',
    ];

    //mutators

    //para que las columnas de creación y actualización estén en la zona horaria española (útil para producción)
    public function setCreatedAtAttribute($value) {
        date_default_timezone_set("Europe/Madrid");
        $this->attributes['created_at'] = Carbon::now();
    }

    public function setUpdatedAtAttribute($value) {
        date_default_timezone_set("Europe/Madrid");
        $this->attributes['updated_at'] = Carbon::now();
    }

    //para obtener datos en un formato concreto
    public function getSizeAttribute($size) {
        $size = (int) $size;
        $base = log($size) / log(1024);
        $suffixes = array(' bytes', ' KB', ' MB', ' GB', ' TB');
        return round(pow(1024, $base - floor($base)), 2) . $suffixes[floor($base)];
    }

    public function getNameFileAttributes() {
        $name = str_replace(' ', '-', $this->file_name);
        $newname = str_replace('_', '-', $name);
        return $newname;
    }

    /*
    //relaciones

    //con chat

    public function Chat() {
        return $this->hasOne(Chat::class, 'chat_file_id');
        return $this->hasMany(Chat::class, 'chat_file_id');
    }
    */
}
