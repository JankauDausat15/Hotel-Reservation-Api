<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    protected $fillable = [
        'user_id',
        'room_type_id',
        'customer_name',
        'customer_email',
        'check_in',
        'check_out',
        'total_price'
    ];

    // Relasi ke RoomType
    public function roomType()
    {
        return $this->belongsTo(RoomType::class);
    }

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke Transaction
    public function transaction()
    {
        return $this->hasOne(Transaction::class);
    }
}