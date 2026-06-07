<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    protected $fillable = [
        'name','email','password','role','avatar','phone','department','is_active'
    ];

    protected $hidden = ['password'];

    protected $casts = ['is_active' => 'boolean'];

    public function getJWTIdentifier()       { return $this->getKey(); }
    public function getJWTCustomClaims()     { return []; }

    public function reservations() { return $this->hasMany(Reservation::class); }
    public function notifications() { return $this->hasMany(Notification::class); }
}