<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Room extends Model
{
    protected $fillable = [
        'name','slug','description','capacity','floor',
        'building','type','status','image','requires_approval'
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($room) {
            $room->slug = Str::slug($room->name) . '-' . Str::random(4);
        });
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function equipment()
    {
        return $this->belongsToMany(Equipment::class, 'room_equipment')
                    ->withPivot('quantity');
    }
}