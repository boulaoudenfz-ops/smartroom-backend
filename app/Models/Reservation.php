<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    protected $fillable = [
        'user_id','room_id','title','description',
        'start_datetime','end_datetime','attendees_count',
        'status','qr_code','checked_in_at','rejection_reason'
    ];

    protected $casts = [
        'start_datetime' => 'datetime',
        'end_datetime'   => 'datetime',
        'checked_in_at'  => 'datetime',
    ];

    public function user()    { return $this->belongsTo(User::class); }
    public function room()    { return $this->belongsTo(Room::class); }
}