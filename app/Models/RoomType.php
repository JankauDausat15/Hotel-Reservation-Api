<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomType extends Model
{
    // Tambahkan 'category' di sini agar Laravel mengizinkan data ini masuk
    protected $fillable = ['category', 'name', 'description', 'price_per_night', 'image'];

    public function reservations() {
        return $this->hasMany(Reservation::class);
    }
}