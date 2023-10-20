<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'surname',
        'birthdate',
        'email',
        'website',
        'phone',
        'password',
        'address',
        'avatar',
        'fb',
        'tw',
        'ig',
        'lnkdn',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

     /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    //mutator: para evitar hacer validación de la contraseña en el controlador
    public function setPasswordAttribute($password){
        if($password){
            // dd($password);
            $this->attributes['password'] = bcrypt($password);
        }
    }

    /*
    //relaciones

    //con chat rooms
    public function ChatRoomFirst() {
        return $this->hasMany(ChatRoom::class, 'first_user');
    }

    public function ChatRoomSecond() {
        return $this->hasMany(ChatRoom::class, 'second_user');
    }

    //con chat
    public function Chat() {
        return $this->hasMany(Chat::class, 'user_id')
    }
    */
}
