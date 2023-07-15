<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
// use Illuminate\Contracts\Auth\Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;
    protected $table = 'users';

    protected $primaryKey = 'id_user';

    protected $fillable = [
        'nom_user',
        'prenom_user',
        'mdp_user',
        'type_user',
        'adresse_user',
    ];



    

    protected $hidden = [
        'mdp_user',
    ];

    public function getAuthPassword()
    {
        return $this->mdp_user;
    }
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}
