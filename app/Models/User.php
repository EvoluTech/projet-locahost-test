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
    protected $table = 'utilisateur';

    protected $primaryKey = 'id_utilisateur';

    protected $fillable = [
        'nom_utilisateur',
        'motpasse_utilisateur',
    ];

    protected $hidden = [
        'motpasse_utilisateur',
    ];

    public function getAuthPassword()
    {
        return $this->motpasse_utilisateur;
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
